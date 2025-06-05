function debounce(func, delay) {
    let timeout;
    return function () {
        clearTimeout(timeout);
        timeout = setTimeout(func, delay);
    };
}
loadCatalogProducts();
loadFavorites();
loadCart();
loadOrders();

document.getElementById('search-title').addEventListener('input', debounce(loadCatalogProducts, 400));
document.getElementById('search-category').addEventListener('input', debounce(loadCatalogProducts, 400));
document.getElementById('search-min-price').addEventListener('input', debounce(loadCatalogProducts, 400));
document.getElementById('search-max-price').addEventListener('input', debounce(loadCatalogProducts, 400));

function loadCatalogProducts() {
    const title = document.getElementById('search-title').value.trim();
    const category = document.getElementById('search-category').value.trim();
    const minPrice = document.getElementById('search-min-price').value.trim();
    const maxPrice = document.getElementById('search-max-price').value.trim();

    const params = new URLSearchParams({ title, category, min_price: minPrice, max_price: maxPrice });

    const container = document.getElementById('product-container');
    const noProducts = document.querySelector('.no-products');

    container.classList.add('loading');
    fetch(`scripts/get-catalog-products.php?${params.toString()}`)
        .then(res => res.json())
        .then(products => {
            container.innerHTML = '';
            container.classList.remove('loading');

            if (products.length === 0) {
                noProducts.classList.remove('hidden');
                return;
            }

            noProducts.classList.add('hidden');
            products.forEach(product => {
                const card = document.createElement('div');
                card.className = 'product-card';
                card.innerHTML = `
                    <img src="img/products/${product.image}" alt="${product.title}">
                    <h3>${product.title}</h3>
                    <p class="price">${product.price} ₽</p>
                    <button class="btn small" onclick="showProductModal(${product.id})">Подробнее</button>
                `;
                container.appendChild(card);
            });
        })
        .catch(err => {
            console.error('Ошибка при загрузке товаров:', err);
            container.classList.remove('loading');
        });
}

const modal = document.getElementById('product-modal');
const closeModalBtn = document.getElementById('product-modal-close');

function showProductModal(productId) {
    fetch(`scripts/get-product.php?id=${productId}`)
        .then(res => res.json())
        .then(product => {
            document.getElementById('modal-product-image').src = product.image ? `img/products/${product.image}` : 'img/products/placeholder.png';
            document.getElementById('modal-product-title').textContent = product.title;
            document.getElementById('modal-product-category').textContent = product.category;
            document.getElementById('modal-product-description').textContent = product.description;
            document.getElementById('modal-product-price').textContent = product.price + ' ₽';

            modal.dataset.productId = productId;

            updateFavoriteBtn(productId);
            loadComments(productId);

            modal.classList.remove('hidden');
        })
        .catch(err => {
            console.error('Ошибка загрузки товара', err);
            alert('Ошибка загрузки товара');
        });
}

closeModalBtn.addEventListener('click', () => {
    modal.classList.add('hidden');
});

window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.classList.add('hidden');
    }
});

function loadComments(productId) {
    fetch(`scripts/get-comments.php?product_id=${productId}`)
        .then(res => res.json())
        .then(comments => {
            const container = document.getElementById('modal-comments-container');
            container.innerHTML = '';

            if (comments.length === 0) {
                container.innerHTML = '<p>Комментариев пока нет.</p>';
                return;
            }

            comments.forEach(comment => {
                const div = document.createElement('div');
                div.classList.add('comment');
                div.innerHTML = `
                    <p><strong>Пользователь #${comment.user_id}:</strong> ${comment.comment}</p>
                    <small>${new Date(comment.created_at).toLocaleString()}</small>
                `;
                container.appendChild(div);
            });
        })
        .catch(e => {
            console.error('Ошибка при загрузке комментариев:', e);
        });
}

document.getElementById('add-comment-form').addEventListener('submit', e => {
    e.preventDefault();

    const productId = modal.dataset.productId;
    const commentText = e.target.comment.value.trim();
    if (!commentText) return alert('Введите комментарий.');

    fetch('scripts/add-comment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, comment: commentText }),
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                e.target.reset();
                loadComments(productId);
            } else {
                alert('Ошибка при добавлении комментария');
            }
        })
        .catch(() => alert('Ошибка при отправке комментария'));
});


document.getElementById('add-to-cart-btn').addEventListener('click', () => {
    const productId = modal.dataset.productId;
    fetch('scripts/add-to-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Товар добавлен в корзину');
            } else {
                alert(data.message || 'Ошибка');
            }
        })
        .catch(() => alert('Ошибка при добавлении в корзину'));
});

document.getElementById('toggle-favorite-btn').addEventListener('click', () => {
    const productId = modal.dataset.productId;
    fetch('scripts/toggle-favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateFavoriteBtn(productId);
            } else {
                alert(data.message || 'Ошибка');
            }
        })
        .catch(() => alert('Ошибка при обновлении избранного'));
});

