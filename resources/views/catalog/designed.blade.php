<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#061810">
    <meta name="description" content="Catálogo premium de AltaEsencia">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('catalog/dynamic/catalog.css') }}">
    <title>{{ $title }} · AltaEsencia</title>
</head>
<body>
    <div class="catalog-shell">
        <div class="toolbar" aria-label="Herramientas del catálogo">
            <button id="topBtn" type="button">↑ Inicio</button>
            <button id="printBtn" type="button">Guardar en PDF</button>
        </div>
        <main id="catalog" aria-live="polite"></main>
    </div>
    <script>
        window.CATALOG_DATA = {
            title: @json($title),
            fields: @json($fields),
            products: @json($catalogProducts)
        };
    </script>
    <script type="module" src="{{ asset('catalog/dynamic/catalog.js') }}"></script>
</body>
</html>
