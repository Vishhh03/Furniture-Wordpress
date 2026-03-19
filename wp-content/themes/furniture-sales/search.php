<?php get_header(); ?>

<main class="site-main catalog-page">
    <?php if ( function_exists( 'furniture_is_product_search' ) && furniture_is_product_search() ) : ?>
        <?php $result_count = (int) $wp_query->found_posts; ?>
        <section class="catalog-hero py-section bg-alt">
            <div class="container catalog-hero-inner">
                <div>
                    <p class="catalog-eyebrow">Product search</p>
                    <h1 class="section-title" style="margin-bottom: 1rem;">Search Results</h1>
                    <p class="catalog-intro">
                        <?php
                        printf(
                            esc_html__( 'Showing matches for "%s".', 'furniture-sales' ),
                            esc_html( get_search_query() )
                        );
                        ?>
                    </p>
                </div>
                <div class="catalog-search-panel">
                    <?php echo furniture_get_product_search_form_markup(); ?>
                    <p class="catalog-meta"><?php echo esc_html( sprintf( _n( '%d matching product', '%d matching products', $result_count, 'furniture-sales' ), $result_count ) ); ?></p>
                </div>
            </div>
        </section>

        <section class="catalog-results py-section">
            <div class="container">
                <?php if ( have_posts() ) : ?>
                    <div class="product-catalog-grid">
                        <?php while ( have_posts() ) : the_post(); ?>
                            <?php $product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : null; ?>
                            <?php if ( ! $product ) { continue; } ?>
                            <article <?php post_class( 'catalog-card' ); ?>>
                                <a class="catalog-card-media" href="<?php the_permalink(); ?>">
                                    <?php $override_image_url = function_exists( 'furniture_get_product_override_image_url' ) ? furniture_get_product_override_image_url( get_the_ID() ) : ''; ?>
                                    <?php
                                    if ( $override_image_url ) {
                                        echo '<img src="' . esc_url( $override_image_url ) . '" alt="' . esc_attr( get_the_title() ) . '" loading="lazy" decoding="async" />';
                                    } elseif ( has_post_thumbnail() ) {
                                        the_post_thumbnail( 'woocommerce_thumbnail' );
                                    } else {
                                        echo wc_placeholder_img( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    }
                                    ?>
                                </a>
                                <div class="catalog-card-body">
                                    <p class="catalog-card-type"><?php echo esc_html( wc_get_product_category_list( get_the_ID(), ', ' ) ? wp_strip_all_tags( wc_get_product_category_list( get_the_ID(), ', ' ) ) : 'Featured Product' ); ?></p>
                                    <h2 class="catalog-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                    <p class="catalog-card-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
                                    <p class="catalog-card-excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ?: get_the_content() ), 18 ) ); ?></p>
                                    <a class="btn catalog-card-cta" href="<?php the_permalink(); ?>">View Product</a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <div class="catalog-pagination">
                        <?php the_posts_pagination(); ?>
                    </div>
                <?php else : ?>
                    <div class="catalog-empty">
                        <h2 class="section-title-sm">No products found</h2>
                        <p>Try a broader search or browse the full shop collection.</p>
                        <a href="<?php echo esc_url( furniture_get_shop_page_url() ); ?>" class="btn">Browse All Products</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php else : ?>
        <section class="page-header py-section bg-alt text-center" style="padding-top: 4rem; padding-bottom: 2rem;">
            <div class="container">
                <h1 class="entry-title section-title" style="margin-bottom: 0.75rem; font-size: 2.5rem; letter-spacing: -0.03em;">Search Results</h1>
                <p style="color: var(--color-muted); margin: 0;">Showing site-wide matches for "<?php echo esc_html( get_search_query() ); ?>".</p>
            </div>
        </section>

        <section class="page-content py-section">
            <div class="container site-search-results">
                <?php if ( have_posts() ) : ?>
                    <?php while ( have_posts() ) : the_post(); ?>
                        <article <?php post_class( 'site-search-card' ); ?>>
                            <h2 class="section-title-sm" style="font-size: 1.35rem; margin-bottom: 0.5rem;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <p style="margin: 0; color: var(--color-muted);"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ?: get_the_content() ), 26 ) ); ?></p>
                        </article>
                    <?php endwhile; ?>

                    <div class="catalog-pagination">
                        <?php the_posts_pagination(); ?>
                    </div>
                <?php else : ?>
                    <div class="catalog-empty">
                        <h2 class="section-title-sm">Nothing matched your search</h2>
                        <p>Try different keywords or head back to the homepage.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
