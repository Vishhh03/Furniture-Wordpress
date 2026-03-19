<?php
/**
 * Furniture Sales – functions.php
 */

// ── Enqueue Styles & Fonts ──────────────────────────────────────────────────
function furniture_sales_scripts() {
    // Theme stylesheet
    wp_enqueue_style( 'furniture-style', get_stylesheet_uri(), array(), '2.0' );

    // Google Fonts: Lexend Deca (headings & body)
    wp_enqueue_style(
        'furniture-fonts',
        'https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap',
        array(),
        null
    );
}
add_action( 'wp_enqueue_scripts', 'furniture_sales_scripts' );

// ── Theme Support ───────────────────────────────────────────────────────────
function furniture_setup_theme() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'gallery', 'caption' ) );

    // Register nav menu for future use
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'furniture-sales' ),
    ) );
}
add_action( 'after_setup_theme', 'furniture_setup_theme' );

function furniture_get_shop_page_url() {
    if ( class_exists( 'WooCommerce' ) ) {
        $shop_url = wc_get_page_permalink( 'shop' );
        if ( $shop_url ) {
            return $shop_url;
        }
    }

    return home_url( '/shop/' );
}

function furniture_is_product_search() {
    if ( ! is_search() ) {
        return false;
    }

    $post_type = get_query_var( 'post_type' );

    if ( is_array( $post_type ) ) {
        return in_array( 'product', $post_type, true );
    }

    if ( 'product' === $post_type ) {
        return true;
    }

    if ( isset( $_GET['post_type'] ) ) {
        return 'product' === sanitize_text_field( wp_unslash( $_GET['post_type'] ) );
    }

    return false;
}

function furniture_get_product_search_form_markup( $query = '' ) {
    $query = '' === $query ? get_search_query() : $query;

    ob_start();
    ?>
    <form role="search" method="get" class="catalog-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <label class="screen-reader-text" for="furniture-product-search"><?php esc_html_e( 'Search products', 'furniture-sales' ); ?></label>
        <input type="search" id="furniture-product-search" class="catalog-search-input" placeholder="<?php esc_attr_e( 'Search products', 'furniture-sales' ); ?>" value="<?php echo esc_attr( $query ); ?>" name="s" />
        <input type="hidden" name="post_type" value="product" />
        <button type="submit" class="catalog-search-button"><?php esc_html_e( 'Search', 'furniture-sales' ); ?></button>
    </form>
    <?php

    return ob_get_clean();
}

function furniture_get_catalog_filter_state() {
    $state = array(
        'product_cat' => '',
        'min_price'   => '',
        'max_price'   => '',
        'in_stock'    => '',
        'on_sale'     => '',
        'per_page'    => '',
    );

    if ( isset( $_GET['product_cat'] ) ) {
        $state['product_cat'] = sanitize_title( wp_unslash( $_GET['product_cat'] ) );
    }
    if ( isset( $_GET['min_price'] ) ) {
        $min_raw = trim( sanitize_text_field( wp_unslash( $_GET['min_price'] ) ) );
        $state['min_price'] = '' !== $min_raw ? absint( $min_raw ) : '';
    }
    if ( isset( $_GET['max_price'] ) ) {
        $max_raw = trim( sanitize_text_field( wp_unslash( $_GET['max_price'] ) ) );
        $state['max_price'] = '' !== $max_raw ? absint( $max_raw ) : '';
    }
    if ( isset( $_GET['in_stock'] ) ) {
        $state['in_stock'] = '1' === wp_unslash( $_GET['in_stock'] ) ? '1' : '';
    }
    if ( isset( $_GET['on_sale'] ) ) {
        $state['on_sale'] = '1' === wp_unslash( $_GET['on_sale'] ) ? '1' : '';
    }
    if ( isset( $_GET['per_page'] ) ) {
        $per_page_raw = trim( sanitize_text_field( wp_unslash( $_GET['per_page'] ) ) );
        $state['per_page'] = '' !== $per_page_raw ? absint( $per_page_raw ) : '';
    }

    return $state;
}

function furniture_apply_catalog_filters_to_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $is_product_archive = $query->is_post_type_archive( 'product' ) || $query->is_tax( array( 'product_cat', 'product_tag' ) );
    $query_post_type    = $query->get( 'post_type' );
    $is_product_search  = $query->is_search() && ( 'product' === $query_post_type || ( is_array( $query_post_type ) && in_array( 'product', $query_post_type, true ) ) );

    if ( ! $is_product_archive && ! $is_product_search ) {
        return;
    }

    $state = furniture_get_catalog_filter_state();

    if ( ! empty( $state['per_page'] ) && in_array( $state['per_page'], array( 12, 24, 36 ), true ) ) {
        $query->set( 'posts_per_page', $state['per_page'] );
    }

    if ( ! empty( $state['product_cat'] ) ) {
        $tax_query   = (array) $query->get( 'tax_query' );
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array( $state['product_cat'] ),
        );
        $query->set( 'tax_query', $tax_query );
    }

    $meta_query = (array) $query->get( 'meta_query' );

    if ( ! empty( $state['in_stock'] ) ) {
        $meta_query[] = array(
            'key'   => '_stock_status',
            'value' => 'instock',
        );
    }

    if ( '' !== $state['min_price'] && '' !== $state['max_price'] && $state['max_price'] >= $state['min_price'] ) {
        $meta_query[] = array(
            'key'     => '_price',
            'value'   => array( $state['min_price'], $state['max_price'] ),
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        );
    } elseif ( '' !== $state['min_price'] ) {
        $meta_query[] = array(
            'key'     => '_price',
            'value'   => $state['min_price'],
            'compare' => '>=',
            'type'    => 'NUMERIC',
        );
    } elseif ( '' !== $state['max_price'] ) {
        $meta_query[] = array(
            'key'     => '_price',
            'value'   => $state['max_price'],
            'compare' => '<=',
            'type'    => 'NUMERIC',
        );
    }

    if ( ! empty( $meta_query ) ) {
        $query->set( 'meta_query', $meta_query );
    }

    if ( ! empty( $state['on_sale'] ) && function_exists( 'wc_get_product_ids_on_sale' ) ) {
        $sale_ids = wc_get_product_ids_on_sale();
        $sale_ids = ! empty( $sale_ids ) ? $sale_ids : array( 0 );
        $query->set( 'post__in', $sale_ids );
    }
}
add_action( 'pre_get_posts', 'furniture_apply_catalog_filters_to_query' );

