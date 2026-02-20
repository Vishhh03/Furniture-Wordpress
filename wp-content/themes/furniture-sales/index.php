<?php get_header(); ?>

<main class="site-main">

    <!-- ═══════════ HERO — Split Screen ═══════════ -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Timeless<br>Comfort</h1>
            <p class="hero-subtitle">Handcrafted furniture for the contemporary home. Experience quality that speaks for itself.</p>
            <a href="#buy-now" class="btn">Shop Now</a>
        </div>
        <div class="hero-image">
            <img src="<?php echo esc_url( get_theme_mod( 'hero_image', 'https://placehold.co/800x1000/d4c8b8/3a3a3a?text=Your+Product+Hero' ) ); ?>" alt="Premium Furniture">
        </div>
    </section>

    <!-- ═══════════ TRUST BAR ═══════════ -->
    <div class="trust-bar">
        <div class="container text-center">
            Free Shipping in India &nbsp;•&nbsp; 5 Year Warranty &nbsp;•&nbsp; Secure Checkout
        </div>
    </div>

    <!-- ═══════════ FEATURES ═══════════ -->
    <section class="features">
        <div class="container">
            <h2 class="section-title text-center">Designed for Living</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-img-wrapper">
                        <img src="<?php echo esc_url( get_theme_mod( 'feature_1_img', 'https://placehold.co/600x450/e3e3dd/282828?text=Premium+Wood' ) ); ?>" alt="Material Detail">
                    </div>
                    <h3>Premium Materials</h3>
                    <p>Sourced from the finest sustainable teak wood and premium fabrics that age beautifully.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-img-wrapper">
                        <img src="<?php echo esc_url( get_theme_mod( 'feature_2_img', 'https://placehold.co/600x450/e3e3dd/282828?text=Ergonomic+Curve' ) ); ?>" alt="Ergonomic Design">
                    </div>
                    <h3>Ergonomic Design</h3>
                    <p>Designed for supportive comfort during long hours of use, without compromising style.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-img-wrapper">
                        <img src="<?php echo esc_url( get_theme_mod( 'feature_3_img', 'https://placehold.co/600x450/e3e3dd/282828?text=Joinery+Detail' ) ); ?>" alt="Built to Last">
                    </div>
                    <h3>Built to Last</h3>
                    <p>Hand-assembled joinery ensuring your furniture stands the test of time.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ REVIEWS ═══════════ -->
    <!-- ═══════════ REVIEWS ═══════════ -->
    <section class="reviews">
        <div class="container">
            <h2 class="section-title text-center">Loved by Designers</h2>
            <?php 
            $reviews_shortcode = get_theme_mod( 'reviews_shortcode' );
            if ( ! empty( $reviews_shortcode ) ) : 
                echo do_shortcode( $reviews_shortcode );
            else : 
            ?>
                <div class="reviews-grid">
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <blockquote>"Absolutely stunning quality. The teak finish is exactly what I was looking for. It feels solid, heavy, and incredibly premium."</blockquote>
                        <cite>— Sarah M., Interior Architect</cite>
                    </div>
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <blockquote>"We ordered two for our lounge and they are the talk of everyone who visits. Delivery was seamless and fast."</blockquote>
                        <cite>— Rohan K., verified buyer</cite>
                    </div>
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <blockquote>"Minimalist perfection. The fabric texture is even better in person than in the photos. Highly recommended."</blockquote>
                        <cite>— Ananya D., Bangalore</cite>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ═══════════ BUY SECTION (WooCommerce) ═══════════ -->
    <section id="buy-now" class="buy-section container">
        <?php 
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 1,
        );
        $loop = new WP_Query( $args );

        if ( $loop->have_posts() ) :
            while ( $loop->have_posts() ) : $loop->the_post();
                global $product;
        ?>
                <div class="product-purchase-wrapper">
                    <div class="product-image">
                        <?php 
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'large' );
                        } else {
                            echo $product->get_image( 'large' );
                        }
                        ?>
                    </div>
                    <div class="product-details">
                        <h1><?php the_title(); ?></h1>
                        <div class="price"><?php echo $product->get_price_html(); ?></div>
                        <div class="short-desc"><?php the_excerpt(); ?></div>
                        
                        <?php
                        // Output the standard WooCommerce Add to Cart button which handles AJAX via class "ajax_add_to_cart"
                        echo apply_filters( 'woocommerce_loop_add_to_cart_link',
                            sprintf( '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
                                esc_url( $product->add_to_cart_url() ),
                                esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
                                esc_attr( isset( $args['class'] ) ? $args['class'] : 'button alt add_to_cart_button ajax_add_to_cart' ),
                                isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
                                esc_html( $product->add_to_cart_text() )
                            ),
                        $product, array() );
                        ?>
                        
                        <div class="trust-badges">
                            <p><strong>🚚 Fast Dispatch:</strong> Ships within 24 hours</p>
                            <p><strong>🛡️ Warranty:</strong> 3 Year comprehensive warranty</p>
                        </div>
                    </div>
                </div>
        <?php
            endwhile;
        else :
            echo '<p class="text-center" style="padding: 4rem 0;">No product found. Please add a product in WooCommerce.</p>';
        endif;

        wp_reset_postdata();
        ?>
    </section>

</main>

<?php get_footer(); ?>
