<?php

namespace Tests\Feature;

use App\Mail\ContactFormSubmitted;
use App\Models\ContactSubmission;
use App\Services\RecaptchaVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.contact.to_email' => ['sales@example.com']]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Doe',
            'rut' => '12.345.678-9',
            'email' => 'jane@example.com',
            'phone' => '+56912345678',
            'company' => 'Acme SpA',
            'message' => 'Quisiera cotizar un servicio.',
            'recaptcha_token' => 'valid-token',
        ], $overrides);
    }

    private function mockRecaptcha(bool $isHuman): void
    {
        $this->mock(RecaptchaVerifier::class, function ($mock) use ($isHuman) {
            $mock->shouldReceive('verify')->once()->andReturn($isHuman);
        });
    }

    public function test_stores_submission_and_sends_email_on_valid_request(): void
    {
        Mail::fake();
        $this->mockRecaptcha(true);

        $response = $this->postJson('/api/contact', $this->validPayload());

        $response->assertCreated()
            ->assertJson(['message' => 'Solicitud recibida correctamente.']);

        $this->assertDatabaseHas('contact_submissions', [
            'name' => 'Jane Doe',
            'rut' => '12.345.678-9',
            'email' => 'jane@example.com',
            'phone' => '+56912345678',
            'company' => 'Acme SpA',
            'message' => 'Quisiera cotizar un servicio.',
        ]);

        $submission = ContactSubmission::sole();

        Mail::assertSent(ContactFormSubmitted::class, function (ContactFormSubmitted $mail) use ($submission) {
            return $mail->hasTo('sales@example.com')
                && $mail->lead['tracking_number'] === $submission->tracking_number
                && $mail->lead['name'] === 'Jane Doe';
        });
    }

    public function test_does_not_persist_recaptcha_token(): void
    {
        Mail::fake();
        $this->mockRecaptcha(true);

        $this->postJson('/api/contact', $this->validPayload());

        $submission = ContactSubmission::sole();

        $this->assertArrayNotHasKey('recaptcha_token', $submission->getAttributes());
    }

    public function test_rejects_request_when_recaptcha_verification_fails(): void
    {
        Mail::fake();
        $this->mockRecaptcha(false);

        $response = $this->postJson('/api/contact', $this->validPayload());

        $response->assertStatus(422)
            ->assertJson(['message' => 'No pudimos verificar que eres humano. Inténtalo de nuevo.']);

        $this->assertDatabaseCount('contact_submissions', 0);
        Mail::assertNothingSent();
    }

    public function test_validation_requires_mandatory_fields(): void
    {
        $response = $this->postJson('/api/contact', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'rut', 'email', 'message', 'recaptcha_token']);
    }

    public function test_validation_rejects_invalid_email(): void
    {
        $response = $this->postJson('/api/contact', $this->validPayload(['email' => 'not-an-email']));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_phone_and_company_are_optional(): void
    {
        Mail::fake();
        $this->mockRecaptcha(true);

        $payload = $this->validPayload();
        unset($payload['phone'], $payload['company']);

        $response = $this->postJson('/api/contact', $payload);

        $response->assertCreated();

        $this->assertDatabaseHas('contact_submissions', [
            'email' => 'jane@example.com',
            'phone' => null,
            'company' => null,
        ]);
    }

    public function test_contact_endpoint_is_rate_limited_per_ip(): void
    {
        Mail::fake();
        $this->mock(RecaptchaVerifier::class, function ($mock) {
            $mock->shouldReceive('verify')->andReturn(true);
        });

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/contact', $this->validPayload())->assertCreated();
        }

        $this->postJson('/api/contact', $this->validPayload())->assertStatus(429);
    }
}
