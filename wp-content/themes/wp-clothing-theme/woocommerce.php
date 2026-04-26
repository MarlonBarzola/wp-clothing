<?php
/**
 * WooCommerce master template router
 *
 * woocommerce.php sits at the theme root and WordPress routes ALL
 * WooCommerce requests through it — archives AND single products.
 * This file inspects the current request and delegates to the right
 * include so each page type gets its own layout.
 *
 * Route map:
 *   is_singular('product')  → woocommerce/single-product-layout.php
 *   everything else         → catalog 2-column layout (inline below)
 *
 * @package WP_Clothing_Theme
 */

defined( 'ABSPATH' ) || exit;

// ── Route: single product ─────────────────────────────────────────────────────
if ( is_singular( 'product' ) ) {
    get_header();

    // Remove related products from the hook — we render them manually
    // below the product container so they sit OUTSIDE the 2-col grid.
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

    ?>
    <div class="pdp-page">
        <div class="pdp-page__inner">
            <?php
            while ( have_posts() ) {
                the_post();
                wc_get_template_part( 'content', 'single-product' );
            }
            ?>
        </div>
    </div>

    <?php
    // ── Related products — full-width section below the product ──────────────
    // Gather the same args WooCommerce would use internally.
    global $product;
    if ( is_a( $product, 'WC_Product' ) ) {
        $related_ids = wc_get_related_products( $product->get_id(), apply_filters( 'woocommerce_related_products_limit', 4 ) );
        if ( ! empty( $related_ids ) ) {
            $related_products = array_filter(
                array_map( 'wc_get_product', $related_ids ),
                fn( $p ) => $p && $p->is_visible()
            );
            if ( ! empty( $related_products ) ) {
                wc_get_template(
                    'single-product/related.php',
                    [ 'related_products' => $related_products ]
                );
            }
        }
    }
    ?>

    <?php
    get_footer();
    return; // stop — do not fall through to the catalog layout
}

// ── Route: shop / category / tag / search archives ────────────────────────────
get_header();
?>

<div class="catalog-page">
    <div class="catalog-page__inner">

        <?php
        /**
         * Fire breadcrumbs + structured data above the 2-col layout.
         * We suppress the default content wrapper (already done in functions.php)
         * so only breadcrumb + WC_Structured_Data fire here.
         */
        do_action( 'woocommerce_before_main_content' );
        ?>

        <?php
        // ── Page title + meta ────────────────────────────────────────────────
        $page_title = woocommerce_page_title( false );
        $total      = wc_get_loop_prop( 'total' );
        ?>
        <header class="catalog-page__header">
            <h1 class="catalog-page__title">
                <span><?php esc_html_e( 'Colección', 'wp-clothing-theme' ); ?></span>
                <?php echo esc_html( $page_title ); ?>
            </h1>
            <?php if ( $total ) : ?>
                <p class="catalog-page__meta">
                    <?php
                    /* translators: %d: product count */
                    printf( esc_html( _n( '%d producto', '%d productos', $total, 'wp-clothing-theme' ) ), intval( $total ) );
                    ?>
                </p>
            <?php endif; ?>
        </header>

        <div class="catalog-layout">

            <?php // ── LEFT: Sidebar filters ─────────────────────────────── ?>
            <aside class="catalog-sidebar" id="catalog-sidebar" aria-label="<?php esc_attr_e( 'Filtros', 'wp-clothing-theme' ); ?>">
                <?php
                if ( is_active_sidebar( 'shop-sidebar' ) ) {
                    dynamic_sidebar( 'shop-sidebar' );
                } else {
                    // Fallback: render WooCommerce layered-nav widgets automatically
                    // (guides admin to add widgets if sidebar is empty)
                    ?>
                    <p class="catalog-sidebar__empty">
                        <?php esc_html_e( 'Añade widgets de filtro en Apariencia → Widgets → Shop Sidebar.', 'wp-clothing-theme' ); ?>
                    </p>
                    <?php
                }
                ?>
            </aside>

            <?php // ── RIGHT: Products grid ──────────────────────────────── ?>
            <div class="catalog-grid">

                <?php if ( woocommerce_product_loop() ) : ?>

                    <div class="catalog-grid__toolbar">
                        <span class="catalog-grid__count">
                            <?php woocommerce_result_count(); ?>
                        </span>
                        <div class="catalog-grid__order">
                            <?php woocommerce_catalog_ordering(); ?>
                        </div>
                    </div>

                    <?php
                    // ── Active filters bar (only when a filter is applied) ──
                    // woocommerce_before_shop_loop fires result_count (20) and
                    // catalog_ordering (30) by default — remove them first so
                    // only the active-filters notice renders here.
                    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
                    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
                    do_action( 'woocommerce_before_shop_loop' );

                    woocommerce_product_loop_start();

                    while ( have_posts() ) {
                        the_post();
                        wc_get_template_part( 'content', 'product' );
                    }

                    woocommerce_product_loop_end();

                    // ── Pagination ──────────────────────────────────────────
                    woocommerce_pagination();

                else : ?>

                    <div class="catalog-grid__empty">
                        <strong><?php esc_html_e( 'Sin resultados', 'wp-clothing-theme' ); ?></strong>
                        <?php esc_html_e( 'Prueba con otros filtros o vuelve al catálogo completo.', 'wp-clothing-theme' ); ?>
                    </div>

                <?php endif; ?>

                <?php do_action( 'woocommerce_after_main_content' ); ?>

            </div><!-- /.catalog-grid -->

        </div><!-- /.catalog-layout -->

    </div><!-- /.catalog-page__inner -->
</div><!-- /.catalog-page -->

<?php get_footer(); ?>
