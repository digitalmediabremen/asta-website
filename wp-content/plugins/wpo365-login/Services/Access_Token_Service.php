<?php
    
    namespace Wpo\Services;

    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    use \Wpo\Services\Request_Service;
    use \Wpo\Services\User_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Access_Token_Service' ) ) {
    
        class Access_Token_Service {

            const SITE_META_ACCESS_TOKEN = 'wpo_app_only_access_tokens';
            const USR_META_REFRESH_TOKEN = 'wpo_refresh_token';
            const USR_META_ACCESS_TOKEN = 'wpo_access_tokens';
            const USR_META_WPO365_AUTH_CODE = 'WPO365_AUTH_CODE';
            
            /**
             * Gets an access token in exchange for an authorization token that was received prior when getting
             * an OpenId Connect token or for a fresh code in case available. This method is only compatible with 
             * AAD v2.0
             *
             * @since   5.2
             * 
             * @param $scope string Scope for AAD v2.0 e.g. https://graph.microsoft.com/user.read
             *
             * @return mixed(stdClass|WP_Error) access token as object or WP_Error
             */
            public static function get_access_token( $scope ) {

                $client_secret = Options_Service::get_global_string_var( 'application_secret' );

                $current_user_id = \get_current_user_id();

                $request_service = Request_Service::get_instance();
                $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );
                $user_is_logging_in = !empty( $request->get_item( 'id_token' ) );
                $previous_access_token_error = !empty( $request->get_item( 'access_token_error' ) );

                if ( true === $previous_access_token_error ) {
                    $warning = 'Cannot retrieve an access token for scope ' . $scope . ' (See previous error)';
                    Log_Service::write_log( 'WARN', __METHOD__ . " -> $warning" );
                    return new \WP_Error( '1025', $warning );
                }
                
                if ( empty( $current_user_id ) && !$user_is_logging_in ) {
                    $warning = 'Cannot retrieve an access token for scope ' . $scope . ' when no logged-on user is detected and the use of an app-only access token has not been configured. See <a href="https://docs.wpo365.com/article/101-app-only-integration" target="_blank">https://docs.wpo365.com/article/101-app-only-integration</a> for more information.';
                    Log_Service::write_log( 'WARN', __METHOD__ . " -> $warning" );
                    $request->set_item( 'access_token_error', $warning );
                    return new \WP_Error( '1020', $warning );
                }
                
                $cached_access_token = self::get_cached_access_token( $scope );

                if ( !empty( $cached_access_token ) ) {
                    return $cached_access_token;
                }

                if ( empty( $client_secret ) ) {
                    $warning = 'Cannot retrieve an access token for scope ' . $scope . ' because the Administrator 
                    has not configured a client secret. Please check the 
                    <a href="https://docs.wpo365.com/article/23-integration" target="_blank">documentation</a> 
                    for detailed step-by-step instructions on how to configure integration with Microsoft Graph and
                    other 365 services';
                    Log_Service::write_log( 'WARN', __METHOD__ . " -> $warning" );
                    $request->set_item( 'access_token_error', $warning );
                    return new \WP_Error( '1025', $warning );
                }

                $authorization_code = self::get_authorization_code();
                $refresh_token = self::get_refresh_token();

                if ( empty( $authorization_code ) && empty( $refresh_token ) ) {
                    
                    $warning = 'No authorization code and refresh token found when trying to get an access token 
                        for ' . $scope . '. The current user must sign out of the WordPress website and log back in again to 
                        retrieve a fresh authorization code that can be used in exchange for access tokens. If
                        this error occurs regularly, then please check the 
                        <a href="https://docs.wpo365.com/article/23-integration" target="_blank">documentation</a> 
                        for detailed step-by-step instructions on how to configure integration with Microsoft Graph and
                        other 365 services.';
                    Log_Service::write_log( 'WARN', __METHOD__ . " -> $warning" );
                    $request->set_item( 'access_token_error', $warning );
                    return new \WP_Error( '1030', $warning );
                }

                if ( stripos( $scope, 'https://analysis.windows.net/powerbi/api/.default' ) === 0 ) {
                    $params = array(
                        'client_id' => Options_Service::get_global_string_var( 'application_id' ),
                        'client_secret' => Options_Service::get_global_string_var( 'application_secret' ),
                        'client_info' => 1,
                        'scope' =>  $scope,
                        'grant_type' => 'client_credentials',
                    );
                }
                else {
                    $params = array(
                        'client_id' => Options_Service::get_global_string_var( 'application_id' ),
                        'client_secret' => Options_Service::get_global_string_var( 'application_secret' ),
                        'redirect_uri' => Options_Service::get_global_string_var( 'redirect_url' ),
                        'scope' =>  'offline_access ' . $scope,
                    );
                }

                // Check if we have a refresh token and if not fallback to the auth code
                
                if ( !isset( $params[ 'grant_type' ] ) ) {

                    if ( !empty( $refresh_token) ) {
                        $params[ 'grant_type' ] = 'refresh_token';
                        $params[ 'refresh_token' ] = $refresh_token->refresh_token;
                    }
                    else {

                        if ( !empty( $authorization_code ) ) {
                            $params[ 'grant_type' ] = 'authorization_code';
                            $params[ 'code' ] = $authorization_code->code;
                        }
                    }
                }
                
                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Requesting access token for ' . $scope );
                
                $params_as_str = http_build_query( $params, '', '&' ); // Fix encoding of ampersand
                
                $home_tenant_id = Options_Service::get_global_string_var( 'tenant_id' );
                $user_tenant_id = User_Service::try_get_user_tenant_id( $current_user_id );
                
                $tenant_id = Options_Service::get_global_boolean_var( 'multi_tenanted' ) && stripos( $home_tenant_id, $user_tenant_id ) === false
                    ? 'common'
                    : $home_tenant_id;
                
                $authorizeUrl = "https://login.microsoftonline.com/$tenant_id/oauth2/v2.0/token";
                
                $curl = curl_init();
                curl_setopt( $curl, CURLOPT_POST, 1 );
                curl_setopt( $curl, CURLOPT_URL, $authorizeUrl );
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $curl, CURLOPT_POSTFIELDS, $params_as_str );
                curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 
                    'Content-Type: application/x-www-form-urlencoded'
                ) );

                if ( true === Options_Service::get_global_boolean_var( 'skip_host_verification' ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Skipping SSL peer and host verification' );
                    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); 
                    curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 ); 
                }

                if ( !empty( $curl_proxy = Options_Service::get_global_string_var( 'curl_proxy' ) ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Using curl proxy ' . $curl_proxy );
                    curl_setopt( $curl, CURLOPT_PROXY, $curl_proxy );
                }
            
                $result = curl_exec( $curl ); // result holds the tokens
            
                if ( curl_error( $curl ) ) {
                    $warning = 'Error occured whilst getting an access token: ' . curl_error( $curl );
                    Log_Service::write_log( 'WARN', __METHOD__ . " -> $warning" );
                    curl_close( $curl );
                    $request->set_item( 'access_token_error', $warning );
                    return new \WP_Error( '1040', $warning );
                }
            
                curl_close( $curl );

                // Validate the access token and return it
                $access_token = json_decode( $result );
                $access_token = self::validate_access_token( $access_token );

                if ( is_wp_error( $access_token ) ) {
                    $warning = 'Access token for ' . $scope . ' is not valid: ' . $access_token->get_error_message();
                    Log_Service::write_log( 'WARN', __METHOD__ . " -> $warning" );
                    $request->set_item( 'access_token_error', $warning );
                    return new \WP_Error( $access_token->get_error_code(), $warning );
                }

                $access_token->expiry = time() + intval( $access_token->expires_in );
                $access_tokens = $request->get_item( 'access_tokens' );

                if ( empty( $access_tokens ) ) {
                    $access_tokens = array();
                }

                // Save access token as request variable -> will be saved on shutdown
                $access_tokens[] = $access_token;
                $request->set_item( 'access_tokens', $access_tokens );

                // Save refresh token as request variable -> will be saved on shutdown
                if ( property_exists( $access_token, 'refresh_token' ) ) {
                    $refresh_token = new \stdClass();
                    $refresh_token->refresh_token = $access_token->refresh_token;
                    $refresh_token->scope = $access_token->scope;
                    $refresh_token->expiry = time( ) + 1209600;
                    $request->set_item( 'refresh_token', $refresh_token );
                }
                
                /**
                 * @since 10.6
                 * 
                 * The wpo365_access_token_processed action hook signals to its subscribers
                 * that a user has just received a fresh access token. As arguments
                 * it provides the WordPress user ID and the (bearer) access token.
                 */
                
                if ( defined( 'WPO_ALLOW_DEVELOPER_HOOKS' ) && constant( 'WPO_ALLOW_DEVELOPER_HOOKS' ) === true ) {
                    do_action( 'wpo365_access_token_processed', $wp_usr_id, $access_token->access_token );
                }
                
                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Successfully obtained a valid access token for ' . $scope );

                return $access_token;
            }

            /**
             * @since 11.0
             */
            private static function get_cached_access_token( $scope ) {
                $request_service = Request_Service::get_instance();
                $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );
                $access_tokens = $request->get_item( 'access_tokens' );
                $wp_usr_id = get_current_user_id(); // 0 if user is not (yet) logged in

                if ( empty( $access_tokens ) ) {
                    $access_tokens = array();
                }

                // Tokens are stored by default as user metadata
                $cached_access_tokens_json = get_user_meta( 
                    $wp_usr_id, 
                    self::USR_META_ACCESS_TOKEN, 
                    true );
                
                if ( !empty( $cached_access_tokens_json ) ) {
                    $cached_access_tokens = json_decode( $cached_access_tokens_json );

                    // json_decode returns null or it isn't an array if an "old" token is found
                    if ( empty( $cached_access_tokens ) || !is_array( $cached_access_tokens ) ) {
                        delete_user_meta( $wp_usr_id, self::USR_META_ACCESS_TOKEN );
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Deleted an access token that is no longer supported.' );
                        Log_Service::write_log( 'DEBUG', $cached_access_tokens );
                        $cached_access_tokens = array();
                    }

                    foreach ( $cached_access_tokens as $key => $cached_access_token ) {
                        
                        if ( isset( $cached_access_token->expiry ) && intval( $cached_access_token->expiry ) < time() ) {
                            unset( $cached_access_tokens[ $key ] );
                            update_user_meta( 
                                $wp_usr_id, 
                                self::USR_META_ACCESS_TOKEN, 
                                json_encode( $cached_access_tokens ) );
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Deleted an expired access token.' );
                        }
                    }

                    $access_tokens = array_merge( $access_tokens, $cached_access_tokens );
                }

                foreach ( $access_tokens as $key => $access_token ) {
                        
                    if ( isset( $access_token->scope ) && false !== stripos( $access_token->scope, $scope ) ) {
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found a previously saved access token for ( ' . $scope . ' ) ' . $access_token->scope . ' that is still valid' );
                        return $access_token;
                    }
                }
            }

            /**
             * @since 11.0
             */
            public static function save_access_tokens( $access_tokens ) {
                $wp_usr_id = get_current_user_id();

                if ( empty( $wp_usr_id ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Cannot save access tokens for user that is not logged in.' );
                    return;
                }

                // Tokens are stored by default as user metadata
                $cached_access_tokens_json = get_user_meta( 
                    $wp_usr_id, 
                    self::USR_META_ACCESS_TOKEN, 
                    true );
                
                $cached_access_tokens = array();
                
                if ( !empty( $cached_access_tokens_json ) ) {
                    $cached_access_tokens = json_decode( $cached_access_tokens_json );

                    // json_decode returns null or it isn't an array if an "old" token is found
                    if ( empty( $cached_access_tokens ) || !is_array( $cached_access_tokens ) ) {
                        delete_user_meta( $wp_usr_id, self::USR_META_ACCESS_TOKEN );
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Deleted an access token that is no longer supported.' );
                        $cached_access_tokens = array();
                    }

                    foreach ( $cached_access_tokens as $key => $cached_access_token ) {
                        
                        if ( isset( $cached_access_token->expiry ) && intval( $cached_access_token->expiry ) < time() ) {
                            unset( $cached_access_tokens[ $key ] );
                            update_user_meta( 
                                $wp_usr_id, 
                                self::USR_META_ACCESS_TOKEN, 
                                json_encode( $cached_access_tokens ) );
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Deleted an expired access token.' );
                        }
                        
                    }
                }

                $cached_access_tokens = array_merge( $cached_access_tokens, $access_tokens );

                update_user_meta( 
                    $wp_usr_id, 
                    self::USR_META_ACCESS_TOKEN, 
                    json_encode( $cached_access_tokens ) );
                
                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Successfully saved access codes' );
            }

            /**
             * Gets an app only access token. This method is only compatible with AAD v2.0
             *
             * @since   10.0
             * 
             * @param $scope string Scope for AAD v2.0 e.g. https://graph.microsoft.com/user.read
             *
             * @return mixed(stdClass|WP_Error) access token as object or WP_Error
             */
            public static function get_app_only_access_token() {
                // Tokens are stored by default as user metadata
                $cached_access_token_json = get_option( self::SITE_META_ACCESS_TOKEN );
                
                // Valid access token was saved previously
                if ( !empty( $cached_access_token_json ) ) {
                    $cached_access_token = json_decode( $cached_access_token_json );
                    
                    // json_decode returns NULL or it isn't an array if an "old" token is found
                    if ( empty( $cached_access_token ) ) {
                        delete_option( self::SITE_META_ACCESS_TOKEN );
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Deleted an invalid app-only access token.' );
                    }

                    // Valid app only token is expired
                    if ( isset( $cached_access_token->expiry ) && intval( $cached_access_token->expiry ) < time() ) {
                        delete_option( self::SITE_META_ACCESS_TOKEN );
                        $cached_access_token = null;
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Deleted an expired app-only access token.' );
                    }

                    // Valid app only token found
                    if ( !empty( $cached_access_token ) ) {
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found a previously saved app-only access token that is still valid' );
                        return $cached_access_token;
                    }
                }

                $params = array(
                    'client_id' => Options_Service::get_global_string_var( 'app_only_application_id' ),
                    'client_secret' => Options_Service::get_global_string_var( 'app_only_application_secret' ),
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://graph.microsoft.com/.default',
                );

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Requesting app-only access token' );
                
                $params_as_str = http_build_query( $params, '', '&' ); // Fix encoding of ampersand
                $directory_id = Options_Service::get_global_string_var( 'tenant_id' );
                $authorizeUrl = "https://login.microsoftonline.com/$directory_id/oauth2/v2.0/token";
                
                $curl = curl_init();
                curl_setopt( $curl, CURLOPT_POST, 1 );
                curl_setopt( $curl, CURLOPT_URL, $authorizeUrl );
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $curl, CURLOPT_POSTFIELDS, $params_as_str );
                curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 
                    'Content-Type: application/x-www-form-urlencoded'
                ) );

                if ( true === Options_Service::get_global_boolean_var( 'skip_host_verification' ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Skipping SSL peer and host verification' );
                    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); 
                    curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 ); 
                }

                if ( !empty( $curl_proxy = Options_Service::get_global_string_var( 'curl_proxy' ) ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Using curl proxy ' . $curl_proxy );
                    curl_setopt( $curl, CURLOPT_PROXY, $curl_proxy );
                }
            
                $result = curl_exec( $curl ); // result holds the token
            
                if ( curl_error( $curl ) ) {
                    $warning = 'Error occured whilst getting an app-only access token: ' . curl_error( $curl );
                    Log_Service::write_log( 'WARN', __METHOD__ . " -> $warning" );
                    curl_close( $curl );
                    return new \WP_Error( '1040', $warning );
                }
            
                curl_close( $curl );

                // Validate the access token and return it
                $access_token = json_decode( $result );
                $access_token = self::validate_access_token( $access_token );

                if ( is_wp_error( $access_token ) ) {
                    $warning = 'App-only access token is not valid: ' . $access_token->get_error_message();
                    Log_Service::write_log( 'WARN', __METHOD__ . " -> $warning" );
                    return new \WP_Error( $access_token->get_error_code(), $warning );
                }

                // Store the new token as user meta with the shorter ttl of both auth and token
                $access_token->expiry = time() + intval( $access_token->expires_in );

                update_option(
                    self::SITE_META_ACCESS_TOKEN, 
                    json_encode( $access_token ) );

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Successfully obtained a valid app-only access token' );
                Log_Service::write_log( 'DEBUG', $access_token );

                return $access_token;
            }

            /**
             * Helper to validate an oauth access token
             *
             * @since   5.0
             *
             * @param   object  access token as PHP std object
             * @return  mixed(stdClass|WP_Error) Access token as standard object or WP_Error when invalid   
             * @todo    make by reference instead by value
             */
            private static function validate_access_token( $access_token_obj ) {

                if ( isset( $access_token_obj->error ) ) {

                    return new \WP_Error( implode( ',', $access_token_obj->error_codes), $access_token_obj->error_description );
                }
            
                if ( empty( $access_token_obj ) 
                    || $access_token_obj === false
                    || !isset( $access_token_obj->access_token ) 
                    || !isset( $access_token_obj->expires_in ) 
                    || !isset( $access_token_obj->token_type )
                    || strtolower( $access_token_obj->token_type ) != 'bearer' ) {
            
                    Log_Service::write_log( 'DEBUG', $access_token_obj );
                    return new \WP_Error( '0', 'Unknown error occurred' );
                }
            
                return $access_token_obj;
            }

            /**
             * Tries and find a refresh token for an AAD resource stored as user meta in the form "expiration,token"
             * In case an expired token is found it will be deleted
             *
             * @since   5.2
             * 
             * @param   string  $resource   Name for the resource key used to store that resource in the site options
             * @return  (stdClass|NULL)  Refresh token or an empty string if not found or when expired
             */
            private static function get_refresh_token() {
                $request_service = Request_Service::get_instance();
                $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );
                $refresh_token = $request->get_item( 'refresh_token' );
                $wp_usr_id = get_current_user_id(); // 0 if user is not (yet) logged in

                if ( empty( $refresh_token ) ) {
                    $cached_refresh_token_json = get_user_meta( 
                        get_current_user_id(),
                        self::USR_META_REFRESH_TOKEN,
                        true );

                    if ( empty( $cached_refresh_token_json ) ) {
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not find a refresh token for user with ID ' . $wp_usr_id );
                        return null;
                    }

                    $refresh_token = json_decode( $cached_refresh_token_json );

                    if ( empty( $refresh_token ) ) {
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not parse cached refresh token for user with ID ' . $wp_usr_id );
                        return null;
                    }
                }

                if ( !\property_exists( $refresh_token, 'refresh_token' ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Deleted an invalid refresh token' );
                    delete_user_meta( get_current_user_id(), self::USR_META_REFRESH_TOKEN );
                    return null;
                }

                if ( isset( $refresh_token->expiry ) && intval( $refresh_token->expiry ) < time() ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Deleted an expired refresh token' );
                    delete_user_meta( get_current_user_id(), self::USR_META_REFRESH_TOKEN );
                    return null;
                }

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found a previously saved valid refresh token' );
                return $refresh_token;
            }

            /**
             * Helper method to persist a refresh token as user meta.
             * 
             * @since 5.1
             * 
             * @param stdClass $access_token Access token as standard object (from json)
             * @return void
             */
            public static function save_refresh_token( $refresh_token ) {

                $wp_usr_id = get_current_user_id();

                if ( empty( $wp_usr_id ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Cannot save refresh token for user that is not logged in.' );
                    return;
                }

                update_user_meta( 
                    $wp_usr_id,
                    self::USR_META_REFRESH_TOKEN,
                    json_encode( $refresh_token ) );

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Successfully saved refresh token' );
            }

            /**
             * Tries and find an authorization code stored as user meta
             * In case an expired token is found it will be deleted
             * 
             * @since 5.2
             * 
             * @return (stdClass|NULL)
             */
            private static function get_authorization_code() {
                $request_service = Request_Service::get_instance();
                $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );
                $authorization_code = $request->get_item( 'authorization_code' );
                $wp_usr_id = get_current_user_id(); // 0 if user is not (yet) logged in

                // Authorization code can only be used once
                if ( !empty( $authorization_code ) ) {
                    $request->remove_item( 'authorization_code' );
                }
                else {
                    $cached_authorization_code = get_user_meta( 
                        $wp_usr_id,
                        self::USR_META_WPO365_AUTH_CODE,
                        true );

                    if ( empty( $cached_authorization_code ) ) {
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not find an authorization code for user with ID ' . $wp_usr_id );
                        return null;
                    }

                    $authorization_code = json_decode( $cached_authorization_code );

                    if ( empty( $authorization_code ) ) {
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Could not parse cached authorization code for user with ID ' . $wp_usr_id );
                        return null;
                    }
                }
                
                $expired = isset( $authorization_code->expiry ) && intval( $authorization_code->expiry ) < time();
                delete_user_meta( $wp_usr_id, self::USR_META_WPO365_AUTH_CODE );

                if ( !$expired ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found a previously saved valid authorization code' );
                    return $authorization_code;
                }
                    
                return null;
            }

            /**
             * Helper method to persist a refresh token as user meta.
             * 
             * @since 5.1
             * 
             * @param stdClass $access_token Access token as standard object (from json)
             * @return void
             */
            public static function save_authorization_code( $authorization_code ) {

                $wp_usr_id = get_current_user_id();

                if ( empty( $wp_usr_id ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Cannot save authorization code for user that is not logged in.' );
                    return;
                }

                if ( !empty( $wp_usr_id ) ) {
                    update_user_meta( 
                        $wp_usr_id,
                        self::USR_META_WPO365_AUTH_CODE,
                        json_encode( $authorization_code ) );
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Successfully saved authorization code' );
                }                
            }
        }
    }