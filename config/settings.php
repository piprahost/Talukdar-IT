<?php

/**
 * Category-wise setting definitions for the ERP.
 * Each key can be used via: settings('category.key') or Setting::get('key', default, 'category')
 * type: text | boolean | integer
 */
return [
    'categories' => [
        'general' => [
            'label' => 'General',
            'icon' => 'fas fa-cog',
            'keys' => [
                'date_format' => [
                    'type' => 'text',
                    'label' => 'Date format',
                    'description' => 'PHP date format (e.g. d/m/Y, Y-m-d). Used in reports and lists.',
                    'default' => 'd/m/Y',
                ],
                'timezone' => [
                    'type' => 'text',
                    'label' => 'Timezone',
                    'description' => 'PHP timezone (e.g. Asia/Dhaka, UTC).',
                    'default' => 'Asia/Dhaka',
                ],
                'currency_code' => [
                    'type' => 'text',
                    'label' => 'Currency code',
                    'description' => 'e.g. BDT, USD.',
                    'default' => 'BDT',
                ],
                'currency_symbol' => [
                    'type' => 'text',
                    'label' => 'Currency symbol',
                    'description' => 'e.g. ৳, $.',
                    'default' => '৳',
                ],
            ],
        ],
        'sales' => [
            'label' => 'Sales',
            'icon' => 'fas fa-cash-register',
            'keys' => [
                'invoice_prefix' => [
                    'type' => 'text',
                    'label' => 'Invoice number prefix',
                    'description' => 'Prefix for invoice numbers (e.g. INV-).',
                    'default' => 'INV-',
                ],
                'default_payment_terms_days' => [
                    'type' => 'integer',
                    'label' => 'Default payment terms (days)',
                    'description' => 'Default due days for new invoices (0 = due on receipt).',
                    'default' => '0',
                ],
                'allow_negative_stock' => [
                    'type' => 'boolean',
                    'label' => 'Allow negative stock on sale',
                    'description' => 'Allow completing sale when product stock is below quantity.',
                    'default' => '0',
                ],
                'customer_optional' => [
                    'type' => 'boolean',
                    'label' => 'Customer optional (walk-in)',
                    'description' => 'Allow sales without selecting a customer (walk-in).',
                    'default' => '1',
                ],
            ],
        ],
        'purchases' => [
            'label' => 'Purchases',
            'icon' => 'fas fa-shopping-cart',
            'keys' => [
                'po_prefix' => [
                    'type' => 'text',
                    'label' => 'PO number prefix',
                    'description' => 'Prefix for purchase order numbers (e.g. PO-).',
                    'default' => 'PO-',
                ],
                'require_supplier' => [
                    'type' => 'boolean',
                    'label' => 'Require supplier',
                    'description' => 'Require selecting a supplier (walk-in still allowed if enabled).',
                    'default' => '0',
                ],
            ],
        ],
        'services' => [
            'label' => 'Service Orders',
            'icon' => 'fas fa-laptop-medical',
            'keys' => [
                'service_memo_prefix' => [
                    'type' => 'text',
                    'label' => 'Service memo number prefix',
                    'description' => 'Prefix for service order numbers (e.g. SRV-).',
                    'default' => 'SRV-',
                ],
                'default_status' => [
                    'type' => 'text',
                    'label' => 'Default status for new orders',
                    'description' => 'pending, in_progress, completed, delivered, cancelled.',
                    'default' => 'pending',
                ],
            ],
        ],
        'products' => [
            'label' => 'Products & Stock',
            'icon' => 'fas fa-boxes',
            'keys' => [
                'low_stock_threshold' => [
                    'type' => 'integer',
                    'label' => 'Global low stock threshold',
                    'description' => 'Default reorder level for dashboard/low-stock when product has none set (0 = use product reorder_level only).',
                    'default' => '5',
                ],
                'require_barcode' => [
                    'type' => 'boolean',
                    'label' => 'Require barcode on products',
                    'description' => 'Make barcode required when creating/editing products.',
                    'default' => '0',
                ],
                'category_required' => [
                    'type' => 'boolean',
                    'label' => 'Category required',
                    'description' => 'Require category when creating products.',
                    'default' => '0',
                ],
            ],
        ],
        'payments' => [
            'label' => 'Payments',
            'icon' => 'fas fa-money-bill-wave',
            'keys' => [
                'default_payment_method' => [
                    'type' => 'text',
                    'label' => 'Default payment method',
                    'description' => 'cash, bank_transfer, card, etc. Used when adding payment.',
                    'default' => 'cash',
                ],
            ],
        ],
        'expenses' => [
            'label' => 'Expenses',
            'icon' => 'fas fa-receipt',
            'keys' => [
                'require_approval' => [
                    'type' => 'boolean',
                    'label' => 'Expenses require approval',
                    'description' => 'New expenses need approval before marking paid.',
                    'default' => '1',
                ],
            ],
        ],
        'warranty' => [
            'label' => 'Warranty',
            'icon' => 'fas fa-shield-alt',
            'keys' => [
                'warranty_days_default' => [
                    'type' => 'integer',
                    'label' => 'Default warranty period (days)',
                    'description' => 'Default warranty days for service/replacement parts.',
                    'default' => '90',
                ],
            ],
        ],
        'returns' => [
            'label' => 'Returns',
            'icon' => 'fas fa-undo',
            'keys' => [
                'require_approval' => [
                    'type' => 'boolean',
                    'label' => 'Returns require approval',
                    'description' => 'Sale/Purchase/Service returns need approval before complete.',
                    'default' => '1',
                ],
            ],
        ],
        'accounting' => [
            'label' => 'Accounting',
            'icon' => 'fas fa-book',
            'keys' => [
                'fiscal_year_start_month' => [
                    'type' => 'integer',
                    'label' => 'Fiscal year start month',
                    'description' => '1-12. e.g. 7 for July start.',
                    'default' => '7',
                ],
            ],
        ],
        'reports' => [
            'label' => 'Reports',
            'icon' => 'fas fa-chart-bar',
            'keys' => [
                'default_date_range_days' => [
                    'type' => 'integer',
                    'label' => 'Default date range (days)',
                    'description' => 'Default number of days for report date range (e.g. 30).',
                    'default' => '30',
                ],
            ],
        ],
        'dashboard' => [
            'label' => 'Dashboard',
            'icon' => 'fas fa-home',
            'keys' => [
                'show_sales_chart' => [
                    'type' => 'boolean',
                    'label' => 'Show sales chart',
                    'description' => 'Display 30-day sales chart on dashboard.',
                    'default' => '1',
                ],
                'show_low_stock_alert' => [
                    'type' => 'boolean',
                    'label' => 'Show low stock alert',
                    'description' => 'Show low stock products in dashboard alerts.',
                    'default' => '1',
                ],
                'low_stock_threshold' => [
                    'type' => 'integer',
                    'label' => 'Low stock threshold (dashboard)',
                    'description' => 'Products at or below this quantity appear in low stock alert (0 = use product reorder_level).',
                    'default' => '5',
                ],
            ],
        ],
        'sms' => [
            'label' => 'SMS & Notifications',
            'icon' => 'fas fa-sms',
            'keys' => [
                'enabled' => [
                    'type' => 'boolean',
                    'label' => 'Enable SMS notifications',
                    'description' => 'Turn all outbound SMS notifications on or off.',
                    'default' => '0',
                ],
                'default_gateway' => [
                    'type' => 'text',
                    'label' => 'SMS provider / gateway',
                    'description' => 'Select the SMS gateway to use. Configure API credentials below.',
                    'default' => '',
                    'options' => [
                        '' => '— Select provider —',
                        'bangladeshsms' => 'Bangladesh SMS',
                        'teletalk' => 'Teletalk',
                        'ssl' => 'SSL Wireless',
                        'bulksmsbd' => 'Bulk SMS BD',
                        'robi' => 'Robi SMS',
                        'gp' => 'Grameenphone SMS',
                        'banglalink' => 'Banglalink SMS',
                        'robi_bulk' => 'Robi Bulk SMS',
                        'gp_bulk' => 'GP Bulk SMS',
                        'infobip' => 'Infobip',
                        'nexmo' => 'Vonage (Nexmo)',
                        'twilio' => 'Twilio',
                        'other' => 'Other (use package config)',
                    ],
                ],
                'api_key' => [
                    'type' => 'text',
                    'label' => 'API Key',
                    'description' => 'API key or API key from your SMS provider (Bangladesh SMS, SSL, Bulk SMS BD, etc.).',
                    'default' => '',
                    'input_type' => 'password',
                ],
                'api_secret' => [
                    'type' => 'text',
                    'label' => 'API Secret (optional)',
                    'description' => 'API secret if required by the provider.',
                    'default' => '',
                    'input_type' => 'password',
                ],
                'sender_id' => [
                    'type' => 'text',
                    'label' => 'Sender ID / Mask',
                    'description' => 'Sender name or mask approved by the provider (e.g. COMPANY).',
                    'default' => '',
                ],
                'username' => [
                    'type' => 'text',
                    'label' => 'Username (Teletalk / some gateways)',
                    'description' => 'Login username if the gateway uses username/password instead of API key.',
                    'default' => '',
                ],
                'password' => [
                    'type' => 'text',
                    'label' => 'Password (Teletalk / some gateways)',
                    'description' => 'Password for the gateway when using username/password auth.',
                    'default' => '',
                    'input_type' => 'password',
                ],
                'from' => [
                    'type' => 'text',
                    'label' => 'From number (Twilio)',
                    'description' => 'Twilio: sender phone number (e.g. +1234567890).',
                    'default' => '',
                ],
                'test_mode' => [
                    'type' => 'boolean',
                    'label' => 'Test mode (send only to test number)',
                    'description' => 'When enabled, all SMS will be redirected to the test number below.',
                    'default' => '0',
                ],
                'test_number' => [
                    'type' => 'text',
                    'label' => 'Test phone number',
                    'description' => 'Used when test mode is enabled.',
                    'default' => '',
                ],
            ],
        ],
        'sms_notifications' => [
            'label' => 'SMS Notification Rules',
            'icon' => 'fas fa-bell',
            'keys' => [
                'sale_completed_enabled' => [
                    'type' => 'boolean',
                    'label' => 'Sale completed – send SMS to customer',
                    'description' => 'Send an SMS when a sale invoice is completed.',
                    'default' => '0',
                ],
                'sale_completed_template' => [
                    'type' => 'text',
                    'label' => 'Sale completed SMS template',
                    'description' => 'Placeholders: {customer_name}, {invoice_number}, {total_amount}, {paid_amount}, {due_amount}.',
                    'default' => 'Dear {customer_name}, your invoice {invoice_number} total {total_amount}, paid {paid_amount}, due {due_amount}. Thank you for shopping with us.',
                ],
                'customer_payment_enabled' => [
                    'type' => 'boolean',
                    'label' => 'Customer payment received – SMS',
                    'description' => 'Send an SMS when you record a customer payment.',
                    'default' => '0',
                ],
                'customer_payment_template' => [
                    'type' => 'text',
                    'label' => 'Customer payment SMS template',
                    'description' => 'Placeholders: {customer_name}, {invoice_number}, {amount}, {due_amount}.',
                    'default' => 'We received {amount} for invoice {invoice_number}. Remaining due: {due_amount}. Thank you, {customer_name}.',
                ],
                'supplier_payment_enabled' => [
                    'type' => 'boolean',
                    'label' => 'Supplier payment made – SMS',
                    'description' => 'Send an SMS when you record a supplier payment.',
                    'default' => '0',
                ],
                'supplier_payment_template' => [
                    'type' => 'text',
                    'label' => 'Supplier payment SMS template',
                    'description' => 'Placeholders: {supplier_name}, {po_number}, {amount}, {due_amount}.',
                    'default' => 'Payment {amount} made for PO {po_number}. Remaining due: {due_amount}. Regards, {supplier_name}.',
                ],
                'service_created_enabled' => [
                    'type' => 'boolean',
                    'label' => 'Service order created – SMS',
                    'description' => 'Send an SMS when a new service order is created.',
                    'default' => '0',
                ],
                'service_created_template' => [
                    'type' => 'text',
                    'label' => 'Service created SMS template',
                    'description' => 'Placeholders: {customer_name}, {service_number}, {problem_notes}.',
                    'default' => 'Dear {customer_name}, your device is received. Service number: {service_number}. Problem: {problem_notes}.',
                ],
                'service_status_enabled' => [
                    'type' => 'boolean',
                    'label' => 'Service status changed – SMS',
                    'description' => 'Send an SMS when service status is updated.',
                    'default' => '0',
                ],
                'service_status_template' => [
                    'type' => 'text',
                    'label' => 'Service status SMS template',
                    'description' => 'Placeholders: {customer_name}, {service_number}, {status}.',
                    'default' => 'Dear {customer_name}, your service {service_number} status is now: {status}.',
                ],
            ],
        ],
    ],
];
