<?php
/**
 * Single Product Image — Gallery with vertical thumbnail strip on the left
 *
 * Layout:
 *   .pdp-gallery
 *     ├── .pdp-gallery__thumbs   (vertical strip, left)
 *     └── .pdp-gallery__main     (large image, right)
 *
 * Thumbnails are driven by WooCommerce's product_gallery_image_ids.
 * Clicking a thumb swaps the main image via a simple data-src swap (no jQuery
 * dependency beyond what WC already loads).
 *
 * @package WP_Clothing_Theme
 * @see     https://woocommerce.com/document/template-structure/
 * @version 10.5.0  (based on)
 */

defined( 'ABSPATH' ) || exit;

global $product;

$main_image_id  = $product->get_image_id();
$gallery_ids    = $product->get_gallery_image_ids();
$all_image_ids  = $main_image_id ? array_merge( [ $main_image_id ], $gallery_ids ) : $gallery_ids;

// Bail gracefully when there's nothing to show
if ( empty( $all_image_ids ) ) {
    echo wc_placeholder_img( 'woocommerce_single' );
    return;
}

// Build the main (first) image src
$main_src    = wp_get_attachment_image_url( $all_image_ids[0], 'wpc-product' );
$main_srcset = wp_get_attachment_image_srcset( $all_image_ids[0], 'wpc-product' );
$main_alt    = get_post_meta( $all_image_ids[0], '_wp_attachment_image_alt', true ) ?: get_the_title();
?>

<div class="pdp-gallery woocommerce-product-gallery" itemscope itemtype="https://schema.org/ImageGallery">

    <?php if ( count( $all_image_ids ) > 1 ) : ?>
    <div class="pdp-gallery__thumbs" role="list" aria-label="<?php esc_attr_e( 'Galería de imágenes', 'wp-clothing-theme' ); ?>">
        <?php foreach ( $all_image_ids as $i => $img_id ) :
            $thumb_src  = wp_get_attachment_image_url( $img_id, 'thumbnail' );
            $full_src   = wp_get_attachment_image_url( $img_id, 'wpc-product' );
            $full_srcset = wp_get_attachment_image_srcset( $img_id, 'wpc-product' );
            $alt        = get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: get_the_title();
            ?>
            <button class="pdp-gallery__thumb <?php echo $i === 0 ? 'is-active' : ''; ?>"
                    type="button"
                    role="listitem"
                    data-src="<?php echo esc_url( $full_src ); ?>"
                    data-srcset="<?php echo esc_attr( $full_srcset ); ?>"
                    data-alt="<?php echo esc_attr( $alt ); ?>"
                    aria-label="<?php echo esc_attr( sprintf( __( 'Ver imagen %d', 'wp-clothing-theme' ), $i + 1 ) ); ?>"
                    aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>">
                <img src="<?php echo esc_url( $thumb_src ); ?>"
                     alt="<?php echo esc_attr( $alt ); ?>"
                     loading="lazy"
                     width="80"
                     height="100">
            </button>
        <?php endforeach; ?>
    </div><!-- /.pdp-gallery__thumbs -->
    <?php endif; ?>

    <div class="pdp-gallery__main">
        <?php
        // Sale flash inside gallery (WooCommerce convention)
        if ( $product->is_on_sale() ) {
            woocommerce_show_product_sale_flash();
        }
        ?>
        <figure class="pdp-gallery__figure" itemprop="associatedMedia" itemscope itemtype="https://schema.org/ImageObject">
            <a href="<?php echo esc_url( wp_get_attachment_url( $main_image_id ) ); ?>"
               class="pdp-gallery__zoom"
               data-elementor-open-lightbox="no"
               itemprop="contentUrl">
                <img id="pdp-main-image"
                     class="pdp-gallery__img wp-post-image"
                     src="<?php echo esc_url( $main_src ); ?>"
                     <?php if ( $main_srcset ) : ?>
                     srcset="<?php echo esc_attr( $main_srcset ); ?>"
                     sizes="(max-width: 768px) 100vw, 50vw"
                     <?php endif; ?>
                     alt="<?php echo esc_attr( $main_alt ); ?>"
                     itemprop="thumbnail"
                     width="600"
                     height="750">
            </a>
        </figure>
    </div><!-- /.pdp-gallery__main -->

</div><!-- /.pdp-gallery -->

<script>
( function () {
    'use strict';
    var thumbs = document.querySelectorAll( '.pdp-gallery__thumb' );
    var mainImg = document.getElementById( 'pdp-main-image' );
    if ( ! thumbs.length || ! mainImg ) return;

    thumbs.forEach( function ( btn ) {
        btn.addEventListener( 'click', function () {
            // Swap image
            mainImg.src = btn.dataset.src;
            if ( btn.dataset.srcset ) mainImg.srcset = btn.dataset.srcset;
            if ( btn.dataset.alt )    mainImg.alt    = btn.dataset.alt;

            // Active state
            thumbs.forEach( function ( b ) {
                b.classList.remove( 'is-active' );
                b.setAttribute( 'aria-pressed', 'false' );
            } );
            btn.classList.add( 'is-active' );
            btn.setAttribute( 'aria-pressed', 'true' );
        } );
    } );
} )();
</script>
