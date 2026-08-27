<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaVerifier
{
    public function verify(string $token, string $expectedAction, string $remoteIp): bool
    {
        $secret = config('services.recaptcha.secret_key');

        if (! $secret) {
            Log::error('RECAPTCHA_SECRET_KEY is not configured.');

            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $remoteIp,
        ]);

        if (! $response->successful()) {
            return false;
        }

        $result = $response->json();
        $minScore = config('services.recaptcha.min_score');

        return ($result['success'] ?? false)
            && ($result['action'] ?? null) === $expectedAction
            && ($result['score'] ?? 0) >= $minScore;
    }
}
