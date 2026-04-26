<?php
/**
 * Product quantity inputs — PDP stepper override
 *
 * Replaces the plain <input> with a − / value / + stepper.
 * JS is inline (no extra file). Styles live in _product.scss.
 *
 * @package WP_Clothing_Theme
 * @see     https://woocommerce.com/document/template-structure/
 * @version 10.1.0  (based on)
 *
 * @var string $input_id
 * @var string $input_name
 * @var string|int $input_value
 * @var string|int $min_value
 * @var string|int $max_value
 * @var string|int $step
 * @var string $placeholder
 * @var string $inputmode
 * @var bool   $readonly
 * @var string $type
 * @var array  $classes
 */

defined( 'ABSPATH' ) || exit;

$label = ! empty( $args['product_name'] )
    ? sprintf( esc_html__( '%s quantity', 'woocommerce' ), wp_strip_all_tags( $args['product_name'] ) )
    : esc_html__( 'Quantity', 'woocommerce' );
?>

<div class="quantity pdp-qty">

    <?php do_action( 'woocommerce_before_quantity_input_field' ); ?>

    <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>">
        <?php echo esc_attr( $label ); ?>
    </label>

    <button type="button"
            class="pdp-qty__btn pdp-qty__btn--minus"
            aria-label="<?php esc_attr_e( 'Reducir cantidad', 'wp-clothing-theme' ); ?>"
            data-target="<?php echo esc_attr( $input_id ); ?>">
        <span aria-hidden="true">−</span>
    </button>

    <input
        type="<?php echo esc_attr( $type ); ?>"
        <?php echo $readonly ? 'readonly="readonly"' : ''; ?>
        id="<?php echo esc_attr( $input_id ); ?>"
        class="<?php echo esc_attr( join( ' ', (array) $classes ) ); ?> pdp-qty__input"
        name="<?php echo esc_attr( $input_name ); ?>"
        value="<?php echo esc_attr( $input_value ); ?>"
        aria-label="<?php esc_attr_e( 'Product quantity', 'woocommerce' ); ?>"
        min="<?php echo esc_attr( $min_value ); ?>"
        <?php if ( 0 < $max_value ) : ?>
            max="<?php echo esc_attr( $max_value ); ?>"
        <?php endif; ?>
        <?php if ( ! $readonly ) : ?>
            step="<?php echo esc_attr( $step ); ?>"
            placeholder="<?php echo esc_attr( $placeholder ); ?>"
            inputmode="<?php echo esc_attr( $inputmode ); ?>"
            autocomplete="<?php echo esc_attr( $autocomplete ?? 'on' ); ?>"
        <?php endif; ?>
    />

    <button type="button"
            class="pdp-qty__btn pdp-qty__btn--plus"
            aria-label="<?php esc_attr_e( 'Aumentar cantidad', 'wp-clothing-theme' ); ?>"
            data-target="<?php echo esc_attr( $input_id ); ?>">
        <span aria-hidden="true">+</span>
    </button>

    <?php do_action( 'woocommerce_after_quantity_input_field' ); ?>

</div><!-- /.pdp-qty -->

<script>
( function () {
    'use strict';
    document.querySelectorAll( '.pdp-qty__btn' ).forEach( function ( btn ) {
        btn.addEventListener( 'click', function () {
            var input = document.getElementById( btn.dataset.target );
            if ( ! input ) return;
            var val  = parseInt( input.value, 10 ) || 1;
            var min  = parseInt( input.min,   10 ) || 1;
            var max  = parseInt( input.max,   10 ) || Infinity;
            var step = parseInt( input.step,  10 ) || 1;

            if ( btn.classList.contains( 'pdp-qty__btn--plus' ) ) {
                input.value = Math.min( val + step, max );
            } else {
                input.value = Math.max( val - step, min );
            }
            // Trigger WooCommerce change event for AJAX add-to-cart
            input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
        } );
    } );
} )();
</script>
