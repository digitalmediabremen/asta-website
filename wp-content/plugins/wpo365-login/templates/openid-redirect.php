<?php

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    $header_sent = headers_sent();

    ?>
    
    <?php if ( !$header_sent ) : ?>
        <!DOCTYPE html>
        <html>
        <body>
    <?php endif ?>

        <div>
            <style>
                .loading:before{content:"";display:block;position:absolute;left:50%;top:50%;font-size:10px;text-indent:-9999em}.loader-animation-1 .loading:before,.loading:before{width:1.25em;height:5em;margin:-2.5em 0 0 -.625em;border:none;-webkit-border-radius:0;border-radius:0;background:#000;-webkit-animation:loader-animation-1 1s infinite ease-in-out;animation:loader-animation-1 1s infinite ease-in-out;-webkit-transform:translateZ(0);transform:translateZ(0)}@-webkit-keyframes loader-animation-1{0%,100%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}12.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}25%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -2.5em 0 0 #000,-1.875em 2.5em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -2.5em 0 0 #000,-1.875em 2.5em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}37.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}50%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -2.5em 0 0 #000,0 2.5em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -2.5em 0 0 #000,0 2.5em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}62.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000}75%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -2.5em 0 0 #000,1.875em 2.5em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -2.5em 0 0 #000,1.875em 2.5em 0 0 #000}87.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000}}@keyframes loader-animation-1{0%,100%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}12.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}25%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -2.5em 0 0 #000,-1.875em 2.5em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -2.5em 0 0 #000,-1.875em 2.5em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}37.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}50%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -2.5em 0 0 #000,0 2.5em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -2.5em 0 0 #000,0 2.5em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}62.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000}75%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -2.5em 0 0 #000,1.875em 2.5em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -2.5em 0 0 #000,1.875em 2.5em 0 0 #000}87.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000}}
                .centered{height: 300px;position: absolute;left: 50%;top: 50%;transform: translate(-50%, -50%);}
            </style>
            <div id="wpo365OpenIdRedirect" class="loading centered"></div>
            <script src="<?php echo $GLOBALS[ 'WPO_CONFIG' ][ 'plugin_url' ] ?>/apps/dist/pintra-redirect.js?v=<?php echo $GLOBALS[ 'WPO_CONFIG' ][ 'version' ] ?>"></script>
            <script>
                window.wpo365 = window.wpo365 || {};
                <?php if ( class_exists( '\Wpo\Core\Url_Helpers' ) && \Wpo\Core\Url_Helpers::is_wp_login() ) : ?>
                    window.wpo365.siteUrl = '<?php echo $GLOBALS[ 'WPO_CONFIG' ][ 'url_info' ][ 'wp_site_url' ]; ?>';
                <?php endif; ?>
                try {
                    window.wpo365.pintraRedirect.toMsOnline('<?php echo ( !empty( $login_hint ) ? $login_hint : '') ?>' );
                }
                catch(err) {
                    console.log('Error occured whilst trying to redirect to MS online');
                    console.error(err);
                }
            </script>
        </div>

    <?php if ( !$header_sent ) : ?>
        </body>
        </html>
    <?php endif ?>
        