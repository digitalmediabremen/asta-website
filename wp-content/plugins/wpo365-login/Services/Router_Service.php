<?php
    
    namespace Wpo\Services;

    use \Wpo\Core\Url_Helpers;
    use \Wpo\Services\Authentication_Service;
    use \Wpo\Services\Error_Service;
    use \Wpo\Services\Id_Token_Service;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    use \Wpo\Tests\Self_Test;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Router_Service' ) ) {
    
        class Router_Service {

            public static function has_route() {
                
                // initiate openidconnect / saml flow
                if ( !empty( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'openidredirect' ) {
                    add_action( 'init', '\Wpo\Services\Router_Service::route_initiate_user_authentication' );
                    return true;
                }

                // test mode
                if ( Options_Service::get_global_boolean_var( 'test_mode', false ) && !( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
                    add_action( 'init', '\Wpo\Services\Router_Service::route_plugin_selftest' );
                    return true;
                }

                // process openid connect error
                if ( isset( $_POST[ 'error' ] ) ) {
                    add_action( 'init', '\Wpo\Services\Router_Service::route_openidconnect_error' );
                    return true;
                }

                // process openid connect id token
                if ( !empty( $_POST[ 'state' ] )  && !empty( $_POST[ 'id_token' ] ) ) {
                    add_action( 'init', '\Wpo\Services\Router_Service::route_openidconnect_token' );
                    return true;
                }

                // process saml response
                if ( !empty( $_REQUEST[ 'SAMLResponse' ] ) ) {
                    add_action( 'init', '\Wpo\Services\Router_Service::route_saml2_response' );
                    return true;
                }

                return false;
            }

            /**
             * Route to initialize user authentication with the option to do
             * so with OpenID Connect or with SAML.
             * 
             * @since 11.0
             * 
             * @return void
             */
            public static function route_initiate_user_authentication() {
                
                if ( Options_Service::get_global_boolean_var( 'use_saml' ) ) {
                    self::route_saml2_initiate();
                }
                else {
                    self::route_openidconnect_initiate();
                }
            }

            /**
             * Route to redirect user to login.microsoftonline.com
             * 
             * @since 11.0
             * 
             * @return void
             */
            public static function route_openidconnect_initiate() {
                $login_hint = !empty( $_POST[ 'login_hint' ] )
                    ? $_POST[ 'login_hint' ]
                    : null;
                
                $redirect_to = !empty( $_POST[ 'redirect_to' ] )
                    ? $_POST[ 'redirect_to' ]
                    : null;

                if ( Options_Service::is_wpo365_configured() ) {
                    $authUrl = Id_Token_Service::get_openidconnect_url( $login_hint, $redirect_to );
                    Url_Helpers::force_redirect( $authUrl );
                    exit();
                }
            }

            /**
             * Route to redirect user to the configured SAML 2.0 IdP
             * 
             * @since 11.0
             * 
             * @return void
             */
            public static function route_saml2_initiate() {
                $redirect_to = !empty( $_POST[ 'redirect_to' ] )
                    ? $_POST[ 'redirect_to' ]
                    : null;

                if ( Options_Service::is_wpo365_configured() ) {
                    \Wpo\Services\Saml2_Service::initiate_request( $redirect_to );
                    exit();
                }
            }

            /**
             * Route to redirect user to the configured SAML 2.0 IdP
             * 
             * @since 11.0
             * 
             * @return void
             */
            public static function route_saml2_response() {
                if ( Options_Service::is_wpo365_configured() ) {
                    try {
                        $wpo_usr = Authentication_Service::authenticate_saml2_user();
                        Url_Helpers::goto_after( $wpo_usr );
                    }
                    catch( \Exception $e ) {
                        Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Could not process SAML 2.0 response (' . $e->getMessage() . ')' );
                        Authentication_Service::goodbye( Error_Service::SAML2_ERROR );
                        exit();
                    }
                }
            }

            /**
             * Route to process an incoming id token
             * 
             * @since 11.0
             * 
             * @return void
             */
            public static function route_openidconnect_token() {
                Id_Token_Service::process_openidconnect_token();
                $wpo_usr = Authentication_Service::authenticate_oidc_user();
                Url_Helpers::goto_after( $wpo_usr );
            }

            /**
             * Route to sign user out of WordPress and redirect to login page
             * 
             * @since 11.0
             * 
             * @return void
             */
            public static function route_openidconnect_error() {
                $error_string = $_POST[ 'error' ] . isset( $_POST[ 'error_description' ] ) ? $_POST[ 'error_description' ] : '';
                Log_Service::write_log( 'ERROR', __METHOD__ . ' -> ' . $error_string );
                Authentication_Service::goodbye( Error_Service::CHECK_LOG );
                exit();
            }

            /**
             * Route to execute plugin selftest and then redirect user back to results (or landing page)
             * 
             * @since 11.0
             * 
             * @return void
             */
            public static function route_plugin_selftest() {
                // Perform a self test
                new Self_Test();

                // Immediately disable test mode
                Options_Service::update_options( 
                    array(
                        'test_mode' => false,
                    ), 
                    true 
                );

                // Get redirect target (default / wpo365 not configured)
                $redirect_to = !empty( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'openidredirect' && !empty( $_POST[ 'redirect_to' ] ) 
                    ? $_POST[ 'redirect_to' ] 
                    : $GLOBALS[ 'WPO_CONFIG' ][ 'url_info' ][ 'wp_site_url' ];

                // Try update redirect target (wpo365 configured)
                $redirect_url = Url_Helpers::get_redirect_url( $redirect_to );

                Url_Helpers::force_redirect( $redirect_url );
                exit();
            }
        }
    }