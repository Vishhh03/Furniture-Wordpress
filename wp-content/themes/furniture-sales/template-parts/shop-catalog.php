<?php
defined( 'ABSPATH' ) || exit;

$result_count = function_exists( 'wc_get_loop_prop' ) ? (int) wc_get_loop_prop( 'total' ) : 0;
$shop_url     = function_exists( 'furniture_get_shop_page_url' ) ? furniture_get_shop_page_url() : home_url( '/shop/' );
$filters      = function_exists( 'furniture_get_catalog_filter_state' ) ? furniture_get_catalog_filter_state() : array();
$categories   = get_terms(
    array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
    )
);
?>

<main class="site-main catalog-page">
    <section class="catalog-hero py-section bg-alt">
        <div class="container catalog-hero-inner">
            <div>
                <h1 class="section-title" style="margin-bottom: 1rem;"><?php woocommerce_page_title(); ?></h1>
                <p class="catalog-intro">Scalable storefront: bed-first today, ready for more products tomorrow.</p>
            </div>
            <div class="catalog-search-panel">
                <?php echo furniture_get_product_search_form_markup(); ?>
                <p class="catalog-meta"><?php echo esc_html( sprintf( _n( '%d product available', '%d products available', $result_count, 'furniture-sales' ), $result_count ) ); ?></p>
            </div>
        </div>
    </section>

    <section class="catalog-results py-section">
        <div class="container">
            <?php if ( woocommerce_product_loop() ) : ?>
                <div class="catalog-toolbar">
                    <p class="catalog-result-copy"><?php echo esc_html( sprintf( _n( '%d product in the catalog', '%d products in the catalog', $result_count, 'furniture-sales' ), $result_count ) ); ?></p>
                    <div class="catalog-ordering"><?php woocommerce_catalog_ordering(); ?></div>
                </div>
            <?php endif; ?>

            <div class="catalog-layout">
                <aside class="catalog-filters" aria-label="Catalog Filters">
                    <div class="catalog-panel">
                        <h2 class="section-title-sm catalog-panel-title">Filter Products</h2>
                        <form method="get" action="<?php echo esc_url( $shop_url ); ?>" class="catalog-filter-form">
                            <div class="catalog-field">
                                <label for="catalog-product-cat">Category</label>
                                <select id="catalog-product-cat" name="product_cat">
                                    <option value="">All categories</option>
                                    <?php if ( ! is_wp_error( $categories ) ) : ?>
                                        <?php foreach ( $categories as $category ) : ?>
                                            <option value="<?php echo esc_attr( $category->slug ); ?>" <?php selected( ! empty( $filters['product_cat'] ) ? $filters['product_cat'] : '', $category->slug ); ?>>
                                                <?php echo esc_html( $category->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="catalog-field-group">
                                <div class="catalog-field">
                                    <label for="catalog-min-price">Min Price</label>
                                    <input id="catalog-min-price" type="number" min="0" step="1" name="min_price" value="<?php echo esc_attr( ! empty( $filters['min_price'] ) ? $filters['min_price'] : '' ); ?>" placeholder="0">
                                </div>
                                <div class="catalog-field">
                                    <label for="catalog-max-price">Max Price</label>
                                    <input id="catalog-max-price" type="number" min="0" step="1" name="max_price" value="<?php echo esc_attr( ! empty( $filters['max_price'] ) ? $filters['max_price'] : '' ); ?>" placeholder="100000">
                                </div>
                            </div>

                            <div class="catalog-checks">
                                <label class="catalog-check">
                                    <input type="checkbox" name="in_stock" value="1" <?php checked( ! empty( $filters['in_stock'] ) ? $filters['in_stock'] : '', '1' ); ?>>
                                    <span>In stock only</span>
                                </label>
                                <label class="catalog-check">
                                    <input type="checkbox" name="on_sale" value="1" <?php checked( ! empty( $filters['on_sale'] ) ? $filters['on_sale'] : '', '1' ); ?>>
                                    <span>On sale only</span>
                                </label>
                            </div>

                            <div class="catalog-field">
                                <label for="catalog-per-page">Products Per Page</label>
                                <select id="catalog-per-page" name="per_page">
                                    <option value="">Default</option>
                                    <option value="12" <?php selected( ! empty( $filters['per_page'] ) ? (int) $filters['per_page'] : 0, 12 ); ?>>12</option>
                                    <option value="24" <?php selected( ! empty( $filters['per_page'] ) ? (int) $filters['per_page'] : 0, 24 ); ?>>24</option>
                                    <option value="36" <?php selected( ! empty( $filters['per_page'] ) ? (int) $filters['per_page'] : 0, 36 ); ?>>36</option>
                                </select>
                            </div>

                            <?php if ( isset( $_GET['orderby'] ) ) : ?>
                                <input type="hidden" name="orderby" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) ); ?>">
                            <?php endif; ?>

                            <div class="catalog-filter-actions">
                                <button type="submit" class="btn">Apply Filters</button>
                                <a href="<?php echo esc_url( $shop_url ); ?>" class="catalog-reset-link">Reset</a>
                            </div>
                        </form>
                    </div>
                </aside>

                <div class="catalog-main">
                    <?php if ( woocommerce_product_loop() ) : ?>
                        <div class="product-catalog-grid">
                            <?php while ( have_posts() ) : the_post(); ?>
                                <?php $product = wc_get_product( get_the_ID() ); ?>
                                <?php if ( ! $product ) { continue; } ?>
                                <?php
                                $rating_count   = $product->get_rating_count();
                                $average_rating = $product->get_average_rating();
                                $is_new_product = ( time() - strtotime( get_the_date( 'c' ) ) ) < ( DAY_IN_SECONDS * 30 );
                                ?>
                                <article <?php post_class( 'catalog-card' ); ?>>
                                    <a class="catalog-card-media" href="<?php the_permalink(); ?>">
                                        <?php $override_image_url = function_exists( 'furniture_get_product_override_image_url' ) ? furniture_get_product_override_image_url( get_the_ID() ) : ''; ?>
                                        <?php if ( $product->is_on_sale() ) : ?>
                                            <span class="catalog-badge catalog-badge-sale">Sale</span>
                                        <?php elseif ( $is_new_product ) : ?>
                                            <span class="catalog-badge catalog-badge-new">New</span>
                                        <?php endif; ?>

                                        <?php if ( $override_image_url ) : ?>
                                            <img src="<?php echo esc_url( $override_image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" decoding="async" />
                                        <?php elseif ( has_post_thumbnail() ) : ?>
                                            <?php the_post_thumbnail( 'woocommerce_thumbnail' ); ?>
                                        <?php else : ?>
                                            <?php echo wc_placeholder_img( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        <?php endif; ?>
                                    </a>
                                    <div class="catalog-card-body">
                                        <p class="catalog-card-type"><?php echo esc_html( wp_strip_all_tags( wc_get_product_category_list( get_the_ID(), ', ' ) ) ?: 'Featured Product' ); ?></p>
                                        <h2 class="catalog-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                                        <div class="catalog-rating">
                                            <?php if ( $rating_count > 0 ) : ?>
                                                <span class="catalog-stars"><?php echo wp_kses_post( wc_get_rating_html( $average_rating, $rating_count ) ); ?></span>
                                                <span class="catalog-rating-count">(<?php echo esc_html( $rating_count ); ?>)</span>
                                            <?php else : ?>
                                                <span class="catalog-rating-empty">No reviews yet</span>
                                            <?php endif; ?>
                                        </div>

                                        <p class="catalog-card-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
                                        <p class="catalog-card-excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ?: get_the_content() ), 12 ) ); ?></p>

                                        <div class="catalog-card-actions">
                                            <?php if ( $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' ) ) : ?>
                                                <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
                                                   data-quantity="1"
                                                   data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
                                                   data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
                                                   class="btn catalog-quick-add add_to_cart_button ajax_add_to_cart product_type_simple">
                                                    Add to Cart
                                                </a>
                                                <a href="<?php the_permalink(); ?>" class="catalog-view-link">View Details</a>
                                            <?php elseif ( $product->is_in_stock() ) : ?>
                                                <a href="<?php the_permalink(); ?>" class="btn catalog-quick-add">Select Options</a>
                                            <?php else : ?>
                                                <span class="catalog-stock-out">Out of stock</span>
                                                <a href="<?php the_permalink(); ?>" class="catalog-view-link">View Details</a>
                                            <?php endif; ?>
                                        </div>
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
                            <p>Try adjusting filters or browse all products again.</p>
                            <a href="<?php echo esc_url( $shop_url ); ?>" class="btn">Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>
