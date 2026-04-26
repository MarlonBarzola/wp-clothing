<?php
/**
 * Product Loop Start — overrides WooCommerce default
 *
 * Adds .catalog-grid__products to the <ul> so our 3-column
 * SCSS grid fires on the WooCommerce product list.
 *
 * @package WP_Clothing_Theme
 */

defined( 'ABSPATH' ) || exit;
?>
<ul class="products catalog-grid__products columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?>">
