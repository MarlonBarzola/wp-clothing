<?php
/**
 * WPC_Nav_Walker
 *
 * Custom nav walker that adds:
 *  - Dropdown toggle buttons (accessible, keyboard-friendly)
 *  - ARIA attributes on sub-menus
 *  - `has-children` class on parent <li>
 *
 * Usage: pass 'walker' => new WPC_Nav_Walker() to wp_nav_menu().
 */

defined( 'ABSPATH' ) || exit;

class WPC_Nav_Walker extends Walker_Nav_Menu {

    /**
     * Start level — wraps a sub-menu <ul>.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent  = str_repeat( "\t", $depth );
        $classes = 'wpc-nav__dropdown';
        $output .= "\n{$indent}<ul class=\"{$classes}\" role=\"list\">\n";
    }

    /**
     * Start element — each <li> item.
     */
    public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
        $item = $data_object;

        $indent       = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        $classes      = empty( $item->classes ) ? [] : (array) $item->classes;
        $has_children = in_array( 'menu-item-has-children', $classes, true );

        if ( $has_children ) {
            $classes[] = 'has-children';
        }

        $class_names = implode( ' ', array_filter( array_map( 'sanitize_html_class', $classes ) ) );
        $id          = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
        $id_attr     = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= "{$indent}<li{$id_attr} class=\"wpc-nav__item {$class_names}\">";

        // Build the link / toggle
        $atts             = [];
        $atts['href']     = ! empty( $item->url ) ? esc_url( $item->url ) : '#';
        $atts['title']    = ! empty( $item->attr_title ) ? esc_attr( $item->attr_title ) : '';
        $atts['target']   = ! empty( $item->target ) ? esc_attr( $item->target ) : '';
        $atts['rel']      = ! empty( $item->xfn ) ? esc_attr( $item->xfn ) : '';
        $atts['class']    = 'wpc-nav__link';

        if ( $has_children ) {
            $atts['aria-haspopup'] = 'true';
            $atts['aria-expanded'] = 'false';
        }

        // Current page
        if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current-menu-ancestor', $classes, true ) ) {
            $atts['aria-current'] = 'page';
            $atts['class']       .= ' current-menu-item';
        }

        $atts_str = '';
        foreach ( $atts as $attr => $value ) {
            if ( $value !== '' ) {
                $atts_str .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title  = apply_filters( 'the_title', $item->title, $item->ID );
        $output .= "<a{$atts_str}><span>{$title}</span>";

        // Chevron icon inside the link for items with children (desktop)
        if ( $has_children && $depth === 0 ) {
            $output .= '<svg class="wpc-nav__chevron" xmlns="http://www.w3.org/2000/svg" '
                     . 'width="12" height="12" viewBox="0 0 24 24" fill="none" '
                     . 'stroke="currentColor" stroke-width="2.5" aria-hidden="true">'
                     . '<polyline points="6 9 12 15 18 9"/></svg>';
        }

        $output .= '</a>';

        // Accessible toggle button for mobile / keyboard (hidden on desktop via CSS)
        if ( $has_children ) {
            $output .= '<button class="wpc-nav__toggle" '
                     . 'aria-expanded="false" '
                     . 'aria-label="' . esc_attr( sprintf( __( 'Expandir %s', 'wp-clothing-theme' ), $title ) ) . '">'
                     . '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" '
                     . 'fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">'
                     . '<polyline points="6 9 12 15 18 9"/></svg>'
                     . '</button>';
        }
    }

    /**
     * End element.
     */
    public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}

/**
 * Fallback when no menu is assigned — shows a link to the menu editor.
 */
function wpc_nav_fallback( array $args ): void {
    if ( current_user_can( 'manage_options' ) ) {
        echo '<ul class="wpc-nav"><li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">'
           . esc_html__( '+ Asignar menú', 'wp-clothing-theme' )
           . '</a></li></ul>';
    }
}
