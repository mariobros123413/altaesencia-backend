const endpoint = '/storefront/categories/cosmetics/products';
const catalog = document.querySelector('#catalog');
const template = document.querySelector('#product-template');
const number = new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

function formatPrice(value) {
    const price = Number(value);

    return Number.isFinite(price) ? number.format(price) : 'Price unavailable';
}

function productImage(product) {
    const images = Array.isArray(product.image_urls) ? product.image_urls : [];

    return [...images, product.image_url].find((url) => typeof url === 'string' && url.trim()) || null;
}

function setText(element, value) {
    element.textContent = value ?? '';
}

function renderProduct(product) {
    const card = template.content.cloneNode(true);
    const image = card.querySelector('.product-image');
    const fallback = card.querySelector('.product-fallback');
    const badge = card.querySelector('.promotion-badge');
    const originalPrice = card.querySelector('.product-original-price');
    const discount = card.querySelector('.product-discount');
    const imageUrl = productImage(product);
    const rating = Number(product.rating);
    const original = Number(product.original_price);
    const price = Number(product.price);

    setText(card.querySelector('.product-category'), product.category);
    setText(card.querySelector('.product-name'), product.name || 'Unnamed product');
    setText(card.querySelector('.product-description'), product.description || 'Product details are not available.');
    setText(card.querySelector('.product-id'), `ID ${product.id ?? '—'}`);
    setText(card.querySelector('.product-rating'), Number.isFinite(rating) ? `★ ${rating.toFixed(1)}` : 'Rating unavailable');
    setText(card.querySelector('.product-price'), formatPrice(product.price));

    if (Number.isFinite(original) && (!Number.isFinite(price) || original > price)) {
        setText(originalPrice, formatPrice(product.original_price));
        originalPrice.hidden = false;
    }

    if (product.is_promotional) {
        badge.hidden = false;
        const percentage = Number(product.discount_percentage);

        if (Number.isFinite(percentage) && percentage > 0) {
            setText(discount, `${percentage}% off`);
            discount.hidden = false;
        }
    }

    if (imageUrl) {
        image.src = imageUrl;
        image.alt = product.name || 'Cosmetics product';
        image.addEventListener('load', () => {
            image.hidden = false;
            fallback.hidden = true;
        });
        image.addEventListener('error', () => {
            image.hidden = true;
            fallback.hidden = false;
        });
    } else {
        image.hidden = true;
    }

    return card;
}

function renderState(message, type = 'status') {
    catalog.replaceChildren();
    catalog.setAttribute('aria-busy', 'false');
    const state = document.createElement('section');
    state.className = `catalog-state catalog-state-${type}`;
    state.setAttribute('role', type === 'error' ? 'alert' : 'status');
    const text = document.createElement('p');
    text.textContent = message;
    state.append(text);
    catalog.append(state);
}

async function loadCatalog() {
    try {
        const response = await fetch(endpoint, { headers: { Accept: 'application/json' } });

        if (!response.ok) {
            throw new Error(`Catalog request failed with status ${response.status}`);
        }

        const products = await response.json();

        if (!Array.isArray(products)) {
            throw new Error('Catalog response is invalid');
        }

        const cosmetics = products.filter((product) => product && product.category === 'cosmetics');

        if (cosmetics.length === 0) {
            renderState('No cosmetics are available at the moment.');
            return;
        }

        catalog.replaceChildren();
        catalog.setAttribute('aria-busy', 'false');
        const grid = document.createElement('section');
        grid.className = 'product-grid';
        cosmetics.forEach((product) => grid.append(renderProduct(product)));
        catalog.append(grid);
    } catch (error) {
        console.error(error);
        renderState('The cosmetics catalog could not be loaded. Please try again later.', 'error');
    }
}

loadCatalog();
