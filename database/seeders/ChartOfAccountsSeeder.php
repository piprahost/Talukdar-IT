<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Seed only minimal system accounts required by accounting flows.
     */
    public function run(): void
    {
        $accounts = [
            ['code' => '1000', 'name' => 'Cash in Hand', 'type' => 'asset', 'category' => 'current_asset', 'balance_type' => 'debit', 'sort_order' => 10],
            ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'asset', 'category' => 'current_asset', 'balance_type' => 'debit', 'sort_order' => 20],
            ['code' => '1300', 'name' => 'Inventory', 'type' => 'asset', 'category' => 'current_asset', 'balance_type' => 'debit', 'sort_order' => 30],
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'category' => 'current_liability', 'balance_type' => 'credit', 'sort_order' => 40],
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue', 'category' => 'sales_revenue', 'balance_type' => 'credit', 'sort_order' => 50],
            ['code' => '4100', 'name' => 'Service Revenue', 'type' => 'revenue', 'category' => 'other_revenue', 'balance_type' => 'credit', 'sort_order' => 60],
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'category' => 'cost_of_goods_sold', 'balance_type' => 'debit', 'sort_order' => 70],
        ];

        foreach ($accounts as $accountData) {
            $account = Account::withTrashed()->where('code', $accountData['code'])->first();

            if ($account) {
                if ($account->trashed()) {
                    $account->restore();
                }

                $account->update(array_merge($accountData, [
                    'opening_balance' => $account->opening_balance ?? 0,
                    'is_active' => true,
                    'is_system' => true,
                    'description' => 'System account',
                ]));

                continue;
            }

            Account::create(array_merge($accountData, [
                'opening_balance' => 0,
                'is_active' => true,
                'is_system' => true,
                'description' => 'System account',
            ]));
        }
    }
}