function updateFavoriteBtn(productId) {
    fetch(`scripts/is-favorite.php?product_id=${productId}`)
        .then(res => res.json())
        .then(data => {
            const btn = document.getElementById('toggle-favorite-btn');
            if (data.favorited) {
                btn.classList.add('active');
                btn.textContent = 'В избранном';
            } else {
                btn.classList.remove('active');
                btn.textContent = 'В избранное';
            }
        });
}

function loadFavorites() {
    const container = document.getElementById('favorites-container');
    const noFavorites = document.getElementById('no-favorites');

    container.classList.add('loading');

    fetch('scripts/get-favorites.php')
        .then(res => res.json())
        .then(products => {
            container.innerHTML = '';
            container.classList.remove('loading');

            if (products.length === 0) {
                noFavorites.classList.remove('hidden');
                return;
            }

            noFavorites.classList.add('hidden');

            products.forEach(product => {
                const card = document.createElement('div');
                card.className = 'product-card'; // используем тот же класс для карточек, чтобы стили были единые
                card.innerHTML = `
                    <img src="img/products/${product.image}" alt="${product.title}">
                    <h3>${product.title}</h3>
                    <p class="price">${product.price} ₽</p>
                    <button class="btn small" onclick="showProductModal(${product.id})">Подробнее</button>
                    <button class="btn danger small" onclick="removeFromFavorites(${product.id})">Удалить</button>
                `;
                container.appendChild(card);
            });
        })
        .catch(err => {
            console.error('Ошибка при загрузке избранного:', err);
            container.classList.remove('loading');
        });
}

function removeFromFavorites(productId) {
    fetch('scripts/remove-favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId }),
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadFavorites();
            } else {
                alert(data.message || 'Ошибка при удалении из избранного');
            }
        })
        .catch(() => alert('Ошибка при удалении из избранного'));
}

function loadCart() {
    const container = document.getElementById('cart-container');
    const noItems = document.getElementById('no-cart-items');
    const checkoutBtn = document.getElementById('checkout-btn');

    container.innerHTML = '';
    container.classList.add('loading');

    fetch('scripts/get-cart.php')
        .then(res => {
            if (!res.ok) throw new Error('Unauthorized or error');
            return res.json();
        })
        .then(items => {
            container.classList.remove('loading');
            if (items.length === 0) {
                noItems.classList.remove('hidden');
                checkoutBtn.disabled = true;
                return;
            }
            noItems.classList.add('hidden');
            checkoutBtn.disabled = false;

            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'cart-item';
                div.dataset.cartId = item.cart_id;
                div.innerHTML = `
                    <img src="img/products/${item.image}" alt="${item.title}" class="cart-item-img" />
                    <div class="cart-item-info">
                      <h4>${item.title}</h4>
                      <p>Цена: ${item.price} ₽</p>
                      <div class="cart-quantity-control">
                        <button class="qty-btn minus-btn">−</button>
                        <input type="number" class="qty-input" min="1" value="${item.quantity}" />
                        <button class="qty-btn plus-btn">+</button>
                      </div>
                      <button class="btn small remove-cart-btn">Удалить</button>
                    </div>
                `;
                container.appendChild(div);
            });

            attachCartEvents();
        })
        .catch(err => {
            console.error('Ошибка загрузки корзины:', err);
            container.classList.remove('loading');
        });
}

function attachCartEvents() {
    const container = document.getElementById('cart-container');

    container.querySelectorAll('.minus-btn').forEach(btn => {
        btn.onclick = () => {
            const input = btn.nextElementSibling;
            if (input.value > 1) {
                input.value = +input.value - 1;
                updateCartQuantity(input);
            }
        };
    });

    container.querySelectorAll('.plus-btn').forEach(btn => {
        btn.onclick = () => {
            const input = btn.previousElementSibling;
            input.value = +input.value + 1;
            updateCartQuantity(input);
        };
    });

    container.querySelectorAll('.qty-input').forEach(input => {
        input.onchange = () => updateCartQuantity(input);
    });

    container.querySelectorAll('.remove-cart-btn').forEach(btn => {
        btn.onclick = () => {
            const cartItem = btn.closest('.cart-item');
            removeCartItem(cartItem.dataset.cartId);
        };
    });
}

function updateCartQuantity(input) {
    const cartItem = input.closest('.cart-item');
    const cartId = cartItem.dataset.cartId;
    const quantity = parseInt(input.value);
    if (quantity < 1) {
        input.value = 1;
        return;
    }

    fetch('scripts/update-cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ cart_id: cartId, quantity })
    })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Ошибка обновления количества');
            } else {
                loadCart();
            }
        })
        .catch(() => alert('Ошибка при обновлении корзины'));
}

function removeCartItem(cartId) {
    fetch('scripts/remove-from-cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ cart_id: cartId })
    })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Ошибка удаления товара из корзины');
            } else {
                loadCart();
            }
        })
        .catch(() => alert('Ошибка при удалении из корзины'));
}

