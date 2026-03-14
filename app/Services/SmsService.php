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

            // Push admin-panel credentials into config so the SMS package uses them
            if ($gateway !== '' && $gateway !== 'other') {
                static::injectGatewayConfig($gateway);
            }

            // Use helper or facade depending on availability
            $sender = function_exists('sms') ? sms() : \SMS::class;

            if ($gateway !== '') {
                // Switch to configured gateway
                $sender = $sender->gateway($gateway);
            }

            // Sender ID / From: prefer settings, then package config
            $from = trim((string) (settings('sms.sender_id') ?: settings('sms.from', '')));
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

    /**
     * Inject gateway credentials from admin settings into config so the SMS package uses them.
     */
    protected static function injectGatewayConfig(string $gateway): void
    {
        $apiKey = trim((string) settings('sms.api_key', ''));
        $apiSecret = trim((string) settings('sms.api_secret', ''));
        $senderId = trim((string) settings('sms.sender_id', ''));
        $username = trim((string) settings('sms.username', ''));
        $password = trim((string) settings('sms.password', ''));
        $from = trim((string) settings('sms.from', ''));

        $payload = array_filter([
            'api_key' => $apiKey ?: null,
            'api_secret' => $apiSecret ?: null,
            'sender_id' => $senderId ?: $from ?: null,
            'username' => $username ?: null,
            'password' => $password ?: null,
            'from' => $from ?: null,
        ], fn ($v) => $v !== null && $v !== '');

        if ($payload === []) {
            return;
        }

        // lara-sms-bd and similar packages read config like smsbd.gateways.{name}
        $key = 'smsbd.gateways.' . $gateway;
        $existing = config($key, []);
        config([$key => array_merge(is_array($existing) ? $existing : [], $payload)]);
    }
}

