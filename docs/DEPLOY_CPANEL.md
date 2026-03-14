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

## If sales (or other) amounts show as 0

This can happen if the production database had sales imported/migrated without correct `sale_items.subtotal` or `sales.total_amount` (e.g. export/import, or different deploy path).

**Fix: recalculate totals from line items**

Run on the server (SSH or cPanel Terminal):

```bash
# Recalculate all sale totals from sale_items (fixes 0 amounts)
php artisan sales:recalculate-totals
```

- **Dry run** (see what would change without saving):
  ```bash
  php artisan sales:recalculate-totals --dry-run
  ```
- **Only fix line item subtotals** first, then run again without the flag to update sale totals:
  ```bash
  php artisan sales:recalculate-totals --items-only
  php artisan sales:recalculate-totals
  ```

Then clear cache again and refresh the sales page.
