<?php
    /**
     *  Plugin Name: WPO365 | SYNC
     *  Plugin URI: https://www.wpo365.com/downloads/wordpress-office-365-login-premium/
     *  Description: Extends WPO365 | LOGIN and gives administrators full control over who can (not) enroll to / sign into their WordPress website plus full synchronization of WordPress roles, profiles and profile images.
     *  Version: 11.5
     *  Author: support@wpo365.com
     *  Author URI: https://www.wpo365.com
     *  License: See license.txt
     */

    namespace Wpo;

    require __DIR__ . '/vendor/autoload.php';

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Premium' ) ) {

        class Premium { 

            public function __construct() {
                // Show admin notification when BASIC edition is not installed
                add_action( 'admin_notices', array( $this, 'ensure_dependencies' ), 10, 0 );
                add_action( 'network_admin_notices', array( $this, 'ensure_dependencies' ), 10, 0 );

                // Cache the extension dir, store item and store item ID
                add_action( 'plugins_loaded', array( $this, 'update_global_vars' ), 11, 0 );
                add_action( 'login_init', array( $this, 'update_global_vars' ), 11, 0 );
            }

            public function ensure_dependencies() {

                // BASIC edition >= v11 installed and activated
                if ( class_exists( '\Wpo\Login' ) ) {
                    return;
                }

                // BASIC edition >= v11 is NOT installed
                if ( !file_exists( dirname( __DIR__ ) . '/wpo365-login/Core/Version.php' ) ) {
                    
                    // BASIC edition < v11 IS installed
                    if ( file_exists( dirname( __DIR__ ) . '/wpo365-login' ) ) {
                        $update_url = wp_nonce_url( 
                            self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . 'wpo365-login/wpo365-login.php', 
                            'upgrade-plugin_wpo365-login/wpo365-login.php' 
                        );
                        echo '<div class="notice notice-error" style="margin-left: 2px;"><p>'
                             . sprintf( __( 'Please <a href="%s">update</a> the %s  plugin to the latest version.', 'wpo365-login' ), $update_url, '<strong>WPO365 | LOGIN (free)</strong>' )
                             . '</p></div>';
                        return;
                    }

                    // BASIC edition >= v11 is NOT installed
                    $install_url = wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => 'install-plugin',
                                'plugin' => 'wpo365-login',
                                'from'   => 'plugins',
                            ),
                            self_admin_url( 'update.php' )
                        ),
                        'install-plugin_wpo365-login'
                    );
                    echo '<div class="notice notice-error" style="margin-left: 2px;"><p>'
                         . sprintf( __( 'Please <a href="%s">install</a> the latest version of the %s plugin.', 'wpo365-login' ), $install_url, '<strong>WPO365 | LOGIN (free)</strong>' )
                         . '</p></div>';
                    return;
                }

                // BASIC edition >= v11 is NOT activated
                if ( !class_exists( '\Wpo\Login' ) ) {
                    $activate_url = add_query_arg(
                        array(
                            '_wpnonce' => wp_create_nonce( 'activate-plugin_wpo365-login/wpo365-login.php' ),
                            'action'   => 'activate',
                            'plugin'   => 'wpo365-login/wpo365-login.php',
                        ),
                        network_admin_url( 'plugins.php' )
                    );

                    if ( is_network_admin() ) {
                        $activate_url = add_query_arg( array( 'networkwide' => 1 ), $activate_url );
                    }
                    
                    echo '<div class="notice notice-error" style="margin-left: 2px;"><p>'
                         . sprintf( __( 'Please <a href="%s">activate</a> the latest version of the %s plugin.', 'wpo365-login' ), $activate_url, '<strong>WPO365 | LOGIN (free)</strong>' )
                         . '</p></div>';
                    return;
                }
            }

            public function update_global_vars() {

                if ( isset( $GLOBALS[ 'WPO_CONFIG' ] ) ) {
                    $GLOBALS[ 'WPO_CONFIG' ][ 'extension_dir' ] = __DIR__;
                    $GLOBALS[ 'WPO_CONFIG' ][ 'extension_file' ] = __FILE__;
                    $GLOBALS[ 'WPO_CONFIG' ][ 'store_item' ] = 'WPO365 PREMIUM';
                    $GLOBALS[ 'WPO_CONFIG' ][ 'store_item_id' ] = 442;

                    if ( is_array( $GLOBALS[ 'WPO_CONFIG' ][ 'extensions' ] ) ) {
                        $GLOBALS[ 'WPO_CONFIG' ][ 'extensions' ][] = 'wpo365-login-premium';
                    }
                }
            }
        }
    }
    
    $wpo365_premium = new Premium();
