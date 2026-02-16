<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="site-branding">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <h1><?php bloginfo( 'name' ); ?></h1>
        </a>
    </div>
    <nav class="site-navigation" style="display: flex; align-items: center; gap: 1.5rem;">
        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
            <a href="<?php echo wc_get_cart_url(); ?>" class="cart-link" title="View Cart">
                <svg class="cart-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
            </a>
        <?php endif; ?>
    </nav>
</header>
