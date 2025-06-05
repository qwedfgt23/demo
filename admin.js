document.addEventListener('DOMContentLoaded', () => {
    const addBtn = document.getElementById('add-product-btn');
    const modal = document.getElementById('product-form-wrapper');
    const cancelBtn = document.getElementById('cancel-form');
    const form = document.getElementById('product-form');
    const formTitle = document.getElementById('form-title');
    const productList = document.getElementById('product-list');

    let editProductId = null;

    addBtn.addEventListener('click', () => {
        formTitle.textContent = 'Добавить товар';
        form.reset();
        editProductId = null;
        modal.classList.remove('hidden');
    });

    cancelBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const url = editProductId ? 'scripts/update-product.php' : 'scripts/add-product.php';
        if (editProductId) formData.append('id', editProductId);

        fetch(url, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    modal.classList.add('hidden');
                    loadProducts();
                } else {
                    alert('Ошибка: ' + (data.error || 'Неизвестная ошибка'));
                }
            })
            .catch(err => {
                console.error('Ошибка при сохранении товара:', err);
            });
    });

    function loadProducts() {
        fetch('scripts/get-products.php')
            .then(res => res.json())
            .then(data => {
                productList.innerHTML = '';

                if (!data.length) {
                    productList.innerHTML = '<p>Товары пока не добавлены.</p>';
                    return;
                }

                data.forEach(product => {
                    const div = document.createElement('div');
                    div.className = 'product-item';
                    div.innerHTML = `
                        <div class="product-info">
                            <p><strong>Название:</strong> ${product.title}</p>
                            <p><strong>Цена:</strong> ${product.price}₽</p>
                            <p><strong>Категория:</strong> ${product.category}</p>
                        </div>
                        <div class="product-actions">
                            <button class="btn edit" data-id="${product.id}">Редактировать</button>
                            <button class="btn danger" data-id="${product.id}">Удалить</button>
                            <button class="btn comments" data-id="${product.id}">Комментарии</button>
                        </div>
                        <div class="product-comments hidden" id="comments-block-${product.id}">
                            <h4>Комментарии</h4>
                            <div id="comments-${product.id}" class="comments-container">
                                <p>Загрузка комментариев...</p>
                            </div>
                        </div>
                    `;
                    productList.appendChild(div);
                });

                document.querySelectorAll('.btn.edit').forEach(btn => {
                    btn.addEventListener('click', () => editProduct(btn.dataset.id));
                });

                document.querySelectorAll('.btn.danger').forEach(btn => {
                    btn.addEventListener('click', () => deleteProduct(btn.dataset.id));
                });

                document.querySelectorAll('.btn.comments').forEach(btn => {
                    btn.addEventListener('click', () => toggleComments(btn.dataset.id));
                });
            })
            .catch(err => {
                console.error('Ошибка при загрузке товаров:', err);
            });
    }

    function editProduct(id) {
        fetch('scripts/get-products.php')
            .then(res => res.json())
            .then(products => {
                const product = products.find(p => p.id == id);
                if (!product) return;

                formTitle.textContent = 'Редактировать товар';
                form.name.value = product.title;
                form.price.value = product.price;
                form.category.value = product.category;
                form.description.value = product.description || '';
                editProductId = product.id;
                modal.classList.remove('hidden');
            });
    }

    function deleteProduct(id) {
        if (!confirm('Удалить товар?')) return;

        fetch('scripts/delete-product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) loadProducts();
                else alert('Ошибка удаления: ' + data.error);
            });
    }

    function toggleComments(productId) {
        const block = document.getElementById(`comments-block-${productId}`);
        if (block.classList.contains('hidden')) {
            block.classList.remove('hidden');
            loadComments(productId);
        } else {
            block.classList.add('hidden');
        }
    }

    function loadComments(productId) {
        fetch(`scripts/get-comments.php?product_id=${productId}`)
            .then(response => response.json())
            .then(comments => {
                const container = document.querySelector(`#comments-${productId}`);
                container.innerHTML = '';

                if (!comments.length) {
                    container.innerHTML = '<p class="no-comments">Нет комментариев.</p>';
                    return;
                }

                comments.forEach(comment => {
                    const commentEl = document.createElement('div');
                    commentEl.classList.add('comment');
                    commentEl.innerHTML = `
                        <p><strong>Пользователь #${comment.user_id}:</strong> ${comment.comment}</p>
                        <small>${new Date(comment.created_at).toLocaleString()}</small>
                        <div class="comment-actions">
                            <button class="btn small danger" onclick="deleteComment(${comment.id}, ${productId})">Удалить</button>
                        </div>
                    `;
                    container.appendChild(commentEl);
                });
            })
            .catch(error => console.error('Ошибка при загрузке комментариев:', error));
    }

    window.deleteComment = function(commentId, productId) {
        if (!confirm('Удалить комментарий?')) return;

        fetch('scripts/delete-comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ comment_id: commentId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) loadComments(productId);
                else alert('Ошибка при удалении');
            });
    };

    loadProducts();
});

function loadOrders() {
    fetch('scripts/get-orders-admin.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderOrders(data.orders);
            } else {
                console.error(data.message);
            }
        })
        .catch(err => console.error('Ошибка загрузки заказов:', err));
}

