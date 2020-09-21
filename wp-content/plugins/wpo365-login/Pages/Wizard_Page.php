<?php
    
    namespace Wpo\Pages;

    use \Wpo\Services\Options_Service;

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Pages\Wizard_Page' ) ) {
    
        class Wizard_Page {

            /**
             * Definition of the Options page (following default Wordpress practice).
             * 
             * @since 2.0
             * 
             * @return void
             */
            public static function add_management_page() {
                
                // Don't add the WPO365 wizard in the network admin when subsite options has been configured
                if ( is_multisite() 
                    && is_network_admin() 
                    && true === Options_Service::mu_use_subsite_options() ) {
                        return;
                }

                // Don't add the WPO365 wizard in the subsite admin when subsite options has not been configured
                if ( is_multisite() 
                    && !is_network_admin()
                    && false === Options_Service::mu_use_subsite_options() ) {
                        return;
                }

                add_menu_page( 
                    'WPO365', 
                    'WPO365',
                    'delete_users',
                    'wpo365-wizard', 
                    '\Wpo\Pages\Wizard_Page::wpo365_wizard_page' );
			}
			
            /**
             * 
             */
            public static function wpo365_wizard_page() {
                ob_start();
                include( $GLOBALS[ 'WPO_CONFIG' ][ 'plugin_dir' ] . '/templates/wizard.php' );
                echo ob_get_clean();
            }
        }
    }