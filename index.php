<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Современный одностраничный интернет-магазин с блогом и личным кабинетом." />
    <meta name="theme-color" content="#A8DADC" />

    <title>Интернет-магазин</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <!-- Подключение шрифта -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet" />

    <!-- Стили -->
    <link rel="stylesheet" href="styles/style.css" />
    <link rel="stylesheet" href="styles/main.css" />

    <!-- Logo -->
    <link rel="icon" href="img/logocompany.png" type="image/png" />
</head>

<body>
<div id="app">
    <!-- Хедер -->
    <?php include 'scripts/header.php'; ?>
    <!-- Авторизация -->
    <section id="login-section" class="page-section hidden">
        <div class="form-wrapper">
            <h2>Вход</h2>
            <form id="login-form">
                <input type="email" name="email" placeholder="Электронная почта" required>
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit">Войти</button>
            </form>
            <div class="form-message" id="login-message"></div>
        </div>
    </section>
    <!-- Регистрация -->
    <section id="register-section" class="page-section hidden">
        <div class="form-wrapper">
            <h2>Регистрация</h2>
            <form id="register-form">
                <input type="text" name="name" placeholder="Имя" required>
                <input type="email" name="email" placeholder="Электронная почта" required>
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit">Зарегистрироваться</button>
            </form>
            <div class="form-message" id="register-message"></div>
        </div>
    </section>
    <!-- Главная -->
    <section id="home" class="page-section">
        <div class="container-info">
            <h1 class="main-title">Добро пожаловать в наш магазин</h1>
            <div class="info-blocks">
                <div class="info-card">
                    <h2>Лучшие товары</h2>
                    <p>Мы предлагаем только качественные и проверенные товары по отличным ценам.</p>
                </div>
                <div class="info-card">
                    <h2>Удобная доставка</h2>
                    <p>Быстрая и надёжная доставка по всей стране. Отслеживайте заказ онлайн.</p>
                </div>
                <div class="info-card">
                    <h2>Поддержка 24/7</h2>
                    <p>Наша команда поддержки готова помочь вам в любое время суток.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Каталог -->
    <section id="product-catalog" class="page-section hidden">
        <div class="catalog-header">
            <h2>Каталог товаров</h2>
            <div class="filters">
                <input type="text" id="search-title" placeholder="Поиск по названию">
                <input type="text" id="search-category" placeholder="Категория">
                <input type="number" id="search-min-price" placeholder="Мин. цена">
                <input type="number" id="search-max-price" placeholder="Макс. цена">
            </div>
        </div>

        <div id="product-container" class="product-grid loading"></div>
        <div class="no-products hidden">Нет подходящих товаров.</div>

        <div id="product-modal" class="modal hidden">
            <div class="modal-content">
                <button id="product-modal-close" class="modal-close">&times;</button>
                <div class="product-details">
                    <img id="modal-product-image" src="" alt="Фото товара" class="modal-product-image">
                    <div class="modal-product-info">
                        <h3 id="modal-product-title"></h3>
                        <p id="modal-product-category" class="modal-product-category"></p>
                        <p id="modal-product-description" class="modal-product-description"></p>
                        <p id="modal-product-price" class="modal-product-price"></p>
                        <div class="modal-actions">
                            <button id="add-to-cart-btn" class="btn primary">Добавить в корзину</button>
                            <button id="toggle-favorite-btn" class="btn">В избранное</button>
                            <button id="delete-product-btn" class="btn danger hidden">Удалить товар</button>
                        </div>
                    </div>
                </div>
                <section class="comments-section">
                    <h4>Комментарии</h4>
                    <div id="modal-comments-container" class="comments-container"></div>
                    <form id="add-comment-form" class="add-comment-form">
                        <textarea name="comment" placeholder="Оставить комментарий" rows="3" required></textarea>
                        <button type="submit" class="btn primary">Отправить</button>
                    </form>
                </section>
            </div>
        </div>
    </section>
    <!-- Личный кабинет -->
    <section id="account" class="page-section hidden">
        <section id="favorites-section" class="cabinet-section">
            <h3>Избранное</h3>
            <div id="favorites-container" class="favorites-grid">
                <!-- Товары будут подгружены сюда -->
            </div>
            <div id="no-favorites" class="no-items hidden">Избранных товаров нет.</div>
        </section>
        <section id="cart-section" class="cabinet-section">
            <h3>Корзина</h3>
            <div id="cart-container" class="cart-list">
                <!-- Товары корзины подгрузятся сюда -->
            </div>
            <div id="no-cart-items" class="no-items hidden">Корзина пуста.</div>
            <button id="checkout-btn" class="btn" disabled>Оформить заказ</button>
        </section>
        <section id="orders-section" class="cabinet-section">
            <h3>Мои заказы</h3>
            <div id="orders-container">
                <!-- Заказы загрузятся сюда -->
            </div>
            <div id="no-orders" class="no-items hidden">У вас пока нет заказов.</div>
            <a href="/scripts/logout.php" style="color: var(--color-error)">Выйти из профиля</a>
        </section>
    </section>
    <!-- Блог -->
    <section id="blog" class="page-section hidden">
        <div class="blog-slider-container">
            <div class="blog-slider" id="blog-slider"></div>
            <button class="blog-prev" onclick="changeBlogSlide(-1)">&#10094;</button>
            <button class="blog-next" onclick="changeBlogSlide(1)">&#10095;</button>
            <div class="blog-dots" id="blog-dots"></div>
        </div>
        <div class="blog-list" id="blog-list"></div>
        <div id="blog-modal">
            <div id="blog-modal-content">
                <span id="blog-modal-close">&times;</span>
                <h2 id="blog-modal-title"></h2>
                <img id="blog-modal-image" src="" alt="">
                <p id="blog-modal-text"></p>
                <small id="blog-modal-date"></small>
            </div>
        </div>
    </section>
    <!-- Контакты -->

    <!-- Футер -->
</div>

<!-- Скрипты -->
<script src="js/router.js"></script>
<script src="js/animations.js"></script>
<script src="js/main.js" defer></script>
<script src="js/burger.js"></script>
<script src="js/auth.js"></script>
</body>
</html>