function furniture_get_featured_product_id() {
    $default_product_id = 15;
    $product_id         = absint( get_theme_mod( 'furniture_featured_product_id', $default_product_id ) );

    return $product_id ? $product_id : $default_product_id;
}

function furniture_get_brass_addon_price() {
    return absint( get_theme_mod( 'furniture_brass_addon_price', 3000 ) );
}

function furniture_get_coverups_addon_price() {
    return absint( get_theme_mod( 'furniture_coverups_addon_price', 5000 ) );
}

function furniture_get_product_base_prices( $product ) {
    if ( ! $product instanceof WC_Product ) {
        return array(
            'regular' => 54999,
            'current' => 54999,
        );
    }

    $regular_price = (float) $product->get_regular_price();
    $sale_price    = (float) $product->get_sale_price();
    $current_price = (float) $product->get_price();

    // Prefer valid sale price to avoid stale _price meta showing wrong current price.
    if ( $sale_price > 0 && ( $regular_price <= 0 || $sale_price < $regular_price ) ) {
        $current_price = $sale_price;
    }

    if ( ! $current_price && $regular_price ) {
        $current_price = $regular_price;
    }

    if ( ! $regular_price ) {
        $regular_price = $current_price;
    }

    if ( ! $regular_price && ! $current_price ) {
        $regular_price = 54999;
        $current_price = 54999;
    }

    return array(
        'regular' => $regular_price,
        'current' => $current_price,
    );
}

function furniture_get_price_display_html( $current_price, $regular_price = 0 ) {
    $current_price = (float) $current_price;
    $regular_price = (float) $regular_price;

    if ( $regular_price > $current_price && $current_price > 0 ) {
        return '<del>' . wc_price( $regular_price ) . '</del> <ins>' . wc_price( $current_price ) . '</ins>';
    }

    return wc_price( $current_price > 0 ? $current_price : $regular_price );
}

function furniture_normalize_equal_sale_price_html( $price_html, $product ) {
    if ( ! $product instanceof WC_Product ) {
        return $price_html;
    }

    $regular_price = (float) $product->get_regular_price();
    $sale_price    = (float) $product->get_sale_price();

    if ( $regular_price > 0 && $sale_price > 0 && abs( $regular_price - $sale_price ) < 0.0001 ) {
        return wc_price( $sale_price );
    }

    return $price_html;
}
add_filter( 'woocommerce_get_price_html', 'furniture_normalize_equal_sale_price_html', 20, 2 );

function furniture_sync_product_active_price_meta( $product_id ) {
    if ( 'product' !== get_post_type( $product_id ) ) {
        return;
    }

    $regular_price = get_post_meta( $product_id, '_regular_price', true );
    $sale_price    = get_post_meta( $product_id, '_sale_price', true );

    $regular = '' !== $regular_price ? (float) $regular_price : 0;
    $sale    = '' !== $sale_price ? (float) $sale_price : 0;

    $active_price = $regular;
    if ( $sale > 0 && ( $regular <= 0 || $sale < $regular ) ) {
        $active_price = $sale;
    }

    update_post_meta( $product_id, '_price', wc_format_decimal( $active_price ) );
}
add_action( 'save_post_product', 'furniture_sync_product_active_price_meta', 20, 1 );

function furniture_fix_typo_product_slug_once() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    $typo_product    = get_page_by_path( 'furniture-prodect', OBJECT, 'product' );
    $correct_product = get_page_by_path( 'furniture-product', OBJECT, 'product' );

    if ( $typo_product && ! $correct_product ) {
        wp_update_post(
            array(
                'ID'        => $typo_product->ID,
                'post_name' => 'furniture-product',
            )
        );
    }

    update_option( 'furniture_fixed_typo_product_slug', gmdate( 'c' ) );
}
add_action( 'init', 'furniture_fix_typo_product_slug_once', 25 );

