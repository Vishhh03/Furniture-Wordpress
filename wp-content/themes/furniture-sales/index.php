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
                        
                        <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
                            <?php woocommerce_quantity_input( array(), $product, true ); ?>
                            <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="btn single_add_to_cart_button button alt">
                                <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                            </button>
                        </form>
                        
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
