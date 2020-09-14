<div class="hamburger hamburger--squeeze" id="hamburger">
    <div class="hamburger-box">
        <div class="hamburger-inner"></div>
    </div>
</div>

<nav id="nav">

    <?php get_search_form(); ?>

    <?php
        wp_nav_menu( 
            array(
                'theme_location' => 'main',
                'menu' => 'Main'
            )
        );
    ?>

    <?php if (get_field('sidebar_note')) : ?>
        <div class="sidebar_note">
            <p>
                <?php the_field('sidebar_note'); ?>
            </p>
        </div>
    <?php else : 
        $parentID = $post->post_parent;
    ?>
        <?php if (get_field('sidebar_note', $parentID)) : ?>
            <div class="sidebar_note">
                <p>
                    <?php the_field('sidebar_note', $parentID); ?>
                </p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</nav>