function furniture_fix_typo_product_text_once() {
    $product_ids = get_posts(
        array(
            'post_type'      => 'product',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        )
    );

    foreach ( $product_ids as $product_id ) {
        $post = get_post( $product_id );
        if ( ! $post ) {
            continue;
        }

        $updated = array( 'ID' => $product_id );
        $changed = false;

        if ( false !== stripos( $post->post_title, 'prodect' ) ) {
            $updated['post_title'] = str_ireplace( 'prodect', 'product', $post->post_title );
            $changed = true;
        }

        if ( false !== stripos( $post->post_excerpt, 'prodect' ) ) {
            $updated['post_excerpt'] = str_ireplace( 'prodect', 'product', $post->post_excerpt );
            $changed = true;
        }

        if ( false !== stripos( $post->post_content, 'prodect' ) ) {
            $updated['post_content'] = str_ireplace( 'prodect', 'product', $post->post_content );
            $changed = true;
        }

        if ( $changed ) {
            wp_update_post( $updated );
        }
    }

    update_option( 'furniture_fixed_typo_product_text', gmdate( 'c' ) );
}
add_action( 'init', 'furniture_fix_typo_product_text_once', 26 );

function furniture_redirect_typo_product_slug() {
    if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
        return;
    }

    $request_path = trim( wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ), '/' );

    if ( 'product/furniture-prodect' === strtolower( $request_path ) ) {
        wp_safe_redirect( home_url( '/product/furniture-product/' ), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'furniture_redirect_typo_product_slug', 5 );

// ── Override Site Name & Tagline ────────────────────────────────────────────
add_filter( 'option_blogname', function( $value ) {
    return 'Rest & Revel';
} );

add_filter( 'option_blogdescription', function( $value ) {
    return 'A modern approach to comfort.';
} );

// ── AJAX Cart Count Fragment (updates cart badge without reload) ─────────
function furniture_cart_count_fragment( $fragments ) {
    if ( class_exists( 'WooCommerce' ) ) {
        $fragments['.cart-count'] = '<span class="cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
    }
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'furniture_cart_count_fragment' );

// ── WooCommerce: Declare HPOS Compatibility ─────────────────────────────────
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

// ── Theme Customizer (Editable Images) ──────────────────────────────────────
function furniture_customize_register( $wp_customize ) {
    // Hero Section
    $wp_customize->add_section( 'furniture_hero', array(
        'title'    => __( 'Hero Section', 'furniture-sales' ),
        'priority' => 30,
    ) );

    $wp_customize->add_setting( 'hero_image', array(
        'default'   => get_template_directory_uri() . '/assets/images/hero_bed.png',
        'transport' => 'refresh',
    ) );

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hero_image', array(
        'label'    => __( 'Hero Image', 'furniture-sales' ),
        'section'  => 'furniture_hero',
        'settings' => 'hero_image',
    ) ) );

    // Homepage Images Section
    $wp_customize->add_section( 'furniture_homepage_images', array(
        'title'    => __( 'Homepage Images', 'furniture-sales' ),
        'priority' => 31,
    ) );

    $settings = array(
        'product_image_white' => array(
            'label'   => 'Configurator Image (White)',
            'default' => get_template_directory_uri() . '/assets/images/product_bed.png',
        ),
        'product_image_black' => array(
            'label'   => 'Configurator Image (Black)',
            'default' => get_template_directory_uri() . '/assets/images/product_bed.png',
        ),
        'product_image_light_brown' => array(
            'label'   => 'Configurator Image (Light Brown)',
            'default' => get_template_directory_uri() . '/assets/images/product_bed.png',
        ),
        'product_image_dark_brown' => array(
            'label'   => 'Configurator Image (Dark Brown)',
            'default' => get_template_directory_uri() . '/assets/images/product_bed.png',
        ),
        'detail_image_steel' => array(
            'label'   => 'Feature Detail Image (Steel)',
            'default' => get_template_directory_uri() . '/assets/images/detail_hardware.png',
        ),
        'detail_image_brass' => array(
            'label'   => 'Feature Detail Image (Brass)',
            'default' => get_template_directory_uri() . '/assets/images/detail_hardware.png',
        ),
        'about_image' => array(
            'label'   => 'About Founders Image',
            'default' => get_template_directory_uri() . '/assets/images/about_founders.png',
        ),
    );

    foreach ( $settings as $id => $data ) {
        $wp_customize->add_setting( $id, array(
            'default'   => $data['default'],
            'transport' => 'refresh',
        ) );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, array(
            'label'    => $data['label'],
            'section'  => 'furniture_homepage_images',
            'settings' => $id,
        ) ) );
    }

    // Product & Pricing Controls (centralized)
    $wp_customize->add_section( 'furniture_pricing_controls', array(
        'title'    => __( 'Product & Pricing Controls', 'furniture-sales' ),
        'priority' => 31,
    ) );

    $wp_customize->add_setting( 'furniture_featured_product_id', array(
        'default'           => 15,
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ) );

    $wp_customize->add_control( 'furniture_featured_product_id', array(
        'label'       => __( 'Featured Product ID (Homepage Configurator)', 'furniture-sales' ),
        'description' => __( 'Set the WooCommerce Product ID used across homepage configurator and pricing.', 'furniture-sales' ),
        'section'     => 'furniture_pricing_controls',
        'type'        => 'number',
    ) );

    $wp_customize->add_setting( 'furniture_brass_addon_price', array(
        'default'           => 3000,
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ) );

    $wp_customize->add_control( 'furniture_brass_addon_price', array(
        'label'   => __( 'Brass Hook Add-on Price', 'furniture-sales' ),
        'section' => 'furniture_pricing_controls',
        'type'    => 'number',
    ) );

    $wp_customize->add_setting( 'furniture_coverups_addon_price', array(
        'default'           => 5000,
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ) );

    $wp_customize->add_control( 'furniture_coverups_addon_price', array(
        'label'   => __( 'Coverup Panels Add-on Price', 'furniture-sales' ),
        'section' => 'furniture_pricing_controls',
        'type'    => 'number',
    ) );

    // Homepage Text Section
    $wp_customize->add_section( 'furniture_homepage_text', array(
        'title'    => __( 'Homepage Text', 'furniture-sales' ),
        'priority' => 32,
    ) );

    $text_settings = array(
        'hero_subtitle' => array(
            'label'   => 'Hero Subtitle',
            'type'    => 'textarea',
            'default' => 'We craft our luxury solid wood beds to fit your lifestyle. From day to night, experience premium quality without compromise.',
        ),
        'banner_title' => array(
            'label'   => 'Banner Title (Black Section)',
            'type'    => 'text',
            'default' => 'Handcrafted, solid wood bed frames',
        ),
        'banner_text' => array(
            'label'   => 'Banner Paragraph (Black Section)',
            'type'    => 'textarea',
            'default' => 'Our signature bed frames are crafted with precision using sustainably sourced premium material. The minimalist design meets maximum comfort, offering a solid foundation for your sleep. Experience British engineering merged with timeless design principles.',
        ),
        'feature_title' => array(
            'label'   => 'Feature Title (Hardware)',
            'type'    => 'text',
            'default' => 'Premium & Versatile',
        ),
        'feature_text' => array(
            'label'   => 'Feature Paragraph (Hardware)',
            'type'    => 'textarea',
            'default' => 'Every element is designed with intention. Featuring bespoke hardware, concealed fixings, and modular construction. Designed to adapt to any bedroom interior effortlessly.',
        ),
        'about_title' => array(
            'label'   => 'About Us Title',
            'type'    => 'text',
            'default' => 'About Us',
        ),
        'about_text' => array(
            'label'   => 'About Us Paragraph',
            'type'    => 'textarea',
            'default' => 'We set out to create the perfect bed. The result is a combination of robust engineering and beautiful design. Manufactured locally, delivered directly to you. No middlemen, just uncompromised quality.',
        ),
    );

    foreach ( $text_settings as $id => $data ) {
        $wp_customize->add_setting( $id, array(
            'default'           => $data['default'],
            'transport'         => 'refresh',
            'sanitize_callback' => $data['type'] === 'textarea' ? 'wp_kses_post' : 'sanitize_text_field',
        ) );
        $wp_customize->add_control( $id, array(
            'label'    => $data['label'],
            'section'  => 'furniture_homepage_text',
            'type'     => $data['type'],
            'settings' => $id,
        ) );
    }

    // Reviews Section (Shortcode Support)
    $wp_customize->add_section( 'furniture_reviews', array(
        'title'    => __( 'Reviews Section', 'furniture-sales' ),
        'priority' => 32,
    ) );

    $wp_customize->add_setting( 'reviews_shortcode', array(
        'default'   => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'wp_kses_post',
    ) );

    $wp_customize->add_control( 'reviews_shortcode', array(
        'label'       => __( 'Reviews Plugin Shortcode', 'furniture-sales' ),
        'description' => __( 'Paste a shortcode from a plugin (e.g. [site_reviews]) to replace the default testimonials.', 'furniture-sales' ),
        'section'     => 'furniture_reviews',
        'type'        => 'textarea',
    ) );
}
add_action( 'customize_register', 'furniture_customize_register' );

