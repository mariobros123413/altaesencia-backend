CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE TABLE IF NOT EXISTS categories (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name varchar(255) NOT NULL,
  slug varchar(255) NOT NULL UNIQUE,
  legacy_key varchar(255) NOT NULL UNIQUE,
  description text,
  estado varchar(30) NOT NULL DEFAULT 'activo',
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS users (
  id bigserial PRIMARY KEY,
  name varchar(255) NOT NULL,
  email varchar(255) NOT NULL UNIQUE,
  phone varchar(30),
  user_type varchar(30) NOT NULL DEFAULT 'cliente' CHECK (user_type IN ('administrativo', 'cliente')),
  document_number varchar(50),
  address varchar(255),
  email_verified_at timestamptz,
  password varchar(255) NOT NULL,
  remember_token varchar(100),
  estado varchar(30) NOT NULL DEFAULT 'activo',
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS products (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  category_id uuid NOT NULL REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  sku varchar(50) NOT NULL UNIQUE,
  name varchar(255) NOT NULL,
  description text,
  price numeric(10, 2) NOT NULL,
  original_price numeric(10, 2),
  category text NOT NULL CHECK (category IN ('clothing', 'perfumes', 'cosmetics')),
  image_url text NOT NULL,
  is_promotional boolean NOT NULL DEFAULT false,
  discount_percentage integer NOT NULL DEFAULT 0,
  rating numeric(3, 1) NOT NULL DEFAULT 5.0,
  stock integer NOT NULL DEFAULT 0,
  minimum_stock integer NOT NULL DEFAULT 0,
  estado varchar(30) NOT NULL DEFAULT 'activo',
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_products_category_estado ON products(category, estado);
CREATE INDEX IF NOT EXISTS idx_products_category_id_estado ON products(category_id, estado);

CREATE TABLE IF NOT EXISTS product_images (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  product_id uuid NOT NULL REFERENCES products(id) ON UPDATE CASCADE ON DELETE CASCADE,
  image_url text NOT NULL,
  image_url_hash varchar(64) NOT NULL,
  is_primary boolean NOT NULL DEFAULT false,
  sort_order integer NOT NULL DEFAULT 1,
  estado varchar(30) NOT NULL DEFAULT 'activo',
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_product_images_product_estado ON product_images(product_id, estado);
CREATE UNIQUE INDEX IF NOT EXISTS uq_product_images_product_url_hash ON product_images(product_id, image_url_hash);

CREATE TABLE IF NOT EXISTS sales (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  sale_number varchar(50) NOT NULL UNIQUE,
  customer_id bigint REFERENCES users(id) ON DELETE SET NULL,
  seller_id bigint REFERENCES users(id) ON DELETE SET NULL,
  sale_date timestamptz NOT NULL DEFAULT now(),
  subtotal numeric(10, 2) NOT NULL DEFAULT 0,
  discount_total numeric(10, 2) NOT NULL DEFAULT 0,
  tax_total numeric(10, 2) NOT NULL DEFAULT 0,
  total numeric(10, 2) NOT NULL DEFAULT 0,
  payment_method varchar(30) NOT NULL DEFAULT 'efectivo' CHECK (payment_method IN ('efectivo', 'tarjeta', 'transferencia', 'qr', 'mixto')),
  payment_status varchar(30) NOT NULL DEFAULT 'pagado' CHECK (payment_status IN ('pendiente', 'pagado', 'anulado')),
  estado varchar(30) NOT NULL DEFAULT 'activo',
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_sales_customer_date ON sales(customer_id, sale_date);
CREATE INDEX IF NOT EXISTS idx_sales_seller_date ON sales(seller_id, sale_date);

CREATE TABLE IF NOT EXISTS sale_details (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  sale_id uuid NOT NULL REFERENCES sales(id) ON UPDATE CASCADE ON DELETE CASCADE,
  product_id uuid REFERENCES products(id) ON UPDATE CASCADE ON DELETE SET NULL,
  product_name varchar(255) NOT NULL,
  quantity integer NOT NULL,
  unit_price numeric(10, 2) NOT NULL,
  discount_amount numeric(10, 2) NOT NULL DEFAULT 0,
  line_total numeric(10, 2) NOT NULL,
  estado varchar(30) NOT NULL DEFAULT 'activo',
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_sale_details_sale_product ON sale_details(sale_id, product_id);

CREATE TABLE IF NOT EXISTS sale_notes (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  sale_id uuid NOT NULL REFERENCES sales(id) ON UPDATE CASCADE ON DELETE CASCADE,
  user_id bigint REFERENCES users(id) ON DELETE SET NULL,
  note_type varchar(30) NOT NULL DEFAULT 'interna' CHECK (note_type IN ('interna', 'cliente')),
  note text NOT NULL,
  estado varchar(30) NOT NULL DEFAULT 'activo',
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS inventory_movements (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  product_id uuid NOT NULL REFERENCES products(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  user_id bigint REFERENCES users(id) ON DELETE SET NULL,
  movement_type varchar(30) NOT NULL CHECK (movement_type IN ('entrada', 'salida', 'ajuste', 'venta', 'devolucion')),
  quantity integer NOT NULL,
  reference_type varchar(50),
  reference_id uuid,
  notes text,
  estado varchar(30) NOT NULL DEFAULT 'activo',
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_inventory_movements_product_type ON inventory_movements(product_id, movement_type);

INSERT INTO categories (name, slug, legacy_key, description, estado)
VALUES
  ('Ropa', 'ropa', 'clothing', 'Prendas premium y accesorios de vestir.', 'activo'),
  ('Perfumes', 'perfumes', 'perfumes', 'Fragancias exclusivas para uso diario y ocasiones especiales.', 'activo'),
  ('Cosmeticos', 'cosmeticos', 'cosmetics', 'Productos de cuidado personal y belleza.', 'activo')
ON CONFLICT (legacy_key) DO NOTHING;

INSERT INTO products (
  name,
  description,
  price,
  original_price,
  category,
  category_id,
  image_url,
  is_promotional,
  discount_percentage,
  rating,
  stock,
  minimum_stock,
  sku,
  estado
)
SELECT
  seed.name,
  seed.description,
  seed.price,
  seed.original_price,
  seed.category,
  c.id,
  seed.image_url,
  seed.is_promotional,
  seed.discount_percentage,
  seed.rating,
  seed.stock,
  seed.minimum_stock,
  seed.sku,
  'activo'
FROM (
  VALUES
    ('Perfume AltaEsencia Negro', 'Fragancia exclusiva con notas de ambar y vainilla', 189.99::numeric(10,2), 249.99::numeric(10,2), 'perfumes', 'https://images.pexels.com/photos/3962286/pexels-photo-3962286.jpeg', true, 24, 4.9::numeric(3,1), 20, 5, 'PERF-0001'),
    ('Chaqueta Premium Negra', 'Chaqueta de lujo en tela 100% algodon', 399.99::numeric(10,2), 499.99::numeric(10,2), 'clothing', 'https://images.pexels.com/photos/3622622/pexels-photo-3622622.jpeg', true, 20, 4.8::numeric(3,1), 12, 3, 'ROPA-0001'),
    ('Serum Facial Dorado', 'Serum antienvejecimiento con oro coloidal', 129.99::numeric(10,2), 179.99::numeric(10,2), 'cosmetics', 'https://images.pexels.com/photos/3762285/pexels-photo-3762285.jpeg', false, 0, 4.7::numeric(3,1), 18, 4, 'COSM-0001'),
    ('Tom Ford Noir', 'Perfume de lujo Tom Ford Negro', 249.99::numeric(10,2), 349.99::numeric(10,2), 'perfumes', 'https://images.pexels.com/photos/3962286/pexels-photo-3962286.jpeg', false, 0, 5.0::numeric(3,1), 10, 2, 'PERF-0002'),
    ('Pantalon Premium Gris', 'Pantalon de vestir en lana fina italiana', 279.99::numeric(10,2), 349.99::numeric(10,2), 'clothing', 'https://images.pexels.com/photos/3622622/pexels-photo-3622622.jpeg', false, 0, 4.6::numeric(3,1), 14, 4, 'ROPA-0002'),
    ('Lipstick Rojo Intenso', 'Labial de larga duracion en rojo profundo', 79.99::numeric(10,2), 99.99::numeric(10,2), 'cosmetics', 'https://images.pexels.com/photos/3987003/pexels-photo-3987003.jpeg', true, 20, 4.8::numeric(3,1), 30, 8, 'COSM-0002'),
    ('Dior Sauvage', 'Perfume fresco y sofisticado de Dior', 199.99::numeric(10,2), 279.99::numeric(10,2), 'perfumes', 'https://images.pexels.com/photos/3962286/pexels-photo-3962286.jpeg', false, 0, 4.9::numeric(3,1), 16, 4, 'PERF-0003'),
    ('Sueter de Cachemira', 'Sueter premium en cachemira pura', 359.99::numeric(10,2), 459.99::numeric(10,2), 'clothing', 'https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg', false, 0, 4.7::numeric(3,1), 9, 2, 'ROPA-0003'),
    ('Crema Hidratante Luxury', 'Crema facial con ingredientes premium', 149.99::numeric(10,2), 199.99::numeric(10,2), 'cosmetics', 'https://images.pexels.com/photos/3738313/pexels-photo-3738313.jpeg', false, 0, 4.8::numeric(3,1), 22, 6, 'COSM-0003'),
    ('Perfume AltaEsencia Oro', 'Fragancia dorada con notas florales', 219.99::numeric(10,2), 299.99::numeric(10,2), 'perfumes', 'https://images.pexels.com/photos/3962286/pexels-photo-3962286.jpeg', true, 26, 5.0::numeric(3,1), 15, 4, 'PERF-0004')
) AS seed(name, description, price, original_price, category, image_url, is_promotional, discount_percentage, rating, stock, minimum_stock, sku)
JOIN categories c ON c.legacy_key = seed.category
ON CONFLICT (sku) DO NOTHING;

INSERT INTO product_images (product_id, image_url, image_url_hash, is_primary, sort_order, estado)
SELECT p.id, p.image_url, md5(p.image_url), true, 1, 'activo'
FROM products p
ON CONFLICT DO NOTHING;
