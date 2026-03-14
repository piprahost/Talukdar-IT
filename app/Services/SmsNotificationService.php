<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Service;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Product;
use App\Models\Expense;

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

    public static function serviceCompleted(Service $service): void
    {
        if (!self::isEnabled('sms_notifications.service_completed_enabled')) {
            return;
        }
        if (!in_array($service->status, ['completed', 'delivered'], true)) {
            return;
        }

        $phone = $service->customer_phone ?? null;
        if (!$phone) {
            return;
        }

        $sym = function_exists('settings') ? (settings('general.currency_symbol') ?: '৳') : '৳';

        $text = self::render('sms_notifications.service_completed_template', [
            'customer_name' => $service->customer_name ?? 'Customer',
            'service_number' => $service->service_number,
            'service_cost' => $sym . number_format((float) ($service->service_cost ?? 0), 2),
        ]);

        if ($text !== '') {
            SmsService::send($phone, $text);
        }
    }

    public static function saleReturnCompleted(SaleReturn $return): void
    {
        if (!self::isEnabled('sms_notifications.sale_return_completed_enabled')) {
            return;
        }

        $return->load(['sale.customer', 'customer']);
        $sale = $return->sale;
        $phone = $return->customer?->phone ?? $sale?->customer_phone ?? $sale?->customer?->phone ?? null;
        if (!$phone) {
            return;
        }

        $sym = function_exists('settings') ? (settings('general.currency_symbol') ?: '৳') : '৳';
        $total = (float) $return->getDisplayTotalAmount();

        $text = self::render('sms_notifications.sale_return_completed_template', [
            'customer_name' => $return->customer?->name ?? $sale?->customer_name ?? 'Customer',
            'return_number' => $return->return_number,
            'invoice_number' => $sale?->invoice_number ?? '',
            'total_amount' => $sym . number_format($total, 2),
        ]);

        if ($text !== '') {
            SmsService::send($phone, $text);
        }
    }

    public static function purchaseReturnCompleted(PurchaseReturn $return): void
    {
        if (!self::isEnabled('sms_notifications.purchase_return_completed_enabled')) {
            return;
        }

        $return->load(['purchase.supplier']);
        $purchase = $return->purchase;
        $phone = $purchase?->supplier?->phone ?? $purchase?->supplier_phone ?? null;
        if (!$phone) {
            return;
        }

        $sym = function_exists('settings') ? (settings('general.currency_symbol') ?: '৳') : '৳';

        $text = self::render('sms_notifications.purchase_return_completed_template', [
            'supplier_name' => $purchase?->supplier?->name ?? $purchase?->supplier_name ?? 'Supplier',
            'return_number' => $return->return_number,
            'po_number' => $purchase?->po_number ?? '',
            'total_amount' => $sym . number_format((float) $return->total_amount, 2),
        ]);

        if ($text !== '') {
            SmsService::send($phone, $text);
        }
    }

    public static function purchaseReceived(Purchase $purchase): void
    {
        if (!self::isEnabled('sms_notifications.purchase_received_enabled')) {
            return;
        }
        if ($purchase->status !== 'received') {
            return;
        }

        $phone = $purchase->supplier?->phone ?? $purchase->supplier_phone ?? null;
        if (!$phone) {
            return;
        }

        $sym = function_exists('settings') ? (settings('general.currency_symbol') ?: '৳') : '৳';

        $text = self::render('sms_notifications.purchase_received_template', [
            'supplier_name' => $purchase->supplier?->name ?? $purchase->supplier_name ?? 'Supplier',
            'po_number' => $purchase->po_number,
            'total_amount' => $sym . number_format((float) $purchase->total_amount, 2),
        ]);

        if ($text !== '') {
            SmsService::send($phone, $text);
        }
    }

    public static function lowStockAlert(Product $product): void
    {
        if (!self::isEnabled('sms_notifications.low_stock_enabled')) {
            return;
        }

        $phone = trim((string) settings('sms_notifications.low_stock_phone', ''));
        if ($phone === '') {
            return;
        }

        $threshold = (int) settings('products.low_stock_threshold', 5);
        $stock = (int) ($product->stock_quantity ?? $product->display_stock ?? 0);
        if ($stock > $threshold) {
            return;
        }

        $text = self::render('sms_notifications.low_stock_template', [
            'product_name' => $product->name ?? 'Product',
            'product_sku' => $product->sku ?? $product->barcode ?? 'N/A',
            'current_stock' => (string) $stock,
            'threshold' => (string) $threshold,
        ]);

        if ($text !== '') {
            SmsService::send($phone, $text);
        }
    }

    public static function expenseApproved(Expense $expense): void
    {
        if (!self::isEnabled('sms_notifications.expense_approved_enabled')) {
            return;
        }

        $phone = trim((string) settings('sms_notifications.expense_alert_phone', ''));
        if ($phone === '') {
            return;
        }

        $sym = function_exists('settings') ? (settings('general.currency_symbol') ?: '৳') : '৳';

        $text = self::render('sms_notifications.expense_approved_template', [
            'amount' => $sym . number_format((float) $expense->amount, 2),
            'category' => $expense->category ?? 'N/A',
            'description' => $expense->description ?? '',
            'date' => $expense->expense_date?->format('d M Y') ?? '',
        ]);

        if ($text !== '') {
            SmsService::send($phone, $text);
        }
    }
}

