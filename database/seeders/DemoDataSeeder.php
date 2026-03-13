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

        // Ensure we have some base data to work with
        $products = Product::inStock()->get();
        if ($products->isEmpty()) {
            $products = Product::all();
        }

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

        $this->command?->info('Demo data seeded successfully.');
    }

    protected function seedCustomers($faker, int $count): Collection
    {
        $customers = collect();

        for ($i = 0; $i < $count; $i++) {
            $customers->push(Customer::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'mobile' => $faker->phoneNumber(),
                'address' => $faker->streetAddress(),
                'city' => $faker->city(),
                'country' => $faker->country(),
                'tax_id' => $faker->optional()->bothify('TIN-########'),
                'notes' => $faker->optional()->sentence(),
                'is_active' => true,
            ]));
        }

        return $customers;
    }

    protected function seedSuppliers($faker, int $count): Collection
    {
        $suppliers = collect();

        for ($i = 0; $i < $count; $i++) {
            $suppliers->push(Supplier::create([
                'name' => $faker->company(),
                'company_name' => $faker->company(),
                'email' => $faker->unique()->companyEmail(),
                'phone' => $faker->phoneNumber(),
                'mobile' => $faker->phoneNumber(),
                'address' => $faker->streetAddress(),
                'city' => $faker->city(),
                'country' => $faker->country(),
                'tax_id' => $faker->optional()->bothify('VAT-########'),
                'notes' => $faker->optional()->sentence(),
                'is_active' => true,
            ]));
        }

        return $suppliers;
    }

    protected function seedBankAccount(): ?BankAccount
    {
        // Try to attach to the "Bank Account" COA entry if it exists
        $bankAccountCoa = Account::where('code', '1100')->first();

        return BankAccount::firstOrCreate(
            ['account_number' => '1222333444'],
            [
                'account_name' => 'Main Bank Account',
                'bank_name' => 'Demo Bank',
                'branch_name' => 'Main Branch',
                'routing_number' => 'RB0001',
                'swift_code' => 'DEMOBANKXXX',
                'account_type' => 'current',
                'opening_balance' => 100000,
                'current_balance' => 100000,
                'account_id' => $bankAccountCoa?->id,
                'is_active' => true,
                'notes' => 'Primary demo bank account',
            ]
        );
    }

    protected function seedPurchasesWithItems($faker, Collection $suppliers, Collection $products, ?BankAccount $bankAccount): Collection
    {
        $purchases = collect();

        if ($suppliers->isEmpty()) {
            return $purchases;
        }

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
                'notes' => 'Demo purchase order',
                'internal_notes' => 'Seeded demo data',
            ]);

            $lineCount = $faker->numberBetween(2, 5);
            for ($l = 0; $l < $lineCount; $l++) {
                $product = $products->random();
                $quantity = $faker->numberBetween(5, 20);
                $costPrice = $product->cost_price ?: $faker->numberBetween(1000, 50000);
                $sellingPrice = $product->selling_price ?: ($costPrice * 1.2);

                PurchaseItem::create([
                    'purchase_order_id' => $purchase->id,
                    'product_id' => $product->id,
                    'barcode' => $faker->ean13(),
                    'serial_number' => $faker->optional(0.4)->bothify('SN-########'),
                    'cost_price' => $costPrice,
                    'selling_price' => $sellingPrice,
                    'quantity' => $quantity,
                    'status' => 'received',
                    'received_date' => $purchase->received_date,
                    'condition_notes' => 'New stock',
                    'warranty_info' => 'Standard manufacturer warranty',
                    'notes' => 'Demo purchase item',
                ]);
            }

            // Calculate totals from items
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
                    'notes' => 'Demo supplier payment',
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
                'notes' => 'Demo sale',
                'internal_notes' => 'Seeded demo data',
            ]);

            $lineCount = $faker->numberBetween(1, 5);
            for ($l = 0; $l < $lineCount; $l++) {
                $product = $products->random();
                $quantity = $faker->numberBetween(1, 5);
                $unitPrice = $product->selling_price ?: $faker->numberBetween(2000, 80000);
                $discount = $faker->randomElement([0, $unitPrice * 0.05, $unitPrice * 0.1]);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'barcode' => $faker->ean13(),
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'quantity' => $quantity,
                    'subtotal' => 0, // will be calculated in model events
                    'notes' => 'Demo sale item',
                ]);
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
                    'notes' => 'Demo customer payment',
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

            $services->push(Service::create([
                'service_number' => 'SRV-' . $receiveDate->format('Y') . '-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'product_name' => $faker->words(3, true),
                'serial_number' => $faker->optional(0.6)->bothify('SRV-########'),
                'problem_notes' => $faker->sentence(8),
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
                'internal_notes' => 'Demo service job',
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
            ['name' => 'Rent', 'account' => $rentAccount],
            ['name' => 'Utilities', 'account' => $utilitiesAccount],
            ['name' => 'Salaries', 'account' => $salaryAccount],
            ['name' => 'Miscellaneous', 'account' => $operatingExpenseAccount],
        ];

        foreach ($categories as $config) {
            for ($i = 0; $i < 3; $i++) {
                $amount = $faker->numberBetween(2000, 20000);

                Expense::create([
                    'expense_date' => now()->subDays($faker->numberBetween(0, 45)),
                    'category' => $config['name'],
                    'account_id' => $config['account']?->id,
                    'amount' => $amount,
                    'payment_method' => $bankAccount ? 'bank_transfer' : 'cash',
                    'vendor_name' => $faker->company(),
                    'vendor_contact' => $faker->phoneNumber(),
                    'description' => $config['name'] . ' expense (demo)',
                    'reference_number' => $faker->bothify('EXP-######'),
                    'bank_account_id' => $bankAccount?->id,
                    'status' => $faker->randomElement(['approved', 'paid']),
                    'payment_date' => now()->subDays($faker->numberBetween(0, 30)),
                    'notes' => 'Demo operating expense',
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
                'reason' => 'Damaged or incorrect items (demo)',
                'notes' => 'Demo purchase return',
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
                    'reason' => 'Demo return line',
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
                'reason' => 'Customer return (demo)',
                'notes' => 'Demo sale return',
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
                    'reason' => 'Demo return line',
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
                'reason' => 'Customer not satisfied (demo)',
                'notes' => 'Demo service return',
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
                'problem_description' => $faker->sentence(10),
                'customer_complaint' => $faker->sentence(8),
                'condition' => $faker->randomElement(['new', 'used', 'damaged']),
                'physical_condition_notes' => $faker->optional()->sentence(8),
                'customer_name' => $customer?->name ?? 'Unknown Customer',
                'customer_phone' => $customer?->phone ?? $customer?->mobile,
                'customer_address' => $customer?->address,
                'status' => $faker->randomElement(['pending', 'in_progress', 'completed']),
                'internal_notes' => 'Demo warranty submission',
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

