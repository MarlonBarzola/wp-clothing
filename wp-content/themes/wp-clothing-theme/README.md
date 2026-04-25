# WP Clothing — WordPress WooCommerce Fashion Store

> Replicación del diseño [DeBebe demo](https://themes.vamtam.com/?theme=debebe&n=1) como proyecto de portafolio: WordPress + WooCommerce + Elementor Pro + SASS modular.

---

## Tabla de contenidos

1. [Stack tecnológico](#1-stack-tecnológico)
2. [Configuración del entorno local](#2-configuración-del-entorno-local)
3. [Plugins a instalar y por qué](#3-plugins-a-instalar-y-por-qué)
4. [Activar tema y compilar SASS](#4-activar-tema-y-compilar-sass)
5. [Estructura de archivos del tema](#5-estructura-de-archivos-del-tema)
6. [Estructura de la Home en Elementor](#6-estructura-de-la-home-en-elementor)
7. [Cómo replicar el slider principal](#7-cómo-replicar-el-slider-principal)
8. [Arquitectura SASS](#8-arquitectura-sass)
9. [Buenas prácticas y plantilla reutilizable](#9-buenas-prácticas-y-plantilla-reutilizable)
10. [Comandos de desarrollo](#10-comandos-de-desarrollo)

---

## 1. Stack tecnológico

| Capa | Herramienta | Motivo |
|---|---|---|
| CMS | WordPress 6.x | Administrable, mayor ecosistema |
| E-commerce | WooCommerce | Estándar de facto, extensible |
| Page builder | **Elementor Pro** | Widgets de WooCommerce, Theme Builder, Swiper nativo |
| Tema base | **Hello Elementor** | Mínimo CSS, cero conflictos con Elementor |
| Child theme | `wp-clothing-theme` | Personalizaciones aisladas, actualizaciones seguras |
| Estilos | **SASS (Dart Sass)** | Variables tipadas, mixins, estructura 7-1 adaptada |
| Control de versiones | Git + GitHub | Portafolio público |

---

## 2. Configuración del entorno local

### Opción A — Laragon (recomendado, ya instalado)

```
1. Laragon ya corre en C:\laragon-2026\www\wp-clothing
2. Acceder a: http://wp-clothing.test
3. Asegurarse de que PHP ≥ 8.1 y MySQL estén activos
```

### Opción B — Local by Flywheel / DevKinsta

Alternativas con interfaz gráfica si se cambia de máquina.

### WordPress — configuración inicial

```bash
# wp-config.php ya existe. Verificar:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

# Para desarrollo activar Script Debug:
define('SCRIPT_DEBUG', true);
```

### WP-CLI (opcional pero muy útil)

```bash
# Instalar plugins desde línea de comandos
wp plugin install woocommerce elementor hello-elementor --activate

# Crear páginas de WooCommerce
wp wc tool run install_pages --user=admin
```

---

## 3. Plugins a instalar y por qué

### Esenciales

| Plugin | Por qué |
|---|---|
| **WooCommerce** | Motor de tienda: productos, carrito, checkout, pedidos |
| **Elementor Pro** | Theme Builder (header/footer editables), widgets WooCommerce, Slides widget |
| **Hello Elementor** | Tema padre ligero (~5 KB CSS), diseñado para Elementor |

### Funcionalidad extra

| Plugin | Por qué |
|---|---|
| **Elementor – Header, Footer & Blocks** (gratuito, si no tienes Pro) | Header/footer editables sin Pro |
| **WooCommerce Wishlist** (YITH o TI WooCommerce) | Botón "♡ guardar" en tarjetas de producto |
| **YITH WooCommerce Quick View** | Modal de producto sin salir de la tienda |
| **WooCommerce Product Filter** (FiboSearch o YITH) | Filtros sidebar (precio, talla, color) |
| **MailPoet / FluentCRM** | Newsletter integrado con formulario en home |
| **WP Rocket / LiteSpeed Cache** | Performance — caché de página completa |
| **Smush / Imagify** | Compresión automática de imágenes |
| **WooCommerce Stripe / PayPal** | Pasarelas de pago |
| **Yoast SEO / Rank Math** | SEO estructurado para productos |

### Opcionales para portafolio

| Plugin | Por qué |
|---|---|
| **WooCommerce Variation Swatches** | Tallas/colores como botones visuales (replica la demo) |
| **WPC Smart Wishlist** | Wishlist ligera sin YITH |
| **Woocommerce Social Login** | Login con Google/Facebook |

---

## 4. Activar tema y compilar SASS

### Activar el child theme

```
WordPress Admin → Apariencia → Temas
Activar: "WP Clothing Theme"
```

### Instalar dependencias Node y compilar

```bash
cd wp-content/themes/wp-clothing-theme

# Instalar Dart Sass
npm install

# Modo desarrollo (watch + source maps)
npm run sass:dev

# Build de producción (minificado, sin source maps)
npm run sass:build
```

> El CSS compilado se genera en `assets/css/main.css` y se encola automáticamente vía `functions.php`.

---

## 5. Estructura de archivos del tema

```
wp-clothing-theme/
├── style.css                  ← Cabecera del tema (requerida por WP)
├── functions.php              ← Enqueue, theme supports, WooCommerce config
├── package.json               ← Scripts npm (sass)
├── screenshot.png             ← Captura 1200×900 para Apariencia > Temas
│
├── assets/
│   ├── css/
│   │   └── main.css           ← CSS compilado (no editar directamente)
│   ├── js/
│   │   └── main.js            ← JS personalizado (tabs, header scroll, etc.)
│   └── images/
│       └── logo.svg
│
└── sass/
    ├── main.scss              ← Entry point — importa todo
    ├── abstracts/
    │   ├── _variables.scss    ← Colores, tipografía, espaciado, breakpoints
    │   ├── _mixins.scss       ← respond-to, container, flex-center, overlay…
    │   └── _functions.scss    ← rem(), spacing(), z()
    ├── base/
    │   ├── _reset.scss        ← Reset moderno
    │   ├── _typography.scss   ← h1-h6, body, .eyebrow
    │   └── _root.scss         ← CSS Custom Properties (--clr-primary, etc.)
    ├── layout/
    │   ├── _grid.scss         ← .container, .section, .grid-4…
    │   ├── _header.scss       ← .wpc-topbar, .wpc-header, nav, burger
    │   └── _footer.scss       ← .wpc-footer grid de columnas
    ├── components/
    │   ├── _buttons.scss      ← .btn, variantes, tamaños
    │   ├── _cards.scss        ← .card genérico
    │   ├── _hero.scss         ← .wpc-hero (slider fullwidth)
    │   ├── _category-grid.scss← .wpc-category-card (shop by category)
    │   ├── _product-card.scss ← Override de WooCommerce .product li
    │   ├── _banner.scss       ← .wpc-banner (banners promocionales 2-col)
    │   ├── _testimonials.scss ← .wpc-features, .wpc-icon-bar
    │   ├── _newsletter.scss   ← .wpc-newsletter
    │   └── _badges.scss       ← .badge--sale, --new, --hot
    ├── pages/
    │   ├── _home.scss         ← Product tabs, community, brand story
    │   ├── _shop.scss         ← Layout sidebar + grid, toolbar
    │   ├── _product.scss      ← Single product WooCommerce overrides
    │   ├── _cart.scss         ← Cart table + checkout form base
    │   └── _checkout.scss     ← Checkout payment section
    └── utilities/
        ├── _spacing.scss      ← .mt-1 … .px-8, .mx-auto
        ├── _colors.scss       ← .text-dark, .bg-primary…
        └── _visibility.scss   ← .sr-only, .hidden, .hide-mobile
```

---

## 6. Estructura de la Home en Elementor

Crear la página Home como **plantilla de Elementor** (no página normal). Ir a:
`Elementor → Mis Plantillas → Nueva → Página`

### Secciones recomendadas (de arriba a abajo)

```
┌─────────────────────────────────────────────────────────────┐
│  HEADER (Theme Builder — sticky, con carrito + búsqueda)    │
├─────────────────────────────────────────────────────────────┤
│  1. HERO SLIDER                                             │
│     Widget: Slides (Elementor Pro)                          │
│     2-3 slides: imagen fondo + eyebrow + título + 2 botones │
├─────────────────────────────────────────────────────────────┤
│  2. SHOP BY CATEGORY                                        │
│     Widget: Loop Grid o Image Box (4 columnas)              │
│     Cada caja: imagen + nombre de categoría + hover effect  │
├─────────────────────────────────────────────────────────────┤
│  3. BEST SELLERS (con tabs Baby/Girls/Boys)                 │
│     Widget: Tabs + dentro de cada tab: Products widget      │
│     Filtrar por categoría, mostrar 4-8 productos            │
├─────────────────────────────────────────────────────────────┤
│  4. BANNER DOBLE (Mix & Match / Cute & Comfy)               │
│     2 columnas — cada una: Image + texto superpuesto        │
│     CSS class: wpc-banner (estilos ya definidos en SASS)    │
├─────────────────────────────────────────────────────────────┤
│  5. ICON BAR — Envío / Free shipping / Soporte              │
│     3 columnas: Icon List o Icon Box                        │
│     CSS class: wpc-icon-bar                                 │
├─────────────────────────────────────────────────────────────┤
│  6. NEW IN — productos nuevos                               │
│     Widget: Products (ordenar por fecha, últimos 8)         │
│     Con botón "Ver todo" → /tienda                          │
├─────────────────────────────────────────────────────────────┤
│  7. BRAND STORY / "Why Organic Matters"                     │
│     2 columnas: imagen izquierda + texto derecha            │
│     CSS class: wpc-brand-story                              │
├─────────────────────────────────────────────────────────────┤
│  8. NEWSLETTER                                              │
│     Widget: Formulario de Elementor o MailPoet              │
│     CSS class: wpc-newsletter                               │
├─────────────────────────────────────────────────────────────┤
│  FOOTER (Theme Builder — columnas: logo/about/links/contact)│
└─────────────────────────────────────────────────────────────┘
```

### Paleta de colores en Elementor (Global Colors)

```
Ir a Elementor → Kit de Sitio → Colores Globales:

Primary     #f4a5a5  (rosa suave)
Secondary   #f9e4d4  (crema)
Accent      #c8e6c9  (menta)
Dark        #2d2d2d  (títulos)
Text        #555555  (cuerpo)
```

### Fuentes globales

```
Elementor → Kit de Sitio → Fuentes Tipográficas:

Titulares: Playfair Display — Serif, 700
Cuerpo:    Nunito — Sans-serif, 400/600
```

---

## 7. Cómo replicar el slider principal

### Opción A — Elementor Pro Slides widget (recomendado)

```
1. Insertar widget "Slides"
2. Por cada slide:
   - Fondo: imagen de alta resolución (1920×900, usar wpc-hero image size)
   - Contenido: activar Content → Title / Description / Button
   - Estilo: overlay de gradiente (ver _hero.scss)
3. Ajustar en Style:
   - Altura mínima: 80vh
   - Animación: Slide o Fade
   - Autoplay: 5000ms
   - Flechas y puntos: activados, color blanco
4. Agregar CSS class: wpc-hero al widget
```

### Opción B — Swiper.js + HTML personalizado (sin Pro)

```html
<!-- En Elementor: widget HTML -->
<div class="swiper wpc-hero__slide">
  <div class="swiper-wrapper">
    <div class="swiper-slide" style="background-image:url(...)">
      <div class="wpc-hero__overlay"></div>
      <div class="wpc-hero__content">
        <span class="wpc-hero__eyebrow">SPRING / 2026</span>
        <h1 class="wpc-hero__title">Nueva colección<br>limitada ya disponible</h1>
        <div class="wpc-hero__cta">
          <a href="/tienda" class="btn btn--primary btn--lg">Comprar ahora</a>
          <a href="/colecciones" class="btn btn--outline btn--lg">Ver colecciones</a>
        </div>
      </div>
    </div>
  </div>
  <div class="swiper-pagination"></div>
  <div class="swiper-button-prev"></div>
  <div class="swiper-button-next"></div>
</div>
```

> Swiper.js está incluido en Elementor Pro. Para la versión gratuita, enqueuear desde CDN en `functions.php`.

---

## 8. Arquitectura SASS

El proyecto sigue una versión adaptada del patrón **7-1** (7 carpetas, 1 archivo de entrada):

```
abstracts/   → Sin salida CSS. Variables, mixins, funciones.
base/        → Estilos globales: reset, tipografía, custom properties.
layout/      → Estructura de página: grid, header, footer.
components/  → Bloques reutilizables: botones, tarjetas, hero, banners.
pages/       → Overrides específicos por página.
utilities/   → Clases de utilidad: spacing, colores, visibilidad.
```

### Reglas de escritura

- Metodología **BEM** dentro de cada componente: `.wpc-product-card__title`
- Los breakpoints usan el mixin `@include respond-to(md)` (mobile-first)
- Los colores siempre via `$color-*` variables, nunca valores inline
- Los `!important` solo en utilities y overrides de WooCommerce

### Compilación

```bash
npm run sass:dev    # desarrollo con watch + source maps
npm run sass:build  # producción minificado
```

---

## 9. Buenas prácticas y plantilla reutilizable

### Para portafolio / cliente reutilizable

- [ ] **Exportar plantillas Elementor**: `Elementor → Mis Plantillas → Exportar`  
      Guardar como archivos `.json` dentro de `templates/elementor/`
- [ ] **Export WooCommerce products**: Productos → Exportar CSV con imágenes
- [ ] **Child Theme siempre**: nunca modificar `hello-elementor` directamente
- [ ] **Variables SASS como fuente de verdad**: cambiar `$color-primary` actualiza todo el sitio
- [ ] **Usar Kit de Sitio de Elementor**: colores y tipografías globales sincronizados con SASS vía `--clr-*`

### Seguridad y performance

```php
// wp-config.php — en producción:
define('WP_DEBUG', false);
define('DISALLOW_FILE_EDIT', true);    // Deshabilitar editor de temas en admin
define('FORCE_SSL_ADMIN', true);
```

```
- Imágenes: formato WebP, lazy loading activado
- Caché: WP Rocket o LiteSpeed Cache
- CDN: Cloudflare (plan gratuito suficiente para portafolio)
- Eliminar plugins inactivos y temas no usados
```

### .gitignore recomendado (ya en repo)

```
wp-content/uploads/       ← imágenes de usuario, no en git
wp-content/cache/         ← generado por caché
wp-config.php             ← credenciales de BD
node_modules/             ← dependencias npm
assets/css/main.css       ← artefacto compilado (opcional: incluir en producción)
```

### Workflow de desarrollo

```
1. git checkout -b feature/nueva-seccion
2. Editar SASS → npm run sass:dev
3. Editar plantillas en Elementor
4. Exportar plantillas → templates/elementor/
5. git add . && git commit -m "feat: nueva sección X"
6. git push origin feature/nueva-seccion
7. Pull Request → merge a main
```

---

## 10. Comandos de desarrollo

```bash
# Desde la raíz del tema
cd wp-content/themes/wp-clothing-theme

# Instalar dependencias (primera vez)
npm install

# Watch SASS (desarrollo)
npm run sass:dev

# Build SASS (producción)
npm run sass:build

# WP-CLI útiles
wp cache flush
wp cron event run --due-now
wp media regenerate --yes           # regenerar tamaños de imagen
wp search-replace 'http://wp-clothing.test' 'https://mi-dominio.com'
```

---

## Referencia visual — Secciones del demo

| Sección | Descripción | Widget Elementor |
|---|---|---|
| Hero | Slider fullwidth, overlay gradiente, botones CTA | Slides (Pro) |
| Shop by Category | Grid 4 cols, imagen + label hover | Image Box / Loop Grid |
| Best Sellers | Tabs con filtro por categoría | Tabs + Products |
| Collection Banners | 2 cols, imagen + texto superpuesto | Columns + Image |
| Icon Bar | 3 features: envío, gratis, soporte | Icon Box |
| New In | Grid 4 cols de productos recientes | Products |
| Brand Story | 2 cols: imagen + texto + CTA | Columns |
| Newsletter | Input email + submit | Form (Pro) |
| Footer | 4 cols: logo + about + links + contacto | Theme Builder |

---

*Proyecto construido para portafolio. Libre de usar como base.*
