<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-building" style="font-size: 28px; color: #ffffff;"></i>
        <h4>ERP System</h4>
    </div>
    
    <nav class="sidebar-menu">
        @can('view dashboard')
        <a href="{{ route('dashboard') }}" class="sidebar-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        @endcan
        
        @canany(['view users', 'view roles'])
        <div class="sidebar-menu-group {{ request()->routeIs('user-management.*') || request()->routeIs('role-management.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'access-control')">
                <i class="fas fa-shield-alt"></i>
                <span>Access Control</span>
                <i class="fas fa-chevron-{{ request()->routeIs('user-management.*') || request()->routeIs('role-management.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-access-control"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('user-management.*') || request()->routeIs('role-management.*') ? 'show' : '' }}" id="submenu-access-control">
                @can('view users')
                <a href="{{ route('user-management.index') }}" class="sidebar-submenu-item {{ request()->routeIs('user-management.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>User Management</span>
                </a>
                @endcan
                @can('view roles')
                <a href="{{ route('role-management.index') }}" class="sidebar-submenu-item {{ request()->routeIs('role-management.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tag"></i>
                    <span>Role Management</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany
        @can('view services')
        <a href="{{ route('services.index') }}" class="sidebar-menu-item {{ request()->routeIs('services.*') ? 'active' : '' }}">
            <i class="fas fa-laptop-medical"></i>
            <span>Service Orders</span>
        </a>
        @endcan
        
        @canany(['view purchases', 'view suppliers'])
        <div class="sidebar-menu-group {{ request()->routeIs('purchases.*') || request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'purchase-management')">
                <i class="fas fa-shopping-cart"></i>
                <span>Purchase Management</span>
                <i class="fas fa-chevron-{{ request()->routeIs('purchases.*') || request()->routeIs('suppliers.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-purchase-management"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('purchases.*') || request()->routeIs('suppliers.*') ? 'show' : '' }}" id="submenu-purchase-management">
                @can('view purchases')
                <a href="{{ route('purchases.index') }}" class="sidebar-submenu-item {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Purchase Orders</span>
                </a>
                @endcan
                @can('view suppliers')
                <a href="{{ route('suppliers.index') }}" class="sidebar-submenu-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span>Suppliers</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany
        
        @canany(['view sales', 'view customers'])
        <div class="sidebar-menu-group {{ request()->routeIs('sales.*') || request()->routeIs('customers.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'sales-management')">
                <i class="fas fa-cash-register"></i>
                <span>Sales Management</span>
                <i class="fas fa-chevron-{{ request()->routeIs('sales.*') || request()->routeIs('customers.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-sales-management"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('sales.*') || request()->routeIs('customers.*') ? 'show' : '' }}" id="submenu-sales-management">
                @can('view sales')
                <a href="{{ route('sales.index') }}" class="sidebar-submenu-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Sales / Invoices</span>
                </a>
                @endcan
                @can('view customers')
                <a href="{{ route('customers.index') }}" class="sidebar-submenu-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany
        
        @canany(['view products', 'view categories', 'view brands', 'view product-models', 'view stock', 'view stock-movements'])
        <div class="sidebar-menu-group {{ request()->routeIs('categories.*') || request()->routeIs('brands.*') || request()->routeIs('product-models.*') || request()->routeIs('products.*') || request()->routeIs('stock.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'product-management')">
                <i class="fas fa-boxes"></i>
                <span>Product Management</span>
                <i class="fas fa-chevron-{{ request()->routeIs('categories.*') || request()->routeIs('brands.*') || request()->routeIs('product-models.*') || request()->routeIs('products.*') || request()->routeIs('stock.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-product-management"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('categories.*') || request()->routeIs('brands.*') || request()->routeIs('product-models.*') || request()->routeIs('products.*') || request()->routeIs('stock.*') ? 'show' : '' }}" id="submenu-product-management">
                @can('view products')
                <a href="{{ route('products.index') }}" class="sidebar-submenu-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
                @endcan
                @can('view categories')
                <a href="{{ route('categories.index') }}" class="sidebar-submenu-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
                @endcan
                @can('view brands')
                <a href="{{ route('brands.index') }}" class="sidebar-submenu-item {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                    <i class="fas fa-certificate"></i>
                    <span>Brands</span>
                </a>
                @endcan
                @can('view product-models')
                <a href="{{ route('product-models.index') }}" class="sidebar-submenu-item {{ request()->routeIs('product-models.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i>
                    <span>Models</span>
                </a>
                @endcan
                @can('view stock-movements')
                <a href="{{ route('stock.index') }}" class="sidebar-submenu-item {{ request()->routeIs('stock.index') || request()->routeIs('stock.low-stock') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i>
                    <span>Stock Management</span>
                </a>
                @endcan
                @can('create stock')
                <a href="{{ route('stock.create-manual') }}" class="sidebar-submenu-item {{ request()->routeIs('stock.create-manual') ? 'active' : '' }}">
                    <i class="fas fa-plus-square"></i>
                    <span>Manual Stock Entry</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany
        
        @canany(['view warranties', 'verify warranties', 'view warranty-submissions'])
        <div class="sidebar-menu-group {{ request()->routeIs('warranties.*') || request()->routeIs('warranty-submissions.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'warranty-management')">
                <i class="fas fa-shield-alt"></i>
                <span>Warranty Management</span>
                <i class="fas fa-chevron-{{ request()->routeIs('warranties.*') || request()->routeIs('warranty-submissions.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-warranty-management"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('warranties.*') || request()->routeIs('warranty-submissions.*') ? 'show' : '' }}" id="submenu-warranty-management">
                @can('verify warranties')
                <a href="{{ route('warranties.verify') }}" class="sidebar-submenu-item {{ request()->routeIs('warranties.verify') ? 'active' : '' }}">
                    <i class="fas fa-search"></i>
                    <span>Verify Warranty</span>
                </a>
                @endcan
                @can('view warranty-submissions')
                <a href="{{ route('warranty-submissions.index') }}" class="sidebar-submenu-item {{ request()->routeIs('warranty-submissions.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Warranty Submissions</span>
                </a>
                @endcan
                @can('view warranties')
                <a href="{{ route('warranties.index') }}" class="sidebar-submenu-item {{ request()->routeIs('warranties.index') || request()->routeIs('warranties.show') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span>All Warranties</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany
        
        @can('view payments')
        <a href="{{ route('payments.index') }}" class="sidebar-menu-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">
            <i class="fas fa-money-bill-wave"></i>
            <span>Payment Management</span>
        </a>
        @endcan
        
        @canany(['view bank-accounts', 'view accounts', 'view journal-entries', 'view accounting-reports'])
        <div class="sidebar-menu-group {{ request()->routeIs('bank-accounts.*') || request()->routeIs('accounts.*') || request()->routeIs('journal-entries.*') || request()->routeIs('accounting.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'accounting')">
                <i class="fas fa-calculator"></i>
                <span>Accounting</span>
                <i class="fas fa-chevron-{{ request()->routeIs('bank-accounts.*') || request()->routeIs('accounts.*') || request()->routeIs('journal-entries.*') || request()->routeIs('accounting.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-accounting"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('bank-accounts.*') || request()->routeIs('accounts.*') || request()->routeIs('journal-entries.*') || request()->routeIs('accounting.*') ? 'show' : '' }}" id="submenu-accounting">
                @can('view bank-accounts')
                <a href="{{ route('bank-accounts.index') }}" class="sidebar-submenu-item {{ request()->routeIs('bank-accounts.*') ? 'active' : '' }}">
                    <i class="fas fa-university"></i>
                    <span>Bank Accounts</span>
                </a>
                @endcan
                @can('view accounts')
                <a href="{{ route('accounts.index') }}" class="sidebar-submenu-item {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Chart of Accounts</span>
                </a>
                @endcan
                @can('view journal-entries')
                <a href="{{ route('journal-entries.index') }}" class="sidebar-submenu-item {{ request()->routeIs('journal-entries.*') ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i>
                    <span>Journal Entries</span>
                </a>
                @endcan
                @can('view accounting-reports')
                <a href="{{ route('accounting.ledger') }}" class="sidebar-submenu-item {{ request()->routeIs('accounting.ledger') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>General Ledger</span>
                </a>
                <a href="{{ route('accounting.trial-balance') }}" class="sidebar-submenu-item {{ request()->routeIs('accounting.trial-balance') ? 'active' : '' }}">
                    <i class="fas fa-balance-scale"></i>
                    <span>Trial Balance</span>
                </a>
                <a href="{{ route('accounting.balance-sheet') }}" class="sidebar-submenu-item {{ request()->routeIs('accounting.balance-sheet') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Balance Sheet</span>
                </a>
                <a href="{{ route('accounting.profit-loss') }}" class="sidebar-submenu-item {{ request()->routeIs('accounting.profit-loss') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Profit & Loss</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany
        
        @can('view expenses')
        <a href="{{ route('expenses.index') }}" class="sidebar-menu-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i>
            <span>Expense Management</span>
        </a>
        @endcan
        
        @canany(['view sales-reports', 'view purchase-reports', 'view financial-reports', 'view inventory-reports'])
        <!-- Reports & Analytics -->
        <div class="sidebar-menu-group {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'reports')">
                <i class="fas fa-chart-bar"></i>
                <span>Reports & Analytics</span>
                <i class="fas fa-chevron-{{ request()->routeIs('reports.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-reports"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="submenu-reports">
                @can('view sales-reports')
                <div class="sidebar-submenu-header">Sales Reports</div>
                <a href="{{ route('reports.sales.index') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.sales.index') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Sales Report</span>
                </a>
                <a href="{{ route('reports.sales.by-product') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.sales.by-product') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Sales by Product</span>
                </a>
                <a href="{{ route('reports.sales.by-category') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.sales.by-category') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Sales by Category</span>
                </a>
                <a href="{{ route('reports.sales.by-brand') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.sales.by-brand') ? 'active' : '' }}">
                    <i class="fas fa-certificate"></i>
                    <span>Sales by Brand</span>
                </a>
                <a href="{{ route('reports.sales.by-customer') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.sales.by-customer') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Sales by Customer</span>
                </a>
                <a href="{{ route('reports.sales.top-products') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.sales.top-products') ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    <span>Top Selling Products</span>
                </a>
                @endcan
                
                @can('view purchase-reports')
                <div class="sidebar-submenu-header">Purchase Reports</div>
                <a href="{{ route('reports.purchases.index') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.purchases.index') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Purchase Report</span>
                </a>
                <a href="{{ route('reports.purchases.by-supplier') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.purchases.by-supplier') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span>Purchases by Supplier</span>
                </a>
                <a href="{{ route('reports.purchases.cost-analysis') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.purchases.cost-analysis') ? 'active' : '' }}">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Cost Analysis</span>
                </a>
                @endcan
                
                @can('view financial-reports')
                <div class="sidebar-submenu-header">Financial Reports</div>
                <a href="{{ route('reports.financial.profit-loss') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.financial.profit-loss') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Profit & Loss</span>
                </a>
                <a href="{{ route('reports.financial.gross-margin-product') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.financial.gross-margin-product') ? 'active' : '' }}">
                    <i class="fas fa-percentage"></i>
                    <span>Gross Margin (Product)</span>
                </a>
                <a href="{{ route('reports.financial.gross-margin-category') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.financial.gross-margin-category') ? 'active' : '' }}">
                    <i class="fas fa-percentage"></i>
                    <span>Gross Margin (Category)</span>
                </a>
                <a href="{{ route('reports.financial.cash-flow') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.financial.cash-flow') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Cash Flow Statement</span>
                </a>
                <a href="{{ route('reports.financial.accounts-receivable') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.financial.accounts-receivable') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Accounts Receivable</span>
                </a>
                <a href="{{ route('reports.financial.accounts-payable') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.financial.accounts-payable') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i>
                    <span>Accounts Payable</span>
                </a>
                @endcan
                
                @can('view inventory-reports')
                <div class="sidebar-submenu-header">Inventory Reports</div>
                <a href="{{ route('reports.inventory.stock-valuation') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.inventory.stock-valuation') ? 'active' : '' }}">
                    <i class="fas fa-calculator"></i>
                    <span>Stock Valuation</span>
                </a>
                <a href="{{ route('reports.inventory.slow-moving') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.inventory.slow-moving') ? 'active' : '' }}">
                    <i class="fas fa-hourglass-half"></i>
                    <span>Slow Moving Products</span>
                </a>
                <a href="{{ route('reports.inventory.fast-moving') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.inventory.fast-moving') ? 'active' : '' }}">
                    <i class="fas fa-rocket"></i>
                    <span>Fast Moving Products</span>
                </a>
                <a href="{{ route('reports.inventory.stock-turnover') }}" class="sidebar-submenu-item {{ request()->routeIs('reports.inventory.stock-turnover') ? 'active' : '' }}">
                    <i class="fas fa-sync-alt"></i>
                    <span>Stock Turnover Ratio</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany
        
        @canany(['view purchase-returns', 'view sale-returns', 'view service-returns'])
        <div class="sidebar-menu-group {{ request()->routeIs('purchase-returns.*') || request()->routeIs('sale-returns.*') || request()->routeIs('service-returns.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'return-management')">
                <i class="fas fa-undo"></i>
                <span>Return Management</span>
                <i class="fas fa-chevron-{{ request()->routeIs('purchase-returns.*') || request()->routeIs('sale-returns.*') || request()->routeIs('service-returns.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-return-management"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('purchase-returns.*') || request()->routeIs('sale-returns.*') || request()->routeIs('service-returns.*') ? 'show' : '' }}" id="submenu-return-management">
                @can('view purchase-returns')
                <a href="{{ route('purchase-returns.index') }}" class="sidebar-submenu-item {{ request()->routeIs('purchase-returns.*') ? 'active' : '' }}">
                    <i class="fas fa-undo"></i>
                    <span>Purchase Returns</span>
                </a>
                @endcan
                @can('view sale-returns')
                <a href="{{ route('sale-returns.index') }}" class="sidebar-submenu-item {{ request()->routeIs('sale-returns.*') ? 'active' : '' }}">
                    <i class="fas fa-undo"></i>
                    <span>Sale Returns</span>
                </a>
                @endcan
                @can('view service-returns')
                <a href="{{ route('service-returns.index') }}" class="sidebar-submenu-item {{ request()->routeIs('service-returns.*') ? 'active' : '' }}">
                    <i class="fas fa-undo"></i>
                    <span>Service Returns</span>
                </a>
                @endcan
            </div>
        </div>
        @endcanany
        
        @can('view settings')
        <div class="sidebar-menu-group {{ request()->routeIs('company-info.*') ? 'active' : '' }}">
            <div class="sidebar-menu-item sidebar-menu-parent" onclick="toggleSubmenu(event, 'settings')">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
                <i class="fas fa-chevron-{{ request()->routeIs('company-info.*') ? 'up' : 'down' }} ms-auto submenu-icon" id="icon-settings"></i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('company-info.*') ? 'show' : '' }}" id="submenu-settings">
                <a href="{{ route('company-info.edit') }}" class="sidebar-submenu-item {{ request()->routeIs('company-info.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>Company Info</span>
                </a>
            </div>
        </div>
        @endcan
    </nav>
</div>

