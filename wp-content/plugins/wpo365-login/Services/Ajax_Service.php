<?php

    namespace Wpo\Services;

    use \Wpo\Core\Permissions_Helpers;
    use \Wpo\Core\Wpmu_Helpers;
    use \Wpo\Services\Access_Token_Service;
    use \Wpo\Services\License_Service;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;

    if ( !class_exists( '\Wpo\Services\Ajax_Service' ) ) {
    
        class Ajax_Service  {

            /**
             * Gets the tokencache with all available bearer tokens
             *
             * @since 5.0
             *
             * @return void
             */
            public static function get_tokencache() {
                
                if ( false === Options_Service::get_global_boolean_var( 'enable_token_service' ) ) {
                    wp_die();
                }

                // Verify AJAX request
                $current_user = self::verify_ajax_request( 'to get the tokencache for a user' );

                self::verify_POSTed_data( array( 'action', 'scope' ) ); // -> wp_die()

                $access_token = Access_Token_Service::get_access_token( $_POST[ 'scope' ] );
                    
                if ( is_wp_error( $access_token ) ) {
                    self::AJAX_response( 'NOK', $access_token->get_error_code(), $access_token->get_error_message(), null );
                }
                    
                $result = new \stdClass();
                $result->expiry = $access_token->expiry;
                $result->accessToken = $access_token->access_token;

                self::AJAX_response( 'OK', '', '', json_encode( $result ) );
            }

            /**
             * Delete all access and refresh tokens.
             *
             * @since xxx
             */
            public static function delete_tokens() {
                // Verify AJAX request
                $current_user = self::verify_ajax_request( 'to delete access and refresh tokens' );
                
                if ( false === Permissions_Helpers::user_is_admin( $current_user ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> User has no permission to get wpo365_options from AJAX service' );
                    wp_die();
                }

                try {
                    global $wpdb;

                    $query_result = $wpdb->query( 
                        $wpdb->prepare( 
                            "DELETE FROM $wpdb->usermeta
                            WHERE meta_key like %s 
                            OR meta_key like %s", 
                                'wpo_access%', 'wpo_refresh%' 
                        )
                    );

                    delete_option( Access_Token_Service::SITE_META_ACCESS_TOKEN );

                    if ( false === $query_result ) {
                        self::AJAX_response( 'NOK', '', '', null);
                    }
                    else {
                        self::AJAX_response( 'OK', '', '', null );
                    }
                }
                catch ( \Exception $e ) {
                    $error_message = $e->getMessage;
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> AJAX request for settings failed: ' . $error_message );
                    self::AJAX_response( 'NOK', '', $error_message, null );
                }
            }

            /**
             * Gets the tokencache with all available bearer tokens
             *
             * @since 6.0
             *
             * @return void
             */
            public static function get_settings() {
                // Verify AJAX request
                $current_user = self::verify_ajax_request( 'to get the wpo365-login settings' );

                if ( false === Permissions_Helpers::user_is_admin( $current_user ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> User has no permission to get wpo365_options from AJAX service' );
                    wp_die();
                }

                $camel_case_options = Options_Service::get_options();
                self::AJAX_response( 'OK', '', '', json_encode( $camel_case_options ) );
            }

            /**
             * Gets the tokencache with all available bearer tokens
             *
             * @since 9.6
             *
             * @return void
             */
            public static function get_self_test_results() {
                // Verify AJAX request
                $current_user = self::verify_ajax_request( 'to get the wpo365-login self-test results' );
                
                if ( false === Permissions_Helpers::user_is_admin( $current_user ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> User has no permission to get self-test results from AJAX service' );
                    wp_die();
                }

                $self_test_results = Wpmu_Helpers::mu_get_transient( 'wpo365_self_test_results' );

                if ( !empty( $self_test_results ) ) {
                    self::AJAX_response( 'OK', '', '', json_encode( $self_test_results ) );
                }
                else {
                    self::AJAX_response( 'OK', '', '', json_encode( array() ) );
                }
            }

            /**
             * Gets the tokencache with all available bearer tokens
             *
             * @since 6.0
             *
             * @return void
             */
            public static function update_settings() {
                // Verify AJAX request
                $current_user = self::verify_ajax_request( 'to update the wpo365-login settings' );

                if ( false === Permissions_Helpers::user_is_admin( $current_user ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> User has no permission to get wpo365_options from AJAX service' );
                    wp_die();
                }

                self::verify_POSTed_data( array( 'settings' ) ); // -> wp_die()

                $updated = Options_Service::update_options( $_POST[ 'settings' ] );

                self::AJAX_response( true === $updated ? 'OK' : 'NOK', '', '', null );
            }

            /**
             * Tries to activate the license using the previously saved license key.
             *
             * @since 6.0
             *
             * @return void
             */
            public static function activate_license() {
                // Verify AJAX request
                $current_user = self::verify_ajax_request( 'to activate license' );

                if ( false === Permissions_Helpers::user_is_admin( $current_user ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> User has no permission to get wpo365_options from AJAX service' );
                    wp_die();
                }

                self::verify_POSTed_data( array() ); // -> wp_die()

                $activation_result = License_Service::check_license();
                $status = $activation_result === true ? 'OK' : 'NOK';
                $error_message = $activation_result === true ? '' : $activation_result;

                self::AJAX_response( $status, '', $error_message, null );
            }

            /**
             * Gets the debug log
             *
             * @since 7.11
             *
             * @return void
             */
            public static function get_log() {
                // Verify AJAX request
                $current_user = self::verify_ajax_request( 'to get the wpo365-login debug log' );

                if ( false === Permissions_Helpers::user_is_admin( $current_user ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> User has no permission to get wpo365_log from AJAX service' );
                    wp_die();
                }

                $log = Wpmu_Helpers::mu_get_transient( 'wpo365_debug_log' );

                if ( empty( $log ) ) {
                    $log = array();
                }

                $log = array_reverse( $log );
                self::AJAX_response( 'OK', '', '', json_encode( $log ) );
            }

            /**
             * Used to proxy a request from the client-side to another O365 service e.g. yammer 
             * to circumvent CORS issues.
             *
             * @since 10.0
             *
             * @return void
             */
            public static function cors_proxy() {
                // Verify AJAX request
                $current_user = self::verify_ajax_request( 'to proxy a request' );

                self::verify_POSTed_data( array( 'url', 'method', 'bearer', 'accept', 'binary' ) ); // -> wp_die()

                $url = $_POST[ 'url' ];
                $method = $_POST[ 'method' ];
                $bearer = 'Authorization: Bearer ' . $_POST[ 'bearer' ];
                $headers[] = $bearer;
                $binary = filter_var( $_POST[ 'binary' ], FILTER_VALIDATE_BOOLEAN );

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url );
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );

                if ( true === $binary ) {
                    curl_setopt( $curl, CURLOPT_BINARYTRANSFER,1);
                }

                if ( stripos( $method, 'POST' ) !== false && array_key_exists( 'post_fields', $_POST ) ) {
                    curl_setopt( $curl, CURLOPT_POSTFIELDS, $_POST[ 'post_fields' ] );
                }

                if ( Options_Service::get_global_boolean_var( 'skip_host_verification' ) ) {
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); 
                }

                if ( !empty( $curl_proxy = Options_Service::get_global_string_var( 'curl_proxy' ) ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Using curl proxy ' . $curl_proxy );
                    curl_setopt( $curl, CURLOPT_PROXY, $curl_proxy );
                }

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Requesting data from ' . $url );

                $raw = curl_exec( $curl );

                Log_Service::write_log( 'DEBUG', $raw );

                $curl_response_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

                if ( curl_error( $curl ) ) {
                    $error_message = 'Error occured whilst fetching from ' . $url . ': ' . curl_error( $curl );
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> ' . $error_message );
                    curl_close( $curl );

                    self::AJAX_response( 'NOK', '', $error_message, null );
                }

                curl_close( $curl );

                if ( $binary ) {
                    self::AJAX_response( 'OK', '', '', base64_encode( $raw ) );
                    // -> die()
                }

                json_decode( $raw );
                $json_error = json_last_error();
                
                if ( $json_error == JSON_ERROR_NONE ) {
                    self::AJAX_response( 'OK', '', '', $raw );
                }

                self::AJAX_response( 'NOK', '', $json_error, null );
            }

            /**
             * Checks for valid nonce and whether user is logged on and returns WP_User if OK or else
             * writes error response message and return it to requester
             *
             * @since 5.0
             *
             * @param   string      $error_message_fragment used to write a specific error message to the log
             * @return  WP_User if verified or else error response is returned to requester
             */
            public static function verify_ajax_request( $error_message_fragment )  {
                $error_message = '';

                if ( !is_user_logged_in() )
                    $error_message = 'Attempt ' . $error_message_fragment . ' by a user that is not logged on';

                if ( Options_Service::get_global_boolean_var( 'enable_nonce_check' ) 
                    && ( !isset( $_POST[ 'nonce' ] )
                    || !wp_verify_nonce( $_POST[ 'nonce' ], 'wpo365_fx_nonce' ) ) )
                        $error_message = 'Request ' . $error_message_fragment . ' has been tampered with (invalid nonce)';

                if (strlen($error_message) > 0) {
                    Log_Service::write_log('DEBUG', __METHOD__ . ' -> ' . $error_message);

                    $response = array('status' => 'NOK', 'message' => $error_message, 'result' => array());
                    wp_send_json($response);
                    wp_die();
                }

                return wp_get_current_user();
            }

            /**
             * Stops the execution of the program flow when a key is not found in the the global $_POST
             * variable and returns a given error message
             *
             * @since 5.0
             *
             * @param   array   $keys array of keys to search for
             * @return void
             */
            public static function verify_POSTed_data( $keys, $sanitize = true ) {

                foreach ( $keys as $key ) {

                    if ( !array_key_exists( $key, $_POST ) ) 
                        self::AJAX_response( 'NOK', '1000', 'Incomplete data posted to complete request: ' . implode( ', ', $keys ), array() );

                    if ( $sanitize ) {
                        $_POST[ $key ] = sanitize_text_field( $_POST[ $key ] );
                    }
                }
            }

            /**
             * Helper method to standardize response returned from a Pintra AJAX request
             *
             * @since 5.0
             *
             * @param   string  $status OK or NOK
             * @param   string  $message customer message returned to requester
             * @param   mixed   $result associative array that is parsed as JSON and returned
             * @return void
             */
            public static function AJAX_response($status, $error_codes, $message, $result) {
                Log_Service::write_log('DEBUG', __METHOD__ . " -> Sending an AJAX response with status $status and message $message");
                wp_send_json(array('status' => $status, 'error_codes' => $error_codes, 'message' => $message, 'result' => $result));
                wp_die();
            }
        }
    }