// ── WooCommerce Custom Configurator Data ────────────────────────────────────

// 1. Intercept Add to Cart and save custom data
add_filter( 'woocommerce_add_cart_item_data', 'furniture_add_cart_item_data', 10, 3 );
function furniture_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
    if ( isset( $_REQUEST['bed_color'] ) ) {
        $cart_item_data['bed_color'] = sanitize_text_field( wp_unslash( $_REQUEST['bed_color'] ) );
    }
    if ( isset( $_REQUEST['hook_finish'] ) ) {
        $cart_item_data['hook_finish'] = sanitize_text_field( wp_unslash( $_REQUEST['hook_finish'] ) );
    }
    if ( isset( $_REQUEST['has_coverups'] ) ) {
        $cart_item_data['has_coverups'] = sanitize_text_field( wp_unslash( $_REQUEST['has_coverups'] ) );
    }
    
    // Also generate a unique key so identical products with different configurations don't stack incorrectly
    if ( isset( $_REQUEST['bed_color'] ) || isset( $_REQUEST['hook_finish'] ) || isset( $_REQUEST['has_coverups'] ) ) {
        $cart_item_data['unique_key'] = md5( microtime() . rand() );
    }
    return $cart_item_data;
}

// 2. Display custom data in the cart and checkout
add_filter( 'woocommerce_get_item_data', 'furniture_get_item_data', 10, 2 );
function furniture_get_item_data( $item_data, $cart_item_data ) {
    $coverups_price = furniture_get_coverups_addon_price();
    $brass_price    = furniture_get_brass_addon_price();
    if ( isset( $cart_item_data['has_coverups'] ) && $cart_item_data['has_coverups'] === 'yes' ) {
        $item_data[] = array(
            'key'     => __( 'Coverup Panels', 'furniture-sales' ),
            'value'   => wc_clean( sprintf( __( 'Included (+%s)', 'furniture-sales' ), wp_strip_all_tags( wc_price( $coverups_price ) ) ) ),
            'display' => '',
        );
    }
    if ( isset( $cart_item_data['bed_color'] ) ) {
        $color_map = array(
            'white'       => 'White',
            'black'       => 'Black',
            'light_brown' => 'Light Brown',
            'dark_brown'  => 'Dark Brown'
        );
        $val = $cart_item_data['bed_color'];
        $display_val = isset($color_map[$val]) ? $color_map[$val] : ucfirst($val);
        $item_data[] = array(
            'key'     => __( 'Wood Color', 'furniture-sales' ),
            'value'   => wc_clean( $display_val ),
            'display' => '',
        );
    }
    if ( isset( $cart_item_data['hook_finish'] ) ) {
        $finish_map = array(
            'steel' => 'Steel Finish',
            'brass' => sprintf( __( 'Brass Finish (+%s)', 'furniture-sales' ), wp_strip_all_tags( wc_price( $brass_price ) ) ),
        );
        $val = $cart_item_data['hook_finish'];
        $display_val = isset($finish_map[$val]) ? $finish_map[$val] : ucfirst($val);
        $item_data[] = array(
            'key'     => __( 'Hardware Finish', 'furniture-sales' ),
            'value'   => wc_clean( $display_val ),
            'display' => '',
        );
    }
    return $item_data;
}

