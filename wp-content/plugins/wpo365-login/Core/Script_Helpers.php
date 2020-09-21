<?php
    
    namespace Wpo\Core;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Core\Script_Helpers' ) ) {
    
        class Script_Helpers {

            /**
             * Helper to enqueue the pintra redirect script.
             * 
             * @since 8.6
             * 
             * @return void
             */
            public static function enqueue_pintra_redirect() { 
                wp_enqueue_script( 'pintraredirectjs', $GLOBALS[ 'WPO_CONFIG' ][ 'plugin_url' ] . '/apps/dist/pintra-redirect.js', array(), $GLOBALS[ 'WPO_CONFIG' ][ 'version' ], false );
            }
        }
    }