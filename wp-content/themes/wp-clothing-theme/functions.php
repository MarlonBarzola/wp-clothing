<?php
/**
 * WP Clothing Theme - functions.php
 * Child theme of Hello Elementor
 */

defined( 'ABSPATH' ) || exit;

// -- Constants ---------------------------------------------------------------
defined( 'WPC_VERSION' ) || define( 'WPC_VERSION', '1.0.0' );
defined( 'WPC_DIR' )     || define( 'WPC_DIR',     get_stylesheet_directory() );
defined( 'WPC_URI' )     || define( 'WPC_URI',     get_stylesheet_directory_uri() );
defined( 'WPC_ASSETS' )  || define( 'WPC_ASSETS',  WPC_URI . '/assets' );

// -- Autoload local classes ----------------------------------------------------
require_once WPC_DIR . '/inc/class-nav-walker.php';

// -- Enqueue styles & scripts -------------------------------------------------
add_action( 'wp_enqueue_scripts', 'wpc_enqueue_assets' );
function wpc_enqueue_assets() {
    // Parent theme CSS
    wp_enqueue_style(
        'hello-elementor-style',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme( 'hello-elementor' )->get( 'Version' )
    );

    // Compiled SASS ? main.css
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

    // Swiper.js -- hero carousel (replaces Elementor Pro Slides widget)
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

    // Google Fonts (Nunito + Playfair Display -- match demo aesthetic)
    wp_enqueue_style(
        'wpc-google-fonts',
        'https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Playfair+Display:wght@400;600;700&display=swap',
        [],
        null
    );
}

// -- Theme supports ------------------------------------------------------------
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
        'primary'      => __( 'Primary Menu', 'wp-clothing-theme' ),
        'footer'       => __( 'Footer Menu', 'wp-clothing-theme' ),
        'secondary'    => __( 'Secondary / Top Bar Menu', 'wp-clothing-theme' ),
        'footer-shop'  => __( 'Footer: Tienda', 'wp-clothing-theme' ),
        'footer-info'  => __( 'Footer: Información', 'wp-clothing-theme' ),
        'footer-legal' => __( 'Footer: Aviso legal (barra inferior)', 'wp-clothing-theme' ),
    ] );
}

// -- WooCommerce: remove default wrappers (Elementor handles layout) -----------
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', 'wpc_woo_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content',  'wpc_woo_wrapper_end',   10 );
function wpc_woo_wrapper_start() { echo '<div class="wpc-woo-content">'; }
function wpc_woo_wrapper_end()   { echo '</div>'; }

// -- WooCommerce: products per page -------------------------------------------
add_filter( 'loop_shop_per_page', fn() => 12 );

// -- WooCommerce: product columns ---------------------------------------------
add_filter( 'loop_shop_columns', fn() => 4 );

// -- Custom image sizes --------------------------------------------------------
add_action( 'after_setup_theme', function () {
    add_image_size( 'wpc-hero',     1920, 900, true );
    add_image_size( 'wpc-category', 600,  750, true ); // 4:5 ratio -- matches category card display
    add_image_size( 'wpc-product',  600,  750, true );
    add_image_size( 'wpc-banner',   900,  600, true );
} );

// -- Widget areas -------------------------------------------------------------
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
    register_sidebar( [
        'name'          => __( 'Footer Columna 4 (Contacto)', 'wp-clothing-theme' ),
        'id'            => 'footer-col-4',
        'before_widget' => '<div class="wpc-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="wpc-footer__col-title">',
        'after_title'   => '</h4>',
    ] );
}

// -- Shortcodes ----------------------------------------------------------------
// Each shortcode lives in its own file under inc/shortcodes/.
// Add new files there -- they load automatically.
foreach ( glob( WPC_DIR . '/inc/shortcodes/*.php' ) as $wpc_shortcode ) {
    require_once $wpc_shortcode;
}
unset( $wpc_shortcode );