// 3. Adjust price dynamically based on all selections
add_action( 'woocommerce_before_calculate_totals', 'furniture_custom_price', 10, 1 );
function furniture_custom_price( $cart_object ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) return;

    foreach ( $cart_object->get_cart() as $cart_item ) {
        $product      = $cart_item['data'];
        $base_prices  = furniture_get_product_base_prices( $product );
        $custom_price = (float) $base_prices['current'];

        if ( isset( $cart_item['hook_finish'] ) && $cart_item['hook_finish'] === 'brass' ) {
            $custom_price += furniture_get_brass_addon_price();
        }

        if ( isset( $cart_item['has_coverups'] ) && $cart_item['has_coverups'] === 'yes' ) {
            $custom_price += furniture_get_coverups_addon_price();
        }

        $product->set_price( $custom_price );
    }
}

// 4. Save custom data to order line item meta
add_action( 'woocommerce_checkout_create_order_line_item', 'furniture_add_custom_data_to_order', 10, 4 );
function furniture_add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
    $brass_price = furniture_get_brass_addon_price();
    $coverups_price = furniture_get_coverups_addon_price();

    if ( isset( $values['has_coverups'] ) && $values['has_coverups'] === 'yes' ) {
        $item->add_meta_data(
            __( 'Coverup Panels', 'furniture-sales' ),
            sprintf( __( 'Included (+%s)', 'furniture-sales' ), wp_strip_all_tags( wc_price( $coverups_price ) ) )
        );
    }
    if ( isset( $values['bed_color'] ) ) {
        $color_map = array(
            'white'       => 'White',
            'black'       => 'Black',
            'light_brown' => 'Light Brown',
            'dark_brown'  => 'Dark Brown'
        );
        $val = $values['bed_color'];
        $display_val = isset($color_map[$val]) ? $color_map[$val] : ucfirst($val);
        $item->add_meta_data( __( 'Wood Color', 'furniture-sales' ), $display_val );
    }
    if ( isset( $values['hook_finish'] ) ) {
        $finish_map = array(
            'steel' => 'Steel Finish',
            'brass' => sprintf( __( 'Brass Finish (+%s)', 'furniture-sales' ), wp_strip_all_tags( wc_price( $brass_price ) ) ),
        );
        $val = $values['hook_finish'];
        $display_val = isset($finish_map[$val]) ? $finish_map[$val] : ucfirst($val);
        $item->add_meta_data( __( 'Hardware Finish', 'furniture-sales' ), $display_val );
    }
}

function furniture_get_bed_color_options() {
    return array(
        'any'         => __( 'Any color', 'furniture-sales' ),
        'white'       => __( 'White', 'furniture-sales' ),
        'black'       => __( 'Black', 'furniture-sales' ),
        'light_brown' => __( 'Light Brown', 'furniture-sales' ),
        'dark_brown'  => __( 'Dark Brown', 'furniture-sales' ),
    );
}

function furniture_get_bed_finish_options() {
    return array(
        'any'   => __( 'Any finish', 'furniture-sales' ),
        'steel' => __( 'Steel', 'furniture-sales' ),
        'brass' => __( 'Brass', 'furniture-sales' ),
    );
}

function furniture_get_bed_coverups_options() {
    return array(
        'any' => __( 'Any', 'furniture-sales' ),
        'no'  => __( 'No coverups', 'furniture-sales' ),
        'yes' => __( 'With coverups', 'furniture-sales' ),
    );
}

function furniture_get_scraped_bed_directories() {
    return array(
        'full'  => 'assets/alterego-beds/UK_EU DOUBLE (US FULL)',
        'queen' => 'assets/alterego-beds/UK_EU KING (US QUEEN)',
        'king'  => 'assets/alterego-beds/UK_EU SUPERKING (US KING)',
    );
}

