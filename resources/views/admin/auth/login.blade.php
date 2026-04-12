<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso Administrativo | AltaEsencia</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/admin.css'])
    @else
        <style>
            body{font-family:Segoe UI,sans-serif;background:#f5efe5;color:#2b241c;margin:0}
            .wrap{min-height:100vh;display:grid;place-items:center;padding:24px}
            .card{max-width:480px;width:100%;background:#fffaf2;border:1px solid #e7d9c2;border-radius:24px;padding:28px;box-shadow:0 18px 45px rgba(72,52,28,.12)}
            .warn{margin-bottom:18px;padding:12px 14px;border-radius:16px;background:#fff3cd;border:1px solid #f2d48c;color:#7a5314;font-size:14px;line-height:1.5}
            .field{margin-bottom:14px}
            .field label{display:block;margin-bottom:8px;font-size:14px;font-weight:600}
            .field input{width:100%;padding:12px 14px;border-radius:14px;border:1px solid #d8c7ad;box-sizing:border-box}
            button{width:100%;padding:13px 16px;border:0;border-radius:999px;background:#a55a1a;color:#fff;font-weight:700;cursor:pointer}
        </style>
    @endif
</head>
<body class="antialiased">
    <main class="min-h-screen px-6 py-10 lg:px-10">
        <div class="mx-auto grid min-h-[calc(100vh-5rem)] max-w-6xl items-center gap-8 lg:grid-cols-[1.15fr_0.85fr]">
            <section class="relative overflow-hidden rounded-[2rem] bg-[#201710] p-8 text-stone-100 shadow-2xl lg:p-12">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(240,181,111,0.34),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(166,91,33,0.26),transparent_28%)]"></div>
                <div class="relative space-y-8">
                    <div class="space-y-4">
                        <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-amber-100">
                            AltaEsencia Admin
                        </span>
                        <h1 class="max-w-xl text-4xl font-black leading-tight lg:text-6xl">
                            Gestiona inventario, ventas y clientes desde un solo panel.
                        </h1>
                        <p class="max-w-xl text-base leading-7 text-stone-300 lg:text-lg">
                            Inicia sesión como usuario administrativo para controlar catálogo, stock, ventas y notas comerciales
                            con una experiencia centralizada.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <article class="rounded-3xl border border-white/10 bg-white/8 p-5 backdrop-blur-sm">
                            <p class="text-sm uppercase tracking-[0.2em] text-stone-300">Productos</p>
                            <p class="mt-3 text-2xl font-black text-amber-100">Catálogo vivo</p>
                            <p class="mt-2 text-sm text-stone-300">Crea, edita y organiza productos con múltiples imágenes.</p>
                        </article>
                        <article class="rounded-3xl border border-white/10 bg-white/8 p-5 backdrop-blur-sm">
                            <p class="text-sm uppercase tracking-[0.2em] text-stone-300">Stock</p>
                            <p class="mt-3 text-2xl font-black text-amber-100">Trazabilidad</p>
                            <p class="mt-2 text-sm text-stone-300">Registra entradas, salidas, ajustes y ventas en tiempo real.</p>
                        </article>
                        <article class="rounded-3xl border border-white/10 bg-white/8 p-5 backdrop-blur-sm">
                            <p class="text-sm uppercase tracking-[0.2em] text-stone-300">Ventas</p>
                            <p class="mt-3 text-2xl font-black text-amber-100">Control total</p>
                            <p class="mt-2 text-sm text-stone-300">Asigna ventas a clientes y administra pagos y anulaciones.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="glass-panel rounded-[2rem] p-7 lg:p-9">
                @unless (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
                    <div class="warn">
                        Faltan assets de Vite. Para ver el diseño completo ejecuta <strong>npm install</strong> y luego <strong>npm run build</strong> o <strong>npm run dev</strong>.
                    </div>
                @endunless
                <div class="mb-8">
                    <p class="panel-title">Ingreso Seguro</p>
                    <h2 class="mt-3 text-3xl font-black text-stone-900">Accede al panel administrativo</h2>
                    <p class="mt-2 text-sm leading-6 text-stone-600">
                        Usa una cuenta con perfil <strong>administrativo</strong> para ingresar.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-stone-700" for="email">Correo electrónico</label>
                        <input class="admin-input" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="admin@altaesencia.com" required autofocus>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-stone-700" for="password">Contraseña</label>
                        <input class="admin-input" id="password" name="password" type="password" placeholder="Ingresa tu contraseña" required>
                    </div>

                    <label class="flex items-center gap-3 rounded-2xl border border-stone-200 bg-white/70 px-4 py-3 text-sm text-stone-600">
                        <input class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500" name="remember" type="checkbox" value="1">
                        Mantener sesión iniciada en este equipo
                    </label>

                    <button class="btn btn-primary w-full py-3 text-base" type="submit">
                        Entrar al panel
                    </button>
                </form>

                <div class="mt-8 rounded-3xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
                    <p class="font-semibold">Usuario demo administrativo</p>
                    <p class="mt-1">Correo: <strong>admin@altaesencia.com</strong></p>
                    <p>Contraseña: <strong>password</strong></p>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