// -- Customizer: editable top bar promo text ----------------------------------
add_action( 'customize_register', 'wpc_customizer_register' );
function wpc_customizer_register( WP_Customize_Manager $wp_customize ): void {
    $wp_customize->add_section( 'wpc_header_section', [
        'title'    => __( 'Header', 'wp-clothing-theme' ),
        'priority' => 30,
    ] );

    $wp_customize->add_setting( 'wpc_topbar_text', [
        'default'           => __( "\u{2726} Env\u{00ed}o gratis en pedidos mayores a \$100 \u{2726}", 'wp-clothing-theme' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ] );

    $wp_customize->add_control( 'wpc_topbar_text', [
        'label'   => __( 'Texto del top bar (barra de aviso)', 'wp-clothing-theme' ),
        'section' => 'wpc_header_section',
        'type'    => 'text',
    ] );
}

// -- Customizer: footer settings ----------------------------------------------
add_action( 'customize_register', 'wpc_footer_customizer_register' );
function wpc_footer_customizer_register( WP_Customize_Manager $wp_customize ): void {
    $wp_customize->add_section( 'wpc_footer_section', [
        'title'    => __( 'Footer', 'wp-clothing-theme' ),
        'priority' => 35,
    ] );

    // Description
    $wp_customize->add_setting( 'wpc_footer_desc', [
        'default'           => __( 'Tu tienda de moda favorita. Calidad y estilo en cada prenda.', 'wp-clothing-theme' ),
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'wpc_footer_desc', [
        'label'   => __( 'Descripción del footer', 'wp-clothing-theme' ),
        'section' => 'wpc_footer_section',
        'type'    => 'textarea',
    ] );

    // Contact text & email
    $wp_customize->add_setting( 'wpc_footer_contact_text', [
        'default'           => __( 'Estamos aquí para ayudarte. Escríbenos en cualquier momento.', 'wp-clothing-theme' ),
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'wpc_footer_contact_text', [
        'label'   => __( 'Texto de contacto', 'wp-clothing-theme' ),
        'section' => 'wpc_footer_section',
        'type'    => 'textarea',
    ] );

    $wp_customize->add_setting( 'wpc_footer_email', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
    ] );
    $wp_customize->add_control( 'wpc_footer_email', [
        'label'   => __( 'Email de contacto', 'wp-clothing-theme' ),
        'section' => 'wpc_footer_section',
        'type'    => 'email',
    ] );

    // Copyright
    $wp_customize->add_setting( 'wpc_footer_copyright', [
        'default'           => '&copy; ' . gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) . '. Todos los derechos reservados.',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'wpc_footer_copyright', [
        'label'   => __( 'Texto de copyright', 'wp-clothing-theme' ),
        'section' => 'wpc_footer_section',
        'type'    => 'text',
    ] );

    // Social media links
    $social_networks = [
        'instagram' => __( 'Instagram URL', 'wp-clothing-theme' ),
        'facebook'  => __( 'Facebook URL', 'wp-clothing-theme' ),
        'tiktok'    => __( 'TikTok URL', 'wp-clothing-theme' ),
        'pinterest' => __( 'Pinterest URL', 'wp-clothing-theme' ),
        'twitter'   => __( 'Twitter / X URL', 'wp-clothing-theme' ),
        'youtube'   => __( 'YouTube URL', 'wp-clothing-theme' ),
    ];
    foreach ( $social_networks as $key => $label ) {
        $wp_customize->add_setting( 'wpc_footer_' . $key, [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ] );
        $wp_customize->add_control( 'wpc_footer_' . $key, [
            'label'   => $label,
            'section' => 'wpc_footer_section',
            'type'    => 'url',
        ] );
    }
}

// -- WooCommerce: update cart count via AJAX (fragment) -----------------------
add_filter( 'woocommerce_add_to_cart_fragments', 'wpc_cart_count_fragment' );
function wpc_cart_count_fragment( array $fragments ): array {
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    $class = 'count js-cart-count' . ( $count > 0 ? '' : ' count--empty' );
    $fragments['.js-cart-count'] =
        '<span class="' . esc_attr( $class ) . '" aria-hidden="true">' . esc_html( $count ) . '</span>';
    return $fragments;
}
