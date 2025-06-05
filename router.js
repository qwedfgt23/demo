document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('[data-route]');
    const sections = document.querySelectorAll('.page-section');

    function showSection(route) {
        sections.forEach(section => {
            if (section.id === route) {
                section.classList.add('active');
                section.classList.remove('hidden');
            } else {
                section.classList.remove('active');
                section.classList.add('hidden');
            }
        });
    }

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const route = link.dataset.route;
            showSection(route);
            document.getElementById('main-nav')?.classList.remove('active');
        });
    });

    showSection('home');
});
