<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f0f9ff 100%);
        overflow-x: hidden;
        min-height: 100vh;
    }
    
    /* Sidebar Styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 260px;
        background: linear-gradient(180deg, #10b981 0%, #059669 100%);
        color: white;
        transition: all 0.3s;
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .sidebar-brand {
        padding: 25px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }
    
    .sidebar-brand h4 {
        font-weight: 700;
        font-size: 22px;
        margin: 0;
        color: white;
    }
    
    .sidebar-menu {
        padding: 20px 0;
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Custom scrollbar for sidebar menu */
    .sidebar-menu::-webkit-scrollbar {
        width: 6px;
    }
    
    .sidebar-menu::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-menu::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
    }
    
    .sidebar-menu::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    
    .sidebar-menu-item {
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s;
        border-left: 3px solid transparent;
    }
    
    .sidebar-menu-item:hover {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border-left-color: #ffffff;
    }
    
    .sidebar-menu-item.active {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border-left-color: #ffffff;
        font-weight: 600;
    }
    
    .sidebar-menu-item i {
        width: 20px;
        font-size: 18px;
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
        transition: transform 0.3s;
        font-size: 12px;
        margin-left: auto;
    }
    
    .sidebar-submenu {
        max-height: 0;
        overflow: hidden;
        overflow-y: auto;
        transition: max-height 0.3s ease-out;
        background: rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }
    
    .sidebar-submenu-header {
        padding: 10px 20px 5px 40px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 8px;
        position: sticky;
        top: 0;
        background: rgba(0, 0, 0, 0.2);
        z-index: 2;
    }
    
    .sidebar-submenu-header:first-child {
        margin-top: 0;
    }
    
    .sidebar-submenu.show {
        max-height: 600px;
        overflow-y: auto;
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
        padding: 10px 20px 10px 50px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.3s;
        border-left: 3px solid transparent;
        font-size: 14px;
    }
    
    .sidebar-submenu-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border-left-color: #ffffff;
        padding-left: 55px;
    }
    
    .sidebar-submenu-item.active {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border-left-color: #ffffff;
        font-weight: 600;
    }
    
    .sidebar-submenu-item i {
        width: 16px;
        font-size: 14px;
    }
    
    .sidebar-menu-group.active > .sidebar-menu-parent {
        background: rgba(255, 255, 255, 0.15);
        border-left-color: #ffffff;
    }
    
    /* Main Content */
    .main-content {
        margin-left: 260px;
        min-height: 100vh;
        transition: all 0.3s;
        display: flex;
        flex-direction: column;
    }
    
    /* Top Navigation */
    .top-navbar {
        background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
        padding: 15px 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #10b981;
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
        font-weight: 600;
        color: #1e293b;
    }
    
    .nav-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .nav-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        color: #10b981;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid #bae6fd;
    }
    
    .nav-icon:hover {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border-color: #10b981;
        transform: scale(1.1);
    }
    
    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        border: 2px solid #34d399;
        box-shadow: 0 2px 5px rgba(16, 185, 129, 0.3);
    }
    
    /* Content Area */
    .content-wrapper {
        padding: 30px;
        min-height: calc(100vh - 70px);
    }
    
    /* Stats Cards */
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        border-left: 4px solid;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card.primary { border-left-color: #3b82f6; }
    .stat-card.success { border-left-color: #10b981; }
    .stat-card.warning { border-left-color: #f59e0b; }
    .stat-card.danger { border-left-color: #ef4444; }
    .stat-card.info { border-left-color: #06b6d4; }
    .stat-card.secondary { border-left-color: #6b7280; }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }
    
    .stat-card.primary .stat-icon { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .stat-card.success .stat-icon { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .stat-card.warning .stat-icon { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .stat-card.danger .stat-icon { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .stat-card.info .stat-icon { background: rgba(6, 182, 212, 0.15); color: #06b6d4; }
    .stat-card.secondary .stat-icon { background: rgba(107, 114, 128, 0.15); color: #6b7280; }
    
    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
    }
    
    /* Table Card */
    .table-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-top: 30px;
    }
    
    .table-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .table-card-header h6 {
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }
    
    .table {
        margin: 0;
    }
    
    .table thead th {
        border: none;
        color: #64748b;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        padding: 15px;
    }
    
    .table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-color: #f1f5f9;
    }
    
    /* Footer */
    .main-footer {
        background: white;
        border-top: 1px solid #e5e7eb;
        padding: 20px 30px;
        text-align: center;
        color: #64748b;
        font-size: 14px;
        margin-top: auto;
    }
    
    /* Mobile Menu Toggle - Integrated in Navbar */
    .mobile-menu-toggle {
        display: none;
        background: #10b981;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        margin-right: 12px;
        transition: all 0.3s;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        width: 40px;
        height: 40px;
    }
    
    .mobile-menu-toggle:hover {
        background: #059669;
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
            padding: 25px 20px;
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
            padding: 12px 15px;
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
            min-width: 800px;
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
            padding: 15px 10px;
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
            padding: 10px 15px;
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
            padding: 15px 20px;
            font-size: 12px;
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
</style>

@stack('styles')

