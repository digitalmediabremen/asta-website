<?php get_header(); ?>

<?php include get_template_directory() . '/ticker.php'; ?>

<div class="wrapper">
    <main>
        <div class="content">
            <?php
                $backgroundBoxes = get_field('background_boxes');
                $i = 1;
                if ( $backgroundBoxes ) : 
            ?>
                <?php foreach( $backgroundBoxes as $backgroundBox ): ?>
                    <div class="backgroundbox backgroundbox_0<?php echo $i; ?>"></div>
                <?php 
                    $i++;
                    endforeach; 
                ?>
            <?php endif; ?>

            <h1><?php the_title(); ?></h1>

            <?php 
                $note = get_field('note');
                if ( $note ) :
            ?>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/note_line.svg" class="note-line">
                <div class="note">
                    <p>
                        <?php the_field('note'); ?>
                    </p>
                </div>
            <?php endif; ?>

            <section>
                <?php the_content(); ?>
            </section>
        </div>
    </main>

    <?php include get_template_directory() . '/nav.php'; ?>

</div>

<?php get_footer(); ?>