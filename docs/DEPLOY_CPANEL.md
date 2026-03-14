# Deploy to cPanel (GitHub)

## After pushing code to cPanel

1. **Pull latest** on the server (or let cPanel auto-deploy from GitHub).
2. **Run migrations** (if any new migrations):
   ```bash
   php artisan migrate --force
   ```
3. **Clear caches** (recommended after every deploy):
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

## If sales, purchases (or dashboard) amounts show as 0

This can happen if the production database had sales/purchases imported or restored without correct totals (e.g. export/import, or backup from before totals were set).

**Fix: recalculate totals from line items**

Run on the server (SSH or cPanel Terminal):

```bash
# Sales (invoices) – recalculate from sale_items
php artisan sales:recalculate-totals

# Purchases – recalculate from purchase_items (fixes dashboard “Purchases 0”)
php artisan purchases:recalculate-totals
```

**Sales options:**
- Dry run: `php artisan sales:recalculate-totals --dry-run`
- Only fix line item subtotals first: `php artisan sales:recalculate-totals --items-only` then `php artisan sales:recalculate-totals`

**Purchases:** Dry run: `php artisan purchases:recalculate-totals --dry-run`

Then clear cache again and refresh the dashboard/sales/purchases pages.
