<?php

    namespace Wpo\Services;

    use \Wpo\Core\Url_Helpers;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;

    if ( !class_exists( '\Wpo\Services\Logout_Service' ) ) {
    
        class Logout_Service  {

            /**
             * Hooks into a default logout action and additionally logs out the user from Office 365 before sending
             * the user to the default login page.
             * 
             * @since 3.1
             * 
             * @return void
             */
            public static function logout_O365() {
                
                if ( Options_Service::get_global_boolean_var( 'logout_from_o365' ) ) {
                    $post_logout_redirect_uri = Options_Service::get_global_string_var( 'post_signout_url' );
                    if ( empty( $post_logout_redirect_uri ) )
                        $post_logout_redirect_uri = Url_Helpers::get_preferred_login_url();
                    
                    $logout_url = "https://login.microsoftonline.com/common/oauth2/logout?post_logout_redirect_uri=$post_logout_redirect_uri";
                    Url_Helpers::force_redirect( $logout_url );
                }
            }
        }
    }