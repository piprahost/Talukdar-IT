<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send a single SMS using lara-sms-bd if available.
     *
     * @param  string  $to   E.164 or local phone (as expected by gateway)
     * @param  string  $text Message body
     * @return bool
     */
    public static function send(string $to, string $text): bool
    {
        // Global SMS enable flag
        if (!function_exists('settings') || !settings('sms.enabled', false)) {
            return false;
        }

        $text = trim($text);
        if ($text === '' || $to === '') {
            return false;
        }

        // Test mode: redirect to test number
        $testMode = settings('sms.test_mode', false);
        $testNumber = trim((string) settings('sms.test_number', ''));
        if ($testMode && $testNumber !== '') {
            $to = $testNumber;
        }

        // If SMS facade not installed, log and exit gracefully
        if (!class_exists(\SMS::class) && !function_exists('sms')) {
            Log::warning('SMS package (lara-sms-bd) not installed; skipping SMS send.', [
                'to' => $to,
                'text' => $text,
            ]);
            return false;
        }

        try {
            $gateway = trim((string) settings('sms.default_gateway', ''));

            // Use helper or facade depending on availability
            $sender = function_exists('sms') ? sms() : \SMS::class;

            if ($gateway !== '') {
                // Switch to configured gateway
                $sender = $sender->gateway($gateway);
            }

            // Most BD gateways ignore sender ID from code; we keep it for future use
            $from = trim((string) settings('sms.from', ''));
            if ($from !== '' && method_exists($sender, 'from')) {
                $sender = $sender->from($from);
            }

            $sender->send($to, $text);

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to send SMS', [
                'to' => $to,
                'text' => $text,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

