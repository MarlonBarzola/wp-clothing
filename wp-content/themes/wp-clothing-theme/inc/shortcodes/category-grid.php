<?php

/**
 * Shortcode: [wpc_category_grid]
 *
 * Atts:
 *   number  — max categories to show (default 4)
 *   parent  — parent category ID (default 0 = top-level only)
 *   orderby — name | count | slug (default name)
 *   ids     — comma-separated category IDs to show specific ones
 */
defined('ABSPATH') || exit;

add_shortcode('wpc_category_grid', 'wpc_render_category_grid');

function wpc_render_category_grid(array $atts = []): string
{
    $atts = shortcode_atts([
        'number'  => 4,
        'parent'  => 0,
        'orderby' => 'name',
        'ids'     => '',
    ], $atts, 'wpc_category_grid');

    $args = [
        'taxonomy'   => 'product_cat',
        'orderby'    => sanitize_key($atts['orderby']),
        'order'      => 'ASC',
        'hide_empty' => false,
        'parent'     => absint($atts['parent']),
        'number'     => absint($atts['number']),
        'exclude'    => get_option('default_product_cat'),
    ];

    if (! empty($atts['ids'])) {
        $args['include'] = array_map('absint', explode(',', $atts['ids']));
        unset($args['parent'], $args['number']);
    }

    $categories = get_terms($args);

    if (is_wp_error($categories) || empty($categories)) {
        return '';
    }

    ob_start();
?>
    <div class="wpc-category-grid">
        <?php $wpc_card_idx = 0; foreach ($categories as $cat) :
            $wpc_card_idx++;
            $link      = esc_url(get_term_link($cat));
            $name      = esc_html($cat->name);
            $thumb_id  = get_term_meta($cat->term_id, 'thumbnail_id', true);
            $thumb_url = $thumb_id
                ? wp_get_attachment_image_url($thumb_id, 'wpc-category')
                : wc_placeholder_img_src('wpc-category');
            $thumb_alt = $thumb_id
                ? esc_attr(get_post_meta($thumb_id, '_wp_attachment_image_alt', true) ?: $name)
                : esc_attr($name);
        ?>
            <a class="wpc-category-card"
               data-scroll="fade-up"
               data-scroll-delay="<?php echo $wpc_card_idx; ?>"
               href="<?php echo $link; ?>"
               aria-label="<?php echo $name; ?>">
                <div class="wpc-category-card__image">
                    <img src="<?php echo esc_url($thumb_url); ?>"
                        alt="<?php echo $thumb_alt; ?>"
                        loading="lazy">
                </div>
                <span class="wpc-category-card__label"><?php echo $name; ?></span>
                <span class="wpc-category-card__cta"><?php esc_html_e('Ver todo', 'wp-clothing-theme'); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
<?php
    return ob_get_clean();
}
