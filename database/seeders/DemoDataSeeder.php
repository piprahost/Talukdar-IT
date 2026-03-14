<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Service;
use App\Models\ServiceReturn;
use App\Models\Supplier;
use App\Models\Warranty;
use App\Models\WarrantySubmission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed realistic demo data for the application.
     */
    public function run(): void
    {
        $faker = fake();

        // Products: stock is built from purchases (1 barcode = 1 unit)
        $products = Product::all();
        if ($products->isEmpty()) {
            $this->command?->warn('No products found. Skipping demo transactional data seeding.');
            return;
        }

        // Customers & Suppliers
        $customers = $this->seedCustomers($faker, 15);
        $suppliers = $this->seedSuppliers($faker, 8);

        // Banking & Accounts
        $bankAccount = $this->seedBankAccount();

        // Purchases (increases stock) and supplier payments
        $purchases = $this->seedPurchasesWithItems($faker, $suppliers, $products, $bankAccount);

        // Sales, sale items and customer payments
        $sales = $this->seedSalesWithItems($faker, $customers, $products, $bankAccount);

        // Services (repair jobs)
        $services = $this->seedServices($faker, $customers, $bankAccount);

        // Returns & warranty submissions so those screens have data
        $this->seedPurchaseReturns($faker, $purchases);
        $this->seedSaleReturns($faker, $sales);
        $this->seedServiceReturns($faker, $services);
        $this->seedWarrantySubmissions($faker);

        // Operating expenses
        $this->seedExpenses($faker, $bankAccount);

        $this->command?->info('Bangladesh demo data seeded successfully.');
    }

    /** Bangladesh-friendly customer data: names, cities, addresses */
    protected function seedCustomers($faker, int $count): Collection
    {
        $bdCustomers = [
            ['name' => 'Abdul Karim', 'phone' => '+880 1712-345001', 'address' => 'House 45, Road 12, Dhanmondi', 'city' => 'Dhaka'],
            ['name' => 'Fatima Begum', 'phone' => '+880 1819-234567', 'address' => 'Block B, Bashundhara R/A', 'city' => 'Dhaka'],
            ['name' => 'Mohammad Rahim', 'phone' => '+880 1911-567890', 'address' => 'Agrabad Commercial Area', 'city' => 'Chittagong'],
            ['name' => 'Ayesha Siddika', 'phone' => '+880 1723-456789', 'address' => 'Zindabazar', 'city' => 'Sylhet'],
            ['name' => 'Hasan Mahmud', 'phone' => '+880 1856-789012', 'address' => 'Shaheb Bazar', 'city' => 'Rajshahi'],
            ['name' => 'Nargis Akter', 'phone' => '+880 1617-890123', 'address' => 'Khalishpur', 'city' => 'Khulna'],
            ['name' => 'Rafiqul Islam', 'phone' => '+880 1789-012345', 'address' => 'Mirpur 10', 'city' => 'Dhaka'],
            ['name' => 'Sharmin Jahan', 'phone' => '+880 1552-345678', 'address' => 'Gulshan 2', 'city' => 'Dhaka'],
            ['name' => 'Kamrul Hasan', 'phone' => '+880 1923-456789', 'address' => 'Chawkbazar', 'city' => 'Chittagong'],
            ['name' => 'Nasrin Akter', 'phone' => '+880 1734-567890', 'address' => 'Uposhohor', 'city' => 'Rajshahi'],
            ['name' => 'Imran Hossain', 'phone' => '+880 1812-678901', 'address' => 'Banani', 'city' => 'Dhaka'],
            ['name' => 'Tahmina Khatun', 'phone' => '+880 1698-789012', 'address' => 'Moulvibazar', 'city' => 'Sylhet'],
            ['name' => 'Jahangir Alam', 'phone' => '+880 1915-890123', 'address' => 'Motijheel', 'city' => 'Dhaka'],
            ['name' => 'Rukhsana Parvin', 'phone' => '+880 1776-901234', 'address' => 'Barisal Town', 'city' => 'Barisal'],
            ['name' => 'Sohel Rana', 'phone' => '+880 1845-012345', 'address' => 'Bogra', 'city' => 'Bogra'],
        ];

        $customers = collect();
        foreach (array_slice($bdCustomers, 0, $count) as $i => $c) {
            $email = 'customer' . ($i + 1) . '@example.com.bd';
            $customers->push(Customer::create([
                'name' => $c['name'],
                'email' => $email,
                'phone' => $c['phone'],
                'mobile' => $c['phone'],
                'address' => $c['address'],
                'city' => $c['city'],
                'country' => 'Bangladesh',
                'tax_id' => $faker->optional(0.3)->numerify('TIN-########'),
                'notes' => $faker->optional(0.2)->sentence(),
                'is_active' => true,
            ]));
        }

        return $customers;
    }

    /** Bangladesh-friendly supplier data: local distributors and shops */
    protected function seedSuppliers($faker, int $count): Collection
    {
        $bdSuppliers = [
            ['name' => 'Tech Solutions BD', 'address' => 'Elephant Road', 'city' => 'Dhaka', 'phone' => '+880 2-9123456'],
            ['name' => 'Computer World Ltd', 'address' => 'Gulshan 1', 'city' => 'Dhaka', 'phone' => '+880 1710-111222'],
            ['name' => 'Star Tech & Engineering', 'address' => 'Agrabad', 'city' => 'Chittagong', 'phone' => '+880 31-2512345'],
            ['name' => 'Walton Plaza', 'address' => 'Mirpur 1', 'city' => 'Dhaka', 'phone' => '+880 2-8012345'],
            ['name' => 'Rahimafrooz Distribution', 'address' => 'Tejgaon I/A', 'city' => 'Dhaka', 'phone' => '+880 2-9881234'],
            ['name' => 'Symphony Showroom Sylhet', 'address' => 'Zindabazar', 'city' => 'Sylhet', 'phone' => '+880 821-712345'],
            ['name' => 'Mega Electronics', 'address' => 'Shaheb Bazar', 'city' => 'Rajshahi', 'phone' => '+880 721-761234'],
            ['name' => 'Chittagong Computer House', 'address' => 'Anderkilla', 'city' => 'Chittagong', 'phone' => '+880 31-2634567'],
        ];

        $suppliers = collect();
        foreach (array_slice($bdSuppliers, 0, $count) as $i => $s) {
            $suppliers->push(Supplier::create([
                'name' => $s['name'],
                'company_name' => $s['name'],
                'email' => 'info@' . Str::slug($s['name'], '') . '.com.bd',
                'phone' => $s['phone'],
                'mobile' => $s['phone'],
                'address' => $s['address'],
                'city' => $s['city'],
                'country' => 'Bangladesh',
                'tax_id' => $faker->optional(0.3)->numerify('VAT-########'),
                'notes' => $faker->optional(0.2)->sentence(),
                'is_active' => true,
            ]));
        }

        return $suppliers;
    }

    protected function seedBankAccount(): ?BankAccount
    {
        $bankAccountCoa = Account::where('code', '1100')->first();

        return BankAccount::firstOrCreate(
            ['account_number' => '1201010012345'],
            [
                'account_name' => 'Talukdar IT - Current Account',
                'bank_name' => 'BRAC Bank Ltd',
                'branch_name' => 'Gulshan Branch, Dhaka',
                'routing_number' => '060270167',
                'swift_code' => 'BRAKBDDH',
                'account_type' => 'current',
                'opening_balance' => 500000,
                'current_balance' => 500000,
                'account_id' => $bankAccountCoa?->id,
                'is_active' => true,
                'notes' => 'Primary business account (Bangladesh demo)',
            ]
        );
    }

    protected function seedPurchasesWithItems($faker, Collection $suppliers, Collection $products, ?BankAccount $bankAccount): Collection
    {
        $purchases = collect();
        if ($suppliers->isEmpty()) {
            return $purchases;
        }

        static $barcodeSeq = 0;

        for ($i = 0; $i < 8; $i++) {
            $supplier = $suppliers->random();
            $orderDate = now()->subDays($faker->numberBetween(20, 60));
            $expectedDate = (clone $orderDate)->addDays($faker->numberBetween(3, 10));

            $purchase = Purchase::create([
                'po_number' => 'PO-' . $orderDate->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'supplier_id' => $supplier->id,
                'order_date' => $orderDate,
                'expected_delivery_date' => $expectedDate,
                'received_date' => $expectedDate->copy()->addDays($faker->numberBetween(0, 3)),
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
                'payment_method' => 'bank_transfer',
                'bank_account_id' => $bankAccount?->id,
                'status' => 'received',
                'payment_status' => 'unpaid',
                'notes' => 'Stock purchase (BD demo)',
                'internal_notes' => 'Bangladesh demo seed',
            ]);

            // 1 stock = 1 barcode: one purchase_item per unit, each with unique barcode
            $lineCount = $faker->numberBetween(2, 5);
            for ($l = 0; $l < $lineCount; $l++) {
                $product = $products->random();
                $units = $faker->numberBetween(5, 20);
                $costPrice = $product->cost_price ?: $faker->numberBetween(1000, 50000);
                $sellingPrice = $product->selling_price ?: ($costPrice * 1.2);

                for ($u = 0; $u < $units; $u++) {
                    $barcode = 'BD-' . sprintf('%010d', ++$barcodeSeq);
                    PurchaseItem::withoutEvents(function () use ($purchase, $product, $barcode, $faker, $costPrice, $sellingPrice) {
                        PurchaseItem::create([
                            'purchase_order_id' => $purchase->id,
                            'product_id' => $product->id,
                            'barcode' => $barcode,
                            'serial_number' => $faker->optional(0.4)->bothify('SN-########'),
                            'cost_price' => $costPrice,
                            'selling_price' => $sellingPrice,
                            'quantity' => 1,
                            'status' => 'received',
                            'received_date' => $purchase->received_date,
                            'condition_notes' => 'New stock',
                            'warranty_info' => 'Standard manufacturer warranty',
                            'notes' => 'BD demo',
                        ]);
                    });
                }
            }

            // Sync product barcodes and stock from purchase items (1 barcode = 1 unit)
            $purchase->items()->get()->groupBy('product_id')->each(function ($items, $productId) {
                $product = Product::find($productId);
                if (!$product) {
                    return;
                }
                $barcodes = $items->pluck('barcode')->filter()->values()->all();
                if (count($barcodes) > 0) {
                    $product->addBarcodes($barcodes, true, 'Demo purchase receive (1 barcode = 1 stock)');
                }
            });

            $purchase->refresh();
            $subtotal = $purchase->items()->sum(\DB::raw('cost_price * quantity'));
            $tax = round($subtotal * 0.05, 2); // 5% tax
            $discount = round($subtotal * 0.02, 2); // 2% discount
            $total = $subtotal + $tax - $discount;

            $paidAmount = $faker->randomElement([$total, $total * 0.5, 0]);

            $purchase->update([
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'paid_amount' => $paidAmount,
                'due_amount' => max(0, $total - $paidAmount),
                'payment_status' => $paidAmount >= $total ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid'),
            ]);

            // Create supplier payment for paid portion
            if ($paidAmount > 0) {
                Payment::create([
                    'payment_number' => 'SP-' . $purchase->order_date->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'payment_type' => 'supplier',
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $supplier->id,
                    'amount' => $paidAmount,
                    'payment_date' => $purchase->order_date,
                    'payment_method' => 'bank_transfer',
                    'reference_number' => $faker->bothify('PMT-PO-######'),
                    'notes' => 'Supplier payment (BD demo)',
                ]);
            }

            $purchases->push($purchase);
        }

        return $purchases;
    }

    protected function seedSalesWithItems($faker, Collection $customers, Collection $products, ?BankAccount $bankAccount): Collection
    {
        $sales = collect();
        if ($customers->isEmpty()) {
            return $sales;
        }

        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            $saleDate = now()->subDays($faker->numberBetween(0, 30));

            $sale = Sale::create([
                'invoice_number' => 'INV-' . $saleDate->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone ?: $customer->mobile,
                'customer_address' => $customer->address,
                'sale_date' => $saleDate,
                'due_date' => $saleDate->copy()->addDays($faker->numberBetween(0, 10)),
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
                'payment_method' => 'cash',
                'bank_account_id' => $bankAccount?->id,
                'status' => 'completed',
                'payment_status' => 'unpaid',
                'notes' => 'Sale (BD demo)',
                'internal_notes' => 'Bangladesh demo seed',
            ]);

            // 1 stock = 1 barcode: one sale_item per unit, each with barcode from product
            $lineCount = $faker->numberBetween(1, 5);
            $saleItemCount = 0;
            for ($l = 0; $l < $lineCount; $l++) {
                $product = $products->random();
                $product->refresh();
                $barcodes = $product->barcodes ?? [];
                if (count($barcodes) === 0) {
                    continue;
                }
                $take = min($faker->numberBetween(1, 5), count($barcodes));
                $toSell = array_slice($barcodes, 0, $take);
                $unitPrice = $product->selling_price ?: $faker->numberBetween(2000, 80000);
                $discount = $faker->randomElement([0, $unitPrice * 0.05, $unitPrice * 0.1]);

                foreach ($toSell as $barcode) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'barcode' => $barcode,
                        'unit_price' => $unitPrice,
                        'discount' => $discount,
                        'quantity' => 1,
                        'subtotal' => 0,
                        'notes' => 'BD demo',
                    ]);
                    $saleItemCount++;
                }
            }

            if ($saleItemCount === 0) {
                $sale->delete();
                continue;
            }

            $sale->refresh();
            $subtotal = $sale->items()->sum('subtotal');
            $tax = round($subtotal * 0.05, 2); // 5% VAT
            $discount = round($subtotal * 0.03, 2); // 3% order discount
            $total = $subtotal + $tax - $discount;

            $paidAmount = $faker->randomElement([$total, $total * 0.5, 0]);

            $sale->update([
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'paid_amount' => $paidAmount,
                'due_amount' => max(0, $total - $paidAmount),
                'payment_status' => $paidAmount >= $total ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid'),
            ]);

            // Create customer payment for paid portion
            if ($paidAmount > 0) {
                Payment::create([
                    'payment_number' => 'CP-' . $sale->sale_date->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'payment_type' => 'customer',
                    'sale_id' => $sale->id,
                    'customer_id' => $customer->id,
                    'amount' => $paidAmount,
                    'payment_date' => $sale->sale_date,
                    'payment_method' => 'cash',
                    'reference_number' => $faker->bothify('PMT-INV-######'),
                    'notes' => 'Customer payment (BD demo)',
                ]);
            }

            $sales->push($sale);
        }

        return $sales;
    }

    protected function seedServices($faker, Collection $customers, ?BankAccount $bankAccount): Collection
    {
        $services = collect();

        if ($customers->isEmpty()) {
            return $services;
        }

        for ($i = 0; $i < 10; $i++) {
            $customer = $customers->random();
            $receiveDate = now()->subDays($faker->numberBetween(0, 20));
            $deliveryDate = $faker->boolean(70)
                ? $receiveDate->copy()->addDays($faker->numberBetween(1, 7))
                : null;

            $serviceCost = $faker->numberBetween(500, 8000);
            $paidAmount = $faker->randomElement([$serviceCost, $serviceCost * 0.5, 0]);

            $productNames = ['HP Laptop 15"', 'Dell Monitor 24"', 'Samsung Galaxy A54', 'Lenovo ThinkPad', 'Laptop screen replacement', 'Printer repair', 'Desktop CPU check-up', 'Keyboard replacement', 'Walton Primo S8', 'Power adapter repair'];
            $problems = ['Screen not turning on', 'Overheating issue', 'Battery not charging', 'Software reinstall required', 'Display flickering', 'USB ports not working', 'Keyboard keys not working', 'Slow performance', 'Speaker not working', 'Touchpad not responsive'];

            $services->push(Service::create([
                'service_number' => 'SRV-' . $receiveDate->format('Y') . '-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'product_name' => $productNames[$i % count($productNames)],
                'serial_number' => $faker->optional(0.6)->bothify('SRV-########'),
                'problem_notes' => $problems[$i % count($problems)],
                'service_notes' => $faker->optional(0.6)->sentence(10),
                'service_cost' => $serviceCost,
                'receive_date' => $receiveDate,
                'delivery_date' => $deliveryDate,
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone ?: $customer->mobile,
                'customer_address' => $customer->address,
                'paid_amount' => $paidAmount,
                'due_amount' => max(0, $serviceCost - $paidAmount),
                'payment_method' => $paidAmount > 0 ? 'cash' : 'other',
                'bank_account_id' => $bankAccount?->id,
                'status' => $deliveryDate ? 'completed' : 'in_progress',
                'internal_notes' => 'Service job (BD demo)',
            ]));
        }

        return $services;
    }

    protected function seedExpenses($faker, ?BankAccount $bankAccount): void
    {
        // Try to link to operating expense accounts if available
        $operatingExpenseAccount = Account::where('code', '6000')->first();
        $rentAccount = Account::where('code', '6100')->first();
        $utilitiesAccount = Account::where('code', '6200')->first();
        $salaryAccount = Account::where('code', '6300')->first();

        $categories = [
            ['name' => 'Rent', 'account' => $rentAccount, 'vendors' => ['Rahim Tower Management', 'Gulshan Plaza Ltd', 'Banani Holdings']],
            ['name' => 'Utilities', 'account' => $utilitiesAccount, 'vendors' => ['DESCO', 'Titas Gas', 'WASA']],
            ['name' => 'Salaries', 'account' => $salaryAccount, 'vendors' => ['Staff salary March', 'Staff salary February', 'Bonus payment']],
            ['name' => 'Miscellaneous', 'account' => $operatingExpenseAccount, 'vendors' => ['Office supplies - Star Kabir', 'Courier - Sundarban', 'Misc - Local']],
        ];

        foreach ($categories as $config) {
            for ($i = 0; $i < 3; $i++) {
                $amount = $faker->numberBetween(5000, 50000);
                $vendor = $config['vendors'][$i % count($config['vendors'])];

                Expense::create([
                    'expense_number' => 'EXP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'expense_date' => now()->subDays($faker->numberBetween(0, 45)),
                    'category' => $config['name'],
                    'account_id' => $config['account']?->id,
                    'amount' => $amount,
                    'payment_method' => $bankAccount ? 'bank_transfer' : 'cash',
                    'vendor_name' => $vendor,
                    'vendor_contact' => '+880 1' . $faker->numerify('###-######'),
                    'description' => $config['name'] . ' (BD demo)',
                    'reference_number' => $faker->bothify('EXP-######'),
                    'bank_account_id' => $bankAccount?->id,
                    'status' => $faker->randomElement(['approved', 'paid']),
                    'payment_date' => now()->subDays($faker->numberBetween(0, 30)),
                    'notes' => 'Operating expense (Bangladesh demo)',
                ]);
            }
        }
    }

    protected function seedPurchaseReturns($faker, Collection $purchases): void
    {
        if ($purchases->isEmpty()) {
            return;
        }

        $purchases->take(3)->each(function (Purchase $purchase) use ($faker) {
            if ($purchase->items()->count() === 0) {
                return;
            }

            $return = PurchaseReturn::create([
                'return_number' => 'PR-' . $purchase->order_date->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'return_date' => $purchase->order_date->copy()->addDays($faker->numberBetween(1, 10)),
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'status' => 'approved',
                'reason' => 'Damaged or incorrect items',
                'notes' => 'Purchase return (BD demo)',
            ]);

            $items = $purchase->items()->take(2)->get();
            foreach ($items as $pi) {
                $qty = max(1, (int) floor($pi->quantity / 4));

                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'purchase_item_id' => $pi->id,
                    'product_id' => $pi->product_id,
                    'barcode' => $pi->barcode,
                    'cost_price' => $pi->cost_price,
                    'quantity' => $qty,
                    'subtotal' => $pi->cost_price * $qty,
                    'reason' => 'Defective / wrong item',
                ]);
            }

            $return->refresh();
            $return->update([
                'tax_amount' => 0,
                'discount_amount' => 0,
                'status' => 'approved',
            ]);

            // Mark as completed to trigger stock update
            $return->approve();
            $return->complete();
        });
    }

    protected function seedSaleReturns($faker, Collection $sales): void
    {
        if ($sales->isEmpty()) {
            return;
        }

        $sales->take(5)->each(function (Sale $sale) use ($faker) {
            if ($sale->items()->count() === 0) {
                return;
            }

            $return = SaleReturn::create([
                'return_number' => 'SR-' . $sale->sale_date->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'return_date' => $sale->sale_date->copy()->addDays($faker->numberBetween(1, 15)),
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'status' => 'approved',
                'reason' => 'Customer return',
                'notes' => 'Sale return (BD demo)',
            ]);

            $items = $sale->items()->take(2)->get();
            foreach ($items as $si) {
                $qty = max(1, (int) floor($si->quantity / 3));

                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'sale_item_id' => $si->id,
                    'product_id' => $si->product_id,
                    'barcode' => $si->barcode,
                    'unit_price' => $si->unit_price,
                    'discount' => 0,
                    'quantity' => $qty,
                    'subtotal' => ($si->unit_price * $qty),
                    'reason' => 'Defective / wrong item',
                ]);
            }

            $return->refresh();
            $return->update([
                'tax_amount' => 0,
                'discount_amount' => 0,
                'status' => 'approved',
            ]);

            // Mark as completed to trigger stock update
            $return->approve();
            $return->complete();
        });
    }

    protected function seedServiceReturns($faker, Collection $services): void
    {
        if ($services->isEmpty()) {
            return;
        }

        $services->take(3)->each(function (Service $service) use ($faker) {
            $refund = $faker->numberBetween(200, (int) $service->service_cost);

            $return = ServiceReturn::create([
                'return_number' => 'SVR-' . $service->receive_date->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'service_id' => $service->id,
                'return_date' => ($service->delivery_date ?? $service->receive_date)->copy()->addDays($faker->numberBetween(1, 5)),
                'status' => 'approved',
                'reason' => 'Customer not satisfied',
                'notes' => 'Service return (BD demo)',
                'refund_amount' => $refund,
                'refund_status' => 'pending',
            ]);

            $return->approve();
            $return->complete();
            $return->processRefund();
        });
    }

    protected function seedWarrantySubmissions($faker): void
    {
        $warranties = Warranty::active()->take(10)->get();

        if ($warranties->isEmpty()) {
            return;
        }

        $users = User::all();

        foreach ($warranties as $warranty) {
            $customer = $warranty->customer;
            $sale = $warranty->sale;

            WarrantySubmission::create([
                'memo_number' => 'WM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'warranty_id' => $warranty->id,
                'sale_id' => $sale?->id,
                'product_id' => $warranty->product_id,
                'customer_id' => $customer?->id,
                'barcode' => $warranty->barcode,
                'submission_date' => now()->subDays($faker->numberBetween(0, 30)),
                'problem_description' => $faker->randomElement(['Screen not working', 'Battery drain', 'Overheating', 'Software issue', 'Charging port damaged', 'Speaker not working']),
                'customer_complaint' => $faker->randomElement(['Stopped working after 2 months', 'Defect out of box', 'Under warranty claim', 'Hardware failure']),
                // Must match enum: excellent, good, fair, poor, damaged
                'condition' => $faker->randomElement(['excellent', 'good', 'fair', 'poor', 'damaged']),
                'physical_condition_notes' => $faker->optional()->sentence(8),
                'customer_name' => $customer?->name ?? 'Unknown Customer',
                'customer_phone' => $customer?->phone ?? $customer?->mobile,
                'customer_address' => $customer?->address,
                'status' => $faker->randomElement(['pending', 'in_progress', 'completed']),
                'internal_notes' => 'Warranty submission (BD demo)',
                'service_notes' => $faker->optional()->sentence(8),
                'service_charge' => $faker->randomElement([0, 500, 1000]),
                'expected_completion_date' => now()->addDays($faker->numberBetween(1, 10)),
                'completion_date' => $faker->boolean(60) ? now()->addDays($faker->numberBetween(1, 10)) : null,
                'return_date' => $faker->boolean(50) ? now()->addDays($faker->numberBetween(5, 15)) : null,
                'assigned_to' => $users->isNotEmpty() ? $users->random()->id : null,
            ]);
        }
    }
}

