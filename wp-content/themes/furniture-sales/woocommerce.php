<?php get_header(); ?>

<?php if ( is_shop() || is_product_taxonomy() || is_post_type_archive( 'product' ) ) : ?>
    <?php get_template_part( 'template-parts/shop', 'catalog' ); ?>
<?php else : ?>
    <main class="site-main page-content container">
        <?php woocommerce_content(); ?>
    </main>
<?php endif; ?>

<?php get_footer(); ?>
