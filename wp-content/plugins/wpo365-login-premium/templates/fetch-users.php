<?php

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();
    
    ?>
    <div>
        <style>
            .loading:before{content:"";display:block;position:absolute;left:50%;top:50%;font-size:10px;text-indent:-9999em}.loader-animation-1 .loading:before,.loading:before{width:1.25em;height:5em;margin:-2.5em 0 0 -.625em;border:none;-webkit-border-radius:0;border-radius:0;background:#000;-webkit-animation:loader-animation-1 1s infinite ease-in-out;animation:loader-animation-1 1s infinite ease-in-out;-webkit-transform:translateZ(0);transform:translateZ(0)}@-webkit-keyframes loader-animation-1{0%,100%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}12.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}25%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -2.5em 0 0 #000,-1.875em 2.5em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -2.5em 0 0 #000,-1.875em 2.5em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}37.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}50%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -2.5em 0 0 #000,0 2.5em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -2.5em 0 0 #000,0 2.5em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}62.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000}75%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -2.5em 0 0 #000,1.875em 2.5em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -2.5em 0 0 #000,1.875em 2.5em 0 0 #000}87.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000}}@keyframes loader-animation-1{0%,100%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}12.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}25%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -2.5em 0 0 #000,-1.875em 2.5em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -2.5em 0 0 #000,-1.875em 2.5em 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}37.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em -1.25em 0 0 #000,-1.875em 1.25em 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}50%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -2.5em 0 0 #000,0 2.5em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -2.5em 0 0 #000,0 2.5em 0 0 #000,1.875em 0 0 0 #000,1.875em 0 0 0 #000}62.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 -1.25em 0 0 #000,0 1.25em 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000}75%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -2.5em 0 0 #000,1.875em 2.5em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -2.5em 0 0 #000,1.875em 2.5em 0 0 #000}87.5%{-webkit-box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000;box-shadow:-1.875em 0 0 0 #000,1.875em 0 0 0 #000,-1.875em 0 0 0 #000,-1.875em 0 0 0 #000,0 0 0 0 #000,0 0 0 0 #000,1.875em -1.25em 0 0 #000,1.875em 1.25em 0 0 #000}}
            .centered{height: 300px;position: absolute;left: 50%;top: 50%;transform: translate(-50%, -50%);}
        </style>
        <div class="loading centered"></div>
        <div id="wpo365FetchMore">
            <form id="wpo365FetchMoreForm" method="POST">
                <?php wp_nonce_field( 'synchronize-users', 'synchronize_users_nonce' ) ?>
            </form>
        </div>
        <script>
            const form = document.getElementById('wpo365FetchMoreForm');
            form.action = window.location;
            const params = {
                next_link: '<?php echo str_replace( $graph_url, '', $response[ 'payload' ][ '@odata.nextLink' ] ) ?>',
                settings: '<?php echo urlencode( json_encode( $settings ) ) ?>'
            };

            for (const key in params) {
                if (params.hasOwnProperty(key)) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = key;
                    hiddenField.value = params[key];

                    form.appendChild(hiddenField);
                }
            }

            form.submit();
        </script>
    </div>