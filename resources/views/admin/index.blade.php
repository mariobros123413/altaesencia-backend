<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel Administrativo | AltaEsencia</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @else
        <style>
            body{font-family:Segoe UI,sans-serif;background:#f5efe5;color:#2b241c;margin:0}
            .fallback-wrap{max-width:900px;margin:48px auto;padding:24px}
            .fallback-card{background:#fffaf2;border:1px solid #e7d9c2;border-radius:24px;padding:28px;box-shadow:0 18px 45px rgba(72,52,28,.12)}
            .fallback-warn{margin:16px 0;padding:14px 16px;border-radius:16px;background:#fff3cd;border:1px solid #f2d48c;color:#7a5314;line-height:1.6}
            code{background:#f2eadf;padding:2px 6px;border-radius:6px}
            ul{line-height:1.8}
        </style>
    @endif
</head>
<body class="antialiased">
    @unless (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        <main class="fallback-wrap">
            <section class="fallback-card">
                <h1>Panel administrativo disponible, pero faltan assets compilados</h1>
                <div class="fallback-warn">
                    El backend ya está listo, pero Laravel no encontró <code>public/build/manifest.json</code>.
                    Necesitas compilar Vite para cargar el CSS y el JavaScript del panel.
                </div>
                <ul>
                    <li>Instala dependencias: <code>npm install</code></li>
                    <li>Modo desarrollo: <code>npm run dev</code></li>
                    <li>O build final: <code>npm run build</code></li>
                </ul>
                <p>Cuando exista <code>public/build/manifest.json</code>, esta vista cargará automáticamente el panel administrativo completo.</p>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="margin-top:18px;padding:12px 18px;border:0;border-radius:999px;background:#a55a1a;color:#fff;font-weight:700;cursor:pointer;">Cerrar sesión</button>
                </form>
            </section>
        </main>
    @else
    <div
        id="admin-app"
        class="admin-shell"
        data-dashboard-url="{{ route('admin.api.dashboard') }}"
        data-categories-url="{{ route('admin.api.categories.index') }}"
        data-users-url="{{ route('admin.api.users.index') }}"
        data-products-url="{{ route('admin.api.products.index') }}"
        data-movements-url="{{ url('/admin/api/inventory-movements') }}"
        data-sales-url="{{ route('admin.api.sales.index') }}"
    >
        <div class="grid min-h-screen lg:grid-cols-[300px_1fr]">
            <aside class="relative overflow-hidden bg-[#201710] px-5 py-6 text-stone-100 lg:px-6 lg:py-8">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(239,177,103,0.26),transparent_26%),radial-gradient(circle_at_bottom_right,rgba(149,80,25,0.24),transparent_22%)]"></div>
                <div class="relative flex h-full flex-col">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-200">AltaEsencia</p>
                        <h1 class="mt-3 text-3xl font-black">Centro Administrativo</h1>
                        <p class="mt-3 text-sm leading-6 text-stone-300">
                            Opera ventas, catálogo, stock y usuarios desde una sola consola.
                        </p>
                    </div>

                    <nav class="mt-8 space-y-3">
                        <button class="nav-chip is-active flex w-full items-center justify-between rounded-2xl px-4 py-3 text-left font-semibold" data-nav-target="dashboard" type="button">
                            <span>Dashboard</span>
                            <span class="text-xs uppercase tracking-[0.25em]">01</span>
                        </button>
                        <button class="nav-chip flex w-full items-center justify-between rounded-2xl px-4 py-3 text-left font-semibold" data-nav-target="products" type="button">
                            <span>Productos</span>
                            <span class="text-xs uppercase tracking-[0.25em]">02</span>
                        </button>
                        <button class="nav-chip flex w-full items-center justify-between rounded-2xl px-4 py-3 text-left font-semibold" data-nav-target="categories" type="button">
                            <span>Categorías</span>
                            <span class="text-xs uppercase tracking-[0.25em]">03</span>
                        </button>
                        <button class="nav-chip flex w-full items-center justify-between rounded-2xl px-4 py-3 text-left font-semibold" data-nav-target="users" type="button">
                            <span>Usuarios</span>
                            <span class="text-xs uppercase tracking-[0.25em]">04</span>
                        </button>
                        <button class="nav-chip flex w-full items-center justify-between rounded-2xl px-4 py-3 text-left font-semibold" data-nav-target="inventory" type="button">
                            <span>Inventario</span>
                            <span class="text-xs uppercase tracking-[0.25em]">05</span>
                        </button>
                        <button class="nav-chip flex w-full items-center justify-between rounded-2xl px-4 py-3 text-left font-semibold" data-nav-target="sales" type="button">
                            <span>Ventas</span>
                            <span class="text-xs uppercase tracking-[0.25em]">06</span>
                        </button>
                    </nav>

                    <div class="mt-auto rounded-[1.75rem] border border-white/10 bg-white/8 p-5 backdrop-blur-sm">
                        <p class="text-xs uppercase tracking-[0.25em] text-stone-300">Sesión activa</p>
                        <p class="mt-3 text-lg font-black">{{ auth()->user()->name }}</p>
                        <p class="mt-1 text-sm text-stone-300">{{ auth()->user()->email }}</p>
                        <form class="mt-5" action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-secondary w-full bg-white/10 text-stone-100 hover:bg-white/18" type="submit">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <main class="px-4 py-4 lg:px-8 lg:py-6">
                <header class="glass-panel stagger-in rounded-[1.8rem] px-5 py-5 lg:px-7">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="panel-title">Operación diaria</p>
                            <h2 id="section-heading" class="mt-2 text-3xl font-black text-stone-900">Dashboard</h2>
                            <p id="section-subheading" class="mt-2 max-w-3xl text-sm leading-6 text-stone-600">
                                Revisa métricas clave, últimas ventas y alertas de stock bajo.
                            </p>
                        </div>
                        <div class="rounded-3xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
                            <strong>Tip:</strong> los cambios se guardan en tiempo real contra rutas protegidas de administración.
                        </div>
                    </div>
                </header>

                <section data-section="dashboard" class="stagger-in mt-6 space-y-6">
                    <div id="dashboard-metrics" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4"></div>

                    <div class="grid gap-6 xl:grid-cols-2">
                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Ventas recientes</p>
                                    <h3 class="mt-2 text-xl font-black">Últimos movimientos comerciales</h3>
                                </div>
                                <button class="btn btn-secondary text-sm" data-refresh-section="dashboard" type="button">Actualizar</button>
                            </div>
                            <div id="dashboard-sales" class="space-y-3"></div>
                        </article>

                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Stock crítico</p>
                                    <h3 class="mt-2 text-xl font-black">Productos para reabastecer</h3>
                                </div>
                                <button class="btn btn-secondary text-sm" data-nav-jump="inventory" type="button">Ir a inventario</button>
                            </div>
                            <div id="dashboard-low-stock" class="space-y-3"></div>
                        </article>
                    </div>
                </section>

                <section data-section="products" class="mt-6 hidden space-y-6">
                    <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Catálogo</p>
                                    <h3 class="mt-2 text-xl font-black">Productos registrados</h3>
                                </div>
                                <button class="btn btn-secondary text-sm" data-refresh-section="products" type="button">Actualizar</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Stock</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-table-body"></tbody>
                                </table>
                            </div>
                        </article>

                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Editor</p>
                                    <h3 id="product-form-title" class="mt-2 text-xl font-black">Nuevo producto</h3>
                                </div>
                                <button class="btn btn-secondary text-sm" id="product-form-reset" type="button">Limpiar</button>
                            </div>

                            <form id="product-form" class="grid gap-4">
                                <input type="hidden" name="id">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Nombre</label>
                                        <input class="admin-input" name="name" required>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">SKU</label>
                                        <input class="admin-input" name="sku" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Descripción</label>
                                    <textarea class="admin-textarea" name="description"></textarea>
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Categoría</label>
                                        <select class="admin-select" name="category_id" id="product-category-select" required></select>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Estado</label>
                                        <select class="admin-select" name="estado">
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid gap-4 md:grid-cols-3">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Precio</label>
                                        <input class="admin-input" name="price" step="0.01" min="0" type="number" required>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Precio original</label>
                                        <input class="admin-input" name="original_price" step="0.01" min="0" type="number">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Rating</label>
                                        <input class="admin-input" name="rating" step="0.1" min="0" max="5" type="number" value="5">
                                    </div>
                                </div>
                                <div class="grid gap-4 md:grid-cols-3">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Stock</label>
                                        <input class="admin-input" name="stock" type="number" min="0" value="0">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Stock mínimo</label>
                                        <input class="admin-input" name="minimum_stock" type="number" min="0" value="0">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Descuento %</label>
                                        <input class="admin-input bg-stone-100 text-stone-500" name="discount_percentage" type="number" min="0" max="100" value="0" readonly>
                                        <p class="mt-2 text-xs text-stone-500">Se calcula autom&aacute;ticamente seg&uacute;n la diferencia entre precio original y precio.</p>
                                    </div>
                                </div>
                                <label class="flex items-center gap-3 rounded-2xl border border-stone-200 bg-white/70 px-4 py-3 text-sm text-stone-600">
                                    <input class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500" name="is_promotional" type="checkbox" value="1">
                                    Marcar como producto promocional
                                </label>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">URLs de imágenes</label>
                                    <textarea class="admin-textarea" name="image_urls" placeholder="Una URL por línea. La primera será la imagen principal."></textarea>
                                </div>
                                <button class="btn btn-primary w-full" type="submit">Guardar producto</button>
                            </form>
                        </article>
                    </div>
                </section>

                <section data-section="categories" class="mt-6 hidden space-y-6">
                    <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Estructura</p>
                                    <h3 class="mt-2 text-xl font-black">Categorías disponibles</h3>
                                </div>
                                <button class="btn btn-secondary text-sm" data-refresh-section="categories" type="button">Actualizar</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Legacy key</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="categories-table-body"></tbody>
                                </table>
                            </div>
                        </article>

                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Editor</p>
                                    <h3 id="category-form-title" class="mt-2 text-xl font-black">Nueva categoría</h3>
                                </div>
                                <button class="btn btn-secondary text-sm" id="category-form-reset" type="button">Limpiar</button>
                            </div>
                            <form id="category-form" class="grid gap-4">
                                <input type="hidden" name="id">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Nombre</label>
                                    <input class="admin-input" name="name" required>
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Slug</label>
                                        <input class="admin-input" name="slug">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Legacy key</label>
                                        <input class="admin-input" name="legacy_key" placeholder="clothing, perfumes, cosmetics...">
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Descripción</label>
                                    <textarea class="admin-textarea" name="description"></textarea>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Estado</label>
                                    <select class="admin-select" name="estado">
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                                <button class="btn btn-primary w-full" type="submit">Guardar categoría</button>
                            </form>
                        </article>
                    </div>
                </section>

                <section data-section="users" class="mt-6 hidden space-y-6">
                    <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Accesos</p>
                                    <h3 class="mt-2 text-xl font-black">Usuarios administrativos y clientes</h3>
                                </div>
                                <button class="btn btn-secondary text-sm" data-refresh-section="users" type="button">Actualizar</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Tipo</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="users-table-body"></tbody>
                                </table>
                            </div>
                        </article>

                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Editor</p>
                                    <h3 id="user-form-title" class="mt-2 text-xl font-black">Nuevo usuario</h3>
                                </div>
                                <button class="btn btn-secondary text-sm" id="user-form-reset" type="button">Limpiar</button>
                            </div>
                            <form id="user-form" class="grid gap-4">
                                <input type="hidden" name="id">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Nombre</label>
                                        <input class="admin-input" name="name" required>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Correo</label>
                                        <input class="admin-input" name="email" type="email" required>
                                    </div>
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Teléfono</label>
                                        <input class="admin-input" name="phone">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Documento</label>
                                        <input class="admin-input" name="document_number">
                                    </div>
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Tipo</label>
                                        <select class="admin-select" name="user_type" required>
                                            <option value="cliente">Cliente</option>
                                            <option value="administrativo">Administrativo</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Estado</label>
                                        <select class="admin-select" name="estado">
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Dirección</label>
                                    <input class="admin-input" name="address">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Contraseña</label>
                                    <input class="admin-input" name="password" type="password" placeholder="Obligatoria al crear, opcional al editar">
                                </div>
                                <button class="btn btn-primary w-full" type="submit">Guardar usuario</button>
                            </form>
                        </article>
                    </div>
                </section>
                <section data-section="inventory" class="mt-6 hidden space-y-6">
                    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Movimientos</p>
                                    <h3 class="mt-2 text-xl font-black">Registrar entradas, salidas y ajustes</h3>
                                </div>
                            </div>
                            <form id="movement-form" class="grid gap-4">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Producto</label>
                                    <select class="admin-select" name="product_id" id="movement-product-select" required></select>
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Usuario responsable</label>
                                        <select class="admin-select" name="user_id" id="movement-user-select">
                                            <option value="">Sin asignar</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Tipo de movimiento</label>
                                        <select class="admin-select" name="movement_type" required>
                                            <option value="entrada">Entrada</option>
                                            <option value="salida">Salida</option>
                                            <option value="ajuste">Ajuste</option>
                                            <option value="devolucion">Devolución</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Cantidad</label>
                                        <input class="admin-input" name="quantity" type="number" required>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Estado</label>
                                        <select class="admin-select" name="estado">
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Notas</label>
                                    <textarea class="admin-textarea" name="notes"></textarea>
                                </div>
                                <button class="btn btn-primary w-full" type="submit">Registrar movimiento</button>
                            </form>
                        </article>

                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Historial</p>
                                    <h3 class="mt-2 text-xl font-black">Bitácora de inventario</h3>
                                </div>
                                <button class="btn btn-secondary text-sm" data-refresh-section="inventory" type="button">Actualizar</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Producto</th>
                                            <th>Tipo</th>
                                            <th>Cantidad</th>
                                            <th>Responsable</th>
                                        </tr>
                                    </thead>
                                    <tbody id="movements-table-body"></tbody>
                                </table>
                            </div>
                        </article>
                    </div>
                </section>

                <section data-section="sales" class="mt-6 hidden space-y-6">
                    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Registrar venta</p>
                                    <h3 class="mt-2 text-xl font-black">Crear una nueva venta</h3>
                                </div>
                                <button class="btn btn-secondary text-sm" id="sale-item-add" type="button">Agregar ítem</button>
                            </div>
                            <form id="sale-form" class="grid gap-4">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Cliente</label>
                                        <select class="admin-select" name="customer_id" id="sale-customer-select">
                                            <option value="">Venta sin cliente</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Vendedor</label>
                                        <select class="admin-select" name="seller_id" id="sale-seller-select">
                                            <option value="">Sin vendedor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid gap-4 md:grid-cols-3">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Método de pago</label>
                                        <select class="admin-select" name="payment_method">
                                            <option value="efectivo">Efectivo</option>
                                            <option value="tarjeta">Tarjeta</option>
                                            <option value="transferencia">Transferencia</option>
                                            <option value="qr">QR</option>
                                            <option value="mixto">Mixto</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Impuesto total</label>
                                        <input class="admin-input" name="tax_total" min="0" step="0.01" type="number" value="0">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold">Descuento extra</label>
                                        <input class="admin-input" name="extra_discount_total" min="0" step="0.01" type="number" value="0">
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Ítems de la venta</label>
                                    <div id="sale-items" class="space-y-3"></div>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Nota interna</label>
                                    <textarea class="admin-textarea" name="sale_note" placeholder="Opcional"></textarea>
                                </div>
                                <button class="btn btn-primary w-full" type="submit">Crear venta</button>
                            </form>
                        </article>

                        <article class="glass-panel rounded-[1.8rem] p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <p class="panel-title">Ventas registradas</p>
                                    <h3 class="mt-2 text-xl font-black">Historial comercial</h3>
                                </div>
                                <button class="btn btn-secondary text-sm" data-refresh-section="sales" type="button">Actualizar</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Nro</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Pago</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sales-table-body"></tbody>
                                </table>
                            </div>
                        </article>
                    </div>
                </section>
            </main>
        </div>

        <dialog id="sale-detail-dialog" class="sale-detail-panel">
            <div class="grid min-h-screen place-items-end">
                <div class="h-full w-full max-w-2xl overflow-y-auto bg-[#fffaf2] p-6 shadow-2xl lg:p-8">
                    <div class="mb-6 flex items-start justify-between gap-4">
                        <div>
                            <p class="panel-title">Detalle de venta</p>
                            <h3 id="sale-detail-title" class="mt-2 text-2xl font-black text-stone-900">Venta</h3>
                            <p id="sale-detail-meta" class="mt-2 text-sm text-stone-600"></p>
                        </div>
                        <button class="btn btn-secondary" id="sale-detail-close" type="button">Cerrar</button>
                    </div>

                    <div id="sale-detail-body" class="space-y-6"></div>

                    <div class="mt-8 grid gap-4 rounded-[1.6rem] border border-stone-200 bg-white/70 p-5">
                        <div class="grid gap-4 md:grid-cols-2">
                            <button class="btn btn-secondary w-full" id="sale-mark-paid" type="button">Marcar como pagado</button>
                            <button class="btn btn-secondary w-full" id="sale-mark-pending" type="button">Marcar como pendiente</button>
                        </div>
                        <form id="sale-note-form" class="grid gap-3">
                            <input type="hidden" name="sale_id">
                            <label class="text-sm font-semibold text-stone-700">Agregar nota</label>
                            <textarea class="admin-textarea" name="note" required placeholder="Escribe una nota interna sobre esta venta"></textarea>
                            <button class="btn btn-primary w-full" type="submit">Guardar nota</button>
                        </form>
                        <form id="sale-cancel-form" class="grid gap-3">
                            <input type="hidden" name="sale_id">
                            <label class="text-sm font-semibold text-stone-700">Anular venta</label>
                            <textarea class="admin-textarea" name="note" placeholder="Motivo de anulación"></textarea>
                            <button class="btn btn-danger w-full" type="submit">Anular y devolver stock</button>
                        </form>
                    </div>
                </div>
            </div>
        </dialog>
    </div>
    @endunless
</body>
</html>
