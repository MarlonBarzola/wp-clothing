<?php
/**
 * WP Clothing Theme - functions.php
 * Child theme of Hello Elementor
 */

defined( 'ABSPATH' ) || exit;

// ── Constants ───────────────────────────────────────────────────────────────
define( 'WPC_VERSION',   '1.0.0' );
define( 'WPC_DIR',       get_stylesheet_directory() );
define( 'WPC_URI',       get_stylesheet_directory_uri() );
define( 'WPC_ASSETS',    WPC_URI . '/assets' );

// ── Autoload local classes ────────────────────────────────────────────────────
require_once WPC_DIR . '/inc/class-nav-walker.php';

// ── Enqueue styles & scripts ─────────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'wpc_enqueue_assets' );
function wpc_enqueue_assets() {
    // Parent theme CSS
    wp_enqueue_style(
        'hello-elementor-style',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme( 'hello-elementor' )->get( 'Version' )
    );

    // Compiled SASS → main.css
    wp_enqueue_style(
        'wpc-main',
        WPC_ASSETS . '/css/main.css',
        [ 'hello-elementor-style' ],
        WPC_VERSION
    );

    // Main JS
    wp_enqueue_script(
        'wpc-main',
        WPC_ASSETS . '/js/main.js',
        [ 'swiper' ],
        WPC_VERSION,
        true // load in footer
    );

    // Swiper.js — hero carousel (replaces Elementor Pro Slides widget)
    wp_enqueue_style(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        [],
        '11'
    );
    wp_enqueue_script(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        [],
        '11',
        true
    );

    // Google Fonts (Nunito + Playfair Display — match demo aesthetic)
    wp_enqueue_style(
        'wpc-google-fonts',
        'https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Playfair+Display:wght@400;600;700&display=swap',
        [],
        null
    );
}

// ── Theme supports ────────────────────────────────────────────────────────────
add_action( 'after_setup_theme', 'wpc_theme_setup' );
function wpc_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption' ] );

    // Register nav menus
    register_nav_menus( [
        'primary'   => __( 'Primary Menu', 'wp-clothing-theme' ),
        'footer'    => __( 'Footer Menu', 'wp-clothing-theme' ),
        'secondary' => __( 'Secondary / Top Bar Menu', 'wp-clothing-theme' ),
    ] );
}

// ── WooCommerce: remove default wrappers (Elementor handles layout) ───────────
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', 'wpc_woo_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content',  'wpc_woo_wrapper_end',   10 );
function wpc_woo_wrapper_start() { echo '<div class="wpc-woo-content">'; }
function wpc_woo_wrapper_end()   { echo '</div>'; }

// ── WooCommerce: products per page ───────────────────────────────────────────
add_filter( 'loop_shop_per_page', fn() => 12 );

// ── WooCommerce: product columns ─────────────────────────────────────────────
add_filter( 'loop_shop_columns', fn() => 4 );

// ── Custom image sizes ────────────────────────────────────────────────────────
add_action( 'after_setup_theme', function () {
    add_image_size( 'wpc-hero',     1920, 900, true );
    add_image_size( 'wpc-category', 600,  800, true );
    add_image_size( 'wpc-product',  600,  750, true );
    add_image_size( 'wpc-banner',   900,  600, true );
} );

// ── Widget areas ─────────────────────────────────────────────────────────────
add_action( 'widgets_init', 'wpc_register_sidebars' );function wpc_register_sidebars() {
    register_sidebar( [
        'name'          => __( 'Shop Sidebar', 'wp-clothing-theme' ),
        'id'            => 'shop-sidebar',
        'before_widget' => '<div class="wpc-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="wpc-widget__title">',
        'after_title'   => '</h4>',
    ] );
    register_sidebar( [
        'name'          => __( 'Footer Column 1', 'wp-clothing-theme' ),
        'id'            => 'footer-col-1',
        'before_widget' => '<div class="wpc-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="wpc-widget__title">',
        'after_title'   => '</h4>',
    ] );
}

// ── Hero Carousel Shortcode ───────────────────────────────────────────────────
// Usage in Elementor → widget "Shortcode":
//   [wpc_hero_slider]
//
// Each slide is registered via a filter so slides can be added/edited
// without touching code — just update the arrays in a child plugin or here.
//
// Slides config: each item supports keys:
//   image     (URL)      — background image
//   eyebrow   (text)     — small uppercase label above title
//   title     (HTML)     — main heading, supports <br>
//   subtitle  (text)     — optional body copy
//   btn1_text / btn1_url — primary CTA button
//   btn2_text / btn2_url — secondary (outline) CTA button
// ─────────────────────────────────────────────────────────────────────────────
add_shortcode( 'wpc_hero_slider', 'wpc_render_hero_slider' );