function furniture_get_scraped_bed_upload_url( $relative_directory, $filename ) {
    $source_file = trailingslashit( get_template_directory() ) . str_replace( '/', DIRECTORY_SEPARATOR, $relative_directory ) . DIRECTORY_SEPARATOR . $filename;

    if ( ! file_exists( $source_file ) ) {
        return '';
    }

    $upload_dir = wp_upload_dir();
    if ( ! empty( $upload_dir['error'] ) ) {
        return '';
    }

    $target_dir = trailingslashit( $upload_dir['basedir'] ) . 'furniture-bed-configs/' . md5( $relative_directory );
    if ( ! wp_mkdir_p( $target_dir ) ) {
        return '';
    }

    $target_file = trailingslashit( $target_dir ) . $filename;
    if ( ! file_exists( $target_file ) ) {
        copy( $source_file, $target_file );
    }

    return trailingslashit( $upload_dir['baseurl'] ) . 'furniture-bed-configs/' . md5( $relative_directory ) . '/' . rawurlencode( $filename );
}

function furniture_get_default_bed_configuration_rows() {
    $scraped_dirs = furniture_get_scraped_bed_directories();

    foreach ( $scraped_dirs as $directory ) {
        $absolute_dir = trailingslashit( get_template_directory() ) . str_replace( '/', DIRECTORY_SEPARATOR, $directory );
        $images       = glob( $absolute_dir . DIRECTORY_SEPARATOR . '*.jpg' );

        if ( empty( $images ) ) {
            continue;
        }

        sort( $images, SORT_NATURAL );
        $filename = basename( $images[0] );
        $image    = furniture_get_scraped_bed_upload_url( $directory, $filename );

        if ( $image ) {
            return array(
                array(
                    'label'       => __( 'Default bed image', 'furniture-sales' ),
                    'bed_color'   => 'any',
                    'hook_finish' => 'any',
                    'coverups'    => 'any',
                    'image_id'    => 0,
                    'image_url'   => $image,
                ),
            );
        }
    }

    return array();
}

function furniture_normalize_bed_configuration_rows( $rows ) {
    if ( ! is_array( $rows ) ) {
        return array();
    }

    $color_options    = furniture_get_bed_color_options();
    $finish_options   = furniture_get_bed_finish_options();
    $coverups_options = furniture_get_bed_coverups_options();
    $normalized       = array();

    foreach ( $rows as $row ) {
        if ( ! is_array( $row ) ) {
            continue;
        }

        $bed_color   = isset( $row['bed_color'] ) ? sanitize_key( $row['bed_color'] ) : 'any';
        $hook_finish = isset( $row['hook_finish'] ) ? sanitize_key( $row['hook_finish'] ) : 'any';
        $coverups    = isset( $row['coverups'] ) ? sanitize_key( $row['coverups'] ) : 'any';
        $image_id    = isset( $row['image_id'] ) ? absint( $row['image_id'] ) : 0;
        $image_url   = isset( $row['image_url'] ) ? esc_url_raw( $row['image_url'] ) : '';
        $label       = isset( $row['label'] ) ? sanitize_text_field( $row['label'] ) : '';

        if ( ! isset( $color_options[ $bed_color ] ) ) {
            $bed_color = 'any';
        }
        if ( ! isset( $finish_options[ $hook_finish ] ) ) {
            $hook_finish = 'any';
        }
        if ( ! isset( $coverups_options[ $coverups ] ) ) {
            $coverups = 'any';
        }

        if ( ! $image_id && ! $image_url ) {
            continue;
        }

        $normalized[] = array(
            'label'       => $label,
            'bed_color'   => $bed_color,
            'hook_finish' => $hook_finish,
            'coverups'    => $coverups,
            'image_id'    => $image_id,
            'image_url'   => $image_url,
        );
    }

    return $normalized;
}

function furniture_get_bed_configuration_rows( $product_id ) {
    $saved_rows = get_post_meta( $product_id, '_furniture_bed_config_rows', true );
    $rows       = furniture_normalize_bed_configuration_rows( $saved_rows );

    if ( ! empty( $rows ) ) {
        return $rows;
    }

    return furniture_get_default_bed_configuration_rows();
}

function furniture_get_configurator_asset_image_url( $bed_color, $variant = 1 ) {
    $color_map = array(
        'white'       => 'white',
        'black'       => 'black',
        'light_brown' => 'lightbrown',
        'dark_brown'  => 'darkbrown',
    );

    if ( empty( $color_map[ $bed_color ] ) ) {
        return '';
    }

    $base_name = $color_map[ $bed_color ] . absint( $variant );
    $extensions = array( 'jpg', 'jpeg', 'png', 'webp' );

    foreach ( $extensions as $extension ) {
        $relative_path = 'assets/images/' . $base_name . '.' . $extension;
        $absolute_path = trailingslashit( get_template_directory() ) . str_replace( '/', DIRECTORY_SEPARATOR, $relative_path );

        if ( file_exists( $absolute_path ) ) {
            return trailingslashit( get_template_directory_uri() ) . $relative_path;
        }
    }

    return '';
}

function furniture_get_configurator_asset_image_map() {
    $image_map = array();

    foreach ( array( 'white', 'black', 'light_brown', 'dark_brown' ) as $bed_color ) {
        $image_map[ $bed_color ] = array(
            'primary'   => furniture_get_configurator_asset_image_url( $bed_color, 1 ),
            'secondary' => furniture_get_configurator_asset_image_url( $bed_color, 2 ),
        );
    }

    return $image_map;
}

