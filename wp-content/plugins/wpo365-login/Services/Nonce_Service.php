<?php
    
    namespace Wpo\Services;

    use \Wpo\Core\Wpmu_Helpers;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Nonce_Service' ) ) {
    
        class Nonce_Service {
            /**
             * Creates a nonce using the nonce_secret
             * 
             * @since 1.6
             * 
             * @return (string|WP_Error) nonce as a string otherwise an WP_Error (most likely when dependency are missing)
             */
            public static function get_nonce() {
                $nonce_value = uniqid( '', true ); // e.g. 5f61e2dcef1ea4.18237943
                $nonce_expiry = time() + 3600; // 60 * 60 = 1 hours
                $nonce = "$nonce_expiry.$nonce_value"; // e.g. 1600272187.5f61e2dcef1ea4.18237943
                $nonces = Wpmu_Helpers::mu_get_transient( 'wpo365_nonces' );

                if ( empty( $nonces ) || !is_array( $nonces ) ) {
                    $nonces = array();
                }

                $nonces[] = $nonce;

                Wpmu_Helpers::mu_set_transient( 'wpo365_nonces', $nonces );
                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Generated nonce: ' . $nonce );                
                $nonces_count = count( $nonces );

                if ( $nonces_count > 1000 ) {
                    $nonces = array_slice( $nonces, 500 );
                    Wpmu_Helpers::mu_set_transient( 'wpo365_nonces', $nonces );
                }

                return $nonce;
            }

            /**
             * Validates a nonce created with Nonce_Helpers::get_nonce()
             * 
             * @since 1.6
             * 
             * @param string $nonce encoded nonce value to validate
             * @return (boolean|WP_Error) true when valide otherwise WP_Error
             */
            public static function validate_nonce( $nonce ) {

                // Skip validation of the nonce
                if ( true === Options_Service::get_global_boolean_var( 'skip_nonce_verification' ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Nonce check has been disabled by the user' );
                    return true;
                }

                $nonces = Wpmu_Helpers::mu_get_transient( 'wpo365_nonces' );
                $used_nonces = Wpmu_Helpers::mu_get_transient( 'wpo365_used_nonces' );

                if ( empty( $used_nonces ) || !is_array( $used_nonces ) ) {
                    $used_nonces = array();
                }

                $exists = !empty( $nonces ) && is_array( $nonces ) && false !== array_search( $nonce, $nonces );
                $not_used = false === array_search( $nonce, $used_nonces );
                $used_nonces[] = $nonce;
                $used_nonces_count = count( $used_nonces );

                if ( $used_nonces_count > 1000 ) {
                    $used_nonces = array_slice( $used_nonces, 500 );
                }

                Wpmu_Helpers::mu_set_transient( 'wpo365_used_nonces', $used_nonces );

                if ( $exists && $not_used ) {
                    return true;
                }
                else {
                    $error_message = __METHOD__ . ' -> Login has been tampered with [nonce ' . $nonce . ' not found]';
                    Log_Service::write_log( 'WARN', $error_message );
                    return new \WP_Error( '5040', $error_message );
                }
            }                
        }
    }