<?php get_header(); ?>

<main class="site-main page-wrapper">
    <!-- Header Section -->
    <header class="page-header py-section bg-alt text-center" style="padding-top: 4rem; padding-bottom: 2rem;">
        <div class="container">
            <h1 class="entry-title section-title" style="margin-bottom: 0; font-size: 2.5rem; letter-spacing: -0.03em;"><?php the_title(); ?></h1>
        </div>
    </header>

    <!-- Main Content -->
    <section class="page-content py-section">
        <div class="container" style="max-width: 800px; margin: 0 auto;">
            <?php
            while ( have_posts() ) :
                the_post();
            ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'theme-formatted-content' ); ?>>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
