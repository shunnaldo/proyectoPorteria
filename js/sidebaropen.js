  // Mobile sidebar toggle
        const menuToggle = document.getElementById('menuToggle');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.getElementById('mainContent');
        
        // Toggle sidebar on mobile
        menuToggle?.addEventListener('click', function() {
            sidebar.classList.add('show');
            sidebarOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        });
        
        // Close sidebar (both mobile and desktop)
        function closeSidebarFunc() {
            if (window.innerWidth >= 992) {
                // Desktop behavior - collapse instead of close
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            } else {
                // Mobile behavior - hide completely
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }
        
        closeSidebar.addEventListener('click', closeSidebarFunc);
        
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            this.classList.remove('show');
            document.body.style.overflow = 'auto';
        });

        // Toggle dropdown menus
        document.getElementById('usersMenu').addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
            document.getElementById('usersDropdown').classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            const dropdowns = document.querySelectorAll('.menu-dropdown');
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
            });
            
            const menuItems = document.querySelectorAll('.has-dropdown');
            menuItems.forEach(item => {
                item.classList.remove('active');
            });
        });

        // Prevent dropdown from closing when clicking inside
        document.querySelectorAll('.menu-dropdown').forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        // Initialize desktop version
        function initSidebar() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                sidebar.classList.remove('collapsed');
                mainContent.classList.add('expanded');
                document.body.style.overflow = 'auto';
            } else {
                mainContent.classList.remove('expanded');
            }
        }

        // Run on load and resize
        window.addEventListener('load', initSidebar);
        window.addEventListener('resize', initSidebar);