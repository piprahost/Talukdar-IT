# ERP Software – Audit & Simplification

This document summarizes the audit of all modules, options, and buttons, and the changes made to keep the software **easy to use**, **clean**, and focused on **mandatory business operations** for a laptop/computer accessories reseller.

---

## 1. What Was Audited

- **Sidebar navigation**: All menu groups and items (Access Control, Service Orders, Purchases, Sales, Products & Stock, Warranty, Payments, Accounting, Expenses, Reports, Returns, Settings).
- **Dashboard**: Stats, charts, alerts, and Quick Actions.
- **Features**: Every linked page and button across modules (permissions apply as before).

---

## 2. Simplifications Made

### 2.1 Sidebar – Reordered and Cleaned

- **Order** (daily operations first, admin last):
  1. Dashboard  
  2. **Sales** (Invoices, Customers)  
  3. **Purchases** (Purchase Orders, Suppliers)  
  4. Service Orders  
  5. Payments  
  6. Expenses  
  7. Products & Stock (Products, Stock & Movement, Categories, Brands, Models)  
  8. Warranty (Verify, All Warranties, Submissions)  
  9. Returns (Sale, Purchase, Service)  
  10. Accounting (Bank Accounts, P&L, Balance Sheet, Ledger, Trial Balance, Chart of Accounts, Journal Entries)  
  11. Reports (grouped by Sales, Purchases, Financial, Inventory)  
  12. Settings (Company Info)  
  13. **Access Control** (Users, Roles) – moved to the bottom so it doesn’t clutter the menu for regular staff.

- **Removed from sidebar**
  - **Manual Stock Entry** – Still available from **Products & Stock → Stock & Movement**; the Stock index page has a “Manual Entry” button. This avoids a duplicate sidebar item.

- **Shorter labels**
  - “Sales Management” → **Sales**  
  - “Purchase Management” → **Purchases**  
  - “Payment Management” → **Payments**  
  - “Expense Management” → **Expenses**  
  - “Product Management” → **Products & Stock**  
  - “Warranty Management” → **Warranty**  
  - “Return Management” → **Returns**  
  - “Reports & Analytics” → **Reports**  
  - “User Management” → **Users** (under Access Control)  
  - “Role Management” → **Roles** (under Access Control)

- **Accounting submenu**
  - Most-used items first: Bank Accounts, then P&L Report, Balance Sheet, Ledger, Trial Balance, then Chart of Accounts and Journal Entries.
  - “General Ledger” label shortened to **Ledger**; “Profit & Loss” to **P&L Report** to reduce clutter.

- **Reports submenu**
  - Shorter labels where it helped (e.g. “By Product”, “Receivable”, “Payable”, “P&L Summary”).
  - **P&L Summary** (under Reports → Financial) is the simple sales/expense summary; **P&L Report** (under Accounting) is the double-entry accounting report.

### 2.2 Products & Stock

- **Stock & Movement** is one menu item and covers both stock movements and low-stock view; **Manual Entry** is done from the Stock index page, so no separate sidebar link.

---

## 3. Mandatory Options Added

### 3.1 Dashboard Quick Actions

These are the **mandatory daily operations** and are always visible on the dashboard (when the user has the right permission):

| Action            | Purpose                    | Permission / Note   |
|-------------------|----------------------------|----------------------|
| New Sale / Invoice| Create a new invoice       | create sales         |
| New Purchase Order| Create a new PO            | create purchases     |
| New Service Order | Create a new service job   | create services      |
| **Add Expense**   | Record an expense          | create expenses      |
| **Payments**      | View/record payments       | view payments        |
| **Low Stock**     | Open low-stock list        | view stock           |
| Stock & Movement  | Stock history and entry    | view stock           |
| Sales Report      | Open sales report          | view sales-reports   |

**Add Expense**, **Payments**, and **Low Stock** were added so that expense entry, payment tracking, and stock alerts are one click away from the dashboard.

### 3.2 What Stayed (No Features Removed)

- No routes or backend features were removed.
- All reports, accounting screens, returns, warranty, and settings remain available from the sidebar or from within their modules.
- Manual Stock Entry is still available from **Products & Stock → Stock & Movement** page via the “Manual Entry” button.

---

## 4. Mandatory vs Optional (Guidance)

### 4.1 Core (Mandatory for Day-to-Day)

- **Dashboard** – Overview and Quick Actions  
- **Sales** – Invoices, Customers  
- **Purchases** – Purchase Orders, Suppliers  
- **Service Orders** – Repair/service jobs  
- **Payments** – All payment records  
- **Expenses** – Expense recording  
- **Products & Stock** – Products, Stock & Movement, Categories, Brands (Models optional for simple setups)  
- **Warranty** – Verify Warranty, All Warranties (Submissions if you use warranty claims)  
- **Returns** – Sale / Purchase / Service returns as needed  
- **Settings** – Company Info (for invoices and documents)

### 4.2 Important for Business Control

- **Accounting** – Bank Accounts, P&L Report, Balance Sheet (and Ledger/Trial Balance if you use full accounting)  
- **Reports** – Sales Report, P&L Summary, Receivable, Payable, Cash Flow, Stock Valuation, Low Stock

### 4.3 Optional / Advanced

- **Access Control** – Users and Roles (only for admins)  
- **Chart of Accounts** and **Journal Entries** – For full double-entry bookkeeping  
- **Reports** – By Category, By Brand, Margin by Category, Slow/Fast Moving, Stock Turnover (use when you need that level of detail)

---

## 5. Suggestions for Future Use

1. **Roles**  
   - Give most staff a role that only has: Dashboard, Sales, Purchases, Service Orders, Payments, Expenses, Products & Stock, Warranty, Returns, and the main Reports.  
   - Restrict Accounting (Chart of Accounts, Journal Entries) and Access Control to one or two admin users.

2. **Daily habit**  
   - Use **Dashboard → Quick Actions** for: New Sale, New Purchase, New Service, Add Expense, and **Low Stock** so daily tasks stay in one place.

3. **Two P&L reports**  
   - **Reports → Financial → P&L Summary**: quick view from sales and expenses.  
   - **Accounting → P&L Report**: accounting-based P&L (double-entry). Use the one that matches how you work.

4. **Manual stock**  
   - Always via **Products & Stock → Stock & Movement**, then **Manual Entry** on that page – no need for a separate menu item.

---

## 6. Summary

- **Sidebar**: Reordered (daily ops first, Access Control last), shorter labels, one “Stock & Movement” entry, Manual Stock Entry removed from menu (still on Stock page).
- **Dashboard**: Quick Actions updated to include **Add Expense**, **Payments**, and **Low Stock**.
- **No features removed**: Everything is still available; only navigation and labels were simplified to make the software easier and cleaner for daily use.
