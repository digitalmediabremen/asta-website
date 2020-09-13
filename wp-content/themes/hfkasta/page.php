<?php get_header(); ?>

<?php include get_template_directory() . '/ticker.php'; ?>

<div class="wrapper">
    <main>
        <div class="content">
            <?php if (get_field('background_boxes') !== 'no-boxes') : ?>
                <div class="<?php the_field('background_boxes') ?>">
                    <div class="backgroundbox backgroundbox_01"></div>
                    <div class="backgroundbox backgroundbox_02"></div>
                    <div class="backgroundbox backgroundbox_03"></div>
                    <div class="backgroundbox backgroundbox_04"></div>
                </div>
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

            <?php $parentID = $post->post_parent; ?>

            <?php if (get_field('sidebar_note') || get_field('sidebar_note', $parentID)) : ?>
                <div class="to_content_note">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow_down.svg">
                </div>
            <?php endif; ?>

            <section>
                <?php the_content(); ?>
            </section>

            <?php if (get_field('sidebar_note')) : ?>
                <div class="content_note" id="content_note">
                    <p>
                        <?php the_field('sidebar_note'); ?>
                    </p>
                </div>
            <?php else : 
                $parentID = $post->post_parent;
            ?>
                <?php if (get_field('sidebar_note', $parentID)) : ?>
                    <div class="content_note" id="content_note">
                        <p>
                            <?php the_field('sidebar_note', $parentID); ?>
                        </p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include get_template_directory() . '/nav.php'; ?>

</div>

<?php get_footer(); ?>