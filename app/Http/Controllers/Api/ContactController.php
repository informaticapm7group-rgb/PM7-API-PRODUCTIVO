<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactFormSubmitted;
use App\Models\ContactSubmission;
use App\Services\RecaptchaVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function __invoke(StoreContactRequest $request, RecaptchaVerifier $recaptcha): JsonResponse
    {
        $data = $request->validated();

        $isHuman = $recaptcha->verify(
            token: $data['recaptcha_token'],
            expectedAction: 'contact_form',
            remoteIp: $request->ip(),
        );

        if (! $isHuman) {
            return response()->json([
                'message' => 'No pudimos verificar que eres humano. Inténtalo de nuevo.',
            ], 422);
        }

        $attributes = collect($data)->except('recaptcha_token')->all();

        $submission = ContactSubmission::create($attributes);

        $lead = [...$attributes, 'tracking_number' => $submission->tracking_number];

        Mail::to(config('services.contact.to_email'))->send(new ContactFormSubmitted($lead));

        return response()->json([
            'message' => 'Solicitud recibida correctamente.',
        ], 201);
    }
}
