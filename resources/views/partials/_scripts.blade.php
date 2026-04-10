<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Initialize Bootstrap Tooltips -->
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Mobile Menu Toggle
    function toggleMobileMenu() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (sidebar && overlay) {
            sidebar.classList.toggle('show-mobile');
            overlay.classList.toggle('show');
        }
    }
    
    function closeMobileMenu() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (sidebar && overlay) {
            sidebar.classList.remove('show-mobile');
            overlay.classList.remove('show');
        }
    }
    
    // Close mobile menu when clicking outside or on navigation links (not parent menu items)
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.querySelector('.mobile-menu-toggle');
        
        if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('show-mobile')) {
            // Check if clicking on parent menu item (has submenu) - don't close in this case
            const parentMenuItem = event.target.closest('.sidebar-menu-parent');
            if (parentMenuItem) {
                // Don't close - let submenu toggle
                return;
            }
            
            // Check if clicking on a regular navigation link (not a parent)
            const navLink = event.target.closest('a.sidebar-menu-item:not(.sidebar-menu-parent)');
            const submenuLink = event.target.closest('a.sidebar-submenu-item');
            
            // Only close if clicking on actual navigation links
            if (navLink || submenuLink) {
                setTimeout(() => {
                    closeMobileMenu();
                }, 300);
            }
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
    });
    
    // Toggle submenu function
    function toggleSubmenu(event, menuId) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        const submenu = document.getElementById('submenu-' + menuId);
        const icon = document.getElementById('icon-' + menuId);
        
        if (submenu && icon) {
            if (submenu.classList.contains('show')) {
                submenu.classList.remove('show');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            } else {
                submenu.classList.add('show');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        }
        
        // Don't close sidebar on mobile when toggling submenu
        return false;
    }
</script>

@include('partials._number_clear_zero_inputs')

@stack('scripts')

