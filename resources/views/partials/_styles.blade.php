<style>
    :root {
        /* Light, minimal, pure green-accent ERP theme */
        --bg-body: #f5f7fb;
        --bg-elevated: #ffffff;
        --bg-elevated-soft: #f9fafb;
        --bg-elevated-lighter: #f0fdf4;
        --bg-sidebar: #166534;
        --bg-accent-soft: rgba(22, 163, 74, 0.08);
        --border-subtle: rgba(209, 213, 219, 0.9);
        --border-strong: rgba(148, 163, 184, 0.9);
        --text-main: #0f172a;
        --text-muted: #6b7280;
        --text-soft: #9ca3af;
        --accent: #22c55e;
        --accent-strong: #16a34a;
        --accent-soft: rgba(22, 163, 74, 0.14);
        --danger: #dc2626;
        --success: #16a34a;
        --warning: #eab308;
        --radius-lg: 18px;
        --radius-md: 12px;
        --radius-sm: 10px;
        --shadow-soft: 0 18px 40px rgba(15, 23, 42, 0.12);
        --shadow-subtle: 0 8px 20px rgba(15, 23, 42, 0.06);
        --transition-fast: 160ms ease-out;
        --transition-med: 220ms ease;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'DM Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        background: #f3f4f6; /* Clean, solid light gray background */
        color: var(--text-main);
        overflow-x: hidden;
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
    }
    
    a {
        color: inherit;
        text-decoration: none;
    }
    
    /* Sidebar Styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        /* Rich, professional pure green sidebar */
        background: #166534;
        color: #ffffff;
        transition: transform var(--transition-med), box-shadow var(--transition-med), width var(--transition-med);
        z-index: 1000;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border-right: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .sidebar-brand {
        padding: 24px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }
    
    .sidebar-brand h4 {
        font-weight: 700;
        letter-spacing: -0.01em;
        font-size: 15px; /* Slightly larger for better readability */
        margin: 0;
        color: #ffffff;
        text-transform: none; 
    }
    
    .sidebar-menu {
        padding: 16px 12px; /* Increased padding slightly */
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1;
        -webkit-overflow-scrolling: touch;
    }

    .sidebar-menu-item {
        padding: 11px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255, 255, 255, 0.75); /* Higher contrast for inactive items */
        text-decoration: none;
        transition: all 0.2s ease;
        border-radius: 8px;
        margin: 4px 0;
        font-weight: 500; /* Medium weight for normal state */
        font-size: 14px;
        border-left: none;
    }
    
    .sidebar-menu-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        transform: translateX(4px);
    }
    
    .sidebar-menu-item.active {
        background: rgba(255, 255, 255, 0.15); /* Clean active state, no gradient */
        color: #ffffff;
        font-weight: 600; /* Bolder active state */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .sidebar-menu-item i {
        width: 20px;
        font-size: 16px;
        opacity: 0.9;
    }
    
    /* Submenu Styles */
    .sidebar-menu-group {
        margin-bottom: 5px;
        position: relative;
        z-index: 1;
    }
    
    .sidebar-menu-parent {
        cursor: pointer;
        position: relative;
        justify-content: space-between;
    }
    
    .sidebar-menu-parent .submenu-icon {
        transition: transform var(--transition-fast);
        font-size: 10px;
        margin-left: auto;
        opacity: 0.7;
    }
    
    .sidebar-submenu {
        max-height: 0;
        overflow: hidden;
        overflow-y: auto;
        transition: max-height var(--transition-med);
        background: rgba(0, 0, 0, 0.15); /* Darker background for nested look */
        position: relative;
        z-index: 1;
        border-radius: 8px;
        margin-top: 4px;
    }
    
    .sidebar-submenu-header {
        padding: 8px 20px 4px 44px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 8px;
        position: sticky;
        top: 0;
        background: transparent; /* Removed specific bg */
        z-index: 2;
    }
    
    .sidebar-submenu-header:first-child {
        margin-top: 0;
    }
    
    .sidebar-submenu.show {
        max-height: 600px;
        overflow-y: auto;
        padding-bottom: 4px; /* Slight padding at bottom */
    }
    
    /* Larger max-height for reports submenu which has more items */
    #submenu-reports.show {
        max-height: 800px;
    }
    
    /* Custom scrollbar for submenus */
    .sidebar-submenu::-webkit-scrollbar {
        width: 4px;
    }
    
    .sidebar-submenu::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-submenu::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
    }
    
    .sidebar-submenu::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    
    .sidebar-submenu-item {
        padding: 8px 16px 8px 48px; /* Indented padding */
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: background-color var(--transition-fast), color var(--transition-fast);
        border-left: none;
        font-size: 13.5px;
        border-radius: 6px;
        margin: 2px 8px;
        font-weight: 400;
    }
    
    .sidebar-submenu-item:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }
    
    .sidebar-submenu-item.active {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        font-weight: 600; /* Bolder active submenu item */
    }
    
    .sidebar-submenu-item i {
        width: 16px;
        font-size: 12px;
        opacity: 0.8;
    }
    
    .sidebar-menu-group.active > .sidebar-menu-parent {
        background: rgba(255, 255, 255, 0.1);
        font-weight: 600;
        color: #ffffff;
    }
    
    /* Main Content */
    .main-content {
        margin-left: 250px;
        min-height: 100vh;
        transition: margin-left var(--transition-med);
        display: flex;
        flex-direction: column;
    }
    
    /* Top Navigation */
    .top-navbar {
        /* Light top bar with subtle green accent */
        background: #ffffff;
        padding: 14px 26px;
        box-shadow: 0 2px 12px rgba(15, 23, 42, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(229, 231, 235, 0.9);
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
    }
    
    .nav-left {
        display: flex;
        align-items: center;
    }
    
    .nav-left h5 {
        margin: 0;
        font-weight: 500;
        color: #0f172a;
        letter-spacing: 0.02em;
        font-size: 14px;
    }
    
    .nav-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .nav-icon {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #0f172a;
        cursor: pointer;
        transition: background-color var(--transition-fast), color var(--transition-fast), transform var(--transition-fast), box-shadow var(--transition-fast), border-color var(--transition-fast);
        border: 1px solid rgba(209, 213, 219, 0.9);
    }
    
    .nav-icon:hover {
        background: rgba(20, 184, 166, 0.12);
        color: #0f172a;
        border-color: rgba(20, 184, 166, 0.9);
        transform: scale(1.05);
        box-shadow: 0 0 0 1px rgba(20, 184, 166, 0.4), 0 10px 22px rgba(15, 23, 42, 0.18);
    }
    
    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
    }
    
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: radial-gradient(circle at 0 0, #bbf7d0, #16a34a);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #022c22;
        font-weight: 600;
        border: 1px solid rgba(34, 197, 94, 0.75);
        box-shadow: 0 10px 26px rgba(22, 163, 74, 0.5);
    }
    
    /* Content Area */
    .content-wrapper {
        padding: 26px 26px 30px;
        min-height: calc(100vh - 70px);
    }
    
    /* Stats Cards */
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 6px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.04);
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
        border-color: rgba(13, 148, 136, 0.2);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px; /* Consistent rounded corners */
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
        background: #f0fdf4; /* Very subtle green bg */
        color: #16a34a; /* Green 600 */
    }
    
    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #111827; /* Gray 900 */
        margin-bottom: 4px;
        letter-spacing: -0.02em;
    }
    
    .stat-label {
        color: #6b7280; /* Gray 500 */
        font-size: 13px;
        font-weight: 500;
    }

    /* Breadcrumbs */
    .breadcrumb-nav {
        padding: 8px 0 12px;
        font-size: 13px;
    }
    .breadcrumb-nav .breadcrumb {
        background: transparent;
        padding: 0;
    }
    .breadcrumb-nav .breadcrumb-item a {
        color: var(--text-muted);
        text-decoration: none;
    }
    .breadcrumb-nav .breadcrumb-item a:hover {
        color: var(--accent-strong);
    }
    .breadcrumb-nav .breadcrumb-item.active {
        color: var(--text-main);
        font-weight: 600;
    }
    .breadcrumb-nav .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: var(--text-soft);
        padding: 0 6px;
    }

    .table-row-clickable { cursor: pointer; }
    .table-row-clickable:hover { background-color: rgba(22, 163, 74, 0.06); }

    .report-hub-card { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .report-hub-card:hover { transform: translateY(-2px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; }

    /* Table Card */
    /* Filter form wrapper inside table-card */
    .table-card .filter-wrapper {
        padding: 20px 24px;
        border-bottom: 1px solid #f3f4f6;
        background: #fafafa;
    }

    /* Module stats row - sits above table-card */
    .module-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }
    .module-stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .module-stat-card .msc-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #9ca3af;
        margin-bottom: 5px;
    }
    .module-stat-card .msc-value {
        font-size: 22px;
        font-weight: 800;
        color: #111;
        line-height: 1;
    }
    .module-stat-card .msc-sub {
        font-size: 11px;
        color: #6b7280;
        margin-top: 3px;
    }

    .table-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 0; /* Remove padding to let table go edge-to-edge or handle internally */
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 6px rgba(0, 0, 0, 0.02);
        margin-top: 0;
        border: 1px solid rgba(0, 0, 0, 0.04);
        overflow: hidden; /* For rounded corners on table */
    }
    
    .table-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
    }
    
    .table-card-header h6 {
        font-weight: 600;
        color: #111827;
        font-size: 16px;
        margin: 0;
    }

    .table {
        width: 100%;
        margin-bottom: 0;
    }
    
    .table thead th {
        background: #f9fafb; /* Gray 50 */
        color: #4b5563; /* Gray 600 */
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 24px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .table tbody td {
        padding: 16px 24px;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
        color: #374151; /* Gray 700 */
        font-size: 14px;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table-hover tbody tr:hover {
        background-color: #f8fafc; /* Very subtle blue-gray hover */
    }
    
    /* Footer */
    .main-footer {
        background: #f9fafb;
        border-top: 1px solid rgba(229, 231, 235, 0.9);
        padding: 16px 26px 20px;
        text-align: center;
        color: var(--text-muted);
        font-size: 12px;
        margin-top: auto;
    }
    
    /* Mobile Menu Toggle - Integrated in Navbar */
    .mobile-menu-toggle {
        display: none;
        background: var(--accent);
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        margin-right: 12px;
        transition: background-color var(--transition-fast), transform var(--transition-fast), box-shadow var(--transition-fast);
        align-items: center;
        justify-content: center;
        font-size: 18px;
        width: 40px;
        height: 40px;
    }
    
    .mobile-menu-toggle:hover {
        background: var(--accent-strong);
        transform: scale(1.05);
    }
    
    .mobile-menu-toggle:active {
        transform: scale(0.95);
    }
    
    .sidebar.show-mobile {
        margin-left: 0;
    }
    
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 998;
    }
    
    .sidebar-overlay.show {
        display: block;
    }
    
    /* Responsive Design */
    /* Large Desktop */
    @media (min-width: 1200px) {
        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
        }
    }
    
    /* Desktop & Tablet */
    @media (max-width: 991px) {
        .content-wrapper {
            padding: 22px 18px;
        }
        
        .table-card {
            padding: 20px;
        }
        
        .stat-card {
            margin-bottom: 20px;
        }
    }
    
    /* Tablet */
    @media (max-width: 768px) {
        .mobile-menu-toggle {
            display: flex;
        }
        
        .nav-left {
            flex: 1;
        }
        
        .nav-left h5 {
            font-size: 18px;
        }
        
        .sidebar {
            margin-left: -260px;
        }
        
        .sidebar.show-mobile {
            margin-left: 0;
        }
        
        .main-content {
            margin-left: 0;
        }
        
        .content-wrapper {
            padding: 20px 15px;
        }
        
        .top-navbar {
            padding: 12px 16px;
            flex-wrap: nowrap;
        }
        
        .nav-left h5 {
            font-size: 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .mobile-menu-toggle {
            margin-right: 10px;
            width: 38px;
            height: 38px;
            padding: 6px 10px;
            font-size: 16px;
            flex-shrink: 0;
        }
        
        .nav-right {
            gap: 8px;
            flex-shrink: 0;
        }
        
        .nav-icon {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }
        
        .table-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .stat-value {
            font-size: 28px;
        }
        
        /* Responsive Tables */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            position: relative;
        }
        
        .table-responsive .table {
            position: relative;
        }
        
        .table-responsive .status-select-wrapper {
            position: static;
        }
        
        .table-responsive .status-dropdown {
            position: fixed !important;
        }
        
        .table {
            min-width: 760px;
        }
        
        .table thead th,
        .table tbody td {
            padding: 10px 8px;
            font-size: 13px;
        }
        
        /* Form Responsive */
        .row {
            margin-left: -10px;
            margin-right: -10px;
        }
        
        .row > [class*="col-"] {
            padding-left: 10px;
            padding-right: 10px;
        }
        
        /* Button Groups Responsive */
        .btn-group-responsive {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .btn-group-responsive .btn {
            flex: 1 1 auto;
            min-width: 100px;
        }
        
        /* Status Filter Buttons - Mobile */
        .status-filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            width: 100%;
        }
        
        .status-filter-group .btn {
            flex: 1 1 calc(33.333% - 8px);
            min-width: 100px;
            font-size: 13px;
            padding: 8px 12px;
        }
    }
    
    /* Mobile */
    @media (max-width: 576px) {
        .content-wrapper {
            padding: 16px 12px;
        }
        
        .table-card {
            padding: 15px;
            border-radius: 10px;
        }
        
        .table-card-header {
            flex-direction: column;
            align-items: stretch;
        }
        
        .table-card-header .btn {
            width: 100%;
            margin-top: 10px;
        }
        
        .top-navbar {
            padding: 10px 14px;
        }
        
        .nav-left h5 {
            font-size: 18px;
        }
        
        .stat-card {
            padding: 20px;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
        
        .stat-value {
            font-size: 24px;
        }
        
        /* Table on Mobile */
        .table {
            min-width: 600px;
            font-size: 12px;
        }
        
        .table thead th,
        .table tbody td {
            padding: 8px 6px;
            font-size: 12px;
        }
        
        /* Status Filter - Small Mobile */
        .status-filter-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        
        .status-filter-group .btn {
            flex: none;
            width: 100%;
            font-size: 12px;
            padding: 10px 8px;
        }
        
        /* Search and Filters Mobile */
        .filter-row {
            flex-direction: column;
        }
        
        .filter-row > [class*="col-"] {
            width: 100% !important;
            margin-bottom: 10px;
        }
        
        /* Form Controls Mobile */
        .form-control,
        .form-select {
            font-size: 16px; /* Prevents zoom on iOS */
        }
        
        /* Button Groups Mobile */
        .btn-group {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        
        .btn-group .btn {
            width: 100%;
            margin-bottom: 5px;
        }
        
        /* Alert Mobile */
        .alert {
            font-size: 14px;
            padding: 12px 15px;
        }
        
        .alert .badge {
            display: block;
            margin: 5px 0;
        }
        
        /* Footer Mobile */
        .main-footer {
            padding: 14px 18px;
            font-size: 11px;
        }
        
        /* Signature Section Mobile */
        .signature-section {
            flex-direction: column;
            gap: 20px;
        }
        
        .signature-box {
            width: 100%;
        }
        
        /* Payment Summary Mobile */
        .payment-summary {
            padding: 15px;
        }
        
        .payment-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
    }
    
    /* Extra Small Mobile */
    @media (max-width: 375px) {
        .sidebar {
            width: 240px;
        }
        
        .sidebar-brand {
            padding: 20px 15px;
        }
        
        .sidebar-brand h4 {
            font-size: 18px;
        }
        
        .status-filter-group {
            grid-template-columns: 1fr;
        }
        
        .stat-value {
            font-size: 20px;
        }
    }
    
    /* Print Styles */
    @media print {
        .sidebar,
        .top-navbar,
        .mobile-menu-toggle,
        .sidebar-overlay,
        .btn,
        .no-print {
            display: none !important;
        }
        
        .main-content {
            margin-left: 0 !important;
        }
        
        .content-wrapper {
            padding: 0 !important;
        }
    }
    /* Professional Status Select Styles */
    .status-select-wrapper {
        position: relative;
        display: inline-block;
        min-width: 160px;
    }
    
    /* Ensure dropdown escapes table cell boundaries */
    .table td {
        position: relative;
        overflow: visible;
    }
    
    .table tbody tr {
        position: relative;
        overflow: visible;
    }
    
    .status-select-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 14px;
        background: white;
        border: 2px solid var(--status-color, #6c757d);
        border-radius: 8px;
        color: var(--status-color, #6c757d);
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .status-select-btn:hover {
        background: var(--status-color, #6c757d);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .status-select-btn:hover .status-arrow {
        transform: rotate(180deg);
    }
    
    .status-select-btn .status-arrow {
        transition: transform 0.3s ease;
        font-size: 10px;
    }
    
    .status-select-btn:active,
    .status-select-btn.active {
        background: var(--status-color, #6c757d);
        color: white;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }
    
    .status-select-btn.active .status-arrow {
        transform: rotate(180deg);
    }
    
    .status-dropdown {
        position: fixed;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        margin: 0;
        display: none;
        overflow: visible;
        animation: slideDown 0.2s ease;
        min-width: 180px;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .status-dropdown.dropdown-up {
        animation: slideUp 0.2s ease;
    }
    
    .status-dropdown.show {
        display: block;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Ensure dropdown is above table rows */
    .table tbody tr {
        position: relative;
    }
    
    .status-select-wrapper {
        position: relative;
        z-index: 10;
    }
    
    .status-select-wrapper .status-dropdown.show {
        z-index: 9999;
    }
    
    .status-option {
        display: flex;
        align-items: center;
        padding: 12px 14px;
        color: #374151;
        text-decoration: none;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f3f4f6;
        font-weight: 500;
        font-size: 13px;
    }
    
    /* Bootstrap 4 Style Pagination */
    .pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        border-radius: 0.25rem;
        margin-bottom: 0;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .page-item {
        display: list-item;
    }
    
    .page-item:first-child .page-link {
        margin-left: 0;
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }
    
    .page-item:last-child .page-link {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
    
    .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #10b981;
        border-color: #10b981;
    }
    
    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        cursor: auto;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    .page-link {
        position: relative;
        display: block;
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #10b981;
        background-color: #fff;
        border: 1px solid #dee2e6;
        text-decoration: none;
    }
    
    .page-link:hover {
        z-index: 2;
        color: #059669;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    
    .page-link:focus {
        z-index: 3;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }
    
    .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #10b981;
        border-color: #10b981;
    }
    
    .page-item.active .page-link:hover {
        color: #fff;
        background-color: #059669;
        border-color: #059669;
    }
    
    .status-option:last-child {
        border-bottom: none;
    }
    
    .status-option:hover {
        background: linear-gradient(90deg, #f9fafb 0%, #f3f4f6 100%);
        padding-left: 18px;
        color: #1f2937;
    }
    
    .status-option i {
        width: 18px;
        font-size: 14px;
    }
    
    .status-select-btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .status-select-btn.loading {
        position: relative;
        pointer-events: none;
    }
    
    .status-select-btn.loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        margin: auto;
        border: 2px solid transparent;
        border-top-color: currentColor;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    /* Responsive Status Select */
    @media (max-width: 768px) {
        .status-select-wrapper {
            min-width: 140px;
        }
        
        .status-select-btn {
            padding: 7px 12px;
            font-size: 12px;
        }
        
        .status-dropdown {
            width: 100%;
            min-width: 160px;
        }
    }
    
    @media (max-width: 576px) {
        .status-select-wrapper {
            width: 100%;
        }
        
        .status-select-btn {
            width: 100%;
        }
    }
    /* Modern Button Styles */
    .btn {
        padding: 10px 20px;
        font-weight: 600;
        font-size: 14px;
        border-radius: 8px; /* Consistent rounded corners */
        transition: all 0.2s ease;
        letter-spacing: 0.01em;
    }

    .btn-sm {
        padding: 6px 14px;
        font-size: 12px;
    }

    .btn-primary {
        background-color: #16a34a;
        border-color: #16a34a;
        color: #ffffff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .btn-primary:hover, .btn-primary:focus {
        background-color: #15803d;
        border-color: #15803d;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.2), 0 2px 4px -1px rgba(22, 163, 74, 0.1);
    }

    .btn-outline-primary {
        color: #16a34a;
        border-color: #16a34a;
        background: transparent;
    }

    .btn-outline-primary:hover {
        background-color: #f0fdf4;
        color: #16a34a;
        border-color: #16a34a;
    }

    .btn-success {
        background-color: #10b981; /* Emerald 500 */
        border-color: #10b981;
        color: white;
    }

    .btn-danger {
        background-color: #ef4444; /* Red 500 */
        border-color: #ef4444;
        color: white;
    }

    /* Modern Badge Styles (Subtle) */
    .badge {
        font-weight: 600;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 11px;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .badge-subtle-success {
        background-color: #dcfce7; /* Emerald 100 */
        color: #166534; /* Emerald 800 */
    }

    .badge-subtle-warning {
        background-color: #fef9c3; /* Yellow 100 */
        color: #854d0e; /* Yellow 800 */
    }

    .badge-subtle-danger {
        background-color: #fee2e2; /* Red 100 */
        color: #991b1b; /* Red 800 */
    }

    .badge-subtle-info {
        background-color: #e0f2fe; /* Sky 100 */
        color: #075985; /* Sky 800 */
    }

    .badge-subtle-secondary {
        background-color: #f3f4f6; /* Gray 100 */
        color: #374151; /* Gray 700 */
    }
    
    /* Modern Form Controls */
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #d1d5db; /* Gray 300 */
        padding: 10px 14px;
        font-size: 14px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        background-color: #ffffff;
    }

    .form-control:focus, .form-select:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
        outline: none;
    }

    .form-label {
        font-weight: 500;
        color: #374151; /* Gray 700 */
        margin-bottom: 6px;
        font-size: 13px;
    }

    /* Custom Input Group */
    .input-group-text {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        color: #6b7280;
    }
    /* Override Bootstrap Badges for Modern Look */
    .badge.bg-success {
        background-color: #dcfce7 !important; /* Emerald 100 */
        color: #166534 !important; /* Emerald 800 */
    }

    .badge.bg-warning {
        background-color: #fef9c3 !important; /* Yellow 100 */
        color: #854d0e !important; /* Yellow 800 */
    }

    .badge.bg-danger {
        background-color: #fee2e2 !important; /* Red 100 */
        color: #991b1b !important; /* Red 800 */
    }

    .badge.bg-info {
        background-color: #e0f2fe !important; /* Sky 100 */
        color: #075985 !important; /* Sky 800 */
    }

    .badge.bg-secondary {
        background-color: #f3f4f6 !important; /* Gray 100 */
        color: #374151 !important; /* Gray 700 */
    }

    .badge.bg-primary {
        background-color: #dcfce7 !important;
        color: #166534 !important;
    }
    /* Modern Alerts */
    .alert {
        border: none;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        font-size: 14px;
        border-left: 4px solid transparent;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .alert-primary {
        background-color: #f0fdf4;
        color: #166534;
        border-left-color: #16a34a;
    }

    .alert-success {
        background-color: #f0fdf4; /* Green 50 */
        color: #166534; /* Green 800 */
        border-left-color: #16a34a; /* Green 600 */
    }

    .alert-warning {
        background-color: #fefce8; /* Yellow 50 */
        color: #854d0e; /* Yellow 800 */
        border-left-color: #ca8a04; /* Yellow 600 */
    }

    .alert-danger {
        background-color: #fef2f2; /* Red 50 */
        color: #991b1b; /* Red 800 */
        border-left-color: #dc2626; /* Red 600 */
    }

    .alert-info {
        background-color: #f0f9ff; /* Sky 50 */
        color: #075985; /* Sky 800 */
        border-left-color: #0284c7; /* Sky 600 */
    }
    
    .alert-secondary {
        background-color: #f8fafc; /* Slate 50 */
        color: #334155; /* Slate 700 */
        border-left-color: #64748b; /* Slate 500 */
    }
    /* Fix padding for forms inside table-cards (Create/Edit pages) */
    .table-card > form {
        padding: 24px;
    }
    /* Padding for Show pages content inside table-card */
    .table-card > .p-4 {
        padding: 24px !important;
    }
    /* Product Add/Edit form layout */
    .product-form-wrap .table-card-header.bg-light {
        background-color: #f8fafc !important;
    }
    @media (min-width: 992px) {
        .product-form-sidebar,
        .purchase-form-sidebar,
        .sale-form-sidebar,
        .service-form-sidebar {
            position: sticky;
            top: 1rem;
        }
    }
</style>

@stack('styles')

