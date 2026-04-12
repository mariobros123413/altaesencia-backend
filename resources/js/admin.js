import './bootstrap';

const app = document.querySelector('#admin-app');

if (app) {
    const state = {
        currentSection: 'dashboard',
        categories: [],
        users: [],
        products: [],
        movements: [],
        sales: [],
        selectedSale: null,
    };

    const sections = Array.from(document.querySelectorAll('[data-section]'));
    const navButtons = Array.from(document.querySelectorAll('[data-nav-target]'));
    const sectionHeading = document.querySelector('#section-heading');
    const sectionSubheading = document.querySelector('#section-subheading');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    const endpoints = {
        dashboard: app.dataset.dashboardUrl,
        categories: app.dataset.categoriesUrl,
        users: app.dataset.usersUrl,
        products: app.dataset.productsUrl,
        movements: app.dataset.movementsUrl,
        sales: app.dataset.salesUrl,
    };

    const sectionMeta = {
        dashboard: {
            title: 'Dashboard',
            subtitle: 'Revisa métricas clave, últimas ventas y alertas de stock bajo.',
        },
        products: {
            title: 'Productos',
            subtitle: 'Administra catálogo, precios, stock mínimo y múltiples imágenes.',
        },
        categories: {
            title: 'Categorías',
            subtitle: 'Organiza el catálogo y define la clasificación que usa el sistema.',
        },
        users: {
            title: 'Usuarios',
            subtitle: 'Gestiona clientes y usuarios administrativos con acceso al panel.',
        },
        inventory: {
            title: 'Inventario',
            subtitle: 'Registra movimientos y controla el stock disponible de cada producto.',
        },
        sales: {
            title: 'Ventas',
            subtitle: 'Crea ventas, revisa detalles, agrega notas y administra estados de pago.',
        },
    };

    const refs = {
        dashboardMetrics: document.querySelector('#dashboard-metrics'),
        dashboardSales: document.querySelector('#dashboard-sales'),
        dashboardLowStock: document.querySelector('#dashboard-low-stock'),
        categoriesTableBody: document.querySelector('#categories-table-body'),
        categoryForm: document.querySelector('#category-form'),
        categoryFormTitle: document.querySelector('#category-form-title'),
        categoryFormReset: document.querySelector('#category-form-reset'),
        usersTableBody: document.querySelector('#users-table-body'),
        userForm: document.querySelector('#user-form'),
        userFormTitle: document.querySelector('#user-form-title'),
        userFormReset: document.querySelector('#user-form-reset'),
        productsTableBody: document.querySelector('#products-table-body'),
        productForm: document.querySelector('#product-form'),
        productFormTitle: document.querySelector('#product-form-title'),
        productFormReset: document.querySelector('#product-form-reset'),
        productCategorySelect: document.querySelector('#product-category-select'),
        movementForm: document.querySelector('#movement-form'),
        movementProductSelect: document.querySelector('#movement-product-select'),
        movementUserSelect: document.querySelector('#movement-user-select'),
        movementsTableBody: document.querySelector('#movements-table-body'),
        saleForm: document.querySelector('#sale-form'),
        saleItems: document.querySelector('#sale-items'),
        saleItemAdd: document.querySelector('#sale-item-add'),
        saleCustomerSelect: document.querySelector('#sale-customer-select'),
        saleSellerSelect: document.querySelector('#sale-seller-select'),
        salesTableBody: document.querySelector('#sales-table-body'),
        saleDetailDialog: document.querySelector('#sale-detail-dialog'),
        saleDetailTitle: document.querySelector('#sale-detail-title'),
        saleDetailMeta: document.querySelector('#sale-detail-meta'),
        saleDetailBody: document.querySelector('#sale-detail-body'),
        saleDetailClose: document.querySelector('#sale-detail-close'),
        saleMarkPaid: document.querySelector('#sale-mark-paid'),
        saleMarkPending: document.querySelector('#sale-mark-pending'),
        saleNoteForm: document.querySelector('#sale-note-form'),
        saleCancelForm: document.querySelector('#sale-cancel-form'),
    };

    const money = new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
        minimumFractionDigits: 2,
    });

    const dateTime = new Intl.DateTimeFormat('es-BO', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type} stagger-in`;
        toast.textContent = message;
        document.body.appendChild(toast);
        window.setTimeout(() => toast.remove(), 3600);
    }

    async function fetchJson(url, options = {}) {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                ...options.headers,
            },
            credentials: 'same-origin',
            ...options,
        });

        const rawText = await response.text();
        let payload = null;

        try {
            payload = rawText ? JSON.parse(rawText) : null;
        } catch {
            payload = rawText;
        }

        if (!response.ok) {
            const errors = payload?.errors ? Object.values(payload.errors).flat().join(' ') : null;
            throw new Error(errors || payload?.message || 'No se pudo completar la operación.');
        }

        return payload;
    }

    function getCollection(payload) {
        return Array.isArray(payload?.data) ? payload.data : Array.isArray(payload) ? payload : [];
    }

    function fillSelect(select, items, mapLabel, placeholder = null) {
        if (!select) {
            return;
        }

        const initial = placeholder ? `<option value="">${placeholder}</option>` : '';
        select.innerHTML = initial + items.map((item) => `<option value="${item.id}">${mapLabel(item)}</option>`).join('');
    }

    function badgeForState(value, variants = {}) {
        const normalized = `${value ?? ''}`.toLowerCase();
        const type = variants[normalized] ?? 'neutral';
        return `<span class="badge badge-${type}">${value ?? 'N/D'}</span>`;
    }

    function setSectionUI(target) {
        const meta = sectionMeta[target];
        state.currentSection = target;
        sectionHeading.textContent = meta.title;
        sectionSubheading.textContent = meta.subtitle;

        navButtons.forEach((button) => {
            button.classList.toggle('is-active', button.dataset.navTarget === target);
        });

        sections.forEach((section) => {
            section.classList.toggle('hidden', section.dataset.section !== target);
        });
    }

    function resetCategoryForm() {
        refs.categoryForm.reset();
        refs.categoryForm.querySelector('[name="id"]').value = '';
        refs.categoryFormTitle.textContent = 'Nueva categoría';
        refs.categoryForm.querySelector('[name="estado"]').value = 'activo';
    }

    function resetUserForm() {
        refs.userForm.reset();
        refs.userForm.querySelector('[name="id"]').value = '';
        refs.userFormTitle.textContent = 'Nuevo usuario';
        refs.userForm.querySelector('[name="estado"]').value = 'activo';
        refs.userForm.querySelector('[name="user_type"]').value = 'cliente';
    }

    function resetProductForm() {
        refs.productForm.reset();
        refs.productForm.querySelector('[name="id"]').value = '';
        refs.productFormTitle.textContent = 'Nuevo producto';
        refs.productForm.querySelector('[name="estado"]').value = 'activo';
        refs.productForm.querySelector('[name="rating"]').value = '5';
        refs.productForm.querySelector('[name="stock"]').value = '0';
        refs.productForm.querySelector('[name="minimum_stock"]').value = '0';
        refs.productForm.querySelector('[name="discount_percentage"]').value = '0';
    }

    function createSaleItemRow(data = {}) {
        const row = document.createElement('div');
        row.className = 'sale-item-row rounded-[1.4rem] border border-stone-200 bg-white/70 p-4';
        row.innerHTML = `
            <div class="grid gap-3 md:grid-cols-[1.7fr_0.7fr_0.8fr_0.8fr_auto]">
                <div>
                    <label class="mb-2 block text-sm font-semibold">Producto</label>
                    <select class="admin-select" name="product_id" required></select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Cantidad</label>
                    <input class="admin-input" name="quantity" type="number" min="1" value="${data.quantity ?? 1}" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Precio</label>
                    <input class="admin-input" name="unit_price" type="number" min="0" step="0.01" value="${data.unit_price ?? ''}" placeholder="Auto">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold">Desc.</label>
                    <input class="admin-input" name="discount_amount" type="number" min="0" step="0.01" value="${data.discount_amount ?? 0}">
                </div>
                <div class="flex items-end">
                    <button class="btn btn-danger w-full px-3 py-3 text-sm" data-sale-item-remove type="button">Quitar</button>
                </div>
            </div>
        `;

        refs.saleItems.appendChild(row);
        const select = row.querySelector('select[name="product_id"]');
        fillSelect(select, state.products.filter((product) => product.estado === 'activo'), (product) => `${product.name} (${product.stock} stock)`);
        if (data.product_id) {
            select.value = data.product_id;
        }
    }

    function syncSaleItemSelects() {
        Array.from(refs.saleItems.querySelectorAll('select[name="product_id"]')).forEach((select) => {
            const selected = select.value;
            fillSelect(select, state.products.filter((product) => product.estado === 'activo'), (product) => `${product.name} (${product.stock} stock)`);
            if (selected) {
                select.value = selected;
            }
        });
    }

    function resetSaleForm() {
        refs.saleForm.reset();
        refs.saleItems.innerHTML = '';
        createSaleItemRow();
    }

    function serializeCategoryForm() {
        const formData = new FormData(refs.categoryForm);
        return {
            name: formData.get('name'),
            slug: formData.get('slug') || null,
            legacy_key: formData.get('legacy_key') || null,
            description: formData.get('description') || null,
            estado: formData.get('estado') || 'activo',
        };
    }

    function serializeUserForm() {
        const formData = new FormData(refs.userForm);
        const payload = {
            name: formData.get('name'),
            email: formData.get('email'),
            phone: formData.get('phone') || null,
            user_type: formData.get('user_type'),
            document_number: formData.get('document_number') || null,
            address: formData.get('address') || null,
            estado: formData.get('estado') || 'activo',
        };

        if (formData.get('password')) {
            payload.password = formData.get('password');
        }

        return payload;
    }

    function serializeProductForm() {
        const formData = new FormData(refs.productForm);
        const imageUrls = `${formData.get('image_urls') ?? ''}`
            .split('\n')
            .map((item) => item.trim())
            .filter(Boolean);

        return {
            category_id: formData.get('category_id'),
            sku: formData.get('sku'),
            name: formData.get('name'),
            description: formData.get('description') || null,
            price: Number(formData.get('price') || 0),
            original_price: formData.get('original_price') ? Number(formData.get('original_price')) : null,
            rating: Number(formData.get('rating') || 5),
            stock: Number(formData.get('stock') || 0),
            minimum_stock: Number(formData.get('minimum_stock') || 0),
            discount_percentage: Number(formData.get('discount_percentage') || 0),
            is_promotional: formData.get('is_promotional') === '1',
            estado: formData.get('estado') || 'activo',
            image_url: imageUrls[0] || null,
            images: imageUrls.length
                ? imageUrls.map((imageUrl, index) => ({
                    image_url: imageUrl,
                    is_primary: index === 0,
                    sort_order: index + 1,
                    estado: 'activo',
                }))
                : null,
        };
    }

    function serializeMovementForm() {
        const formData = new FormData(refs.movementForm);
        return {
            product_id: formData.get('product_id'),
            user_id: formData.get('user_id') || null,
            movement_type: formData.get('movement_type'),
            quantity: Number(formData.get('quantity')),
            notes: formData.get('notes') || null,
            estado: formData.get('estado') || 'activo',
        };
    }

    function serializeSaleForm() {
        const formData = new FormData(refs.saleForm);
        const items = Array.from(refs.saleItems.children).map((row) => {
            const productId = row.querySelector('select[name="product_id"]').value;
            const quantity = row.querySelector('input[name="quantity"]').value;
            const unitPrice = row.querySelector('input[name="unit_price"]').value;
            const discountAmount = row.querySelector('input[name="discount_amount"]').value;

            return {
                product_id: productId,
                quantity: Number(quantity),
                ...(unitPrice ? { unit_price: Number(unitPrice) } : {}),
                discount_amount: Number(discountAmount || 0),
            };
        });

        const noteText = `${formData.get('sale_note') ?? ''}`.trim();
        const sellerId = formData.get('seller_id') || null;

        return {
            customer_id: formData.get('customer_id') || null,
            seller_id: sellerId,
            payment_method: formData.get('payment_method'),
            tax_total: Number(formData.get('tax_total') || 0),
            extra_discount_total: Number(formData.get('extra_discount_total') || 0),
            items,
            ...(noteText ? {
                notes: [
                    {
                        user_id: sellerId,
                        note_type: 'interna',
                        note: noteText,
                    },
                ],
            } : {}),
        };
    }

    function setCategoryForm(category) {
        refs.categoryFormTitle.textContent = 'Editar categoría';
        refs.categoryForm.querySelector('[name="id"]').value = category.id;
        refs.categoryForm.querySelector('[name="name"]').value = category.name ?? '';
        refs.categoryForm.querySelector('[name="slug"]').value = category.slug ?? '';
        refs.categoryForm.querySelector('[name="legacy_key"]').value = category.legacy_key ?? '';
        refs.categoryForm.querySelector('[name="description"]').value = category.description ?? '';
        refs.categoryForm.querySelector('[name="estado"]').value = category.estado ?? 'activo';
    }

    function setUserForm(user) {
        refs.userFormTitle.textContent = 'Editar usuario';
        refs.userForm.querySelector('[name="id"]').value = user.id;
        refs.userForm.querySelector('[name="name"]').value = user.name ?? '';
        refs.userForm.querySelector('[name="email"]').value = user.email ?? '';
        refs.userForm.querySelector('[name="phone"]').value = user.phone ?? '';
        refs.userForm.querySelector('[name="document_number"]').value = user.document_number ?? '';
        refs.userForm.querySelector('[name="address"]').value = user.address ?? '';
        refs.userForm.querySelector('[name="user_type"]').value = user.user_type ?? 'cliente';
        refs.userForm.querySelector('[name="estado"]').value = user.estado ?? 'activo';
        refs.userForm.querySelector('[name="password"]').value = '';
    }

    function setProductForm(product) {
        refs.productFormTitle.textContent = 'Editar producto';
        refs.productForm.querySelector('[name="id"]').value = product.id;
        refs.productForm.querySelector('[name="name"]').value = product.name ?? '';
        refs.productForm.querySelector('[name="sku"]').value = product.sku ?? '';
        refs.productForm.querySelector('[name="description"]').value = product.description ?? '';
        refs.productForm.querySelector('[name="category_id"]').value = product.category_id ?? '';
        refs.productForm.querySelector('[name="estado"]').value = product.estado ?? 'activo';
        refs.productForm.querySelector('[name="price"]').value = product.price ?? 0;
        refs.productForm.querySelector('[name="original_price"]').value = product.original_price ?? '';
        refs.productForm.querySelector('[name="rating"]').value = product.rating ?? 5;
        refs.productForm.querySelector('[name="stock"]').value = product.stock ?? 0;
        refs.productForm.querySelector('[name="minimum_stock"]').value = product.minimum_stock ?? 0;
        refs.productForm.querySelector('[name="discount_percentage"]').value = product.discount_percentage ?? 0;
        refs.productForm.querySelector('[name="is_promotional"]').checked = Boolean(product.is_promotional);
        const orderedImages = (product.images ?? []).slice().sort((a, b) => a.sort_order - b.sort_order);
        refs.productForm.querySelector('[name="image_urls"]').value = orderedImages.map((image) => image.image_url).join('\n') || product.image_url || '';
    }

    async function loadDashboard() {
        const payload = await fetchJson(endpoints.dashboard);
        const summary = payload.summary ?? {};

        refs.dashboardMetrics.innerHTML = [
            ['Productos', summary.products_total ?? 0, 'Catálogo total'],
            ['Stock Bajo', summary.products_low_stock ?? 0, 'Productos con alerta'],
            ['Ventas Hoy', summary.sales_today ?? 0, 'Transacciones del día'],
            ['Ingresos Hoy', money.format(summary.sales_today_amount ?? 0), 'Total vendido hoy'],
        ].map(([label, value, caption]) => `
            <article class="metric-card rounded-[1.6rem] p-5">
                <p class="panel-title">${label}</p>
                <h3 class="mt-4 text-3xl font-black text-stone-900">${value}</h3>
                <p class="mt-2 text-sm text-stone-600">${caption}</p>
            </article>
        `).join('');

        refs.dashboardSales.innerHTML = getCollection(payload.latest_sales).map((sale) => `
            <article class="section-card rounded-[1.4rem] p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-stone-900">${sale.sale_number}</p>
                        <p class="mt-1 text-sm text-stone-600">${sale.customer?.name ?? 'Sin cliente'} • ${dateTime.format(new Date(sale.sale_date))}</p>
                    </div>
                    ${badgeForState(sale.payment_status, { pagado: 'success', pendiente: 'warning', anulado: 'danger' })}
                </div>
                <p class="mt-3 text-lg font-black text-stone-900">${money.format(sale.total)}</p>
            </article>
        `).join('') || '<p class="text-sm text-stone-500">No hay ventas recientes.</p>';

        refs.dashboardLowStock.innerHTML = getCollection(payload.low_stock_products).map((product) => `
            <article class="section-card rounded-[1.4rem] p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-stone-900">${product.name}</p>
                        <p class="mt-1 text-sm text-stone-600">${product.sku} • ${product.category}</p>
                    </div>
                    ${badgeForState(product.stock_status, { agotado: 'danger', bajo: 'warning', disponible: 'success' })}
                </div>
                <p class="mt-3 text-sm text-stone-700">Stock actual: <strong>${product.stock}</strong> / mínimo: ${product.minimum_stock}</p>
            </article>
        `).join('') || '<p class="text-sm text-stone-500">No hay alertas de stock.</p>';
    }

    async function loadCategories() {
        const payload = await fetchJson(`${endpoints.categories}?per_page=100`);
        state.categories = getCollection(payload);
        refs.categoriesTableBody.innerHTML = state.categories.map((category) => `
            <tr>
                <td>
                    <p class="font-semibold text-stone-900">${category.name}</p>
                    <p class="mt-1 text-sm text-stone-500">${category.description ?? 'Sin descripción'}</p>
                </td>
                <td>${category.legacy_key}</td>
                <td>${badgeForState(category.estado, { activo: 'success', inactivo: 'neutral' })}</td>
                <td class="space-x-2">
                    <button class="btn btn-secondary px-3 py-2 text-xs" data-edit-category="${category.id}" type="button">Editar</button>
                    <button class="btn btn-danger px-3 py-2 text-xs" data-delete-category="${category.id}" type="button">Desactivar</button>
                </td>
            </tr>
        `).join('') || '<tr><td colspan="4" class="text-sm text-stone-500">Sin categorías registradas.</td></tr>';

        fillSelect(refs.productCategorySelect, state.categories, (item) => `${item.name} (${item.legacy_key})`);
    }

    async function loadUsers() {
        const payload = await fetchJson(`${endpoints.users}?per_page=100`);
        state.users = getCollection(payload);
        refs.usersTableBody.innerHTML = state.users.map((user) => `
            <tr>
                <td>
                    <p class="font-semibold text-stone-900">${user.name}</p>
                    <p class="mt-1 text-sm text-stone-500">${user.email}</p>
                </td>
                <td>${badgeForState(user.user_type, { administrativo: 'warning', cliente: 'neutral' })}</td>
                <td>${badgeForState(user.estado, { activo: 'success', inactivo: 'neutral' })}</td>
                <td class="space-x-2">
                    <button class="btn btn-secondary px-3 py-2 text-xs" data-edit-user="${user.id}" type="button">Editar</button>
                    <button class="btn btn-danger px-3 py-2 text-xs" data-delete-user="${user.id}" type="button">Desactivar</button>
                </td>
            </tr>
        `).join('') || '<tr><td colspan="4" class="text-sm text-stone-500">Sin usuarios registrados.</td></tr>';

        fillSelect(refs.movementUserSelect, state.users.filter((user) => user.user_type === 'administrativo'), (user) => user.name, 'Sin asignar');
        fillSelect(refs.saleCustomerSelect, state.users.filter((user) => user.user_type === 'cliente'), (user) => `${user.name} (${user.email})`, 'Venta sin cliente');
        fillSelect(refs.saleSellerSelect, state.users.filter((user) => user.user_type === 'administrativo'), (user) => user.name, 'Sin vendedor');
    }

    async function loadProducts() {
        const payload = await fetchJson(`${endpoints.products}?per_page=100`);
        state.products = getCollection(payload);
        refs.productsTableBody.innerHTML = state.products.map((product) => `
            <tr>
                <td>
                    <div class="flex items-start gap-3">
                        <img class="h-14 w-14 rounded-2xl object-cover" src="${product.image_url}" alt="${product.name}">
                        <div>
                            <p class="font-semibold text-stone-900">${product.name}</p>
                            <p class="mt-1 text-sm text-stone-500">${product.sku} • ${product.category}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <p class="font-semibold">${money.format(product.price)}</p>
                    <p class="mt-1 text-sm text-stone-500">${product.is_promotional ? `Promo ${product.discount_percentage}%` : 'Sin promo'}</p>
                </td>
                <td>
                    <p class="font-semibold">${product.stock}</p>
                    <p class="mt-1 text-sm text-stone-500">Mínimo ${product.minimum_stock}</p>
                </td>
                <td>${badgeForState(product.stock_status, { disponible: 'success', bajo: 'warning', agotado: 'danger' })}</td>
                <td class="space-x-2">
                    <button class="btn btn-secondary px-3 py-2 text-xs" data-edit-product="${product.id}" type="button">Editar</button>
                    <button class="btn btn-danger px-3 py-2 text-xs" data-delete-product="${product.id}" type="button">Desactivar</button>
                </td>
            </tr>
        `).join('') || '<tr><td colspan="5" class="text-sm text-stone-500">Sin productos registrados.</td></tr>';

        fillSelect(refs.movementProductSelect, state.products.filter((product) => product.estado === 'activo'), (product) => `${product.name} (${product.sku})`);
        syncSaleItemSelects();
    }

    async function loadMovements() {
        const payload = await fetchJson(`${endpoints.movements}?per_page=100`);
        state.movements = getCollection(payload);
        refs.movementsTableBody.innerHTML = state.movements.map((movement) => `
            <tr>
                <td>${movement.created_at ? dateTime.format(new Date(movement.created_at)) : 'N/D'}</td>
                <td>
                    <p class="font-semibold text-stone-900">${movement.product?.name ?? 'Producto eliminado'}</p>
                    <p class="mt-1 text-sm text-stone-500">${movement.product?.sku ?? ''}</p>
                </td>
                <td>${badgeForState(movement.movement_type, { entrada: 'success', salida: 'danger', ajuste: 'warning', venta: 'warning', devolucion: 'success' })}</td>
                <td class="font-semibold">${movement.quantity}</td>
                <td>${movement.user?.name ?? 'Sin asignar'}</td>
            </tr>
        `).join('') || '<tr><td colspan="5" class="text-sm text-stone-500">Sin movimientos registrados.</td></tr>';
    }

    async function loadSales() {
        const payload = await fetchJson(`${endpoints.sales}?per_page=100`);
        state.sales = getCollection(payload);
        refs.salesTableBody.innerHTML = state.sales.map((sale) => `
            <tr>
                <td>
                    <p class="font-semibold text-stone-900">${sale.sale_number}</p>
                    <p class="mt-1 text-sm text-stone-500">${sale.sale_date ? dateTime.format(new Date(sale.sale_date)) : 'Sin fecha'}</p>
                </td>
                <td>${sale.customer?.name ?? 'Sin cliente'}</td>
                <td class="font-semibold">${money.format(sale.total)}</td>
                <td>${badgeForState(sale.payment_status, { pagado: 'success', pendiente: 'warning', anulado: 'danger' })}</td>
                <td class="space-x-2">
                    <button class="btn btn-secondary px-3 py-2 text-xs" data-view-sale="${sale.id}" type="button">Ver</button>
                </td>
            </tr>
        `).join('') || '<tr><td colspan="5" class="text-sm text-stone-500">Sin ventas registradas.</td></tr>';
    }

    async function loadSection(target) {
        switch (target) {
            case 'dashboard':
                await loadDashboard();
                break;
            case 'categories':
                await loadCategories();
                break;
            case 'users':
                await loadUsers();
                break;
            case 'products':
                await Promise.all([loadCategories(), loadProducts()]);
                break;
            case 'inventory':
                await Promise.all([loadUsers(), loadProducts(), loadMovements()]);
                break;
            case 'sales':
                await Promise.all([loadUsers(), loadProducts(), loadSales()]);
                break;
            default:
                break;
        }
    }

    async function switchSection(target) {
        setSectionUI(target);
        await loadSection(target);
    }

    async function saveCategory(event) {
        event.preventDefault();
        const id = refs.categoryForm.querySelector('[name="id"]').value;
        const url = id ? `${endpoints.categories}/${id}` : endpoints.categories;
        const method = id ? 'PUT' : 'POST';

        await fetchJson(url, { method, body: JSON.stringify(serializeCategoryForm()) });
        showToast(id ? 'Categoría actualizada.' : 'Categoría creada.', 'success');
        resetCategoryForm();
        await Promise.all([loadCategories(), loadDashboard()]);
    }

    async function saveUser(event) {
        event.preventDefault();
        const id = refs.userForm.querySelector('[name="id"]').value;
        const url = id ? `${endpoints.users}/${id}` : endpoints.users;
        const method = id ? 'PUT' : 'POST';

        await fetchJson(url, { method, body: JSON.stringify(serializeUserForm()) });
        showToast(id ? 'Usuario actualizado.' : 'Usuario creado.', 'success');
        resetUserForm();
        await Promise.all([loadUsers(), loadDashboard()]);
    }

    async function saveProduct(event) {
        event.preventDefault();
        const id = refs.productForm.querySelector('[name="id"]').value;
        const url = id ? `${endpoints.products}/${id}` : endpoints.products;
        const method = id ? 'PUT' : 'POST';

        await fetchJson(url, { method, body: JSON.stringify(serializeProductForm()) });
        showToast(id ? 'Producto actualizado.' : 'Producto creado.', 'success');
        resetProductForm();
        await Promise.all([loadProducts(), loadDashboard(), loadMovements()]);
    }

    async function saveMovement(event) {
        event.preventDefault();
        await fetchJson(endpoints.movements, {
            method: 'POST',
            body: JSON.stringify(serializeMovementForm()),
        });

        showToast('Movimiento registrado.', 'success');
        refs.movementForm.reset();
        await Promise.all([loadMovements(), loadProducts(), loadDashboard()]);
    }

    async function saveSale(event) {
        event.preventDefault();
        await fetchJson(endpoints.sales, {
            method: 'POST',
            body: JSON.stringify(serializeSaleForm()),
        });

        showToast('Venta creada correctamente.', 'success');
        resetSaleForm();
        await Promise.all([loadSales(), loadProducts(), loadMovements(), loadDashboard()]);
    }

    async function deactivateResource(url, label) {
        if (!window.confirm(`¿Deseas desactivar este ${label}?`)) {
            return;
        }

        await fetchJson(url, { method: 'DELETE' });
        showToast(`${label} desactivado correctamente.`, 'success');
    }

    async function openSaleDetail(saleId) {
        const sale = await fetchJson(`${endpoints.sales}/${saleId}`);
        state.selectedSale = sale;

        refs.saleDetailTitle.textContent = sale.sale_number;
        refs.saleDetailMeta.textContent = `${sale.customer?.name ?? 'Sin cliente'} • ${sale.payment_method} • ${sale.sale_date ? dateTime.format(new Date(sale.sale_date)) : ''}`;
        refs.saleDetailBody.innerHTML = `
            <section class="rounded-[1.5rem] border border-stone-200 bg-white/70 p-5">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <p class="panel-title">Total</p>
                        <p class="mt-2 text-2xl font-black">${money.format(sale.total)}</p>
                    </div>
                    <div>
                        <p class="panel-title">Pago</p>
                        <div class="mt-2">${badgeForState(sale.payment_status, { pagado: 'success', pendiente: 'warning', anulado: 'danger' })}</div>
                    </div>
                    <div>
                        <p class="panel-title">Estado</p>
                        <div class="mt-2">${badgeForState(sale.estado, { activo: 'success', anulado: 'danger' })}</div>
                    </div>
                </div>
            </section>
            <section class="rounded-[1.5rem] border border-stone-200 bg-white/70 p-5">
                <p class="panel-title">Detalle</p>
                <div class="mt-4 space-y-3">
                    ${(sale.details ?? []).map((detail) => `
                        <article class="rounded-2xl border border-stone-200 bg-white px-4 py-3">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-stone-900">${detail.product_name}</p>
                                    <p class="mt-1 text-sm text-stone-500">Cantidad ${detail.quantity} • ${money.format(detail.unit_price)} c/u</p>
                                </div>
                                <p class="font-black text-stone-900">${money.format(detail.line_total)}</p>
                            </div>
                        </article>
                    `).join('') || '<p class="text-sm text-stone-500">Sin detalle.</p>'}
                </div>
            </section>
            <section class="rounded-[1.5rem] border border-stone-200 bg-white/70 p-5">
                <p class="panel-title">Notas</p>
                <div class="mt-4 space-y-3">
                    ${(sale.notes ?? []).map((note) => `
                        <article class="rounded-2xl border border-stone-200 bg-white px-4 py-3">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-stone-900">${note.user?.name ?? 'Sistema'}</p>
                                    <p class="mt-1 text-sm text-stone-500">${note.note_type} • ${note.created_at ? dateTime.format(new Date(note.created_at)) : ''}</p>
                                </div>
                                ${badgeForState(note.estado, { activo: 'success', inactivo: 'neutral' })}
                            </div>
                            <p class="mt-3 text-sm leading-6 text-stone-700">${note.note}</p>
                        </article>
                    `).join('') || '<p class="text-sm text-stone-500">Sin notas.</p>'}
                </div>
            </section>
        `;

        refs.saleNoteForm.querySelector('[name="sale_id"]').value = sale.id;
        refs.saleCancelForm.querySelector('[name="sale_id"]').value = sale.id;
        refs.saleDetailDialog.showModal();
    }

    async function updateSalePaymentStatus(status) {
        if (!state.selectedSale) {
            return;
        }

        await fetchJson(`${endpoints.sales}/${state.selectedSale.id}/payment-status`, {
            method: 'PATCH',
            body: JSON.stringify({ payment_status: status }),
        });

        showToast('Estado de pago actualizado.', 'success');
        await Promise.all([openSaleDetail(state.selectedSale.id), loadSales(), loadDashboard()]);
    }

    async function saveSaleNote(event) {
        event.preventDefault();
        const formData = new FormData(refs.saleNoteForm);
        const saleId = formData.get('sale_id');
        const note = `${formData.get('note') ?? ''}`.trim();

        if (!saleId || !note) {
            return;
        }

        await fetchJson(`${endpoints.sales}/${saleId}/notes`, {
            method: 'POST',
            body: JSON.stringify({
                note,
                note_type: 'interna',
            }),
        });

        refs.saleNoteForm.reset();
        refs.saleNoteForm.querySelector('[name="sale_id"]').value = saleId;
        showToast('Nota agregada.', 'success');
        await openSaleDetail(saleId);
    }

    async function cancelSale(event) {
        event.preventDefault();

        if (!state.selectedSale || !window.confirm('¿Deseas anular esta venta y devolver el stock?')) {
            return;
        }

        const formData = new FormData(refs.saleCancelForm);

        await fetchJson(`${endpoints.sales}/${state.selectedSale.id}/cancel`, {
            method: 'POST',
            body: JSON.stringify({
                note: formData.get('note') || null,
            }),
        });

        showToast('Venta anulada y stock reintegrado.', 'success');
        refs.saleCancelForm.reset();
        refs.saleCancelForm.querySelector('[name="sale_id"]').value = state.selectedSale.id;
        await Promise.all([openSaleDetail(state.selectedSale.id), loadSales(), loadProducts(), loadMovements(), loadDashboard()]);
    }

    function handleTableActions(event) {
        const categoryId = event.target.closest('[data-edit-category]')?.dataset.editCategory;
        if (categoryId) {
            const category = state.categories.find((item) => item.id === categoryId);
            if (category) {
                setCategoryForm(category);
            }
            return;
        }

        const deleteCategoryId = event.target.closest('[data-delete-category]')?.dataset.deleteCategory;
        if (deleteCategoryId) {
            deactivateResource(`${endpoints.categories}/${deleteCategoryId}`, 'categoría')
                .then(() => Promise.all([loadCategories(), loadDashboard()]))
                .catch((error) => showToast(error.message, 'error'));
            return;
        }

        const userId = event.target.closest('[data-edit-user]')?.dataset.editUser;
        if (userId) {
            const user = state.users.find((item) => `${item.id}` === `${userId}`);
            if (user) {
                setUserForm(user);
            }
            return;
        }

        const deleteUserId = event.target.closest('[data-delete-user]')?.dataset.deleteUser;
        if (deleteUserId) {
            deactivateResource(`${endpoints.users}/${deleteUserId}`, 'usuario')
                .then(() => Promise.all([loadUsers(), loadDashboard()]))
                .catch((error) => showToast(error.message, 'error'));
            return;
        }

        const productId = event.target.closest('[data-edit-product]')?.dataset.editProduct;
        if (productId) {
            fetchJson(`${endpoints.products}/${productId}`)
                .then((product) => setProductForm(product))
                .catch((error) => showToast(error.message, 'error'));
            return;
        }

        const deleteProductId = event.target.closest('[data-delete-product]')?.dataset.deleteProduct;
        if (deleteProductId) {
            deactivateResource(`${endpoints.products}/${deleteProductId}`, 'producto')
                .then(() => Promise.all([loadProducts(), loadDashboard()]))
                .catch((error) => showToast(error.message, 'error'));
            return;
        }

        const saleId = event.target.closest('[data-view-sale]')?.dataset.viewSale;
        if (saleId) {
            openSaleDetail(saleId).catch((error) => showToast(error.message, 'error'));
        }
    }

    function bindEvents() {
        navButtons.forEach((button) => {
            button.addEventListener('click', () => {
                switchSection(button.dataset.navTarget).catch((error) => showToast(error.message, 'error'));
            });
        });

        document.querySelectorAll('[data-refresh-section]').forEach((button) => {
            button.addEventListener('click', () => {
                loadSection(button.dataset.refreshSection).catch((error) => showToast(error.message, 'error'));
            });
        });

        document.querySelectorAll('[data-nav-jump]').forEach((button) => {
            button.addEventListener('click', () => {
                switchSection(button.dataset.navJump).catch((error) => showToast(error.message, 'error'));
            });
        });

        refs.categoryForm.addEventListener('submit', (event) => saveCategory(event).catch((error) => showToast(error.message, 'error')));
        refs.userForm.addEventListener('submit', (event) => saveUser(event).catch((error) => showToast(error.message, 'error')));
        refs.productForm.addEventListener('submit', (event) => saveProduct(event).catch((error) => showToast(error.message, 'error')));
        refs.movementForm.addEventListener('submit', (event) => saveMovement(event).catch((error) => showToast(error.message, 'error')));
        refs.saleForm.addEventListener('submit', (event) => saveSale(event).catch((error) => showToast(error.message, 'error')));

        refs.categoryFormReset.addEventListener('click', resetCategoryForm);
        refs.userFormReset.addEventListener('click', resetUserForm);
        refs.productFormReset.addEventListener('click', resetProductForm);
        refs.saleItemAdd.addEventListener('click', () => createSaleItemRow());

        refs.saleItems.addEventListener('click', (event) => {
            if (event.target.closest('[data-sale-item-remove]')) {
                event.target.closest('.sale-item-row')?.remove();
                if (!refs.saleItems.children.length) {
                    createSaleItemRow();
                }
            }
        });

        refs.saleDetailClose.addEventListener('click', () => refs.saleDetailDialog.close());
        refs.saleMarkPaid.addEventListener('click', () => updateSalePaymentStatus('pagado').catch((error) => showToast(error.message, 'error')));
        refs.saleMarkPending.addEventListener('click', () => updateSalePaymentStatus('pendiente').catch((error) => showToast(error.message, 'error')));
        refs.saleNoteForm.addEventListener('submit', (event) => saveSaleNote(event).catch((error) => showToast(error.message, 'error')));
        refs.saleCancelForm.addEventListener('submit', (event) => cancelSale(event).catch((error) => showToast(error.message, 'error')));

        document.addEventListener('click', handleTableActions);
    }

    async function init() {
        bindEvents();
        resetCategoryForm();
        resetUserForm();
        resetProductForm();
        resetSaleForm();

        try {
            await switchSection('dashboard');
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    init();
}
