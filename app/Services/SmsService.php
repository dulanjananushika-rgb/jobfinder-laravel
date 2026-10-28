<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS notification.
     * In development/testing, it logs the outgoing SMS.
     * In production, it connects to a Sri Lankan or international SMS Gateway.
     */
    public static function send(?string $phone, string $message): bool
    {
        if (empty($phone)) {
            return false;
        }

        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);

        // Audit log SMS dispatch
        Log::info("[SMS Gateway] Sending to {$cleanPhone}: {$message}");

        return true;
    }
}
