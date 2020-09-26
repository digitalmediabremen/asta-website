<?php

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    ?>
        <div id="wpo365OpenIdRedirect" style="display: none;">
            <script>
                window.wpo365 = window.wpo365 || {};
                window.wpo365.siteUrl = '<?php echo $site_url ?>';
            </script>
        </div>