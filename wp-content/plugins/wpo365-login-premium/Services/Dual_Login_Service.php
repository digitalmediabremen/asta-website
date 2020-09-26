<?php

    namespace Wpo\Services;

    use \Wpo\Core\Url_Helpers;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;

    if ( !class_exists( '\Wpo\Services\Dual_Login_Service' ) ) {
    
        class Dual_Login_Service  {

            public static function redirect() {

                // Allow for dual login (if user isn't on the login form and tries to enter his username)
                if ( !Url_Helpers::is_wp_login() ) {
                    $dual_login = true === Options_Service::get_global_boolean_var( 'redirect_to_login' )
                        ? 'DUAL_LOGIN'
                        : ( true === Options_Service::get_global_boolean_var( 'redirect_to_login_v2' )
                            ? 'DUAL_LOGIN_V2'
                            : '' );
                    
                    if ( !empty( $dual_login ) ) {
                        $redirect_url = Options_Service::get_global_string_var( 'redirect_url' );
                        $referer = ( stripos( $redirect_url, 'https' ) !== false ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        $login_url = Url_Helpers::get_preferred_login_url();
                        $login_url = add_query_arg( 'login_errors', $dual_login, $login_url );
                        $login_url = add_query_arg( 'redirect_to', rawurlencode( $referer ), $login_url );
                        Url_Helpers::force_redirect(  $login_url );
                        exit();
                    }
                }
            }
        }
    }