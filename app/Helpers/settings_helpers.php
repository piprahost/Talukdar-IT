<?php

/**
 * Format a number as money using the currency symbol from settings.
 */
if (!function_exists('money')) {
    function money($amount, int $decimals = 0): string
    {
        $symbol = function_exists('settings') ? (settings('general.currency_symbol') ?: '৳') : '৳';
        return $symbol . number_format((float) $amount, $decimals);
    }
}

/**
 * Format a date using the date format from settings.
 */
if (!function_exists('format_date')) {
    function format_date($date, ?string $overrideFormat = null): string
    {
        if ($date === null) {
            return '';
        }
        $dt = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
        $format = $overrideFormat ?? (function_exists('settings') ? (settings('general.date_format') ?: 'd/m/Y') : 'd/m/Y');
        return $dt->format($format);
    }
}
