<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#061810">
    <meta name="description" content="AltaEsencia cosmetics catalog">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('catalog/cosmetics/catalog.css') }}">
    <title>AltaEsencia Cosmetics Catalog</title>
</head>
<body>
    <div class="catalog-shell">
        <header class="catalog-header">
            <div class="brand-mark" aria-hidden="true">AE</div>
            <div>
                <p class="eyebrow">AltaEsencia</p>
                <h1>Cosmetics Catalog</h1>
            </div>
        </header>

        <main id="catalog" aria-live="polite" aria-busy="true">
            <section class="catalog-state" role="status">
                <span class="loader" aria-hidden="true"></span>
                <p>Loading cosmetics…</p>
            </section>
        </main>
    </div>

    <template id="product-template">
        <article class="product-card">
            <div class="product-image-wrap">
                <img class="product-image" alt="" loading="lazy">
                <div class="product-fallback" aria-hidden="true">
                    <span class="fallback-mark">AE</span>
                </div>
                <span class="promotion-badge" hidden>Promotion</span>
            </div>
            <div class="product-content">
                <p class="product-category">Cosmetics</p>
                <h2 class="product-name"></h2>
                <p class="product-description"></p>
                <div class="product-meta">
                    <span class="product-rating"></span>
                    <span class="product-id"></span>
                </div>
                <div class="product-pricing">
                    <strong class="product-price"></strong>
                    <del class="product-original-price" hidden></del>
                    <span class="product-discount" hidden></span>
                </div>
            </div>
        </article>
    </template>

    <script type="module" src="{{ asset('catalog/cosmetics/catalog.js') }}"></script>
</body>
</html>
