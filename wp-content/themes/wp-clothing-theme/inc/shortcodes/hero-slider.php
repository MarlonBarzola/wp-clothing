<?php

/**
 * Shortcode: [wpc_hero_slider]
 *
 * Slides config (filterable via 'wpc_hero_slides'):
 *   image     (URL)      — background image
 *   eyebrow   (text)     — small uppercase label above title
 *   title     (HTML)     — main heading, supports <br>
 *   subtitle  (text)     — optional body copy
 *   btn1_text / btn1_url — primary CTA button
 *   btn2_text / btn2_url — secondary (outline) CTA button
 */
defined('ABSPATH') || exit;

add_shortcode('wpc_hero_slider', 'wpc_render_hero_slider');

function wpc_render_hero_slider(): string
{
    $slides = apply_filters('wpc_hero_slides', [
        [
            'image'     => WPC_ASSETS . '/images/hero-slide-1.jpg',
            'eyebrow'   => 'SPRING / 2026',
            'title'     => 'Nueva colección<br>limitada ya disponible',
            'subtitle'  => 'Prendas orgánicas, diseño atemporal.',
            'btn1_text' => 'Comprar ahora',
            'btn1_url'  => '/tienda',
            'btn2_text' => 'Ver colecciones',
            'btn2_url'  => '/product-category/shop-all',
        ],
        [
            'image'     => WPC_ASSETS . '/images/hero-slide-2.jpg',
            'eyebrow'   => 'NUEVOS INGRESOS',
            'title'     => 'Looks frescos<br>para esta temporada',
            'subtitle'  => 'Descubre los estilos más vendidos.',
            'btn1_text' => 'Ver novedades',
            'btn1_url'  => '/product-category/new-in',
            'btn2_text' => '',
            'btn2_url'  => '',
        ],
    ]);

    if (empty($slides)) {
        return '';
    }

    ob_start();
?>
    <div class="wpc-hero swiper js-hero-swiper" aria-label="<?php esc_attr_e('Carrusel principal', 'wp-clothing-theme'); ?>">
        <div class="swiper-wrapper">
            <?php foreach ($slides as $slide) :
                $image     = esc_url($slide['image'] ?? '');
                $eyebrow   = esc_html($slide['eyebrow'] ?? '');
                $title     = wp_kses($slide['title'] ?? '', ['br' => [], 'em' => [], 'strong' => []]);
                $subtitle  = esc_html($slide['subtitle'] ?? '');
                $btn1_text = esc_html($slide['btn1_text'] ?? '');
                $btn1_url  = esc_url($slide['btn1_url'] ?? '');
                $btn2_text = esc_html($slide['btn2_text'] ?? '');
                $btn2_url  = esc_url($slide['btn2_url'] ?? '');
            ?>
                <div class="swiper-slide wpc-hero__slide"
                    style="background-image: url('<?php echo $image; ?>');"
                    role="group">
                    <div class="wpc-hero__overlay" aria-hidden="true"></div>
                    <div class="wpc-hero__content">
                        <?php if ($eyebrow) : ?>
                            <span class="wpc-hero__eyebrow"><?php echo $eyebrow; ?></span>
                        <?php endif; ?>
                        <?php if ($title) : ?>
                            <h1 class="wpc-hero__title"><?php echo $title; ?></h1>
                        <?php endif; ?>
                        <?php if ($subtitle) : ?>
                            <p class="wpc-hero__subtitle"><?php echo $subtitle; ?></p>
                        <?php endif; ?>
                        <?php if ($btn1_text || $btn2_text) : ?>
                            <div class="wpc-hero__cta">
                                <?php if ($btn1_text) : ?>
                                    <a href="<?php echo $btn1_url; ?>" class="btn btn--primary btn--lg">
                                        <?php echo $btn1_text; ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($btn2_text) : ?>
                                    <a href="<?php echo $btn2_url; ?>" class="btn btn--outline btn--lg">
                                        <?php echo $btn2_text; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="swiper-button-prev" aria-label="<?php esc_attr_e('Slide anterior', 'wp-clothing-theme'); ?>"></div>
        <div class="swiper-button-next" aria-label="<?php esc_attr_e('Slide siguiente', 'wp-clothing-theme'); ?>"></div>
        <div class="swiper-pagination"></div>
    </div>
<?php
    return ob_get_clean();
}
