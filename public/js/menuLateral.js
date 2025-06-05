document.addEventListener('DOMContentLoaded', () => {
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const userButton = document.getElementById('userButton');
    const userDropdown = document.getElementById('userDropdown');
    const menusWithSubmenu = document.querySelectorAll('.has-submenu');
    
    // Toggle sidebar
    if (toggleSidebar) {
        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }

    // Toggle menú de usuario
    if (userButton && userDropdown) {
        userButton.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (userDropdown.classList.contains('active') && 
                !userButton.contains(e.target) && 
                !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }

    // Submenús
    menusWithSubmenu.forEach(menu => {
        const submenu = menu.nextElementSibling;
        const chevron = menu.querySelector('.fa-chevron-down');
        
        if (submenu && submenu.classList.contains('submenu')) {
            menu.addEventListener('click', () => {
                submenu.classList.toggle('submenu-active');
                
                if (chevron) {
                    chevron.style.transform = submenu.classList.contains('submenu-active') 
                        ? 'rotate(180deg)' 
                        : 'rotate(0)';
                }
            });
            
            if (submenu.querySelector('.active') || menu.classList.contains('active')) {
                submenu.classList.add('submenu-active');
                if (chevron) {
                    chevron.style.transform = 'rotate(180deg)';
                }
            }
        }
    });

    // Cerrar sidebar en móvil
    document.addEventListener('click', (e) => {
        const isMobile = window.innerWidth <= 768;
        if (isMobile && 
            sidebar && 
            sidebar.classList.contains('active') && 
            !sidebar.contains(e.target) && 
            !toggleSidebar.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    });
});