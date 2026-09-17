document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('navModal');
    if (!modal) return;

    const links = modal.querySelectorAll('.modal-body a');
    const path = window.location.pathname.split('/').pop();

    links.forEach(a => {
        const href = a.getAttribute('href');
        if (!href) return;
        const hrefFile = href.split('/').pop();

        if (hrefFile === path || (hrefFile === 'dashboard.php' && (path === '' || path === 'index.php')) ) {
            a.classList.add('nav-active');
        }

        if (hrefFile === 'tela_login.php' || /logout/.test(hrefFile)) {
            a.classList.add('nav-logout');
        }
    });
});
