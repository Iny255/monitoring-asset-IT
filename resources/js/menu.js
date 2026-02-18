document.addEventListener('DOMContentLoaded', function () {
    const menuItems = document.querySelectorAll('.menu-item');
    const currentUrl = window.location.pathname; // ambil path saja

    menuItems.forEach(item => {
        const link = item.querySelector('a');
        if (!link) return;

        const menuUrl = new URL(link.href).pathname;

        // Cek apakah URL sekarang diawali oleh URL menu
        if (currentUrl.startsWith(menuUrl)) {
            item.classList.add('active');

            // Aktifkan parent jika submenu
            let parent = item.closest('.menu-sub');
            if (parent) {
                const parentMenu = parent.closest('.menu-item');
                if (parentMenu) parentMenu.classList.add('active');
            }
        }
    });
});
