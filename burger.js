document.addEventListener('DOMContentLoaded', () => {
    const burger = document.getElementById('burger');
    const nav = document.getElementById('main-nav');

    burger.addEventListener('click', () => {
        nav.classList.toggle('active');
    });
});
