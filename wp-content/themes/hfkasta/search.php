<?php get_header(); ?>

<?php include get_template_directory() . '/ticker.php'; ?>

<div class="wrapper">
    <main>
        <div class="content">
            <div class="backgroundbox backgroundbox_01"></div>
            <div class="backgroundbox backgroundbox_02"></div>

            <?php
                global $wp_query;

                if ( $wp_query->found_posts ) {
                    ?>
                        <h1><?php echo $wp_query->found_posts ?> Suchergebnis<?php if ($wp_query->found_posts > 1) : ?>se<?php endif; ?> für <br><span>"<?php echo get_search_query(); ?>"</span></h1>
                    <?php
                } else {
                    ?>
                        <h1>Keine Suchergebnisse für <span><?php echo get_search_query() ?></span></h1>
                    <?php
                }

                if ( have_posts() ) {
                    ?>
                        <section class="results">
                    <?php
                        while ( have_posts() ) {
                            the_post();
                            ?>
                                <a href="<?php the_permalink(); ?>" class="search-result-link">
                                    <h3><?php the_title(); ?></h3>
                                    <span><?php the_permalink(); ?></span>

                                    <!--<p><?php // echo the_content(); ?></p>-->
                                </a>
                            <?php
                        }
                    ?>
                        </section>
                    <?php
                }
            ?>
        </div>
        </main>

        <?php include get_template_directory() . '/nav.php'; ?>

</div>

<?php get_footer(); ?>