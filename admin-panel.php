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
    <link rel="stylesheet" href="styles/admin.css" />

    <!-- Logo -->
    <link rel="icon" href="img/logocompany.png" type="image/png" />
</head>

<body>
<div id="app">
    <!-- Хедер -->
    <?php include 'scripts/header.php'; ?>
    <!-- Товары -->
    <section id="admin-products" class="admin-section page-section">
        <div class="admin-header">
            <h2>Товары</h2>
            <button id="add-product-btn" class="btn primary">Добавить товар</button>
        </div>

        <div id="product-list" class="product-list">
        </div>

        <div id="product-form-wrapper" class="modal hidden">
            <form id="product-form">
                <h3 id="form-title">Добавить товар</h3>
                <input type="text" name="name" placeholder="Название" required>
                <input type="number" name="price" placeholder="Цена" required>
                <input type="text" name="category" placeholder="Категория" required>
                <input type="file" name="image">
                <textarea name="description" placeholder="Описание товара" rows="4"></textarea>
                <div class="form-actions">
                    <button type="submit" class="btn primary">Сохранить</button>
                    <button type="button" id="cancel-form" class="btn">Отмена</button>
                </div>
            </form>
        </div>
    </section>
    <!-- Заказы -->
    <section id="admin-orders" class="admin-section page-section hidden">
        <div class="admin-header">
            <h2>Заказы</h2>
        </div>

        <div id="order-list" class="order-list">
            <!-- Список заказов будет загружаться сюда -->
        </div>

        <template id="order-template">
            <div class="order">
                <div>Заказ № <span class="order-id-number"></span></div>
                <div>Пользователь: <span class="order-user-id"></span></div>
                <div>Дата: <span class="order-date"></span></div>
                <div>
                    Статус:
                    <select class="order-status-select">
                        <option value="поступил">Поступил</option>
                        <option value="в обработке">В обработке</option>
                        <option value="в сборке">В сборке</option>
                        <option value="отправлен">Отправлен</option>
                        <option value="выдан">Выдан</option>
                        <option value="отменён">Отменён</option>
                    </select>
                </div>
                <div class="order-items"></div>
            </div>
        </template>
    </section>
    <!-- Блог -->
    <section id="blog-section" class="admin-section page-section hidden">
        <h2>Блог</h2>
        <button id="add-blog-btn" class="btn">Добавить пост</button>
        <div id="blog-list" class="admin-list"></div>
        <div id="blog-form-wrapper" class="modal hidden">
            <form id="blog-form" enctype="multipart/form-data">
                <h3 id="blog-form-title">Добавить пост</h3>
                <input type="text" name="title" placeholder="Заголовок" required>
                <textarea name="content" placeholder="Содержание" required></textarea>
                <input type="file" name="image">
                <input type="hidden" name="id">
                <button type="submit" class="btn">Сохранить</button>
                <button type="button" id="cancel-blog-form" class="btn danger">Отмена</button>
            </form>
        </div>
    </section>
    <!-- Пользователи -->
    <div id="users-section" class="admin-section page-section hidden">
        <h2>Управление пользователями</h2>
        <div id="user-list"></div>
    </div>
</div>

<!-- Скрипты -->
<script src="js/router.js"></script>
<script src="js/animations.js"></script>
<script src="js/admin.js"></script>
<script src="js/burger.js"></script>
</body>
</html>
