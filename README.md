# AltaEsencia Backend

Backend Laravel para **AltaEsencia**, con dos capas principales:

- **Storefront público** para que el frontend consuma catálogo y contenido comercial.
- **Panel administrativo** para gestionar inventario, productos, usuarios y ventas.

## Qué hace el proyecto

Este proyecto centraliza la lógica del negocio de AltaEsencia:

- expone endpoints públicos para el frontend de tienda
- administra productos, categorías e imágenes
- controla stock y movimientos de inventario
- registra ventas, detalle, notas y anulaciones
- permite acceso administrativo con login web
- entrega métricas y reportes para dashboard

## Funcionalidades principales

- Storefront público sin autenticación
- Dashboard administrativo protegido por sesión
- CRUD de categorías
- CRUD de productos
- Múltiples imágenes por producto
- Gestión de usuarios administrativos y clientes
- Movimientos de inventario: entrada, salida, ajuste, venta y devolución
- Registro de ventas con detalle y notas
- Anulación de ventas con reversión de stock
- Reportes para dashboard
- Configuración CORS para conectar frontend externo

## Tecnologías

- PHP 8.2+
- Laravel 12
- MySQL
- Vite
- Tailwind CSS

## Endpoints públicos del storefront

Rutas pensadas para el frontend público:

- `GET /storefront/bootstrap`
- `GET /storefront/categories/{categoryId}/products`

También están disponibles bajo prefijo `/api`:

- `GET /api/storefront/bootstrap`
- `GET /api/storefront/categories/{categoryId}/products`

Categorías válidas:

- `clothing`
- `perfumes`
- `cosmetics`

## Panel administrativo

Rutas principales:

- `/admin/login`
- `/admin`
- `/admin/api/*`

El panel permite:

- ver métricas del negocio
- crear y editar productos
- administrar categorías
- gestionar usuarios
- registrar movimientos de inventario
- crear y revisar ventas

## Instalación

1. Instalar dependencias PHP:

```bash
composer install
```

2. Crear el archivo de entorno:

```bash
copy .env.example .env
```

3. Generar la key:

```bash
php artisan key:generate
```

4. Configurar la base de datos en `.env`

Ejemplo MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=altaesencia_bd
DB_USERNAME=root
DB_PASSWORD=
```

5. Ejecutar migraciones y seeders:

```bash
php artisan migrate --seed
```

6. Instalar dependencias frontend:

```bash
npm install
```

## Ejecución en desarrollo

Backend Laravel:

```bash
php artisan serve
```

Frontend assets con Vite:

```bash
npm run dev
```

También puedes usar el script combinado:

```bash
composer run dev
```

## Build de producción

```bash
npm run build
```

Esto genera `public/build/manifest.json`, necesario para cargar los assets compilados del panel administrativo.

## Variables útiles de entorno

### Backend Laravel

```env
APP_NAME=AltaEsencia
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
```

### Storefront

```env
FRONTEND_URL=http://localhost:5174
STOREFRONT_WHATSAPP_NUMBER=59175540850
STOREFRONT_MAX_QUANTITY_PER_PRODUCT=3
STOREFRONT_BRAND_NAME=AltaEsencia
STOREFRONT_BRAND_SHORT_NAME=AE
STOREFRONT_BRAND_TAGLINE=Estilo y Exclusividad
```

## CORS

El proyecto ya está preparado para aceptar peticiones del frontend local, por ejemplo:

- `http://localhost:5173`
- `http://localhost:5174`
- `http://127.0.0.1:5173`
- `http://127.0.0.1:5174`

La configuración vive en [config/cors.php](config/cors.php).

## Usuario administrativo demo

Si ejecutas los seeders, se crea este acceso:

- correo: `admin@altaesencia.com`
- contraseña: `password`

## Estructura general

- [routes/web.php](routes/web.php): rutas web y panel admin
- [routes/api.php](routes/api.php): endpoints públicos y API general
- [routes/admin-api.php](routes/admin-api.php): endpoints del panel admin
- [app/Http/Controllers/Api](app/Http/Controllers/Api): controladores API
- [app/Http/Controllers/Admin](app/Http/Controllers/Admin): login y vistas del panel
- [app/Services](app/Services): lógica de negocio
- [database/migrations](database/migrations): esquema de base de datos
- [database/seeders](database/seeders): datos iniciales

## Estado actual

El sistema ya cuenta con:

- catálogo público consumible desde frontend React
- backend administrativo funcional
- autenticación para administración
- base de inventario y ventas
- reportes iniciales para dashboard

## Próximas mejoras recomendadas

- mover contenido del storefront desde `config` a tablas administrables
- agregar roles y permisos por módulo
- exportación de reportes
- autenticación API si en el futuro el frontend privado la necesita
- tests funcionales conectados a MySQL de pruebas

## Licencia

Proyecto privado para uso del sistema AltaEsencia.
