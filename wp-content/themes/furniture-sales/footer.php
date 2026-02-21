<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4 style="font-size: 0.95rem; font-weight: 700;">The Bed Company</h4>
                <p>Shop Quality Beds.</p>
                <p>Elevate your sleep with our premium beds. Designed and crafted with passion.</p>
                <p style="margin-top: 1.5rem;">VAT ID: 123 4567 89<br>&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</p>
            </div>
            <div class="footer-col">
                <h4 style="font-size: 0.95rem; font-weight: 700;">Helpful Links</h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#shop">Shop</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#about">Our Story</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#reviews">Reviews</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#faq">FAQ</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 style="font-size: 0.95rem; font-weight: 700;">Legal Info</h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>">Privacy Policy</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/refund-policy' ) ); ?>">Refund Policy</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/shipping-policy' ) ); ?>">Shipping Policy</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/terms-of-service' ) ); ?>">Terms of Service</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <!-- Newsletter form placeholder or additional spacing -->
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="newsletter-signup">
                <p style="margin: 0 0 0.5rem 0; font-weight: 700; color: var(--color-text);">Let's connect</p>
                <form action="#" method="get" style="display:flex;">
                    <input type="email" placeholder="Email Address" style="padding: 0.6rem 1rem; border: 1px solid var(--color-border); background: #ffffff; width: 220px; font-family: var(--font-body); font-size: 0.8rem; outline: none;">
                    <button type="submit" style="background: var(--color-text); color: #fff; border: none; padding: 0.6rem 1rem; cursor: pointer; text-transform: uppercase; font-size: 0.7rem; font-weight: 600;">Subscribe</button>
                </form>
            </div>
            <div class="payment-icons">
                <span style="font-weight:600; font-family: sans-serif; letter-spacing:-0.5px;">AMEX</span>
                <span style="font-weight:600; font-family: sans-serif; letter-spacing:-0.5px;">ApplePay</span>
                <span style="font-weight:600; font-family: sans-serif; letter-spacing:-0.5px;">GPay</span>
                <span style="font-weight:600; font-family: sans-serif; letter-spacing:-0.5px;">MasterCard</span>
                <span style="font-weight:600; font-family: sans-serif; letter-spacing:-0.5px;">PayPal</span>
                <span style="font-weight:600; font-family: sans-serif; letter-spacing:-0.5px;">VISA</span>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
