<?php

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    ?>
        <div id="wpo365OpenIdRedirect" style="display: none;">
            <script>
                window.wpo365 = window.wpo365 || {};
                <?php if( class_exists( '\Wpo\Core\Url_Helpers' ) ) : ?>
                    <?php if( \Wpo\Core\Url_Helpers::is_wp_login() ) : ?>
                            window.wpo365.siteUrl = '<?php echo $site_url ?>';
                    <?php endif; ?>
                <?php endif; ?>
            </script>
        </div>
        