document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            const res = await fetch('scripts/auth.php', {
                method: 'POST',
                body: formData
            });
            const text = await res.text();
            document.getElementById('login-message').innerText = text;
            if (text.includes('Успешный вход')) location.reload();
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(registerForm);
            const res = await fetch('scripts/register.php', {
                method: 'POST',
                body: formData
            });
            const text = await res.text();
            document.getElementById('register-message').innerText = text;
        });
    }
});