function furniture_get_bed_configuration_image_url( $product_id, $selection = array() ) {
    $selection = wp_parse_args(
        $selection,
        array(
            'bed_color'   => 'white',
            'hook_finish' => 'steel',
            'coverups'    => 'no',
        )
    );

    $asset_primary = furniture_get_configurator_asset_image_url( $selection['bed_color'], 1 );

    if ( $asset_primary && 'steel' === $selection['hook_finish'] && 'no' === $selection['coverups'] ) {
        return $asset_primary;
    }

    $rows       = furniture_get_bed_configuration_rows( $product_id );
    $best_match = '';
    $best_score = -1;

    foreach ( $rows as $row ) {
        $score = 0;

        foreach ( array( 'bed_color', 'hook_finish', 'coverups' ) as $field ) {
            if ( empty( $row[ $field ] ) || 'any' === $row[ $field ] ) {
                continue;
            }

            if ( $row[ $field ] !== $selection[ $field ] ) {
                $score = -1;
                break;
            }

            $score++;
        }

        if ( $score < 0 ) {
            continue;
        }

        if ( $score >= $best_score ) {
            $best_score = $score;
            $best_match = $row;
        }
    }

    if ( ! empty( $best_match['image_id'] ) ) {
        $attachment_url = wp_get_attachment_image_url( $best_match['image_id'], 'large' );
        if ( $attachment_url ) {
            return $attachment_url;
        }
    }

    if ( ! empty( $best_match['image_url'] ) ) {
        return $best_match['image_url'];
    }

    if ( $asset_primary ) {
        return $asset_primary;
    }

    $fallback_key = 'product_image_' . $selection['bed_color'];

    return get_theme_mod( $fallback_key, get_template_directory_uri() . '/assets/images/product_bed.png' );
}

function furniture_get_bed_configuration_payload( $product_id ) {
    $rows         = furniture_get_bed_configuration_rows( $product_id );
    $payload_rows = array();

    foreach ( $rows as $row ) {
        $image_url = '';

        if ( ! empty( $row['image_id'] ) ) {
            $image_url = wp_get_attachment_image_url( $row['image_id'], 'large' );
        }

        if ( ! $image_url && ! empty( $row['image_url'] ) ) {
            $image_url = $row['image_url'];
        }

        if ( ! $image_url ) {
            continue;
        }

        $payload_rows[] = array(
            'label'       => $row['label'],
            'bed_color'   => $row['bed_color'],
            'hook_finish' => $row['hook_finish'],
            'coverups'    => $row['coverups'],
            'image_url'   => $image_url,
        );
    }

    return array(
        'rows' => $payload_rows,
    );
}

function furniture_add_bed_configuration_meta_box() {
    add_meta_box(
        'furniture-bed-config-images',
        __( 'Bed Configuration Images', 'furniture-sales' ),
        'furniture_render_bed_configuration_meta_box',
        'product',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'furniture_add_bed_configuration_meta_box' );

function furniture_render_bed_configuration_meta_box( $post ) {
    wp_nonce_field( 'furniture_save_bed_configuration_rows', 'furniture_bed_configuration_nonce' );

    $rows             = furniture_get_bed_configuration_rows( $post->ID );
    $color_options    = furniture_get_bed_color_options();
    $finish_options   = furniture_get_bed_finish_options();
    $coverups_options = furniture_get_bed_coverups_options();
    ?>
    <p><?php esc_html_e( 'Assign a bed image to any color, hardware, and coverup combination. The homepage configurator will use the closest matching image. Leave a field on "Any" to use it as a fallback.', 'furniture-sales' ); ?></p>
    <table class="widefat striped" id="furniture-bed-config-table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Label', 'furniture-sales' ); ?></th>
                <th><?php esc_html_e( 'Wood color', 'furniture-sales' ); ?></th>
                <th><?php esc_html_e( 'Hardware', 'furniture-sales' ); ?></th>
                <th><?php esc_html_e( 'Coverups', 'furniture-sales' ); ?></th>
                <th><?php esc_html_e( 'Image', 'furniture-sales' ); ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $rows as $index => $row ) : ?>
                <?php furniture_render_bed_configuration_admin_row( $index, $row, $color_options, $finish_options, $coverups_options ); ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p>
        <button type="button" class="button" id="furniture-add-config-row"><?php esc_html_e( 'Add Configuration Row', 'furniture-sales' ); ?></button>
    </p>
    <script type="text/html" id="tmpl-furniture-bed-config-row">
        <?php
        furniture_render_bed_configuration_admin_row(
            '{{{data.index}}}',
            array(
                'label'       => '',
                'bed_color'   => 'any',
                'hook_finish' => 'any',
                'coverups'    => 'any',
                'image_id'    => 0,
                'image_url'   => '',
            ),
            $color_options,
            $finish_options,
            $coverups_options
        );
        ?>
    </script>
    <?php
}

