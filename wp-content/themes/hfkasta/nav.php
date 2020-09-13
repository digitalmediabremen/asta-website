<div class="hamburger hamburger--squeeze" id="hamburger">
    <div class="hamburger-box">
        <div class="hamburger-inner"></div>
    </div>
</div>

<nav id="nav">

    <?php get_search_form(); ?>

    <?php 
        $menuLocations = get_nav_menu_locations();
        $menuID = $menuLocations['main'];
        wp_nav_menu($menuID);
    ?>

    <?php if ( get_field('sidebar_note') ) : ?>
        <div class="sidebar_note">
            <p>
                <?php the_field('sidebar_note'); ?>
            </p>
        </div>
    <?php endif; ?>
</nav>