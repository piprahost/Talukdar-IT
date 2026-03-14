<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use Illuminate\Console\Command;

class RecalculatePurchaseTotals extends Command
{
    protected $signature = 'purchases:recalculate-totals
                            {--dry-run : Show what would be updated without saving}';

    protected $description = 'Recalculate purchase totals (subtotal, total_amount, due_amount) from purchase items. Fixes purchases showing 0 on dashboard after import/restore.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Dry run – no changes will be saved.');
        }

        $query = Purchase::query();
        $total = $query->count();
        if ($total === 0) {
            $this->info('No purchases found.');
            return self::SUCCESS;
        }

        $this->info('Recalculating purchase totals from items…');
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;
        $query->chunkById(100, function ($purchases) use (&$updated, $dryRun, $bar) {
            foreach ($purchases as $purchase) {
                $beforeTotal = (float) $purchase->total_amount;
                if ($dryRun) {
                    $result = $purchase->items()->selectRaw('SUM(cost_price * quantity) as total')->first();
                    $subtotal = $result ? (float) $result->total : 0;
                    $taxAmount = (float) ($purchase->tax_amount ?? 0);
                    $discountAmount = (float) ($purchase->discount_amount ?? 0);
                    $newTotal = $subtotal + $taxAmount - $discountAmount;
                    if (abs($beforeTotal - $newTotal) > 0.001) {
                        $updated++;
                    }
                } else {
                    $purchase->calculateTotals();
                    if (abs($beforeTotal - (float) $purchase->total_amount) > 0.001) {
                        $updated++;
                    }
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("Processed {$total} purchase(s). " . ($dryRun ? "Would update {$updated}." : "Updated {$updated}."));
        return self::SUCCESS;
    }
}
