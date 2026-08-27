<?php

namespace Tests\Unit;

use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_number_is_derived_from_the_submission_id(): void
    {
        $submission = ContactSubmission::create([
            'name' => 'Jane Doe',
            'rut' => '12.345.678-9',
            'email' => 'jane@example.com',
            'message' => 'Hola, quisiera más información.',
        ]);

        $this->assertSame(sprintf('PM7-%06d', $submission->id), $submission->tracking_number);
    }

    public function test_tracking_number_pads_the_id_with_leading_zeros(): void
    {
        $submission = ContactSubmission::create([
            'name' => 'John Doe',
            'rut' => '9.876.543-2',
            'email' => 'john@example.com',
            'message' => 'Otra consulta.',
        ]);

        $this->assertMatchesRegularExpression('/^PM7-\d{6}$/', $submission->tracking_number);
    }
}
