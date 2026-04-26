<?php
/**
 * Footer Template
 *
 * 4-column layout:
 *   1. Brand  — logo + description + social icons
 *   2. Tienda — wp_nav_menu( footer-shop )
 *   3. Info   — wp_nav_menu( footer-info )
 *   4. Contacto — widget area OR customizer fallback
 *
 * Social links: Apariencia › Personalizar › Footer
 * Menus:        Apariencia › Menús (footer-shop / footer-info / footer-legal)
 */

defined( 'ABSPATH' ) || exit;

// ── Social SVG icons (inline, no icon library needed) ─────────────────────
$wpc_social_icons = [
    'instagram' => [
        'label' => 'Instagram',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
    ],
    'facebook'  => [
        'label' => 'Facebook',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
    ],
    'tiktok'    => [
        'label' => 'TikTok',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.32 6.32 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.78a4.85 4.85 0 0 1-1.01-.09z"/></svg>',
    ],
    'pinterest' => [
        'label' => 'Pinterest',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>',
    ],
    'twitter'   => [
        'label' => 'Twitter / X',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
    ],
    'youtube'   => [
        'label' => 'YouTube',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
    ],
];
?>

<footer class="wpc-footer" role="contentinfo">

    <?php /* ── Top grid ──────────────────────────────────────────────── */ ?>
    <div class="wpc-footer__top">

        <?php /* Col 1 — Brand */ ?>
        <div class="wpc-footer__col wpc-footer__col--brand">

            <?php if ( has_custom_logo() ) : ?>
                <div class="wpc-footer__logo"><?php the_custom_logo(); ?></div>
            <?php else : ?>
                <div class="wpc-footer__logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="wpc-footer__site-name">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php $wpc_desc = get_theme_mod( 'wpc_footer_desc', __( 'Tu tienda de moda favorita. Calidad y estilo en cada prenda.', 'wp-clothing-theme' ) ); ?>
            <?php if ( $wpc_desc ) : ?>
                <p class="wpc-footer__about"><?php echo wp_kses_post( $wpc_desc ); ?></p>
            <?php endif; ?>

            <?php
            // Render social icons
            $wpc_has_social = false;
            foreach ( array_keys( $wpc_social_icons ) as $wpc_key ) {
                if ( get_theme_mod( 'wpc_footer_' . $wpc_key, '' ) ) {
                    $wpc_has_social = true;
                    break;
                }
            }
            ?>
            <?php if ( $wpc_has_social ) : ?>
                <div class="wpc-footer__social">
                    <?php foreach ( $wpc_social_icons as $wpc_key => $wpc_data ) :
                        $wpc_url = get_theme_mod( 'wpc_footer_' . $wpc_key, '' );
                        if ( ! $wpc_url ) continue;
                    ?>
                        <a href="<?php echo esc_url( $wpc_url ); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="<?php echo esc_attr( $wpc_data['label'] ); ?>"
                           class="wpc-footer__social-link">
                            <?php echo $wpc_data['svg']; // phpcs:ignore WordPress.Security.EscapeOutput -- sanitized SVG ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div><!-- /.col--brand -->

        <?php /* Col 2 — Tienda */ ?>
        <?php if ( has_nav_menu( 'footer-shop' ) ) : ?>
            <nav class="wpc-footer__col" aria-label="<?php esc_attr_e( 'Links de tienda', 'wp-clothing-theme' ); ?>">
                <h4 class="wpc-footer__col-title"><?php esc_html_e( 'Tienda', 'wp-clothing-theme' ); ?></h4>
                <?php wp_nav_menu( [
                    'theme_location' => 'footer-shop',
                    'container'      => false,
                    'menu_class'     => 'wpc-footer__links',
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ] ); ?>
            </nav>
        <?php endif; ?>

        <?php /* Col 3 — Información */ ?>
        <?php if ( has_nav_menu( 'footer-info' ) ) : ?>
            <nav class="wpc-footer__col" aria-label="<?php esc_attr_e( 'Links de información', 'wp-clothing-theme' ); ?>">
                <h4 class="wpc-footer__col-title"><?php esc_html_e( 'Información', 'wp-clothing-theme' ); ?></h4>
                <?php wp_nav_menu( [
                    'theme_location' => 'footer-info',
                    'container'      => false,
                    'menu_class'     => 'wpc-footer__links',
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ] ); ?>
            </nav>
        <?php endif; ?>

        <?php /* Col 4 — Contacto (widget area o fallback) */ ?>
        <div class="wpc-footer__col">
            <?php if ( is_active_sidebar( 'footer-col-4' ) ) : ?>
                <?php dynamic_sidebar( 'footer-col-4' ); ?>
            <?php else : ?>
                <h4 class="wpc-footer__col-title"><?php esc_html_e( 'Contacto', 'wp-clothing-theme' ); ?></h4>
                <p class="wpc-footer__about">
                    <?php echo esc_html( get_theme_mod(
                        'wpc_footer_contact_text',
                        __( 'Estamos aquí para ayudarte. Escríbenos en cualquier momento.', 'wp-clothing-theme' )
                    ) ); ?>
                </p>
                <?php $wpc_email = get_theme_mod( 'wpc_footer_email', '' ); ?>
                <?php if ( $wpc_email ) : ?>
                    <a href="mailto:<?php echo esc_attr( $wpc_email ); ?>" class="wpc-footer__email">
                        <?php echo esc_html( $wpc_email ); ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div><!-- /.wpc-footer__top -->

    <?php /* ── Bottom bar ─────────────────────────────────────────────── */ ?>
    <div class="wpc-footer__bottom">
        <div class="wpc-footer__bottom__inner">

            <span class="wpc-footer__copyright">
                <?php echo wp_kses_post( get_theme_mod(
                    'wpc_footer_copyright',
                    '&copy; ' . gmdate( 'Y' ) . ' ' . esc_html( get_bloginfo( 'name' ) ) . '. Todos los derechos reservados.'
                ) ); ?>
            </span>

            <?php if ( has_nav_menu( 'footer-legal' ) ) : ?>
                <?php wp_nav_menu( [
                    'theme_location' => 'footer-legal',
                    'container'      => false,
                    'menu_class'     => 'wpc-footer__legal-links',
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ] ); ?>
            <?php endif; ?>

        </div>
    </div><!-- /.wpc-footer__bottom -->

</footer>

<?php wp_footer(); ?>
</body>
</html>
