<?php
    
    namespace Wpo\Services;

    use \Wpo\Core\Plugin_Helpers;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\License_Service' ) ) {
    
        class License_Service {

            /**
             * Checks whether a valid EDD license has been activated
             * 
             * @since 7.15
             * 
             * @return mixed(boolean|string) true if active otherwise error message
             */
            public static function check_license() {
                // Basic edition doesn't require a license
                if ( !isset( $GLOBALS[ 'WPO_CONFIG' ][ 'extensions' ] ) || !\is_array( $GLOBALS[ 'WPO_CONFIG' ][ 'extensions' ] ) || sizeof( $GLOBALS[ 'WPO_CONFIG' ][ 'extensions' ] ) !== 1 ) {
                    $warning = 'License check skipped, because cannot determine unambigiousy what extension is being used (see log for details).';
                    Log_Service::write_log( 'WARN', $warning );
                    Log_Service::write_log( 'WARN', $GLOBALS[ 'WPO_CONFIG' ][ 'extensions' ] );
                    return self::prepare_response( $warning, 'WARN' );
                }

                $license_key = Options_Service::get_global_string_var( 'license_key' );

                // No license to check
                if ( empty( $license_key ) ) {
                    return self::prepare_response( 'License check skipped, because no license key was found.', 'ERROR' );
                }
                
                // Try check license
                $store_url = $GLOBALS[ 'WPO_CONFIG' ][ 'store' ];
                $item_name = $GLOBALS[ 'WPO_CONFIG' ][ 'store_item' ];
                
                $api_params = array(
                    'edd_action' => 'check_license',
                    'license' => $license_key,
                    'item_name' => urlencode( $item_name ),
                    'url' => $GLOBALS[ 'WPO_CONFIG' ][ 'url_info' ][ 'wp_site_url' ]
                );

                // Process response after license check
                $response = wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
                return self::process_response( $response, true );
            }

            /**
             * Tries and automatically activate the license for the current site if a key was found.
             * 
             * @since 7.15
             * 
             * @return mixed(boolean|string) True if active otherwise error message.
             */
            private static function try_activate() {
                $store_url = $GLOBALS[ 'WPO_CONFIG' ][ 'store' ];
                $item_id = $GLOBALS[ 'WPO_CONFIG' ][ 'store_item_id' ];
                $license_key = Options_Service::get_global_string_var( 'license_key' );

                $api_params = array(
                    'edd_action' => 'activate_license',
                    'license'    => $license_key,
                    'item_id'    => $item_id,
                    'url'        => $GLOBALS[ 'WPO_CONFIG' ][ 'url_info' ][ 'wp_site_url' ]
                );
                
                $response = wp_remote_post( $store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
                return self::process_response( $response, false ); 
            }

            /**
             * Generic handler for any EDD license API response (for checking and activation of license).
             * 
             * @since 7.15
             * 
             * @param $response mixed
             * @param $try_activate boolean if license is not active try and activate it if a key is found
             * 
             * @return mixed(boolean|string) True if license is valid otherwise error message.
             */
            private static function process_response( $response, $try_activate = false ) {

                // Unknown error occurred
                if ( is_wp_error( $response ) ) {
                    return self::prepare_response( 'Checking license key failed (wp error:  ' . $response->get_error_message() . ').', 'ERROR' );
                }

                $license_data = json_decode( wp_remote_retrieve_body( $response ) );

                // Unexpected response
                if ( !is_object( $license_data ) ) {
                    Log_Service::write_log( 'ERROR', $license_data );
                    return self::prepare_response( 'Checking license key failed (see error log for detailed response).', 'ERROR' );
                }

                // Handle error
                if ( property_exists( $license_data, 'error' ) ) {
                    $error_message = self::process_error( $license_data );
                    return self::prepare_response( 'Checking license key failed (error:  ' . $error_message . ').', 'ERROR' );
                }

                // Try activate if a key has been configured
                if ( property_exists( $license_data, 'license' ) 
                    && $license_data->license != 'valid') {

                        if ( !empty( Options_Service::get_global_string_var( 'license_key' ) ) ) {
                            return $try_activate 
                                ? self::try_activate()
                                : self::prepare_response( 'Not trying to activate the license (most likely a previous attempt already failed - check the error log for details).', 'ERROR' );
                        }

                        return self::prepare_response( 'License key not found and hence skipped attempt to activate license.', 'ERROR' );
                }

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> License active / has been activated.' );
                return true;
            }

            /**
             * Generic error handler for EDD license API (activation)
             * 
             * @since 7.15
             * 
             * @param stdClass license data object received from EDD license API
             * 
             * @return string Error message
             */
            private static function process_error( $license_data ) {

                $message = '';

                switch( $license_data->error ) {
					case 'expired' :
						$message = sprintf(
							__( 'Your license key expired on %s.', 'wpo365-login' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
                        $message = __( 'Your license key has been disabled.', 'wpo365-login' );
						break;
					case 'missing' :
                        $message = __( 'Invalid license.', 'wpo365-login' );
						break;
					case 'invalid' :
					case 'site_inactive' :
                        $message = __( 'Your license is not active for this URL.', 'wpo365-login' );
                        break;
					case 'item_name_mismatch' :
                        $message = sprintf( __( 'This appears to be an invalid license key for %s.', 'wpo365-login' ), $GLOBALS[ 'WPO_CONFIG' ][ 'store_item' ] );
						break;
					case 'no_activations_left':
                        $message = __( 'Your license key has reached its activation limit.', 'wpo365-login' );
						break;
					default :
                        $message = __( 'An error occurred, please try again.', 'wpo365-login' );
                }
                
                return $message;
            }

            /**
             * Simple helper that logs the message that is eventually returned.
             * 
             * @since 9.1
             * 
             * @param string $message Error message that will be logged and returned
             * @param string $log_level ERROR or DEBUG
             * @return string Returns $message
             */
            private static function prepare_response( $message, $log_level ) {
                Log_Service::write_log( $log_level, __METHOD__ . ' -> ' . $message );
                return $message;
            }
        }
    }