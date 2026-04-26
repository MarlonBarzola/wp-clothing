<?php
/**
 * Content Product — catalog card override
 *
 * Renders each product as a .catalog-card BEM component.
 * Structure matches the SCSS in sass/components/_catalog-card.scss.
 *
 * @package WP_Clothing_Theme
 * @version 9.4.0  (WooCommerce template version this is based on)
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Guard: must be a valid, visible WooCommerce product.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
    return;
}

// ── Helper flags ─────────────────────────────────────────────────────────────
$is_new      = $product->get_date_created() && ( time() - $product->get_date_created()->getTimestamp() ) < ( 45 * DAY_IN_SECONDS );
$is_on_sale  = $product->is_on_sale();
$purchasable = $product->is_purchasable() && $product->is_in_stock();
$product_id  = $product->get_id();
$product_url = get_permalink( $product_id );

// ── Category eyebrow (first term) ────────────────────────────────────────────
$categories = get_the_terms( $product_id, 'product_cat' );
$cat_name   = ( ! empty( $categories ) && ! is_wp_error( $categories ) )
    ? esc_html( $categories[0]->name )
    : '';

// ── Add-to-cart attributes for AJAX ──────────────────────────────────────────
$add_to_cart_text = $product->add_to_cart_text();
$add_to_cart_url  = $product->add_to_cart_url();
$btn_classes      = implode( ' ', array_filter( [
    'catalog-card__add-btn',
    'add_to_cart_button',
    $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() ? 'ajax_add_to_cart' : '',
] ) );
?>

<li <?php wc_product_class( 'catalog-card', $product ); ?>>

    <?php // ── Image zone ───────────────────────────────────────────────── ?>
    <div class="catalog-card__img-wrap">

        <?php // Product image (links to single product page) ?>
        <a href="<?php echo esc_url( $product_url ); ?>"
           tabindex="-1"
           aria-hidden="true">
            <?php
            if ( has_post_thumbnail() ) {
                the_post_thumbnail( 'wpc-product', [
                    'class' => 'catalog-card__img',
                    'alt'   => get_the_title(),
                ] );
            } else {
                echo wc_placeholder_img( 'wpc-product', [ 'class' => 'catalog-card__img' ] );
            }
            ?>
        </a>

        <?php // "Nuevo" circular badge ?>
        <?php if ( $is_new ) : ?>
            <span class="catalog-card__badge-new" aria-label="<?php esc_attr_e( 'Producto nuevo', 'wp-clothing-theme' ); ?>">
                <?php esc_html_e( 'Nuevo', 'wp-clothing-theme' ); ?>
            </span>
        <?php endif; ?>

        <?php // Sale badge (uses WooCommerce's own flash so i18n is consistent) ?>
        <?php if ( $is_on_sale && ! $is_new ) : ?>
            <?php woocommerce_show_product_loop_sale_flash(); ?>
        <?php endif; ?>

        <?php // Wave "Añadir" overlay button ?>
        <?php if ( $purchasable ) : ?>
            <a href="<?php echo esc_url( $add_to_cart_url ); ?>"
               class="<?php echo esc_attr( $btn_classes ); ?>"
               data-product_id="<?php echo esc_attr( $product_id ); ?>"
               data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
               data-quantity="1"
               aria-label="<?php echo esc_attr( sprintf(
                   /* translators: %s: product name */
                   __( 'Añadir "%s" al carrito', 'wp-clothing-theme' ),
                   get_the_title()
               ) ); ?>"
               rel="nofollow">
                <?php echo esc_html( $add_to_cart_text ); ?>
            </a>
        <?php elseif ( ! $product->is_in_stock() ) : ?>
            <span class="catalog-card__add-btn catalog-card__add-btn--oos" aria-disabled="true">
                <?php esc_html_e( 'Agotado', 'wp-clothing-theme' ); ?>
            </span>
        <?php endif; ?>

    </div><!-- /.catalog-card__img-wrap -->

    <?php // ── Card body ────────────────────────────────────────────────── ?>
    <div class="catalog-card__body">

        <?php if ( $cat_name ) : ?>
            <span class="catalog-card__category"><?php echo $cat_name; ?></span>
        <?php endif; ?>

        <h2 class="catalog-card__title">
            <a href="<?php echo esc_url( $product_url ); ?>">
                <?php the_title(); ?>
            </a>
        </h2>

        <div class="catalog-card__footer">
            <div class="catalog-card__price">
                <?php echo wp_kses_post( $product->get_price_html() ); ?>
            </div>

            <?php // YITH Wishlist button (renders automatically if plugin is active) ?>
            <?php do_action( 'yith_wcwl_add_to_wishlist', $product_id ); ?>
        </div>

    </div><!-- /.catalog-card__body -->

</li>
