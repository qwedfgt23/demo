<?php
session_start();
$isAdminPanel = basename($_SERVER['PHP_SELF']) === 'admin-panel.php';
$isAuthorized = $_SESSION['user'] ?? false;
$isAdmin = ($_SESSION['user']['role'] ?? null) === 'admin' ;
?>

<header class="site-header">
    <div class="container">
        <div class="logo">
            <a href="#">MyShop</a>
        </div>

        <nav class="main-nav" id="main-nav">
            <ul>
                <?php if ($isAdminPanel): ?>
                    <li><a href="#" data-route="admin-products">Товары</a></li>
                    <li><a href="#" data-route="admin-orders">Заказы</a></li>
                    <li><a href="#" data-route="blog-section">Блог</a></li>
                    <li><a href="#" data-route="users-section">Пользователи</a></li>
                    <li><a href="/index.php">Выйти</a></li>
                <?php else: ?>
                    <?php if ($isAuthorized): ?>
                        <li><a href="#" data-route="home">Главная</a></li>
                        <li><a href="#" data-route="product-catalog">Каталог</a></li>
                        <li><a href="#" data-route="account">Личный кабинет</a></li>
                        <li><a href="#" data-route="blog">Блог</a></li>
                        <?php if ($isAdmin): ?>
                            <li><a href="/admin-panel.php">Админка</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="#" data-route="home">Главная</a></li>
                        <li><a href="#" data-route="blog">Блог</a></li>
                        <li><a href="#" data-route="login-section">Авторизация</a></li>
                        <li><a href="#" data-route="register-section">Регистрация</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="burger" id="burger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</header>
