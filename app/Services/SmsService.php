<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send a single SMS via custom API (HTTP request from settings).
     *
     * @param  string  $to   Phone number
     * @param  string  $text Message body
     * @return bool
     */
    public static function send(string $to, string $text): bool
    {
        if (!function_exists('settings') || !settings('sms.enabled', false)) {
            return false;
        }

        $text = trim($text);
        if ($text === '' || $to === '') {
            return false;
        }

        $testMode = settings('sms.test_mode', false);
        $testNumber = trim((string) settings('sms.test_number', ''));
        if ($testMode && $testNumber !== '') {
            $to = $testNumber;
        }

        return static::sendViaCustomApi($to, $text);
    }

    /**
     * Send SMS via custom API (URL, method, headers, body from settings).
     */
    protected static function sendViaCustomApi(string $to, string $text): bool
    {
        $url = trim((string) settings('sms.custom_url', ''));
        if ($url === '') {
            Log::warning('SMS: API URL not configured.');
            return false;
        }

        $method = strtoupper(trim((string) settings('sms.custom_method', 'POST')));
        $headersRaw = trim((string) settings('sms.custom_headers', ''));
        $bodyRaw = trim((string) settings('sms.custom_body', ''));
        $senderId = trim((string) settings('sms.sender_id', ''));

        $replace = [
            '{{phone}}' => $to,
            '{{message}}' => $text,
            '{{sender_id}}' => $senderId,
        ];

        $url = str_replace(array_keys($replace), array_values($replace), $url);

        $headers = [];
        if ($headersRaw !== '') {
            foreach (preg_split('/\r?\n/', $headersRaw) as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                if (preg_match('/^([^:]+):\s*(.*)$/s', $line, $m)) {
                    $headers[trim($m[1])] = trim(str_replace(array_keys($replace), array_values($replace), $m[2]));
                }
            }
        }

        $body = $bodyRaw !== '' ? str_replace(array_keys($replace), array_values($replace), $bodyRaw) : '';

        try {
            $request = Http::withHeaders($headers)->timeout(15);

            if (in_array($method, ['GET', 'HEAD'], true)) {
                $response = $method === 'GET'
                    ? $request->get($url)
                    : $request->head($url);
            } else {
                $contentType = $headers['Content-Type'] ?? $headers['content-type'] ?? 'application/json';
                $response = $request->withBody($body, $contentType)->{strtolower($method)}($url);
            }

            if ($response->successful()) {
                return true;
            }

            Log::warning('SMS API: non-success response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('SMS API failed', [
                'to' => $to,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