document.getElementById('checkout-btn').addEventListener('click', () => {
    fetch('scripts/get-cart-items.php')
        .then(res => res.json())
        .then(cartItems => {
            if (cartItems.length === 0) {
                alert('Корзина пуста');
                return;
            }

            fetch('scripts/create-order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ items: cartItems })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Заказ оформлен успешно!');
                        loadCartItems(); // функция обновления корзины, которую нужно реализовать
                    } else {
                        alert('Ошибка при оформлении заказа: ' + (data.message || ''));
                    }
                })
                .catch(() => alert('Ошибка при отправке заказа'));
        })
        .catch(() => alert('Ошибка загрузки корзины'));
});

function loadOrders() {
    const container = document.getElementById('orders-container');
    const noOrders = document.getElementById('no-orders');

    container.innerHTML = '';
    fetch('scripts/get-orders.php')
        .then(res => res.json())
        .then(orders => {
            if (orders.length === 0) {
                noOrders.classList.remove('hidden');
                return;
            }
            noOrders.classList.add('hidden');

            orders.forEach(order => {
                const orderDiv = document.createElement('div');
                orderDiv.className = 'order-card';

                const itemsHtml = order.items.map(item => `
                    <div class="order-item">
                        <img src="img/products/${item.image || 'placeholder.png'}" alt="${item.title}" class="order-item-img">
                        <div class="order-item-info">
                            <h4>${item.title}</h4>
                            <p>Количество: ${item.quantity}</p>
                            <p>Цена за шт.: ${item.price} ₽</p>
                        </div>
                    </div>
                `).join('');

                orderDiv.innerHTML = `
                    <div class="order-header">
                        <span>Заказ #${order.id}</span>
                        <span>Статус: <strong>${order.status}</strong></span>
                        <span>Дата: ${new Date(order.created_at).toLocaleString()}</span>
                    </div>
                    <div class="order-items">${itemsHtml}</div>
                `;

                container.appendChild(orderDiv);
            });
        })
        .catch(() => {
            noOrders.textContent = 'Ошибка при загрузке заказов.';
            noOrders.classList.remove('hidden');
        });
}

let blogSlideIndex = 0;
let blogSlides = [];

function renderBlogSlider() {
    fetch('scripts/get-blog-posts.php')
        .then(res => res.json())
        .then(posts => {
            blogSlides = posts;
            const slider = document.getElementById('blog-slider');
            const dots = document.getElementById('blog-dots');
            slider.innerHTML = '';
            dots.innerHTML = '';

            posts.forEach((post, index) => {
                const slide = document.createElement('div');
                slide.classList.add('blog-slide');
                slide.innerHTML = `
          <img src="img/blog/${post.image}" alt="">
          <div class="blog-slide-title">${post.title}</div>
        `;
                slide.addEventListener('click', () => openBlogModal(post));
                slider.appendChild(slide);

                const dot = document.createElement('span');
                dot.classList.add('blog-dot');
                if (index === 0) dot.classList.add('active');
                dot.addEventListener('click', () => showBlogSlide(index));
                dots.appendChild(dot);
            });

            showBlogSlide(0);
        });
}

function showBlogSlide(index) {
    const slider = document.getElementById('blog-slider');
    const dots = document.querySelectorAll('.blog-dot');
    if (index >= blogSlides.length) blogSlideIndex = 0;
    else if (index < 0) blogSlideIndex = blogSlides.length - 1;
    else blogSlideIndex = index;

    slider.style.transform = `translateX(-${blogSlideIndex * 100}%)`;
    dots.forEach(dot => dot.classList.remove('active'));
    dots[blogSlideIndex].classList.add('active');
}

function changeBlogSlide(n) {
    showBlogSlide(blogSlideIndex + n);
}

function openBlogModal(post) {
    document.getElementById('blog-modal-title').innerText = post.title;
    document.getElementById('blog-modal-image').src = `img/blog/${post.image}`;
    document.getElementById('blog-modal-text').innerText = post.content;
    document.getElementById('blog-modal-date').innerText = new Date(post.created_at).toLocaleDateString();
    document.getElementById('blog-modal').style.display = 'block';
}

document.getElementById('blog-modal-close').addEventListener('click', () => {
    document.getElementById('blog-modal').style.display = 'none';
});

window.addEventListener('click', (e) => {
    if (e.target === document.getElementById('blog-modal')) {
        document.getElementById('blog-modal').style.display = 'none';
    }
});

renderBlogSlider();

function renderBlogList() {
    fetch('scripts/get-blog-list.php')
        .then(res => res.json())
        .then(posts => {
            const listContainer = document.getElementById('blog-list');
            listContainer.innerHTML = '';

            posts.forEach(post => {
                const excerpt = post.content.length > 120
                    ? post.content.slice(0, 120) + '...'
                    : post.content;

                const card = document.createElement('div');
                card.className = 'blog-card';
                card.innerHTML = `
          <img src="img/blog/${post.image}" alt="">
          <div class="blog-card-content">
            <div class="blog-card-title">${post.title}</div>
            <div class="blog-card-excerpt">${excerpt}</div>
            <div class="blog-card-date">${new Date(post.created_at).toLocaleDateString()}</div>
          </div>
        `;
                card.addEventListener('click', () => openBlogModal(post));
                listContainer.appendChild(card);
            });
        });
}

renderBlogList();
