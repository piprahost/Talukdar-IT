# Backend: Standard Relations & Connected Data

This doc describes how we keep **connected data** consistent: one place for relation names, consistent loading in controllers, and filters for related entities where useful.

## Pattern

1. **Models** define:
   - `getStandardRelations(): array` — list of relation names used for show/detail (and sometimes index).
   - Optionally `scopeWithStandardRelations($query)` — applies `with(static::getStandardRelations())` for index/list queries.

2. **Controllers** use:
   - **Show/detail**: `$model->load(Model::getStandardRelations())` (or equivalent).
   - **Index**: `Model::withStandardRelations()` when the list needs the same relations.

3. **Filters**: Where a resource belongs to another (e.g. payments to a sale/purchase), the index supports filters like `sale_id` and `purchase_id` so list views stay connected.

## Models Using the Pattern

| Model           | Standard relations (show/index) |
|----------------|----------------------------------|
| **Sale**       | `customer`, `items.product`, `creator`, `returns`, `payments`, `bankAccount` |
| **Purchase**   | `supplier`, `items.product`, `creator`, `receiver`, `returns`, `payments`, `bankAccount` |
| **Payment**    | `sale`, `purchase`, `customer`, `supplier`, `creator` (uses relations in `updateRelatedPaymentStatus`) |
| **Customer**   | Scope only: `withCount('sales')`, `withSum('sales', 'total_amount')`, `withSum('sales', 'due_amount')` |
| **Service**    | `creator`, `customer`, `returns`, `bankAccount` (optional `customer_id`; snapshot fields kept for history) |
| **Expense**    | `account`, `bankAccount`, `creator`, `approver` |
| **JournalEntry** | `items.account`, `creator`, `poster` |
| **SaleReturn** | `sale`, `customer`, `items.product`, `items.saleItem`, `creator`, `approver` |
| **PurchaseReturn** | `purchase`, `supplier`, `items.product`, `items.purchaseItem`, `creator`, `approver` |
| **ServiceReturn** | `service`, `creator`, `approver` |

## Data consistency (relations over copies)

- **Sale**: When `customer_id` is set, `customer_name`, `customer_phone`, `customer_address` are synced from `Customer` on create/update so the snapshot stays in sync while keeping one source of truth. Use `display_customer_name` accessor when you want to prefer the relation when loaded.
- **Service**: Optional `customer_id` links to `Customer`; `customer_name`, `customer_phone`, `customer_address` remain as snapshot. Use `display_customer_name` when relation is loaded.
- **Payment**: `updateRelatedPaymentStatus()` uses `$this->sale` and `$this->purchase` (relations) instead of re-fetching by id.

## Payment index filters

- **Sale payments**: filter by `sale_id`.
- **Purchase payments**: filter by `purchase_id`.

Use these in list/connected views (e.g. “Payments for this invoice”) so data stays in sync with the parent record.

## Adding the pattern to a new model

1. Add `getStandardRelations(): array` (and optionally `scopeWithStandardRelations`) to the model.
2. In the controller: for **show** use `$model->load(MyModel::getStandardRelations())`; for **index** use `MyModel::withStandardRelations()` when you need those relations on the list.

This keeps relation names in one place and avoids N+1 and inconsistent loads.