function furniture_render_bed_configuration_admin_row( $index, $row, $color_options, $finish_options, $coverups_options ) {
    $image_url = '';

    if ( ! empty( $row['image_id'] ) ) {
        $image_url = wp_get_attachment_image_url( $row['image_id'], 'medium' );
    }

    if ( ! $image_url && ! empty( $row['image_url'] ) ) {
        $image_url = $row['image_url'];
    }
    ?>
    <tr class="furniture-bed-config-row">
        <td>
            <input type="text" class="widefat" name="furniture_bed_config_rows[<?php echo esc_attr( $index ); ?>][label]" value="<?php echo esc_attr( $row['label'] ); ?>" />
        </td>
        <td>
            <select name="furniture_bed_config_rows[<?php echo esc_attr( $index ); ?>][bed_color]">
                <?php foreach ( $color_options as $value => $label ) : ?>
                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $row['bed_color'], $value ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select name="furniture_bed_config_rows[<?php echo esc_attr( $index ); ?>][hook_finish]">
                <?php foreach ( $finish_options as $value => $label ) : ?>
                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $row['hook_finish'], $value ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select name="furniture_bed_config_rows[<?php echo esc_attr( $index ); ?>][coverups]">
                <?php foreach ( $coverups_options as $value => $label ) : ?>
                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $row['coverups'], $value ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <div class="furniture-bed-image-picker">
                <img src="<?php echo esc_url( $image_url ); ?>" alt="" style="width: 90px; height: 90px; object-fit: cover; display: <?php echo $image_url ? 'block' : 'none'; ?>; margin-bottom: 8px;" />
                <input type="hidden" class="furniture-bed-image-id" name="furniture_bed_config_rows[<?php echo esc_attr( $index ); ?>][image_id]" value="<?php echo esc_attr( $row['image_id'] ); ?>" />
                <input type="url" class="widefat furniture-bed-image-url" name="furniture_bed_config_rows[<?php echo esc_attr( $index ); ?>][image_url]" value="<?php echo esc_attr( $row['image_url'] ); ?>" placeholder="https://..." />
                <p style="margin: 8px 0 0;">
                    <button type="button" class="button furniture-bed-image-upload"><?php esc_html_e( 'Choose Image', 'furniture-sales' ); ?></button>
                    <button type="button" class="button-link-delete furniture-bed-image-remove"><?php esc_html_e( 'Remove', 'furniture-sales' ); ?></button>
                </p>
            </div>
        </td>
        <td>
            <button type="button" class="button-link-delete furniture-remove-config-row"><?php esc_html_e( 'Delete', 'furniture-sales' ); ?></button>
        </td>
    </tr>
    <?php
}

function furniture_save_bed_configuration_meta_box( $post_id ) {
    if ( empty( $_POST['furniture_bed_configuration_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['furniture_bed_configuration_nonce'] ) ), 'furniture_save_bed_configuration_rows' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( 'product' !== get_post_type( $post_id ) ) {
        return;
    }

    $rows = isset( $_POST['furniture_bed_config_rows'] ) ? wp_unslash( $_POST['furniture_bed_config_rows'] ) : array();
    $rows = furniture_normalize_bed_configuration_rows( $rows );

    if ( empty( $rows ) ) {
        delete_post_meta( $post_id, '_furniture_bed_config_rows' );
        return;
    }

    update_post_meta( $post_id, '_furniture_bed_config_rows', array_values( $rows ) );
}
add_action( 'save_post_product', 'furniture_save_bed_configuration_meta_box' );

function furniture_enqueue_bed_configuration_admin_assets( $hook ) {
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || 'product' !== $screen->post_type ) {
        return;
    }

    wp_enqueue_media();

    $script = <<<'JS'
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#furniture-bed-config-table tbody');
    const addButton = document.querySelector('#furniture-add-config-row');
    const template = document.querySelector('#tmpl-furniture-bed-config-row');

    if (!tableBody || !addButton || !template || typeof wp === 'undefined' || !wp.media) {
        return;
    }

    function bindRow(row) {
        const uploadButton = row.querySelector('.furniture-bed-image-upload');
        const removeButton = row.querySelector('.furniture-bed-image-remove');
        const deleteRowButton = row.querySelector('.furniture-remove-config-row');
        const imagePreview = row.querySelector('.furniture-bed-image-picker img');
        const imageIdInput = row.querySelector('.furniture-bed-image-id');
        const imageUrlInput = row.querySelector('.furniture-bed-image-url');

        if (uploadButton) {
            uploadButton.addEventListener('click', function(event) {
                event.preventDefault();
                const frame = wp.media({
                    title: 'Select configuration image',
                    button: { text: 'Use this image' },
                    multiple: false
                });

                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    imageIdInput.value = attachment.id || '';
                    imageUrlInput.value = attachment.url || '';
                    imagePreview.src = attachment.url || '';
                    imagePreview.style.display = attachment.url ? 'block' : 'none';
                });

                frame.open();
            });
        }

        if (removeButton) {
            removeButton.addEventListener('click', function(event) {
                event.preventDefault();
                imageIdInput.value = '';
                imageUrlInput.value = '';
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            });
        }

        if (deleteRowButton) {
            deleteRowButton.addEventListener('click', function(event) {
                event.preventDefault();
                row.remove();
            });
        }

        if (imageUrlInput) {
            imageUrlInput.addEventListener('change', function() {
                imagePreview.src = imageUrlInput.value;
                imagePreview.style.display = imageUrlInput.value ? 'block' : 'none';
            });
        }
    }

    tableBody.querySelectorAll('.furniture-bed-config-row').forEach(bindRow);

    addButton.addEventListener('click', function(event) {
        event.preventDefault();
        const index = tableBody.querySelectorAll('.furniture-bed-config-row').length;
        const html = template.innerHTML.replaceAll('{{{data.index}}}', String(index));
        const wrapper = document.createElement('tbody');
        wrapper.innerHTML = html.trim();
        const row = wrapper.firstElementChild;
        if (!row) {
            return;
        }
        tableBody.appendChild(row);
        bindRow(row);
    });
});
JS;

    wp_add_inline_script( 'jquery-core', $script );
}
add_action( 'admin_enqueue_scripts', 'furniture_enqueue_bed_configuration_admin_assets' );

