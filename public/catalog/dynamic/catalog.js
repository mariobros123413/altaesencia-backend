const $ = (selector, root = document) => root.querySelector(selector);
const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]);
const node = (tag, className = '', html = '') => {
  const element = document.createElement(tag);
  if (className) element.className = className;
  element.innerHTML = html;
  return element;
};
const data = window.CATALOG_DATA || { title: 'Catálogo AltaEsencia', fields: [], products: [] };
const fields = new Set(data.fields || []);
const brand = {
  name: 'AltaEsencia', tagline: 'ESTILO Y EXCLUSIVIDAD', subline: 'SELECCIÓN PREMIUM',
  edition: 'Edición clientes', currency: 'Bs', catalogTitle: data.title,
  catalogSubtitle: 'Una selección curada de productos AltaEsencia, presentada con su diseño editorial original.',
  footerNote: 'Información referencial. Consulta disponibilidad y condiciones con AltaEsencia.',
  theme: { ink: '#061810', ink2: '#0d2b21', olive: '#282d22', gold: '#c6a75b', goldSoft: '#e3cf98', cream: '#f4ecd4', paper: '#fbf5e8', line: '#d9c992' }
};
const grouped = new Map();
(data.products || []).forEach((product) => {
  const key = product.category || product.tag || 'Selección';
  if (!grouped.has(key)) grouped.set(key, []);
  grouped.get(key).push(product);
});
function setTheme(theme) {
  Object.entries(theme).forEach(([key, value]) => document.documentElement.style.setProperty(`--${key.replace(/[A-Z]/g, (letter) => `-${letter.toLowerCase()}`)}`, value));
}
function page(type = 'paper-bg') { return node('section', `page ${type}`); }
function numberPage(element, number) { element.append(node('div', 'page-number', String(number).padStart(2, '0'))); }
function brandBar() { return node('header', 'brandbar', `<h1>${esc(brand.name)}</h1><p>${esc(brand.tagline)} · ${esc(brand.subline)}</p>`); }
function coverPage() {
  const result = page('cover page-dark');
  const content = node('div', 'cover-content', `<div class="ae-logo">AE</div><div class="cover-title">${esc(brand.name)}</div><div class="cover-tag">${esc(brand.tagline)}</div><div class="cover-rule"></div><h2>${esc(brand.catalogTitle)}</h2><p class="cover-copy">${esc(brand.catalogSubtitle)}</p>`);
  const chips = node('div', 'chips');
  [...grouped.keys()].slice(0, 4).forEach((category) => chips.append(node('div', 'chip', esc(category))));
  content.append(chips);
  const collage = node('div', 'cover-collage');
  (data.products || []).slice(0, 4).forEach((product) => {
    const item = node('div', 'mini-product', '<span></span>');
    if (product.image) item.insertAdjacentHTML('afterbegin', `<img src="${esc(product.image)}" alt="${esc(product.name)}">`);
    collage.append(item);
  });
  content.append(collage, node('div', 'cover-edition', `${esc(brand.edition)} · Selección de productos`));
  result.append(content);
  return result;
}
function overviewPage() {
  const result = page('overview paper-bg'); result.append(brandBar(), node('div', 'gold-orbit'));
  const content = node('div', 'page-content', `<h2>Una selección para descubrir</h2><p class="overview-desc">${esc(brand.catalogSubtitle)}</p>`);
  const cards = node('div', 'overview-grid');
  [...grouped.entries()].forEach(([category, products]) => cards.append(node('article', 'overview-card', `<h3>${esc(category)}</h3><p>${products.map((product) => esc(product.name)).join(' · ')}</p><small>${products.length} producto${products.length === 1 ? '' : 's'}</small>`)));
  content.append(cards); result.append(content); return result;
}
function dividerPage(category) {
  const result = page('divider page-dark');
  result.append(node('div', 'divider-content', `<div class="kicker">ALTAESENCIA · CATÁLOGO</div><h2>${esc(category)}</h2><div class="divider-rule"></div><p>Selección especial de ${esc(category.toLowerCase())}.</p><div class="chip">${grouped.get(category).length} producto${grouped.get(category).length === 1 ? '' : 's'}</div>`));
  return result;
}
function productImage(product) {
  const wrap = node('div', 'product-image-wrap');
  if (product.image) {
    const img = node('img', 'product-image'); img.src = product.image; img.alt = product.name; img.loading = 'lazy';
    img.addEventListener('error', () => { img.hidden = true; });
    wrap.append(img);
  }
  return wrap;
}
function productPage(category, product) {
  const result = page('product-page paper-bg'); result.dataset.code = product.code || '';
  result.append(brandBar(), node('div', 'gold-orbit'));
  const content = node('div', 'page-content');
  if (fields.has('category')) content.append(node('div', 'product-tag', esc(category)));
  const layout = node('div', 'product-layout');
  const showImage = fields.has('image'); const showPrice = fields.has('price');
  const left = node('div');
  if (showImage) {
    const visual = node('div', 'product-visual-card'); visual.append(productImage(product)); left.append(visual);
  }
  if (showPrice) {
    const currentPrice = Number(product.price || 0);
    const originalPrice = Number(product.originalPrice || 0);
    const discountPercent = Number(product.promotion || 0);
    const showOriginalPrice = fields.has('promotion') && originalPrice > currentPrice;
    const hasPromotion = showOriginalPrice && discountPercent > 0;
    const formatPrice = (value) => value.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    let priceHtml = `<div class="price-label">PRECIO CLIENTE</div>`;
    if (showOriginalPrice) {
      const savings = originalPrice - currentPrice;
      priceHtml += `<div class="price-original"><s>${esc(brand.currency)} ${formatPrice(originalPrice)}</s></div><div class="price-current-row"><div class="price">${esc(brand.currency)} ${formatPrice(currentPrice)}</div>${hasPromotion ? `<span class="price-discount">-${esc(discountPercent)}%</span>` : ''}</div>${hasPromotion ? `<div class="price-savings">Ahorras ${esc(brand.currency)} ${formatPrice(savings)}</div>` : ''}`;
    } else {
      priceHtml += `<div class="price">${esc(brand.currency)} ${formatPrice(currentPrice)}</div>`;
    }
    if (fields.has('sku')) priceHtml += `<div class="price-meta">Código ${esc(product.code)}</div>`;
    left.append(node('div', 'price-card', priceHtml));
  }
  const right = node('div', 'product-copy');
  right.innerHTML = `${fields.has('category') ? `<div class="brand-name">${esc(category)}</div>` : ''}${fields.has('name') ? `<h2 class="product-name">${esc(product.name)}</h2>` : ''}<div class="product-gold-line"></div>${fields.has('description') && product.description ? `<p class="description">${esc(product.description)}</p>` : ''}`;
  const factsList = [];
  if (fields.has('sku') && !showPrice) factsList.push(['Código', product.code]);
  if (fields.has('rating')) factsList.push(['Calificación', `★ ${Number(product.rating || 0).toFixed(1)}`]);
  if (fields.has('promotion') && Number(product.promotion) > 0) factsList.push(['Promoción', `${product.promotion}% de descuento`]);
  if (factsList.length) {
    const facts = node('div', 'facts-card'); facts.append(node('h3', '', 'Detalles del producto'));
    factsList.forEach(([label, value]) => facts.append(node('div', 'fact', `<span class="fact-label">${esc(label)}</span><div class="fact-value">${esc(value)}</div>`)));
    right.append(facts);
  }
  if (!showImage && !showPrice) layout.style.gridTemplateColumns = '1fr';
  layout.append(...(!showImage && !showPrice ? [right] : [left, right])); content.append(layout); result.append(content);
  result.append(node('div', 'product-footer', esc(brand.footerNote)));
  return result;
}
function render() {
  setTheme(brand.theme); document.title = `${brand.catalogTitle} | ${brand.name}`;
  const mount = $('#catalog');
  if (!data.products?.length) { mount.textContent = 'No hay productos seleccionados disponibles.'; return; }
  const pages = [coverPage(), overviewPage()];
  grouped.forEach((products, category) => { pages.push(dividerPage(category)); products.forEach((product) => pages.push(productPage(category, product))); });
  pages.forEach((element, index) => { numberPage(element, index + 1); mount.append(element); });
}
$('#printBtn')?.addEventListener('click', () => window.print());
$('#topBtn')?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
render();
