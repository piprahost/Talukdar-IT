<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class AccountingReportController extends Controller
{
    public function ledger(Request $request)
    {
        $this->authorizePermission('view accounting-reports');
        $accountId = $request->get('account_id');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $accounts = Account::active()->orderBy('code')->get();
        $account = null;
        $entries = collect();
        $openingBalance = 0;
        $closingBalance = 0;

        if ($accountId) {
            $account = Account::findOrFail($accountId);
            $openingBalance = $account->getBalanceAsOfDate(Carbon::parse($dateFrom)->subDay());

            $entries = JournalEntryItem::where('journal_entry_items.account_id', $accountId)
                ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
                ->where('journal_entries.status', 'posted')
                ->whereBetween('journal_entries.entry_date', [$dateFrom, $dateTo])
                ->whereNull('journal_entries.deleted_at')
                ->select('journal_entry_items.*')
                ->with('journalEntry')
                ->orderBy('journal_entries.entry_date', 'asc')
                ->orderBy('journal_entries.id', 'asc')
                ->get();

            $closingBalance = $account->getBalanceAsOfDate($dateTo);
        }

        return view('accounting.reports.ledger', compact('accounts', 'account', 'entries', 'openingBalance', 'closingBalance', 'dateFrom', 'dateTo'));
    }

    public function trialBalance(Request $request)
    {
        $this->authorizePermission('view accounting-reports');
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        
        $accounts = Account::active()->orderBy('code')->get();
        
        $trialBalance = $accounts->map(function($account) use ($date) {
            $balance = $account->getBalanceAsOfDate($date);
            
            return [
                'account' => $account,
                'debit' => $account->balance_type === 'debit' && $balance > 0 ? $balance : 0,
                'credit' => $account->balance_type === 'credit' && $balance > 0 ? $balance : ($account->balance_type === 'debit' && $balance < 0 ? abs($balance) : 0),
            ];
        })->filter(function($item) {
            return abs($item['debit'] - $item['credit']) > 0.01;
        });

        $totalDebit = $trialBalance->sum('debit');
        $totalCredit = $trialBalance->sum('credit');

        return view('accounting.reports.trial-balance', compact('trialBalance', 'totalDebit', 'totalCredit', 'date'));
    }

    public function balanceSheet(Request $request)
    {
        $this->authorizePermission('view accounting-reports');
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        
        // Assets
        $assets = Account::active()
            ->where('type', 'asset')
            ->orderBy('code')
            ->get()
            ->map(function($account) use ($date) {
                return [
                    'account' => $account,
                    'balance' => $account->getBalanceAsOfDate($date),
                ];
            })
            ->filter(function($item) {
                return abs($item['balance']) > 0.01;
            });

        $totalAssets = $assets->sum('balance');

        // Liabilities
        $liabilities = Account::active()
            ->where('type', 'liability')
            ->orderBy('code')
            ->get()
            ->map(function($account) use ($date) {
                return [
                    'account' => $account,
                    'balance' => $account->getBalanceAsOfDate($date),
                ];
            })
            ->filter(function($item) {
                return abs($item['balance']) > 0.01;
            });

        $totalLiabilities = $liabilities->sum('balance');

        // Equity
        $equity = Account::active()
            ->where('type', 'equity')
            ->orderBy('code')
            ->get()
            ->map(function($account) use ($date) {
                return [
                    'account' => $account,
                    'balance' => $account->getBalanceAsOfDate($date),
                ];
            })
            ->filter(function($item) {
                return abs($item['balance']) > 0.01;
            });

        // Calculate retained earnings (profit/loss)
        $retainedEarnings = $this->calculateRetainedEarnings($date);
        $totalEquity = $equity->sum('balance') + $retainedEarnings;

        return view('accounting.reports.balance-sheet', compact(
            'assets', 'liabilities', 'equity', 
            'totalAssets', 'totalLiabilities', 'totalEquity', 
            'retainedEarnings', 'date'
        ));
    }

    public function profitLoss(Request $request)
    {
        $this->authorizePermission('view accounting-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Revenue
        $revenue = Account::active()
            ->where('type', 'revenue')
            ->orderBy('code')
            ->get()
            ->map(function($account) use ($dateFrom, $dateTo) {
                $balance = $account->getBalanceAsOfDate($dateTo) - $account->getBalanceAsOfDate(Carbon::parse($dateFrom)->subDay());
                return [
                    'account' => $account,
                    'amount' => $balance > 0 ? $balance : 0,
                ];
            })
            ->filter(function($item) {
                return $item['amount'] > 0.01;
            });

        $totalRevenue = $revenue->sum('amount');

        // Expenses
        $expenses = Account::active()
            ->where('type', 'expense')
            ->orderBy('code')
            ->get()
            ->map(function($account) use ($dateFrom, $dateTo) {
                $balance = $account->getBalanceAsOfDate($dateTo) - $account->getBalanceAsOfDate(Carbon::parse($dateFrom)->subDay());
                return [
                    'account' => $account,
                    'amount' => $balance > 0 ? $balance : 0,
                ];
            })
            ->filter(function($item) {
                return $item['amount'] > 0.01;
            });

        $totalExpenses = $expenses->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        return view('accounting.reports.profit-loss', compact(
            'revenue', 'expenses', 
            'totalRevenue', 'totalExpenses', 'netProfit',
            'dateFrom', 'dateTo'
        ));
    }

    private function calculateRetainedEarnings($date)
    {
        // Calculate profit/loss up to the date
        $revenue = Account::where('type', 'revenue')
            ->get()
            ->sum(function($account) use ($date) {
                return max(0, $account->getBalanceAsOfDate($date));
            });

        $expenses = Account::where('type', 'expense')
            ->get()
            ->sum(function($account) use ($date) {
                return max(0, $account->getBalanceAsOfDate($date));
            });

        return $revenue - $expenses;
    }

    // Export Methods
    public function exportLedger(Request $request, $format)
    {
        $this->authorizePermission('export accounting-reports');
        $accountId = $request->get('account_id');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        if (!$accountId) {
            return back()->with('error', 'Please select an account to export.');
        }

        $account = Account::findOrFail($accountId);
        $openingBalance = $account->getBalanceAsOfDate(Carbon::parse($dateFrom)->subDay());

        $entries = JournalEntryItem::where('journal_entry_items.account_id', $accountId)
            ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.status', 'posted')
            ->whereBetween('journal_entries.entry_date', [$dateFrom, $dateTo])
            ->whereNull('journal_entries.deleted_at')
            ->select('journal_entry_items.*')
            ->with('journalEntry')
            ->orderBy('journal_entries.entry_date', 'asc')
            ->orderBy('journal_entries.id', 'asc')
            ->get();

        $closingBalance = $account->getBalanceAsOfDate($dateTo);

        $filename = 'Ledger_' . $account->code . '_' . $dateFrom . '_to_' . $dateTo;

        switch ($format) {
            case 'csv':
                return $this->exportLedgerCSV($account, $entries, $openingBalance, $closingBalance, $dateFrom, $dateTo, $filename);
            case 'excel':
                return $this->exportLedgerExcel($account, $entries, $openingBalance, $closingBalance, $dateFrom, $dateTo, $filename);
            case 'pdf':
                return $this->exportLedgerPDF($account, $entries, $openingBalance, $closingBalance, $dateFrom, $dateTo, $filename);
            default:
                return back()->with('error', 'Invalid export format.');
        }
    }

    public function exportTrialBalance(Request $request, $format)
    {
        $this->authorizePermission('export accounting-reports');
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        
        $accounts = Account::active()->orderBy('code')->get();
        
        $trialBalance = $accounts->map(function($account) use ($date) {
            $balance = $account->getBalanceAsOfDate($date);
            
            return [
                'account' => $account,
                'debit' => $account->balance_type === 'debit' && $balance > 0 ? $balance : 0,
                'credit' => $account->balance_type === 'credit' && $balance > 0 ? $balance : ($account->balance_type === 'debit' && $balance < 0 ? abs($balance) : 0),
            ];
        })->filter(function($item) {
            return abs($item['debit'] - $item['credit']) > 0.01;
        });

        $totalDebit = $trialBalance->sum('debit');
        $totalCredit = $trialBalance->sum('credit');

        $filename = 'Trial_Balance_' . $date;

        switch ($format) {
            case 'csv':
                return $this->exportTrialBalanceCSV($trialBalance, $totalDebit, $totalCredit, $date, $filename);
            case 'excel':
                return $this->exportTrialBalanceExcel($trialBalance, $totalDebit, $totalCredit, $date, $filename);
            case 'pdf':
                return $this->exportTrialBalancePDF($trialBalance, $totalDebit, $totalCredit, $date, $filename);
            default:
                return back()->with('error', 'Invalid export format.');
        }
    }

    public function exportBalanceSheet(Request $request, $format)
    {
        $this->authorizePermission('export accounting-reports');
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        
        $assets = Account::active()->where('type', 'asset')->orderBy('code')->get()
            ->map(function($account) use ($date) {
                return ['account' => $account, 'balance' => $account->getBalanceAsOfDate($date)];
            })->filter(function($item) { return abs($item['balance']) > 0.01; });

        $liabilities = Account::active()->where('type', 'liability')->orderBy('code')->get()
            ->map(function($account) use ($date) {
                return ['account' => $account, 'balance' => $account->getBalanceAsOfDate($date)];
            })->filter(function($item) { return abs($item['balance']) > 0.01; });

        $equity = Account::active()->where('type', 'equity')->orderBy('code')->get()
            ->map(function($account) use ($date) {
                return ['account' => $account, 'balance' => $account->getBalanceAsOfDate($date)];
            })->filter(function($item) { return abs($item['balance']) > 0.01; });

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance') + $this->calculateRetainedEarnings($date);
        $retainedEarnings = $this->calculateRetainedEarnings($date);

        $filename = 'Balance_Sheet_' . $date;

        switch ($format) {
            case 'csv':
                return $this->exportBalanceSheetCSV($assets, $liabilities, $equity, $totalAssets, $totalLiabilities, $totalEquity, $retainedEarnings, $date, $filename);
            case 'excel':
                return $this->exportBalanceSheetExcel($assets, $liabilities, $equity, $totalAssets, $totalLiabilities, $totalEquity, $retainedEarnings, $date, $filename);
            case 'pdf':
                return $this->exportBalanceSheetPDF($assets, $liabilities, $equity, $totalAssets, $totalLiabilities, $totalEquity, $retainedEarnings, $date, $filename);
            default:
                return back()->with('error', 'Invalid export format.');
        }
    }

    public function exportProfitLoss(Request $request, $format)
    {
        $this->authorizePermission('export accounting-reports');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $revenue = Account::active()->where('type', 'revenue')->orderBy('code')->get()
            ->map(function($account) use ($dateFrom, $dateTo) {
                $balance = $account->getBalanceAsOfDate($dateTo) - $account->getBalanceAsOfDate(Carbon::parse($dateFrom)->subDay());
                return ['account' => $account, 'amount' => $balance > 0 ? $balance : 0];
            })->filter(function($item) { return $item['amount'] > 0.01; });

        $expenses = Account::active()->where('type', 'expense')->orderBy('code')->get()
            ->map(function($account) use ($dateFrom, $dateTo) {
                $balance = $account->getBalanceAsOfDate($dateTo) - $account->getBalanceAsOfDate(Carbon::parse($dateFrom)->subDay());
                return ['account' => $account, 'amount' => $balance > 0 ? $balance : 0];
            })->filter(function($item) { return $item['amount'] > 0.01; });

        $totalRevenue = $revenue->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        $filename = 'Profit_Loss_' . $dateFrom . '_to_' . $dateTo;

        switch ($format) {
            case 'csv':
                return $this->exportProfitLossCSV($revenue, $expenses, $totalRevenue, $totalExpenses, $netProfit, $dateFrom, $dateTo, $filename);
            case 'excel':
                return $this->exportProfitLossExcel($revenue, $expenses, $totalRevenue, $totalExpenses, $netProfit, $dateFrom, $dateTo, $filename);
            case 'pdf':
                return $this->exportProfitLossPDF($revenue, $expenses, $totalRevenue, $totalExpenses, $netProfit, $dateFrom, $dateTo, $filename);
            default:
                return back()->with('error', 'Invalid export format.');
        }
    }

    // CSV Export Methods
    private function exportLedgerCSV($account, $entries, $openingBalance, $closingBalance, $dateFrom, $dateTo, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function() use ($account, $entries, $openingBalance, $closingBalance, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Header
            fputcsv($file, ['Ledger Report']);
            fputcsv($file, ['Account:', $account->code . ' - ' . $account->name]);
            fputcsv($file, ['Period:', $dateFrom . ' to ' . $dateTo]);
            fputcsv($file, []);
            fputcsv($file, ['Opening Balance:', number_format($openingBalance, 2)]);
            fputcsv($file, []);
            
            // Table Headers
            fputcsv($file, ['Date', 'Journal Entry', 'Description', 'Debit', 'Credit', 'Balance']);
            
            $runningBalance = $openingBalance;
            foreach ($entries as $entry) {
                $debit = $entry->debit;
                $credit = $entry->credit;
                
                if ($account->balance_type === 'debit') {
                    $runningBalance += ($debit - $credit);
                } else {
                    $runningBalance += ($credit - $debit);
                }
                
                fputcsv($file, [
                    $entry->journalEntry->entry_date->format('Y-m-d'),
                    $entry->journalEntry->entry_number,
                    $entry->description ?? $entry->journalEntry->description,
                    number_format($debit, 2),
                    number_format($credit, 2),
                    number_format($runningBalance, 2),
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['Closing Balance:', number_format($closingBalance, 2)]);
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportTrialBalanceCSV($trialBalance, $totalDebit, $totalCredit, $date, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function() use ($trialBalance, $totalDebit, $totalCredit, $date) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['Trial Balance Report']);
            fputcsv($file, ['Date:', $date]);
            fputcsv($file, []);
            fputcsv($file, ['Account Code', 'Account Name', 'Debit', 'Credit']);
            
            foreach ($trialBalance as $item) {
                fputcsv($file, [
                    $item['account']->code,
                    $item['account']->name,
                    number_format($item['debit'], 2),
                    number_format($item['credit'], 2),
                ]);
            }
            
            fputcsv($file, ['TOTAL', '', number_format($totalDebit, 2), number_format($totalCredit, 2)]);
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportBalanceSheetCSV($assets, $liabilities, $equity, $totalAssets, $totalLiabilities, $totalEquity, $retainedEarnings, $date, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function() use ($assets, $liabilities, $equity, $totalAssets, $totalLiabilities, $totalEquity, $retainedEarnings, $date) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['Balance Sheet']);
            fputcsv($file, ['Date:', $date]);
            fputcsv($file, []);
            fputcsv($file, ['ASSETS']);
            fputcsv($file, ['Account Code', 'Account Name', 'Balance']);
            
            foreach ($assets as $item) {
                fputcsv($file, [$item['account']->code, $item['account']->name, number_format($item['balance'], 2)]);
            }
            fputcsv($file, ['', 'Total Assets', number_format($totalAssets, 2)]);
            fputcsv($file, []);
            
            fputcsv($file, ['LIABILITIES']);
            foreach ($liabilities as $item) {
                fputcsv($file, [$item['account']->code, $item['account']->name, number_format($item['balance'], 2)]);
            }
            fputcsv($file, ['', 'Total Liabilities', number_format($totalLiabilities, 2)]);
            fputcsv($file, []);
            
            fputcsv($file, ['EQUITY']);
            foreach ($equity as $item) {
                fputcsv($file, [$item['account']->code, $item['account']->name, number_format($item['balance'], 2)]);
            }
            fputcsv($file, ['', 'Retained Earnings', number_format($retainedEarnings, 2)]);
            fputcsv($file, ['', 'Total Equity', number_format($totalEquity, 2)]);
            fputcsv($file, []);
            fputcsv($file, ['Total Liabilities + Equity', '', number_format($totalLiabilities + $totalEquity, 2)]);
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportProfitLossCSV($revenue, $expenses, $totalRevenue, $totalExpenses, $netProfit, $dateFrom, $dateTo, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function() use ($revenue, $expenses, $totalRevenue, $totalExpenses, $netProfit, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['Profit & Loss Statement']);
            fputcsv($file, ['Period:', $dateFrom . ' to ' . $dateTo]);
            fputcsv($file, []);
            fputcsv($file, ['REVENUE']);
            fputcsv($file, ['Account Code', 'Account Name', 'Amount']);
            
            foreach ($revenue as $item) {
                fputcsv($file, [$item['account']->code, $item['account']->name, number_format($item['amount'], 2)]);
            }
            fputcsv($file, ['', 'Total Revenue', number_format($totalRevenue, 2)]);
            fputcsv($file, []);
            
            fputcsv($file, ['EXPENSES']);
            foreach ($expenses as $item) {
                fputcsv($file, [$item['account']->code, $item['account']->name, number_format($item['amount'], 2)]);
            }
            fputcsv($file, ['', 'Total Expenses', number_format($totalExpenses, 2)]);
            fputcsv($file, []);
            fputcsv($file, ['Net Profit/Loss', '', number_format($netProfit, 2)]);
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // Excel Export Methods (using Excel XML Spreadsheet format)
    private function exportLedgerExcel($account, $entries, $openingBalance, $closingBalance, $dateFrom, $dateTo, $filename)
    {
        $xml = $this->generateExcelXMLHeader('Ledger');
        
        // Header rows
        $xml .= '<Row><Cell><Data ss:Type="String">Ledger Report</Data></Cell></Row>' . "\n";
        $xml .= '<Row><Cell><Data ss:Type="String">Account:</Data></Cell><Cell><Data ss:Type="String">' . htmlspecialchars($account->code . ' - ' . $account->name) . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row><Cell><Data ss:Type="String">Period:</Data></Cell><Cell><Data ss:Type="String">' . $dateFrom . ' to ' . $dateTo . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";
        $xml .= '<Row><Cell><Data ss:Type="String">Opening Balance:</Data></Cell><Cell><Data ss:Type="Number">' . $openingBalance . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";
        
        // Table header
        $xml .= '<Row>';
        $xml .= '<Cell><Data ss:Type="String">Date</Data></Cell>';
        $xml .= '<Cell><Data ss:Type="String">Journal Entry</Data></Cell>';
        $xml .= '<Cell><Data ss:Type="String">Description</Data></Cell>';
        $xml .= '<Cell><Data ss:Type="String">Debit</Data></Cell>';
        $xml .= '<Cell><Data ss:Type="String">Credit</Data></Cell>';
        $xml .= '<Cell><Data ss:Type="String">Balance</Data></Cell>';
        $xml .= '</Row>' . "\n";

        $runningBalance = $openingBalance;
        foreach ($entries as $entry) {
            $debit = $entry->debit;
            $credit = $entry->credit;
            
            if ($account->balance_type === 'debit') {
                $runningBalance += ($debit - $credit);
            } else {
                $runningBalance += ($credit - $debit);
            }
            
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="String">' . $entry->journalEntry->entry_date->format('Y-m-d') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($entry->journalEntry->entry_number) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($entry->description ?? $entry->journalEntry->description) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . $debit . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . $credit . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . $runningBalance . '</Data></Cell>';
            $xml .= '</Row>' . "\n";
        }

        $xml .= '<Row></Row>' . "\n";
        $xml .= '<Row><Cell><Data ss:Type="String">Closing Balance:</Data></Cell><Cell><Data ss:Type="Number">' . $closingBalance . '</Data></Cell></Row>' . "\n";
        $xml .= $this->generateExcelXMLFooter();

        return Response::make($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xls"',
        ]);
    }

    private function exportTrialBalanceExcel($trialBalance, $totalDebit, $totalCredit, $date, $filename)
    {
        $xml = $this->generateExcelXMLHeader('Trial Balance');
        $xml .= '<Row><Cell><Data ss:Type="String">Trial Balance Report</Data></Cell></Row>' . "\n";
        $xml .= '<Row><Cell><Data ss:Type="String">Date:</Data></Cell><Cell><Data ss:Type="String">' . $date . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";
        $xml .= '<Row>';
        $xml .= '<Cell><Data ss:Type="String">Account Code</Data></Cell>';
        $xml .= '<Cell><Data ss:Type="String">Account Name</Data></Cell>';
        $xml .= '<Cell><Data ss:Type="String">Debit</Data></Cell>';
        $xml .= '<Cell><Data ss:Type="String">Credit</Data></Cell>';
        $xml .= '</Row>' . "\n";

        foreach ($trialBalance as $item) {
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->code) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . $item['debit'] . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . $item['credit'] . '</Data></Cell>';
            $xml .= '</Row>' . "\n";
        }

        $xml .= '<Row>';
        $xml .= '<Cell><Data ss:Type="String">TOTAL</Data></Cell>';
        $xml .= '<Cell></Cell>';
        $xml .= '<Cell><Data ss:Type="Number">' . $totalDebit . '</Data></Cell>';
        $xml .= '<Cell><Data ss:Type="Number">' . $totalCredit . '</Data></Cell>';
        $xml .= '</Row>' . "\n";
        $xml .= $this->generateExcelXMLFooter();

        return Response::make($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xls"',
        ]);
    }

    private function exportBalanceSheetExcel($assets, $liabilities, $equity, $totalAssets, $totalLiabilities, $totalEquity, $retainedEarnings, $date, $filename)
    {
        $xml = $this->generateExcelXMLHeader('Balance Sheet');
        $xml .= '<Row><Cell><Data ss:Type="String">Balance Sheet</Data></Cell></Row>' . "\n";
        $xml .= '<Row><Cell><Data ss:Type="String">Date:</Data></Cell><Cell><Data ss:Type="String">' . $date . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";
        
        $xml .= '<Row><Cell><Data ss:Type="String">ASSETS</Data></Cell></Row>' . "\n";
        $xml .= '<Row><Cell><Data ss:Type="String">Account Code</Data></Cell><Cell><Data ss:Type="String">Account Name</Data></Cell><Cell><Data ss:Type="String">Balance</Data></Cell></Row>' . "\n";
        foreach ($assets as $item) {
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->code) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . $item['balance'] . '</Data></Cell>';
            $xml .= '</Row>' . "\n";
        }
        $xml .= '<Row><Cell></Cell><Cell><Data ss:Type="String">Total Assets</Data></Cell><Cell><Data ss:Type="Number">' . $totalAssets . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";

        $xml .= '<Row><Cell><Data ss:Type="String">LIABILITIES</Data></Cell></Row>' . "\n";
        foreach ($liabilities as $item) {
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->code) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . $item['balance'] . '</Data></Cell>';
            $xml .= '</Row>' . "\n";
        }
        $xml .= '<Row><Cell></Cell><Cell><Data ss:Type="String">Total Liabilities</Data></Cell><Cell><Data ss:Type="Number">' . $totalLiabilities . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";

        $xml .= '<Row><Cell><Data ss:Type="String">EQUITY</Data></Cell></Row>' . "\n";
        foreach ($equity as $item) {
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->code) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . $item['balance'] . '</Data></Cell>';
            $xml .= '</Row>' . "\n";
        }
        $xml .= '<Row><Cell></Cell><Cell><Data ss:Type="String">Retained Earnings</Data></Cell><Cell><Data ss:Type="Number">' . $retainedEarnings . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row><Cell></Cell><Cell><Data ss:Type="String">Total Equity</Data></Cell><Cell><Data ss:Type="Number">' . $totalEquity . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";
        $xml .= '<Row><Cell><Data ss:Type="String">Total Liabilities + Equity</Data></Cell><Cell></Cell><Cell><Data ss:Type="Number">' . ($totalLiabilities + $totalEquity) . '</Data></Cell></Row>' . "\n";
        $xml .= $this->generateExcelXMLFooter();

        return Response::make($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xls"',
        ]);
    }

    private function exportProfitLossExcel($revenue, $expenses, $totalRevenue, $totalExpenses, $netProfit, $dateFrom, $dateTo, $filename)
    {
        $xml = $this->generateExcelXMLHeader('Profit Loss');
        $xml .= '<Row><Cell><Data ss:Type="String">Profit &amp; Loss Statement</Data></Cell></Row>' . "\n";
        $xml .= '<Row><Cell><Data ss:Type="String">Period:</Data></Cell><Cell><Data ss:Type="String">' . $dateFrom . ' to ' . $dateTo . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";
        
        $xml .= '<Row><Cell><Data ss:Type="String">REVENUE</Data></Cell></Row>' . "\n";
        $xml .= '<Row><Cell><Data ss:Type="String">Account Code</Data></Cell><Cell><Data ss:Type="String">Account Name</Data></Cell><Cell><Data ss:Type="String">Amount</Data></Cell></Row>' . "\n";
        foreach ($revenue as $item) {
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->code) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . $item['amount'] . '</Data></Cell>';
            $xml .= '</Row>' . "\n";
        }
        $xml .= '<Row><Cell></Cell><Cell><Data ss:Type="String">Total Revenue</Data></Cell><Cell><Data ss:Type="Number">' . $totalRevenue . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";

        $xml .= '<Row><Cell><Data ss:Type="String">EXPENSES</Data></Cell></Row>' . "\n";
        foreach ($expenses as $item) {
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->code) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['account']->name) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . $item['amount'] . '</Data></Cell>';
            $xml .= '</Row>' . "\n";
        }
        $xml .= '<Row><Cell></Cell><Cell><Data ss:Type="String">Total Expenses</Data></Cell><Cell><Data ss:Type="Number">' . $totalExpenses . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";
        $xml .= '<Row><Cell><Data ss:Type="String">Net Profit/Loss</Data></Cell><Cell></Cell><Cell><Data ss:Type="Number">' . $netProfit . '</Data></Cell></Row>' . "\n";
        $xml .= $this->generateExcelXMLFooter();

        return Response::make($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xls"',
        ]);
    }

    // Helper methods for Excel XML generation
    private function generateExcelXMLHeader($worksheetName = 'Sheet1')
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        $xml .= '<Worksheet ss:Name="' . htmlspecialchars($worksheetName) . '">' . "\n";
        $xml .= '<Table>' . "\n";
        return $xml;
    }

    private function generateExcelXMLFooter()
    {
        return '</Table></Worksheet></Workbook>';
    }

    // PDF Export Methods
    private function exportLedgerPDF($account, $entries, $openingBalance, $closingBalance, $dateFrom, $dateTo, $filename)
    {
        $pdf = PDF::loadView('accounting.reports.pdf.ledger', compact(
            'account', 'entries', 'openingBalance', 'closingBalance', 'dateFrom', 'dateTo'
        ));
        return $pdf->download($filename . '.pdf');
    }

    private function exportTrialBalancePDF($trialBalance, $totalDebit, $totalCredit, $date, $filename)
    {
        $pdf = PDF::loadView('accounting.reports.pdf.trial-balance', compact(
            'trialBalance', 'totalDebit', 'totalCredit', 'date'
        ));
        return $pdf->download($filename . '.pdf');
    }

    private function exportBalanceSheetPDF($assets, $liabilities, $equity, $totalAssets, $totalLiabilities, $totalEquity, $retainedEarnings, $date, $filename)
    {
        $pdf = PDF::loadView('accounting.reports.pdf.balance-sheet', compact(
            'assets', 'liabilities', 'equity', 'totalAssets', 'totalLiabilities', 'totalEquity', 'retainedEarnings', 'date'
        ));
        return $pdf->download($filename . '.pdf');
    }

    private function exportProfitLossPDF($revenue, $expenses, $totalRevenue, $totalExpenses, $netProfit, $dateFrom, $dateTo, $filename)
    {
        $pdf = PDF::loadView('accounting.reports.pdf.profit-loss', compact(
            'revenue', 'expenses', 'totalRevenue', 'totalExpenses', 'netProfit', 'dateFrom', 'dateTo'
        ));
        return $pdf->download($filename . '.pdf');
    }
}
