<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Service;
use App\Models\Payment;
use App\Models\Purchase;

class SmsNotificationService
{
    protected static function isEnabled(string $key): bool
    {
        return function_exists('settings') && settings($key, false);
    }

    protected static function render(string $templateKey, array $data): string
    {
        $template = function_exists('settings')
            ? (string) settings($templateKey, '')
            : '';

        if ($template === '') {
            return '';
        }

        $replacements = [];
        foreach ($data as $k => $v) {
            $replacements['{' . $k . '}'] = (string) $v;
        }

        return strtr($template, $replacements);
    }

    public static function saleCompleted(Sale $sale): void
    {
        if (!self::isEnabled('sms_notifications.sale_completed_enabled')) {
            return;
        }

        $phone = $sale->customer_phone ?? $sale->customer?->phone ?? null;
        if (!$phone) {
            return;
        }

        $sym = function_exists('settings') ? (settings('general.currency_symbol') ?: '৳') : '৳';

        $text = self::render('sms_notifications.sale_completed_template', [
            'customer_name' => $sale->customer?->name ?? $sale->customer_name ?? 'Customer',
            'invoice_number' => $sale->invoice_number,
            'total_amount' => $sym . number_format((float) $sale->total_amount, 2),
            'paid_amount' => $sym . number_format((float) $sale->paid_amount, 2),
            'due_amount' => $sym . number_format((float) $sale->due_amount, 2),
        ]);

        if ($text !== '') {
            SmsService::send($phone, $text);
        }
    }

    public static function customerPayment(Payment $payment): void
    {
        if (!self::isEnabled('sms_notifications.customer_payment_enabled')) {
            return;
        }
        if ($payment->payment_type !== 'customer') {
            return;
        }

        $sale = $payment->sale;
        $service = $payment->service;

        $phone = $sale?->customer_phone
            ?? $sale?->customer?->phone
            ?? $service?->customer_phone
            ?? null;

        if (!$phone) {
            return;
        }

        $sym = function_exists('settings') ? (settings('general.currency_symbol') ?: '৳') : '৳';

        $invoiceNumber = $sale?->invoice_number ?? $service?->service_number ?? $payment->payment_number;
        $customerName = $sale?->customer?->name
            ?? $sale?->customer_name
            ?? $service?->customer_name
            ?? 'Customer';

        // Use latest due from related model if available
        $dueAmount = null;
        if ($sale) {
            $dueAmount = $sale->fresh()->due_amount;
        } elseif ($service) {
            $dueAmount = $service->fresh()->due_amount;
        }

        $text = self::render('sms_notifications.customer_payment_template', [
            'customer_name' => $customerName,
            'invoice_number' => $invoiceNumber,
            'amount' => $sym . number_format((float) $payment->amount, 2),
            'due_amount' => $sym . number_format((float) ($dueAmount ?? 0), 2),
        ]);

        if ($text !== '') {
            SmsService::send($phone, $text);
        }
    }

    public static function supplierPayment(Payment $payment): void
    {
        if (!self::isEnabled('sms_notifications.supplier_payment_enabled')) {
            return;
        }
        if ($payment->payment_type !== 'supplier') {
            return;
        }

        $purchase = $payment->purchase;
        if (!$purchase) {
            return;
        }

        $supplier = $purchase->supplier;
        $phone = $supplier?->phone ?? $purchase->supplier_phone ?? null;
        if (!$phone) {
            return;
        }

        $sym = function_exists('settings') ? (settings('general.currency_symbol') ?: '৳') : '৳';
        $purchaseFresh = $purchase->fresh();

        $text = self::render('sms_notifications.supplier_payment_template', [
            'supplier_name' => $supplier?->name ?? $purchase->supplier_name ?? 'Supplier',
            'po_number' => $purchase->po_number,
            'amount' => $sym . number_format((float) $payment->amount, 2),
            'due_amount' => $sym . number_format((float) $purchaseFresh->due_amount, 2),
        ]);

        if ($text !== '') {
            SmsService::send($phone, $text);
        }
    }

    public static function serviceCreated(Service $service): void
    {
        if (!self::isEnabled('sms_notifications.service_created_enabled')) {
            return;
        }

        $phone = $service->customer_phone ?? null;
        if (!$phone) {
            return;
        }

        $text = self::render('sms_notifications.service_created_template', [
            'customer_name' => $service->customer_name ?? 'Customer',
            'service_number' => $service->service_number,
            'problem_notes' => $service->problem_notes ?? '',
        ]);

        if ($text !== '') {
            SmsService::send($phone, $text);
        }
    }

    public static function serviceStatusChanged(Service $service): void
    {
        if (!self::isEnabled('sms_notifications.service_status_enabled')) {
            return;
        }

        $phone = $service->customer_phone ?? null;
        if (!$phone) {
            return;
        }

        $text = self::render('sms_notifications.service_status_template', [
            'customer_name' => $service->customer_name ?? 'Customer',
            'service_number' => $service->service_number,
            'status' => $service->status,
        ]);

        if ($text !== '') {
            SmsService::send($phone, $text);
        }
    }
}

