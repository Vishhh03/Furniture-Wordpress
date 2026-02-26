<?php get_header(); ?>

<main class="site-main">

    <!-- 1. HERO - ONE BED, TWO MOODS -->
    <section class="py-section bg-alt" style="padding-top: 2rem;">
        <div class="container">
            <div class="split-section" style="gap: 2rem;">
                <div class="split-image">
                    <img src="<?php echo esc_url( get_theme_mod( 'hero_image', get_template_directory_uri() . '/assets/images/hero_bed.png' ) ); ?>" alt="Premium Bed Image" style="min-height: 500px; border-radius: 4px;">
                </div>
                <div class="split-content" style="padding-left: 3rem;">
                    <h1 class="section-title">One Bed, Two Moods.</h1>
                    <p style="font-size: 1.05rem; line-height: 1.7; margin-bottom: 2.5rem;"><?php echo wp_kses_post( get_theme_mod( 'hero_subtitle', 'We craft our luxury solid wood beds to fit your lifestyle. From day to night, experience premium quality without compromise.' ) ); ?></p>
                    <a href="#shop" class="btn">Shop The Collection</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. SHOP THE BED (CONFIGURATOR) -->
    <section id="shop" class="py-section">
        <div class="container">
            <div class="split-section" style="gap: 4rem;">
                <div class="product-gallery">
                    <img id="main-product-image" 
                         src="<?php echo esc_url( get_theme_mod( 'product_image_white', get_template_directory_uri() . '/assets/images/product_bed.png' ) ); ?>" 
                         data-white="<?php echo esc_url( get_theme_mod( 'product_image_white', get_template_directory_uri() . '/assets/images/product_bed.png' ) ); ?>"
                         data-black="<?php echo esc_url( get_theme_mod( 'product_image_black', get_template_directory_uri() . '/assets/images/product_bed.png' ) ); ?>"
                         data-light_brown="<?php echo esc_url( get_theme_mod( 'product_image_light_brown', get_template_directory_uri() . '/assets/images/product_bed.png' ) ); ?>"
                         data-dark_brown="<?php echo esc_url( get_theme_mod( 'product_image_dark_brown', get_template_directory_uri() . '/assets/images/product_bed.png' ) ); ?>"
                         alt="Signature Bed Frame" style="border-radius: 4px; border: 1px solid var(--color-border); width: 100%; transition: opacity 0.2s ease;">
                </div>
                <div class="product-configurator woocommerce">
                    <?php
                    $product = wc_get_product( 15 );
                    $base_price = $product ? (float) $product->get_price() : 54999;
                    $formatted_price = '₹' . number_format( $base_price );
                    ?>
                    <h2 class="section-title-sm" style="font-size: 2.2rem; margin-bottom: 0.5rem; letter-spacing: -0.02em;"><?php echo esc_html( get_the_title( 15 ) ); ?></h2>
                    <p class="price" style="font-size: 1.4rem; color: var(--color-muted); margin-bottom: 2rem;"><span class="base-price-display"><?php echo esc_html( $formatted_price ); ?></span> <span style="font-size: 0.9rem;">+ Shipping</span></p>
                    
                    <div class="config-group" style="margin-bottom: 2.5rem;">
                        <h4 style="font-size: 0.85rem; text-transform: uppercase; margin-bottom: 1rem; letter-spacing: 0.05em; color: var(--color-muted);">Select Wood Color</h4>
                        <div class="color-swatches" style="display: flex; gap: 1.5rem;">
                            <label class="swatch-label">
                                <input type="radio" name="bed_color" value="white" checked>
                                <span class="swatch" style="background-color: #f4f4f4; border: 1px solid #d1d1d1;"></span>
                                <span class="swatch-name">White</span>
                            </label>
                            <label class="swatch-label">
                                <input type="radio" name="bed_color" value="black">
                                <span class="swatch" style="background-color: #222222; border: 1px solid #222;"></span>
                                <span class="swatch-name">Black</span>
                            </label>
                            <label class="swatch-label">
                                <input type="radio" name="bed_color" value="light_brown">
                                <span class="swatch" style="background-color: #c4a482; border: 1px solid #b3926f;"></span>
                                <span class="swatch-name">Light Brown</span>
                            </label>
                            <label class="swatch-label">
                                <input type="radio" name="bed_color" value="dark_brown">
                                <span class="swatch" style="background-color: #4a3424; border: 1px solid #3d2a1c;"></span>
                                <span class="swatch-name">Dark Brown</span>
                            </label>
                        </div>
                    </div>

                    <div class="config-group" style="margin-bottom: 2.5rem;">
                        <h4 style="font-size: 0.85rem; text-transform: uppercase; margin-bottom: 1rem; letter-spacing: 0.05em; color: var(--color-muted);">Additional Add-ons</h4>
                        <div class="hardware-options">
                            <label class="hardware-label" style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" name="show_coverups" id="show_coverups" style="margin-right: 15px; width: 18px; height: 18px; cursor: pointer;">
                                <span style="display: flex; align-items: center; gap: 0.4rem;">
                                    Add Coverup Panels
                                    <div class="furniture-tooltip">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-muted);"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                        <div class="furniture-tooltip-text">Coverup panels seamlessly hide the assembly holes for a flawless exterior finish, perfect if your bed is placed centrally in the room.</div>
                                    </div>
                                </span>
                                <span style="margin-left: auto; color: var(--color-muted); font-size: 0.9rem;">+₹5,000</span>
                            </label>
                        </div>
                    </div>

                    <div class="config-group" style="margin-bottom: 2.5rem;">
                        <h4 style="font-size: 0.85rem; text-transform: uppercase; margin-bottom: 1rem; letter-spacing: 0.05em; color: var(--color-muted);">Select Metal Hook Finish</h4>
                        <div class="hardware-options">
                            <label class="hardware-label">
                                <input type="radio" name="hook_finish" value="steel" checked>
                                <span>Steel Finish</span>
                                <span style="margin-left: auto; color: var(--color-muted); font-size: 0.9rem;">Included</span>
                            </label>
                            <label class="hardware-label">
                                <input type="radio" name="hook_finish" value="brass">
                                <span>Brass Finish</span>
                                <span style="margin-left: auto; color: var(--color-muted); font-size: 0.9rem;">+₹3,000</span>
                            </label>
                        </div>
                    </div>

                    <div class="trust-pill" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--color-bg-alt); padding: 0.6rem 1rem; border-radius: 50px; font-size: 0.8rem; font-weight: 500; margin-bottom: 2rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        7-day replacement guarantee
                    </div>

                    <a href="?add-to-cart=15" data-quantity="1" class="btn button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="15" aria-label="Add to cart" style="display: block; width: 100%; text-align: center; font-size: 1rem; padding: 1.2rem; text-decoration: none; box-sizing: border-box;">Add to Cart — <?php echo esc_html( $formatted_price ); ?></a>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        if (typeof jQuery === 'undefined') return;
                        
                        // 1. Update Price Dynamically
                        const basePrice = <?php echo esc_js( $base_price ); ?>;
                        const hardwarePrice = 3000;
                        const coverupsPrice = 5000;
                        
                        function updatePrice() {
                            const finish = jQuery('input[name="hook_finish"]:checked').val();
                            const hasCoverups = jQuery('input[name="show_coverups"]').is(':checked');
                            
                            let total = basePrice;
                            if (finish === 'brass') {
                                total += hardwarePrice;
                            }
                            if (hasCoverups) {
                                total += coverupsPrice;
                            }
                            
                            const formattedTotal = '₹' + total.toLocaleString('en-IN');
                            jQuery('.product-configurator .price').html('<span class="base-price-display">' + formattedTotal + '</span> <span style="font-size: 0.9rem;">+ Shipping</span>');
                            jQuery('.add_to_cart_button').text('Add to Cart — ' + formattedTotal);
                        }
                        
                        jQuery('input[name="hook_finish"], input[name="show_coverups"]').on('change', updatePrice);
                        
                        // 2. Inject Configurator Data into WooCommerce AJAX Add to Cart
                        jQuery(document.body).on('adding_to_cart', function(event, $button, data) {
                            data.bed_color = jQuery('input[name="bed_color"]:checked').val();
                            data.hook_finish = jQuery('input[name="hook_finish"]:checked').val();
                            if (jQuery('input[name="show_coverups"]').is(':checked')) {
                                data.has_coverups = 'yes';
                            }
                            
                            if (!$button.data('original-text')) {
                                $button.data('original-text', $button.text());
                            }
                            $button.html('<span class="furniture-spinner"></span> Adding to Cart...');
                            $button.css('opacity', '0.8');
                            $button.css('pointer-events', 'none');
                        });

                        // 3. Force Cart Fragment Refresh after adding
                        jQuery(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
                            jQuery(document.body).trigger('wc_fragment_refresh');
                            
                            $button.html('Added to Cart! ✓');
                            $button.css('opacity', '1');
                            
                            setTimeout(() => {
                                updatePrice(); // Restores original calculated text
                                $button.css('pointer-events', '');
                            }, 2500);
                        });
                        
                        // 4. Dynamic Image Swapping
                        const $mainImg = jQuery('#main-product-image');
                        const $detailImg = jQuery('#detail-hardware-image');

                        jQuery('input[name="bed_color"]').on('change', function() {
                            const selectedColor = jQuery(this).val();
                            const newSrc = $mainImg.data(selectedColor);
                            if (newSrc && $mainImg.attr('src') !== newSrc) {
                                $mainImg.css('opacity', 0.5);
                                setTimeout(() => {
                                    $mainImg.attr('src', newSrc).css('opacity', 1);
                                }, 200);
                            }
                        });

                        jQuery('input[name="hook_finish"]').on('change', function() {
                            const selectedFinish = jQuery(this).val();
                            const newSrc = $detailImg.data(selectedFinish);
                            if (newSrc && $detailImg.attr('src') !== newSrc) {
                                $detailImg.css('opacity', 0.5);
                                setTimeout(() => {
                                    $detailImg.attr('src', newSrc).css('opacity', 1);
                                }, 200);
                            }
                        });
                    });
                    </script>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. TEXT BANNER -->
    <section class="py-section" style="background-color: var(--color-text); color: var(--color-bg);">
        <div class="container text-center">
            <h2 class="section-title-sm" style="font-size: 1.8rem; margin-bottom: 1.5rem; color: var(--color-bg);"><?php echo esc_html( get_theme_mod( 'banner_title', 'Handcrafted, solid wood bed frames' ) ); ?></h2>
            <p style="max-width: 800px; margin: 0 auto; color: rgba(255, 255, 255, 0.7); font-size: 1.05rem; line-height: 1.7;">
                <?php echo wp_kses_post( get_theme_mod( 'banner_text', 'Our signature bed frames are crafted with precision using sustainably sourced premium material. The minimalist design meets maximum comfort, offering a solid foundation for your sleep. Experience British engineering merged with timeless design principles.' ) ); ?>
            </p>
        </div>
    </section>

    <!-- 4. PREMIUM & VERSATILE -->
    <section class="py-section">
        <div class="container">
            <div class="split-section hero-split" style="gap: 5rem; height: auto;">
                <div class="split-image">
                    <img id="detail-hardware-image" 
                         src="<?php echo esc_url( get_theme_mod( 'detail_image_steel', get_template_directory_uri() . '/assets/images/detail_hardware.png' ) ); ?>"
                         data-steel="<?php echo esc_url( get_theme_mod( 'detail_image_steel', get_template_directory_uri() . '/assets/images/detail_hardware.png' ) ); ?>"
                         data-brass="<?php echo esc_url( get_theme_mod( 'detail_image_brass', get_template_directory_uri() . '/assets/images/detail_hardware.png' ) ); ?>"
                         alt="Premium Detail" style="border-radius: 4px; transition: opacity 0.2s ease; width:100%;">
                </div>
                <div class="split-content">
                    <h2 class="section-title-sm" style="font-size: 1.6rem;"><?php echo esc_html( get_theme_mod( 'feature_title', 'Premium & Versatile' ) ); ?></h2>
                    <p style="font-size: 1rem; margin-bottom: 1.5rem;"><?php echo wp_kses_post( get_theme_mod( 'feature_text', 'Every element is designed with intention. Featuring bespoke hardware, concealed fixings, and modular construction. Designed to adapt to any bedroom interior effortlessly.' ) ); ?></p>
                    <!-- Details removed as they are now in the Shop config section -->
                </div>
            </div>
        </div>
    </section>

    <!-- 5. ABOUT US -->
    <section id="about" class="py-section bg-alt" style="margin-top: 2rem;">
        <div class="container">
            <div class="split-section" style="gap: 5rem;">
                <div class="split-content" style="order: 1; padding-right: 2rem;">
                    <h2 class="section-title-sm" style="font-size: 1.6rem;"><?php echo esc_html( get_theme_mod( 'about_title', 'About Us' ) ); ?></h2>
                    <p style="font-size: 1rem; margin-bottom: 2rem;"><?php echo wp_kses_post( get_theme_mod( 'about_text', 'We set out to create the perfect bed. The result is a combination of robust engineering and beautiful design. Manufactured locally, delivered directly to you. No middlemen, just uncompromised quality.' ) ); ?></p>
                    <a href="#" class="btn">Our Story</a>
                </div>
                <div class="split-image" style="order: 2;">
                    <!-- Grayscale style image based on reference screenshot -->
                    <img src="<?php echo esc_url( get_theme_mod( 'about_image', get_template_directory_uri() . '/assets/images/about_founders.png' ) ); ?>" alt="About Us Founders" style="filter: grayscale(100%); border-radius: 4px;">
                </div>
            </div>
        </div>
    </section>

    <!-- 6. ICONS GRID -->
    <section class="py-section">
        <div class="container">
            <h2 class="section-title-sm text-center" style="margin-bottom: 3.5rem; font-size: 1.8rem;">The Everyday Bed With A Dark Side</h2>
            <div class="icon-grid" style="background: transparent; padding: 0;">
                <div class="icon-item">
                    <div class="icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10h16v10H4z"/><path d="M8 10V6a4 4 0 0 1 8 0v4"/><path d="M12 14v2"/></svg>
                    </div>
                    <h4>Craftsmanship & Quality</h4>
                    <p>Premium quality hardware. Solid wood frames.</p>
                </div>
                <div class="icon-item">
                    <div class="icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="12" rx="2" ry="2"/><line x1="12" y1="8" x2="12" y2="20"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/></svg>
                    </div>
                    <h4>Dual-Purpose & Innovative</h4>
                    <p>Discreetly transform your bed with hidden anchor points.</p>
                </div>
                <div class="icon-item">
                    <div class="icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                    </div>
                    <h4>Safety, Security, & Trust</h4>
                    <p>We don't cut corners. 7-day replacement guarantee.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. REVIEWS -->
    <section id="reviews" class="py-section bg-alt">
        <div class="container">
            <h2 class="section-title-sm text-center" style="margin-bottom: 3.5rem; font-size: 1.8rem;">What Our Customers Say</h2>
            <div class="grid-3">
                <div class="review-card" style="background: var(--color-surface); padding: 2rem; border-radius: 4px; border: 1px solid var(--color-border);">
                    <div style="color: #FFD700; margin-bottom: 1rem; font-size: 1.2rem;">★★★★★</div>
                    <p style="font-style: italic; font-size: 0.95rem; margin-bottom: 1.5rem; color: var(--color-text);">"The quality of the solid wood is exceptional. Best sleep I've had in years, and the assembly was incredibly straightforward."</p>
                    <p style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">— Sarah M.</p>
                </div>
                <div class="review-card" style="background: var(--color-surface); padding: 2rem; border-radius: 4px; border: 1px solid var(--color-border);">
                    <div style="color: #FFD700; margin-bottom: 1rem; font-size: 1.2rem;">★★★★★</div>
                    <p style="font-style: italic; font-size: 0.95rem; margin-bottom: 1.5rem; color: var(--color-text);">"A beautiful piece of furniture. The brass hardware option adds such a nice premium touch to the dark brown wood finish."</p>
                    <p style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">— James T.</p>
                </div>
                <div class="review-card" style="background: var(--color-surface); padding: 2rem; border-radius: 4px; border: 1px solid var(--color-border);">
                    <div style="color: #FFD700; margin-bottom: 1rem; font-size: 1.2rem;">★★★★★</div>
                    <p style="font-style: italic; font-size: 0.95rem; margin-bottom: 1.5rem; color: var(--color-text);">"Customer service is fantastic. True to their word on the 7-day guarantee, handled a minor delivery query immediately."</p>
                    <p style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">— Elena R.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. FAQ -->
    <section id="faq" class="py-section">
        <div class="container">
            <h2 class="section-title-sm text-center" style="margin-bottom: 3.5rem; font-size: 1.8rem;">Frequently Asked Questions</h2>
            <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="faq-item" style="border-bottom: 1px solid var(--color-border); padding-bottom: 1.5rem;">
                    <h4 style="font-size: 1.05rem; margin-bottom: 0.5rem;">How long does delivery take?</h4>
                    <p style="color: var(--color-muted); font-size: 0.95rem; margin: 0;">We typically process and ship orders within 3-5 business days across India.</p>
                </div>
                <div class="faq-item" style="border-bottom: 1px solid var(--color-border); padding-bottom: 1.5rem;">
                    <h4 style="font-size: 1.05rem; margin-bottom: 0.5rem;">Is assembly required?</h4>
                    <p style="color: var(--color-muted); font-size: 0.95rem; margin: 0;">Yes, but it is extremely straightforward. The bed frame is designed with modular construction and concealed fixings for easy, tool-free setup in under 30 minutes.</p>
                </div>
                <div class="faq-item" style="border-bottom: 1px solid var(--color-border); padding-bottom: 1.5rem;">
                    <h4 style="font-size: 1.05rem; margin-bottom: 0.5rem;">What is the 7-day replacement guarantee?</h4>
                    <p style="color: var(--color-muted); font-size: 0.95rem; margin: 0;">If you receive a defective or damaged product, we will replace it free of charge within 7 days of delivery to ensure complete satisfaction.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. CONTACT -->
    <section id="contact" class="py-section bg-alt" style="border-top: 1px solid var(--color-border);">
        <div class="container text-center">
            <h2 class="section-title-sm" style="font-size: 1.8rem; margin-bottom: 1rem;">Have more questions?</h2>
            <p style="color: var(--color-muted); font-size: 1.05rem; margin-bottom: 2rem;">Our team is here to help you build the perfect sleep setup.</p>
            <a href="mailto:support@restandrevel.com" class="btn">Contact Support</a>
        </div>
    </section>

</main>

<?php get_footer(); ?>
