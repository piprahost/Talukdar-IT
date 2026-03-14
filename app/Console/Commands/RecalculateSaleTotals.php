<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateSaleTotals extends Command
{
    protected $signature = 'sales:recalculate-totals
                            {--dry-run : Show what would be updated without saving}
                            {--items-only : Only fix sale_items subtotals (then run again without this to fix sales)}';

    protected $description = 'Recalculate sale_items.subtotal and sale totals (subtotal, total_amount, due_amount). Fixes sales showing 0 when line items had subtotal=0.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $itemsOnly = $this->option('items-only');
        if ($dryRun) {
            $this->warn('Dry run – no changes will be saved.');
        }

        // Step 1: Fix sale_items.subtotal (many have 0 in DB from older code or import)
        $itemsAffected = 0;
        if (!$itemsOnly) {
            $this->info('Fixing sale_items.subtotal from unit_price, quantity, discount…');
            if (!$dryRun) {
                $itemsAffected = DB::update('
                    UPDATE sale_items
                    SET subtotal = (unit_price * quantity) - COALESCE(discount, 0)
                    WHERE ABS(COALESCE(subtotal, 0)) < 0.01
                       OR subtotal IS NULL
                ');
            } else {
                $itemsAffected = SaleItem::whereRaw('ABS(COALESCE(subtotal, 0)) < 0.01 OR subtotal IS NULL')->count();
            }
            $this->info(($dryRun ? 'Would fix ' : 'Fixed ') . $itemsAffected . ' sale item(s).');
        }

        // Step 2: Recalculate each sale's totals from items
        $query = Sale::query();
        $total = $query->count();
        if ($total === 0) {
            $this->info('No sales found.');
            return self::SUCCESS;
        }

        $this->info('Recalculating sale totals…');
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;
        $query->chunkById(100, function ($sales) use (&$updated, $dryRun, $bar) {
            foreach ($sales as $sale) {
                $beforeTotal = (float) $sale->total_amount;
                if ($dryRun) {
                    $subtotal = (float) $sale->items()->sum('subtotal');
                    $taxAmount = (float) ($sale->tax_amount ?? 0);
                    $discountAmount = (float) ($sale->discount_amount ?? 0);
                    $newTotal = $subtotal + $taxAmount - $discountAmount;
                    if (abs($beforeTotal - $newTotal) > 0.001) {
                        $updated++;
                    }
                } else {
                    $sale->calculateTotals();
                    if (abs($beforeTotal - (float) $sale->total_amount) > 0.001) {
                        $updated++;
                    }
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("Processed {$total} sale(s). " . ($dryRun ? "Would update {$updated}." : "Updated {$updated}."));
        return self::SUCCESS;
    }
}
