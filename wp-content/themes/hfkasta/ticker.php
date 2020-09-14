<?php 
    // the query
    $the_query = new WP_Query( array(
        'posts_per_page' => 3,
        'category_name' => 'ticker'
    )); 
?>


<div class="ticker-wrap">
    <div class="ticker" id="ticker">
        <?php if ( $the_query->have_posts() ) : ?>
            <?php for ($i = 0 ; $i < 2; $i++) : ?>
                <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                    <div class="news">
                    <?php if ( get_field('news_link') ) : ?>
                        <a href="<?php the_field('news_link'); ?>" 
                            <?php
                                $newTab = get_field('new_tab');

                                if( $newTab && in_array('ja', $newTab) ) {
                                    echo 'target="_blank"';
                                }
                            ?>
                        >
                    <?php endif; ?>
                        <?php the_excerpt(); ?>
                    <?php if ( get_field('news_link') ) : ?>                
                        </a>
                    <?php endif; ?>
                    </div>
                    <div class="news_seperator">+++</div>
                <?php endwhile; ?>
            <?php endfor; ?>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>
    </div>
</div>