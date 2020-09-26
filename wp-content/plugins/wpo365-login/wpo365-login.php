<?php
    /**
     *  Plugin Name: WPO365 | LOGIN
     *  Plugin URI: https://wordpress.org/plugins/wpo365-login
     *  Description: With WPO365 users can sign in with their corporate or school (Azure AD / Microsoft Office 365) account into your Wordpress website: No username or password required.
     *  Version: 11.5
     *  Author: support@wpo365.com
     *  Author URI: https://www.wpo365.com
     *  License: GPL2+
     */

    namespace Wpo;

    require __DIR__ . '/vendor/autoload.php';

    use \Wpo\Core\Globals;
    use \Wpo\Core\Wp_Hooks;
    use \Wpo\Services\Authentication_Service;
    use \Wpo\Services\Dependency_Service;
    use \Wpo\Services\Files_Service;
    use \Wpo\Services\Request_Service;
    use \Wpo\Services\Router_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Login' ) ) {
    
        class Login {

            private $dependencies;

            public function __construct() {
                $this->deactivation_hooks();
                add_action( 'plugins_loaded', array( $this, 'init' ), 1 );
            }

            public function init() {
                $skip_init = defined( 'WPO_AUTH_MODE' ) && constant( 'WPO_AUTH_MODE' ) == 'internet' && !\is_admin();

                if ( $skip_init ) {
                    add_action( 'login_init', array( $this, 'load' ), 1 );
                    return;
                }

                $this->load();
            }

            public function load() {
                Globals::set_global_vars( __FILE__, __DIR__ );
                load_plugin_textdomain( 'wpo365-login', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
                Wp_Hooks::add_wp_hooks();
                $this->cache_dependencies();

                $has_route = Router_Service::has_route();
                
                if ( !$has_route ) {
                    add_action( 'init', '\Wpo\Services\Authentication_Service::authenticate_request', 1 );
                }
            }

            private function cache_dependencies() {
                $this->dependencies = Dependency_Service::get_instance();
                $this->dependencies->add( 'Request_Service', Request_Service::get_instance( true ) );
                $this->dependencies->add( 'Files_Service', Files_Service::get_instance() );
            }

            private function deactivation_hooks() {

                if ( \class_exists( '\Wpo\Sync\Sync_Manager' ) ) {
                    // Delete possible cron jobs
                    register_deactivation_hook( __FILE__, function() {
                        \Wpo\Sync\Sync_Manager::get_scheduled_events( true );
                    } );
                }
            }
        }   
    }

    $wpo365_login = new Login();