function wpc_render_hero_slider(): string {
    $slides = apply_filters( 'wpc_hero_slides', [
        [
            'image'     => WPC_ASSETS . '/images/hero-slide-1.jpg',
            'eyebrow'   => 'SPRING / 2026',
            'title'     => 'Nueva colección<br>limitada ya disponible',
            'subtitle'  => 'Prendas orgánicas, diseño atemporal.',
            'btn1_text' => 'Comprar ahora',
            'btn1_url'  => '/tienda',
            'btn2_text' => 'Ver colecciones',
            'btn2_url'  => '/product-category/shop-all',
        ],
        [
            'image'     => WPC_ASSETS . '/images/hero-slide-2.jpg',
            'eyebrow'   => 'NUEVOS INGRESOS',
            'title'     => 'Looks frescos<br>para esta temporada',
            'subtitle'  => 'Descubre los estilos más vendidos.',
            'btn1_text' => 'Ver novedades',
            'btn1_url'  => '/product-category/new-in',
            'btn2_text' => '',
            'btn2_url'  => '',
        ],
    ] );

    if ( empty( $slides ) ) {
        return '';
    }

    ob_start();
    ?>
    <div class="wpc-hero swiper js-hero-swiper" aria-label="<?php esc_attr_e( 'Carrusel principal', 'wp-clothing-theme' ); ?>">
        <div class="swiper-wrapper">
            <?php foreach ( $slides as $slide ) :
                $image    = esc_url( $slide['image'] ?? '' );
                $eyebrow  = esc_html( $slide['eyebrow'] ?? '' );
                $title    = wp_kses( $slide['title'] ?? '', [ 'br' => [], 'em' => [], 'strong' => [] ] );
                $subtitle = esc_html( $slide['subtitle'] ?? '' );
                $btn1_text = esc_html( $slide['btn1_text'] ?? '' );
                $btn1_url  = esc_url( $slide['btn1_url'] ?? '' );
                $btn2_text = esc_html( $slide['btn2_text'] ?? '' );
                $btn2_url  = esc_url( $slide['btn2_url'] ?? '' );
            ?>
            <div class="swiper-slide wpc-hero__slide"
                 style="background-image: url('<?php echo $image; ?>');"
                 role="group">
                <div class="wpc-hero__overlay" aria-hidden="true"></div>
                <div class="wpc-hero__content">
                    <?php if ( $eyebrow ) : ?>
                        <span class="wpc-hero__eyebrow"><?php echo $eyebrow; ?></span>
                    <?php endif; ?>
                    <?php if ( $title ) : ?>
                        <h1 class="wpc-hero__title"><?php echo $title; ?></h1>
                    <?php endif; ?>
                    <?php if ( $subtitle ) : ?>
                        <p class="wpc-hero__subtitle"><?php echo $subtitle; ?></p>
                    <?php endif; ?>
                    <?php if ( $btn1_text || $btn2_text ) : ?>
                        <div class="wpc-hero__cta">
                            <?php if ( $btn1_text ) : ?>
                                <a href="<?php echo $btn1_url; ?>" class="btn btn--primary btn--lg">
                                    <?php echo $btn1_text; ?>
                                </a>
                            <?php endif; ?>
                            <?php if ( $btn2_text ) : ?>
                                <a href="<?php echo $btn2_url; ?>" class="btn btn--outline btn--lg">
                                    <?php echo $btn2_text; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigation arrows -->
        <div class="swiper-button-prev" aria-label="<?php esc_attr_e( 'Slide anterior', 'wp-clothing-theme' ); ?>"></div>
        <div class="swiper-button-next" aria-label="<?php esc_attr_e( 'Slide siguiente', 'wp-clothing-theme' ); ?>"></div>

        <!-- Pagination dots -->
        <div class="swiper-pagination"></div>
    </div>
    <?php
    return ob_get_clean();
}

// ── Customizer: editable top bar promo text ──────────────────────────────────
add_action( 'customize_register', 'wpc_customizer_register' );
function wpc_customizer_register( WP_Customize_Manager $wp_customize ): void {
    $wp_customize->add_section( 'wpc_header_section', [
        'title'    => __( 'Header', 'wp-clothing-theme' ),
        'priority' => 30,
    ] );

    $wp_customize->add_setting( 'wpc_topbar_text', [
        'default'           => __( '✦ Envío gratis en pedidos mayores a $100 ✦', 'wp-clothing-theme' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );

    $wp_customize->add_control( 'wpc_topbar_text', [
        'label'   => __( 'Texto del top bar (barra de aviso)', 'wp-clothing-theme' ),
        'section' => 'wpc_header_section',
        'type'    => 'text',
    ] );
}

// ── WooCommerce: update cart count via AJAX (fragment) ───────────────────────
// WooCommerce handles this natively via wc_cart_fragments — no extra code needed.
// The cart icon count in header.php auto-refreshes because WC uses JS fragments.