function renderOrders(orders) {
    const container = document.getElementById('order-list');
    const template = document.getElementById('order-template');
    container.innerHTML = '';

    orders.forEach(order => {
        const clone = template.content.cloneNode(true);
        clone.querySelector('.order-id-number').textContent = order.id;
        clone.querySelector('.order-user-id').textContent = order.user_id;
        clone.querySelector('.order-date').textContent = new Date(order.created_at).toLocaleString();

        const statusSelect = clone.querySelector('.order-status-select');
        statusSelect.value = order.status;

        statusSelect.addEventListener('change', () => {
            const newStatus = statusSelect.value;
            fetch('scripts/update-order-status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: order.id, status: newStatus })
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert('Ошибка обновления статуса: ' + data.message);
                        statusSelect.value = order.status; // откат
                    }
                })
                .catch(() => {
                    alert('Ошибка сети при обновлении статуса');
                    statusSelect.value = order.status;
                });
        });

        const itemsContainer = clone.querySelector('.order-items');
        order.items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'order-item';
            div.innerHTML = `<strong>${item.product_name}</strong> — ${item.quantity} × ${item.price} ₽`;
            itemsContainer.appendChild(div);
        });

        container.appendChild(clone);
    });
}

loadOrders();

// Добавим в переключение табов
document.querySelectorAll('.admin-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.admin-section').forEach(section => section.classList.add('hidden'));
        document.getElementById(`${tab.dataset.tab}-section`).classList.remove('hidden');

        if (tab.dataset.tab === 'users') {
            loadUsers();
        }
    });
});

// Загрузка пользователей
function loadUsers() {
    fetch('scripts/get-users.php')
        .then(res => res.json())
        .then(users => {
            const userList = document.getElementById('user-list');
            userList.innerHTML = '';

            if (!users.length) {
                userList.innerHTML = '<p>Нет пользователей.</p>';
                return;
            }

            users.forEach(user => {
                const div = document.createElement('div');
                div.className = 'user-item';
                div.innerHTML = `
  <p><strong>ID:</strong> ${user.id}</p>
  <p><strong>Логин:</strong> ${user.name}</p>
  <p><strong>Email:</strong> ${user.email}</p>
  <p><strong>Дата регистрации:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
  <label><strong>Роль:</strong>
    <select onchange="updateUserRole(${user.id}, this.value)">
      <option value="user" ${user.role === 'user' ? 'selected' : ''}>user</option>
      <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>admin</option>
    </select>
  </label>
  <button class="btn danger" onclick="deleteUser(${user.id})">Удалить</button>
`;

                userList.appendChild(div);
            });
        })
        .catch(err => {
            console.error('Ошибка при загрузке пользователей:', err);
        });
}

window.deleteUser = function(userId) {
    if (!confirm('Удалить пользователя?')) return;

    fetch('scripts/delete-user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: userId })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) loadUsers();
            else alert('Ошибка при удалении: ' + data.error);
        })
        .catch(err => {
            console.error('Ошибка при удалении пользователя:', err);
        });
};

window.updateUserRole = function(userId, role) {
    fetch('scripts/update-user-role.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: userId, role })
    })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert('Ошибка при обновлении роли: ' + data.error);
            }
        })
        .catch(err => console.error('Ошибка при обновлении роли:', err));
};

loadUsers();

    const blogList = document.getElementById('blog-list');
    const blogFormWrapper = document.getElementById('blog-form-wrapper');
    const blogForm = document.getElementById('blog-form');
    const blogFormTitle = document.getElementById('blog-form-title');
    const cancelBlogForm = document.getElementById('cancel-blog-form');
    const addBlogBtn = document.getElementById('add-blog-btn');

    let editBlogId = null;

    addBlogBtn.addEventListener('click', () => {
        blogForm.reset();
        blogFormTitle.textContent = 'Добавить пост';
        blogForm.id.value = '';
        editBlogId = null;
        blogFormWrapper.classList.remove('hidden');
    });

    cancelBlogForm.addEventListener('click', () => {
        blogFormWrapper.classList.add('hidden');
    });

    blogForm.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(blogForm);
        const url = formData.get('id') ? 'scripts/update-blog-post.php' : 'scripts/add-blog-post.php';

        fetch(url, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadBlogPosts();
                    blogFormWrapper.classList.add('hidden');
                } else {
                    alert('Ошибка: ' + data.error);
                }
            });
    });

    function loadBlogPosts() {
        fetch('scripts/get-blog-posts.php')
            .then(res => res.json())
            .then(posts => {
                blogList.innerHTML = '';
                if (!posts.length) {
                    blogList.innerHTML = '<p>Постов пока нет.</p>';
                    return;
                }

                posts.forEach(post => {
                    const div = document.createElement('div');
                    div.className = 'admin-item';
                    div.innerHTML = `
                        <strong>${post.title}</strong>
                        <small>${new Date(post.created_at).toLocaleString()}</small>
                        <div class="admin-actions">
                            <button class="btn small edit" data-id="${post.id}">Редактировать</button>
                            <button class="btn small danger delete" data-id="${post.id}">Удалить</button>
                        </div>
                    `;
                    blogList.appendChild(div);
                });

                blogList.querySelectorAll('.btn.edit').forEach(btn => {
                    btn.addEventListener('click', () => editBlogPost(btn.dataset.id));
                });

                blogList.querySelectorAll('.btn.delete').forEach(btn => {
                    btn.addEventListener('click', () => deleteBlogPost(btn.dataset.id));
                });
            });
    }

    function editBlogPost(id) {
        fetch('scripts/get-blog-posts.php')
            .then(res => res.json())
            .then(posts => {
                const post = posts.find(p => p.id == id);
                if (!post) return;

                blogFormTitle.textContent = 'Редактировать пост';
                blogForm.title.value = post.title;
                blogForm.content.value = post.content || '';
                blogForm.id.value = post.id;
                blogFormWrapper.classList.remove('hidden');
            });
    }

    function deleteBlogPost(id) {
        if (!confirm('Удалить пост?')) return;

        fetch('scripts/delete-blog-post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) loadBlogPosts();
                else alert('Ошибка удаления: ' + data.error);
            });
    }

    loadBlogPosts();
