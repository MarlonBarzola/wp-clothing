<?php
/**
 * Header template
 * Replicates the DeBebe nav: top bar + sticky header with dropdowns + mobile drawer
 */
defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ═══════════════════════════════════════════════
     TOP BAR
═══════════════════════════════════════════════ -->
<div class="wpc-topbar" role="banner">
    <div class="wpc-topbar__inner">
        <!-- Left: secondary links -->
        <nav class="wpc-topbar__links" aria-label="<?php esc_attr_e( 'Links secundarios', 'wp-clothing-theme' ); ?>">
            <a href="<?php echo esc_url( home_url( '/faq' ) ); ?>">
                <?php esc_html_e( 'FAQ', 'wp-clothing-theme' ); ?>
            </a>
            <a href="<?php echo esc_url( home_url( '/track-order' ) ); ?>">
                <?php esc_html_e( 'Rastrear pedido', 'wp-clothing-theme' ); ?>
            </a>
        </nav>

        <!-- Center: promo text (editable via Customizer) -->
        <p class="wpc-topbar__promo">
            <?php echo esc_html( get_theme_mod( 'wpc_topbar_text', __( '✦ Envío gratis en pedidos mayores a $100 ✦', 'wp-clothing-theme' ) ) ); ?>
        </p>

        <!-- Right: country / currency placeholder -->
        <div class="wpc-topbar__right hide-mobile">
            <span><?php esc_html_e( 'ES / USD', 'wp-clothing-theme' ); ?></span>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════
     MAIN HEADER
═══════════════════════════════════════════════ -->
<header class="wpc-header js-header" role="banner">
    <div class="wpc-header__inner">

        <!-- Logo -->
        <a class="wpc-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?> — <?php esc_attr_e( 'Inicio', 'wp-clothing-theme' ); ?>">
            <?php
            $logo_id = get_theme_mod( 'custom_logo' );
            if ( $logo_id ) {
                echo wp_get_attachment_image( $logo_id, 'full', false, [ 'alt' => get_bloginfo( 'name' ) ] );
            } else {
                echo '<span class="wpc-header__logo-text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
            }
            ?>
        </a>

        <!-- Primary navigation -->
        <nav class="wpc-header__nav js-nav" aria-label="<?php esc_attr_e( 'Menú principal', 'wp-clothing-theme' ); ?>">
            <?php
            wp_nav_menu( [
                'theme_location' => 'primary',
                'menu_class'     => 'wpc-nav',
                'container'      => false,
                'walker'         => new WPC_Nav_Walker(),
                'fallback_cb'    => 'wpc_nav_fallback',
            ] );
            ?>
        </nav>

        <!-- Action icons: search, wishlist, account, cart -->
        <div class="wpc-header__actions">

            <!-- Search toggle -->
            <button class="wpc-icon-btn js-search-toggle"
                    aria-label="<?php esc_attr_e( 'Abrir buscador', 'wp-clothing-theme' ); ?>"
                    aria-expanded="false"
                    aria-controls="wpc-search-overlay">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
            </button>

            <!-- Wishlist (YITH / TI WooCommerce, graceful fallback) -->
            <a class="wpc-icon-btn"
               href="<?php echo esc_url( function_exists( 'YITH_WCWL' ) ? YITH_WCWL()->get_wishlist_url() : home_url( '/wishlist' ) ); ?>"
               aria-label="<?php esc_attr_e( 'Lista de deseos', 'wp-clothing-theme' ); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </a>

            <!-- My account -->
            <a class="wpc-icon-btn"
               href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>"
               aria-label="<?php esc_attr_e( 'Mi cuenta', 'wp-clothing-theme' ); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </a>

            <!-- Cart -->
            <button type="button" class="wpc-icon-btn wpc-icon-btn--cart xoo-wsc-cart-trigger"
               aria-label="<?php echo esc_attr( sprintf( __( 'Carrito — %d artículos', 'wp-clothing-theme' ), WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ) ); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <span class="count js-cart-count<?php echo ( WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) ? '' : ' count--empty'; ?>" aria-hidden="true">
                    <?php echo WC()->cart ? esc_html( WC()->cart->get_cart_contents_count() ) : '0'; ?>
                </span>
            </button>

            <!-- Hamburger — mobile only -->
            <button class="wpc-header__burger js-burger hide-desktop"
                    aria-label="<?php esc_attr_e( 'Abrir menú', 'wp-clothing-theme' ); ?>"
                    aria-expanded="false"
                    aria-controls="wpc-mobile-drawer">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div><!-- /.wpc-header__inner -->
</header>

<!-- ═══════════════════════════════════════════════
     SEARCH OVERLAY
═══════════════════════════════════════════════ -->
<div class="wpc-search-overlay js-search-overlay" id="wpc-search-overlay"
     role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Buscador', 'wp-clothing-theme' ); ?>"
     hidden>
    <div class="wpc-search-overlay__inner">
        <?php get_search_form(); ?>
        <button class="wpc-search-overlay__close js-search-close"
                aria-label="<?php esc_attr_e( 'Cerrar buscador', 'wp-clothing-theme' ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
</div>

<!-- ═══════════════════════════════════════════════
     MOBILE DRAWER
═══════════════════════════════════════════════ -->
<div class="wpc-mobile-drawer js-mobile-drawer" id="wpc-mobile-drawer"
     role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menú móvil', 'wp-clothing-theme' ); ?>"
     hidden>

    <div class="wpc-mobile-drawer__header">
        <span class="wpc-mobile-drawer__logo-text"><?php bloginfo( 'name' ); ?></span>
        <button class="wpc-mobile-drawer__close js-drawer-close"
                aria-label="<?php esc_attr_e( 'Cerrar menú', 'wp-clothing-theme' ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    <nav class="wpc-mobile-drawer__nav" aria-label="<?php esc_attr_e( 'Menú móvil', 'wp-clothing-theme' ); ?>">
        <?php
        wp_nav_menu( [
            'theme_location' => 'primary',
            'menu_class'     => 'wpc-mobile-nav',
            'container'      => false,
            'depth'          => 2,
            'fallback_cb'    => 'wpc_nav_fallback',
        ] );
        ?>
    </nav>

    <div class="wpc-mobile-drawer__footer">
        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>">
            <?php esc_html_e( 'Mi cuenta', 'wp-clothing-theme' ); ?>
        </a>
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
            <?php esc_html_e( 'Carrito', 'wp-clothing-theme' ); ?>
        </a>
    </div>
</div>
<div class="wpc-overlay-bg js-overlay-bg" aria-hidden="true"></div>

<!-- ═══════════════════════════════════════════════
     PAGE CONTENT STARTS
═══════════════════════════════════════════════ -->
<div id="content" class="site-content">
