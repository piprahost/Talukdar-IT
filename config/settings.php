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
    ],
];
