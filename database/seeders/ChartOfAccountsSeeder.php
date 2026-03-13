<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // Assets
        $cash = Account::create([
            'code' => '1000',
            'name' => 'Cash',
            'type' => 'asset',
            'category' => 'current_asset',
            'balance_type' => 'debit',
            'is_system' => true,
            'is_active' => true,
        ]);

        Account::create([
            'code' => '1100',
            'name' => 'Bank Account',
            'type' => 'asset',
            'category' => 'current_asset',
            'balance_type' => 'debit',
            'is_system' => true,
            'is_active' => true,
        ]);

        Account::create([
            'code' => '1200',
            'name' => 'Accounts Receivable',
            'type' => 'asset',
            'category' => 'current_asset',
            'balance_type' => 'debit',
            'is_system' => true,
            'is_active' => true,
        ]);

        Account::create([
            'code' => '1300',
            'name' => 'Inventory',
            'type' => 'asset',
            'category' => 'current_asset',
            'balance_type' => 'debit',
            'is_system' => true,
            'is_active' => true,
        ]);

        Account::create([
            'code' => '1500',
            'name' => 'Fixed Assets',
            'type' => 'asset',
            'category' => 'fixed_asset',
            'balance_type' => 'debit',
            'is_system' => true,
            'is_active' => true,
        ]);

        // Liabilities
        Account::create([
            'code' => '2000',
            'name' => 'Accounts Payable',
            'type' => 'liability',
            'category' => 'current_liability',
            'balance_type' => 'credit',
            'is_system' => true,
            'is_active' => true,
        ]);

        // Equity
        Account::create([
            'code' => '3000',
            'name' => 'Capital',
            'type' => 'equity',
            'category' => 'capital',
            'balance_type' => 'credit',
            'is_system' => true,
            'is_active' => true,
        ]);

        Account::create([
            'code' => '3100',
            'name' => 'Retained Earnings',
            'type' => 'equity',
            'category' => 'retained_earnings',
            'balance_type' => 'credit',
            'is_system' => true,
            'is_active' => true,
        ]);

        // Revenue
        Account::create([
            'code' => '4000',
            'name' => 'Sales Revenue',
            'type' => 'revenue',
            'category' => 'sales_revenue',
            'balance_type' => 'credit',
            'is_system' => true,
            'is_active' => true,
        ]);

        Account::create([
            'code' => '4100',
            'name' => 'Service Revenue',
            'type' => 'revenue',
            'category' => 'other_revenue',
            'balance_type' => 'credit',
            'is_system' => true,
            'is_active' => true,
        ]);

        // Expenses
        Account::create([
            'code' => '5000',
            'name' => 'Cost of Goods Sold',
            'type' => 'expense',
            'category' => 'cost_of_goods_sold',
            'balance_type' => 'debit',
            'is_system' => true,
            'is_active' => true,
        ]);

        $operatingExpense = Account::create([
            'code' => '6000',
            'name' => 'Operating Expenses',
            'type' => 'expense',
            'category' => 'operating_expense',
            'balance_type' => 'debit',
            'is_system' => true,
            'is_active' => true,
        ]);

        Account::create([
            'code' => '6100',
            'name' => 'Rent Expense',
            'type' => 'expense',
            'category' => 'operating_expense',
            'balance_type' => 'debit',
            'parent_id' => $operatingExpense->id,
            'is_system' => false,
            'is_active' => true,
        ]);

        Account::create([
            'code' => '6200',
            'name' => 'Utilities Expense',
            'type' => 'expense',
            'category' => 'operating_expense',
            'balance_type' => 'debit',
            'parent_id' => $operatingExpense->id,
            'is_system' => false,
            'is_active' => true,
        ]);

        Account::create([
            'code' => '6300',
            'name' => 'Salary Expense',
            'type' => 'expense',
            'category' => 'operating_expense',
            'balance_type' => 'debit',
            'parent_id' => $operatingExpense->id,
            'is_system' => false,
            'is_active' => true,
        ]);
    }
}
