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
        'default'   => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80',
        'transport' => 'refresh',
    ) );

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hero_image', array(
        'label'    => __( 'Hero Image', 'furniture-sales' ),
        'section'  => 'furniture_hero',
        'settings' => 'hero_image',
    ) ) );

    // Features Section
    $wp_customize->add_section( 'furniture_features', array(
        'title'    => __( 'Feature Images', 'furniture-sales' ),
        'priority' => 31,
    ) );

    $settings = array(
        'feature_1_img' => 'https://images.unsplash.com/photo-1533090481720-856c6e3c1fdc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        'feature_2_img' => 'https://images.unsplash.com/photo-1519947486511-46149fa0a254?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        'feature_3_img' => 'https://images.unsplash.com/photo-1581539250439-c96689b516dd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    );

    foreach ( $settings as $id => $default ) {
        $wp_customize->add_setting( $id, array(
            'default'   => $default,
            'transport' => 'refresh',
        ) );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, array(
            'label'    => ucfirst( str_replace( '_', ' ', $id ) ),
            'section'  => 'furniture_features',
            'settings' => $id,
        ) ) );
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
