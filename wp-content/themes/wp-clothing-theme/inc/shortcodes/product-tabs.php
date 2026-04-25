<?php

/**
 * Shortcode: [wpc_product_tabs]
 *
 * Atts:
 *   number  — products per tab (default 8)
 *   orderby — date | popularity | rating | rand | price (default date)
 *   title   — section heading (default "Más vendidos")
 *
 * Tabs are defined via filter 'wpc_product_tabs_config':
 *   add_filter( 'wpc_product_tabs_config', function() {
 *       return [
 *           [ 'label' => 'Bebé',  'slug' => 'bebe'  ],
 *           [ 'label' => 'Niñas', 'slug' => 'ninas' ],
 *       ];
 *   });
 */
defined('ABSPATH') || exit;

add_shortcode('wpc_product_tabs', 'wpc_render_product_tabs');

function wpc_render_product_tabs(array $atts = []): string
{
    if (! function_exists('wc_get_products')) {
        return '';
    }

    $atts = shortcode_atts([
        'number'  => 8,
        'orderby' => 'date',
        'title'   => __('Más vendidos', 'wp-clothing-theme'),
    ], $atts, 'wpc_product_tabs');

    $tabs = apply_filters('wpc_product_tabs_config', [
        ['label' => __('Baby',  'wp-clothing-theme'), 'slug' => 'baby'],
        ['label' => __('Girls', 'wp-clothing-theme'), 'slug' => 'girls'],
        ['label' => __('Boys',  'wp-clothing-theme'), 'slug' => 'boys'],
    ]);

    if (empty($tabs)) {
        return '';
    }

    $number  = absint($atts['number']);
    $orderby = sanitize_key($atts['orderby']);

    $wc_orderby_map = [
        'date'       => ['orderby' => 'date',           'order' => 'DESC'],
        'popularity' => ['orderby' => 'meta_value_num', 'order' => 'DESC', 'meta_key' => 'total_sales'],
        'rating'     => ['orderby' => 'meta_value_num', 'order' => 'DESC', 'meta_key' => '_wc_average_rating'],
        'price'      => ['orderby' => 'meta_value_num', 'order' => 'ASC',  'meta_key' => '_price'],
        'rand'       => ['orderby' => 'rand',           'order' => 'ASC'],
    ];
    $order_args = $wc_orderby_map[$orderby] ?? $wc_orderby_map['date'];

    // Days threshold to show "Nuevo" badge
    $new_days = apply_filters( 'wpc_product_new_days', 30 );

    $shop_url = apply_filters( 'wpc_product_tabs_shop_url', get_permalink( wc_get_page_id( 'shop' ) ) );

    ob_start();
?>
    <section class="wpc-product-tabs">

        <div class="wpc-product-tabs__header">

            <?php if ( $atts['title'] ) : ?>
                <h2 class="wpc-product-tabs__title"><?php echo esc_html( $atts['title'] ); ?></h2>
            <?php endif; ?>

            <nav class="wpc-product-tabs__nav" role="tablist"
                 aria-label="<?php esc_attr_e( 'Categorias de productos', 'wp-clothing-theme' ); ?>">
                <?php foreach ( $tabs as $i => $tab ) :
                    $tab_id    = 'wpc-tab-'   . sanitize_html_class( $tab['slug'] );
                    $panel_id  = 'wpc-panel-' . sanitize_html_class( $tab['slug'] );
                    $is_active = ( $i === 0 );
                ?>
                <button class="wpc-product-tabs__tab<?php echo $is_active ? ' is-active' : ''; ?>"
                        role="tab"
                        id="<?php echo esc_attr( $tab_id ); ?>"
                        aria-controls="<?php echo esc_attr( $panel_id ); ?>"
                        aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                        data-tab="<?php echo esc_attr( $tab['slug'] ); ?>">
                    <?php echo esc_html( $tab['label'] ); ?>
                </button>
                <?php endforeach; ?>
            </nav>

            <?php if ( $shop_url ) : ?>
                <a class="wpc-product-tabs__view-all" href="<?php echo esc_url( $shop_url ); ?>">
                    <?php esc_html_e( 'Ver más', 'wp-clothing-theme' ); ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" width="14" height="14"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            <?php endif; ?>

        </div><!-- /.wpc-product-tabs__header -->

        <?php foreach ( $tabs as $i => $tab ) :
            $panel_id  = 'wpc-panel-' . sanitize_html_class( $tab['slug'] );
            $tab_id    = 'wpc-tab-'   . sanitize_html_class( $tab['slug'] );
            $swiper_id = 'wpc-swiper-' . sanitize_html_class( $tab['slug'] );
            $is_active = ( $i === 0 );

            $products = wc_get_products( array_merge( [
                'status'   => 'publish',
                'limit'    => $number,
                'category' => [ $tab['slug'] ],
            ], $order_args ) );
        ?>
        <div class="wpc-product-tabs__panel<?php echo $is_active ? ' is-active' : ''; ?>"
             id="<?php echo esc_attr( $panel_id ); ?>"
             role="tabpanel"
             aria-labelledby="<?php echo esc_attr( $tab_id ); ?>">

            <?php if ( empty( $products ) ) : ?>
                <p class="wpc-product-tabs__empty">
                    <?php esc_html_e( 'No hay productos en esta categoria aun.', 'wp-clothing-theme' ); ?>
                </p>
            <?php else : ?>

                <div class="wpc-product-tabs__carousel">

                    <div class="swiper wpc-tabs-swiper" id="<?php echo esc_attr( $swiper_id ); ?>">
                        <div class="swiper-wrapper">
                            <?php foreach ( $products as $product ) :
                                $pid        = $product->get_id();
                                $permalink  = esc_url( get_permalink( $pid ) );
                                $img_id     = $product->get_image_id();
                                $img_src    = $img_id
                                    ? wp_get_attachment_image_url( $img_id, 'wpc-product' )
                                    : wc_placeholder_img_src( 'wpc-product' );
                                $img_alt    = esc_attr( get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: $product->get_name() );
                                $name       = esc_html( $product->get_name() );
                                $price_html = $product->get_price_html();
                                $is_on_sale = $product->is_on_sale();
                                $date_created = $product->get_date_created();
                                $is_new     = $date_created && ( $date_created->getTimestamp() > strtotime( '-' . $new_days . ' days' ) );
                            ?>
                            <div class="swiper-slide">
                                <div class="wpc-product-card">

                                    <a class="wpc-product-card__image-wrap" href="<?php echo $permalink; ?>">
                                        <img src="<?php echo esc_url( $img_src ); ?>"
                                             alt="<?php echo $img_alt; ?>"
                                             loading="lazy">

                                        <?php if ( $is_on_sale ) : ?>
                                            <span class="wpc-badge wpc-badge--sale"><?php esc_html_e( 'Sale', 'wp-clothing-theme' ); ?></span>
                                        <?php elseif ( $is_new ) : ?>
                                            <span class="wpc-badge wpc-badge--new"><?php esc_html_e( 'Nuevo', 'wp-clothing-theme' ); ?></span>
                                        <?php endif; ?>

                                        <div class="wpc-product-card__overlay">
                                            <span class="wpc-product-card__overlay-btn">
                                                <?php esc_html_e( 'Seleccionar opciones', 'wp-clothing-theme' ); ?>
                                            </span>
                                        </div>
                                    </a>

                                    <div class="wpc-product-card__body">
                                        <h3 class="wpc-product-card__title">
                                            <a href="<?php echo $permalink; ?>"><?php echo $name; ?></a>
                                        </h3>
                                        <div class="wpc-product-card__price"><?php echo $price_html; ?></div>
                                    </div>

                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button class="wpc-tabs-nav wpc-tabs-nav--prev"
                            aria-label="<?php esc_attr_e( 'Productos anteriores', 'wp-clothing-theme' ); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button class="wpc-tabs-nav wpc-tabs-nav--next"
                            aria-label="<?php esc_attr_e( 'Productos siguientes', 'wp-clothing-theme' ); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>

                </div><!-- /.wpc-product-tabs__carousel -->

            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </section>
<?php
    return ob_get_clean();
}
