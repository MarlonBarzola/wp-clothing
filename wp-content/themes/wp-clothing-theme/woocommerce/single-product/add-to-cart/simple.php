<?php
/**
 * Simple product add to cart — PDP override
 *
 * Renders:
 *   · Stock status line
 *   · .pdp-actions row  →  quantity stepper  +  scalloped CTA button  +  wishlist
 *   · .pdp-trust        →  Trust badges (shipping + returns)
 *
 * @package WP_Clothing_Theme
 * @see     https://woocommerce.com/document/template-structure/
 * @version 10.2.0  (based on)
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
    return;
}

echo wc_get_stock_html( $product );

if ( ! $product->is_in_stock() ) {
    return;
}

do_action( 'woocommerce_before_add_to_cart_form' );
?>

<form class="cart pdp-cart-form"
      action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>"
      method="post"
      enctype="multipart/form-data">

    <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

    <div class="pdp-actions">

        <?php // ── Quantity stepper ─────────────────────────────────────── ?>
        <div class="pdp-actions__qty">
            <?php
            do_action( 'woocommerce_before_add_to_cart_quantity' );

            woocommerce_quantity_input( [
                'min_value'   => $product->get_min_purchase_quantity(),
                'max_value'   => $product->get_max_purchase_quantity(),
                'input_value' => isset( $_POST['quantity'] )
                    ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) )
                    : $product->get_min_purchase_quantity(),
            ] );

            do_action( 'woocommerce_after_add_to_cart_quantity' );
            ?>
        </div>

        <?php // ── Scalloped CTA button ──────────────────────────────────── ?>
        <button type="submit"
                name="add-to-cart"
                value="<?php echo esc_attr( $product->get_id() ); ?>"
                class="pdp-actions__cta single_add_to_cart_button button alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>">
            <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
        </button>

        <?php // ── Wishlist icon (YITH or fallback) ──────────────────────── ?>
        <div class="pdp-actions__wishlist">
            <?php if ( function_exists( 'YITH_WCWL' ) ) :
                echo do_shortcode( '[yith_wcwl_add_to_wishlist icon="heart" label=""]' );
            else : ?>
                <button type="button"
                        class="pdp-wishlist-btn"
                        aria-label="<?php esc_attr_e( 'Añadir a favoritos', 'wp-clothing-theme' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="22" height="22" aria-hidden="true">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </button>
            <?php endif; ?>
        </div>

    </div><!-- /.pdp-actions -->

    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

</form>

<?php // ── Trust badges ──────────────────────────────────────────────────── ?>
<div class="pdp-trust" aria-label="<?php esc_attr_e( 'Información de envío y devoluciones', 'wp-clothing-theme' ); ?>">

    <div class="pdp-trust__item">
        <span class="pdp-trust__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 5v3h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </span>
        <div class="pdp-trust__text">
            <strong><?php esc_html_e( 'Envío gratis', 'wp-clothing-theme' ); ?></strong>
            <span><?php esc_html_e( 'Entrega estimada: 3–5 días hábiles', 'wp-clothing-theme' ); ?></span>
        </div>
    </div>

    <div class="pdp-trust__item">
        <span class="pdp-trust__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                <polyline points="1 4 1 10 7 10"/>
                <path d="M3.51 15a9 9 0 1 0 .49-4.95"/>
            </svg>
        </span>
        <div class="pdp-trust__text">
            <strong><?php esc_html_e( 'Devoluciones fáciles', 'wp-clothing-theme' ); ?></strong>
            <span><?php esc_html_e( '30 días para cambios o reembolso', 'wp-clothing-theme' ); ?></span>
        </div>
    </div>

</div><!-- /.pdp-trust -->

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
