<?php
    
    namespace Wpo\Services;

    use \Wpo\Core\Domain_Helpers;
    use \Wpo\Services\Authentication_Service;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Login_Service' ) ) {
    
        class Login_Service { 

            /**
             * WordPress authentication hook that will be triggered before the authentication process 
             * is started.
             * 
             * @param   string  $user_name User name (by reference) the user entered in the login form.
             * 
             * @return void
             */
            public static function prevent_default_login_for_o365_users( &$user_name ) {

                if ( empty( $user_name ) ) {
                    return;
                }
                    
                if ( false === Options_Service::get_global_boolean_var( 'intercept_wp_login' ) ) {
                    return;
                }

                // If the user name is an email address we get the domain otherwise false
                $email_domain = Domain_Helpers::get_smtp_domain_from_email_address( $user_name );

                if ( empty( $email_domain ) ) 
                    return;

                if ( true === Domain_Helpers::is_tenant_domain( $email_domain ) ) {
                    Log_Service::write_log( 'DEBUG', 'Authentication attempt detected by O365 user ' . $user_name );
                    Authentication_Service::redirect_to_microsoft( $user_name );
                    // -> Script will exit
                }
            }
        }
    }
