<?php
/**
 * Related Products — PDP section override
 *
 * Wraps the related products loop inside .pdp-related so it inherits
 * the same catalog-card styles used in the shop archive.
 * The loop itself reuses:
 *   - woocommerce/loop/loop-start.php      → adds .catalog-grid__products
 *   - woocommerce/content-product.php      → renders .catalog-card BEM
 *
 * @package WP_Clothing_Theme
 * @see     https://woocommerce.com/document/template-structure/
 * @version 10.3.0  (based on)
 */

defined( 'ABSPATH' ) || exit;

if ( ! $related_products ) {
    return;
}

// Ensure remaining images are lazy-loaded
if ( function_exists( 'wp_increase_content_media_count' ) ) {
    $content_media_count = wp_increase_content_media_count( 0 );
    if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
        wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
    }
}

$heading = apply_filters(
    'woocommerce_product_related_products_heading',
    __( 'También te puede gustar', 'wp-clothing-theme' )
);
?>

<section class="pdp-related related products">

    <?php if ( $heading ) : ?>
        <header class="pdp-related__header">
            <h2 class="pdp-related__title">
                <span><?php esc_html_e( 'Colección', 'wp-clothing-theme' ); ?></span>
                <?php echo esc_html( $heading ); ?>
            </h2>
        </header>
    <?php endif; ?>

    <?php
    woocommerce_product_loop_start();

    foreach ( $related_products as $related_product ) {
        $post_object = get_post( $related_product->get_id() );
        setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore
        wc_get_template_part( 'content', 'product' );
    }

    woocommerce_product_loop_end();
    wp_reset_postdata();
    ?>

</section>
