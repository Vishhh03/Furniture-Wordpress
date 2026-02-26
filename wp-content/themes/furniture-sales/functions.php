<?php
/**
 * Furniture Sales – functions.php
 */

// ── Enqueue Styles & Fonts ──────────────────────────────────────────────────
function furniture_sales_scripts() {
    // Theme stylesheet
    wp_enqueue_style( 'furniture-style', get_stylesheet_uri(), array(), '2.0' );

    // Google Fonts: Outfit (headings) & Inter (body)
    wp_enqueue_style(
        'furniture-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;700&display=swap',
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
    if ( isset( $cart_item_data['has_coverups'] ) && $cart_item_data['has_coverups'] === 'yes' ) {
        $item_data[] = array(
            'key'     => __( 'Coverup Panels', 'furniture-sales' ),
            'value'   => wc_clean( __( 'Included (+₹5,000)', 'furniture-sales' ) ),
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
            'brass' => 'Brass Finish (+₹3,000)'
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

    // Avoid running this recursively
    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) return;

    foreach ( $cart_object->get_cart() as $cart_item ) {
        $product = $cart_item['data'];
        $base_price = (float) $product->get_regular_price();
        if ( ! $base_price ) {
            $base_price = 54999;
        }
        
        $custom_price = $base_price;
        
        // Add hardware cost
        if ( isset( $cart_item['hook_finish'] ) && $cart_item['hook_finish'] === 'brass' ) {
            $custom_price += 3000;
        }
        
        // Add panel styles cost
        if ( isset( $cart_item['has_coverups'] ) && $cart_item['has_coverups'] === 'yes' ) {
            $custom_price += 5000;
        }

        if ($custom_price !== $base_price) {
            $product->set_price( $custom_price );
        }
    }
}

// 4. Save custom data to order line item meta
add_action( 'woocommerce_checkout_create_order_line_item', 'furniture_add_custom_data_to_order', 10, 4 );
function furniture_add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
    if ( isset( $values['has_coverups'] ) && $values['has_coverups'] === 'yes' ) {
        $item->add_meta_data( __( 'Coverup Panels', 'furniture-sales' ), __( 'Included', 'furniture-sales' ) );
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
            'brass' => 'Brass Finish (+₹3,000)'
        );
        $val = $values['hook_finish'];
        $display_val = isset($finish_map[$val]) ? $finish_map[$val] : ucfirst($val);
        $item->add_meta_data( __( 'Hardware Finish', 'furniture-sales' ), $display_val );
    }
}
