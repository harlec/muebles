# Muebles & Estilo — Dashboard multilocal

Panel de control de ventas en tiempo real para una mueblería con 4 locales.
Implementación en PHP (MVC ligero, sin framework) + MySQL, a partir del
handoff de diseño en [`design_handoff_dashboard_multilocal/`](design_handoff_dashboard_multilocal/README.md)
(fuente de verdad de fórmulas, copys y tokens visuales).

## Estructura

```
app/
  Core/          Database (PDO), Router, Controller base
  Controllers/   DashboardController (vista), Api/SalesController (JSON)
  Models/        Local, Venta, Producto — queries de agregación
config/config.php  Credenciales DB y ajustes de la app
database/        schema.sql (DDL) y seed.sql (locales/categorías/productos base)
public/          Document root: index.php (front controller), css/, js/, assets/
resources/views/ Vistas PHP (dashboard/index.php)
```

Sin Composer: autoload PSR-4 simple registrado en `public/index.php`
(namespace `App\` → carpeta `app/`).

## Requisitos

- PHP 8.1+
- MySQL 5.7+ / MariaDB
- Node.js (solo para compilar Tailwind, no se necesita en el servidor)

## Setup local

```bash
# 1. Base de datos
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql

# 2. Credenciales (o exporta DB_HOST/DB_USER/DB_PASS/DB_NAME antes de levantar el server)
#    editar config/config.php si no usas los valores por defecto

# 3. CSS (Tailwind)
npm install
npm run build:css     # o: npm run watch:css mientras desarrollas

# 4. Servidor de desarrollo
php -S localhost:8899 -t public public/index.php
```

Abrir http://localhost:8899/. Sin filas en `ventas`, el dashboard carga con
ceros — inserta ventas de prueba en la tabla para ver el ranking, el feed y
los paneles con datos.

## Deploy a Plesk

1. Sube todo el proyecto **excepto** que el *document root* del dominio
   apunte a la carpeta `public/` (Plesk → Hosting Settings → Document root).
   El resto de carpetas (`app/`, `config/`, `database/`, `resources/`)
   deben quedar **fuera** del árbol servido públicamente, un nivel arriba
   de `public/` (así es como quedan en este repo).
2. Crea la base de datos MySQL desde el panel de Plesk e importa
   `database/schema.sql` y `database/seed.sql` (Bases de datos → phpMyAdmin,
   o `mysql -u ... -p ... < archivo.sql` por SSH si tienes acceso).
3. Configura las credenciales: en Plesk → Sitios web y dominios → tu
   dominio → PHP → Variables de entorno, define `DB_HOST`, `DB_NAME`,
   `DB_USER`, `DB_PASS` (o edita `config/config.php` directamente con los
   valores reales antes de subir).
4. Verifica que `mod_rewrite` esté activo (lo está por defecto en Apache de
   Plesk) para que `public/.htaccess` enrute todo a `index.php`.
5. Compila el CSS **antes** de subir (`npm run build:css`) y sube
   `public/css/app.css` — Plesk no necesita Node instalado, solo sirve el
   archivo estático resultante.

## API

Endpoints JSON que consume `public/js/dashboard.js` (parámetro `local`:
`global` o el id del local; ver `database/schema.sql` para los ids):

| Endpoint | Uso |
|---|---|
| `GET /api/today?local=` | KPIs de hoy, ranking por local, ventas por hora, feed |
| `GET /api/month?local=` | Serie diaria del mes, proyección por local |
| `GET /api/payments?scope=today\|month&local=` | Mix de métodos de pago |
| `GET /api/rotation?dias=30&local=` | Top de productos y ventas por categoría |
| `GET /api/feed?since=` | Feed incremental (polling) |

Los cálculos derivados (`metaPct`, proyección de cierre, `avanceDelDía`,
deltas vs. ayer, etc.) se hacen en `dashboard.js` con las fórmulas exactas
del README de diseño — la API entrega datos crudos agregados.

## Estado del scaffold / pendientes

Este es el andamiaje inicial (estructura, esquema, rutas, chrome global y
tarjetas KPI funcionales). Lo que falta para fidelidad visual completa con
el handoff de diseño:

- **Gráficos SVG**: donas de participación/métodos de pago y la curva
  "real vs proyección" del mes están simplificados a barras/listas planas;
  el diseño original usa `conic-gradient` y `<svg>` (ver README de diseño,
  secciones "Quién vende más", "Métodos de pago" y "Septiembre · real vs
  proyección").
- **Rotación de inventario**: `índice de rotación` y `días de stock`
  necesitan una tabla de stock por producto que no existe todavía en
  `database/schema.sql` — hoy se muestran como `N/D`.
- **Selector de periodo de categorías** (HOY/MES/AÑO con los multiplicadores
  del README) no está cableado aún; se muestra solo el acumulado de 30 días.
- **Alertas de rotación** (quiebre cercano / baja rotación / oportunidad)
  pendientes, dependen del stock también.
- Tema claro/oscuro y filtro de local ya son funcionales end-to-end.
