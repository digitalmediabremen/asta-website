<?php
    
    namespace Wpo\Services;

    use \Wpo\Firebase\JWT;
    use \Wpo\Services\Authentication_Service;
    use \Wpo\Services\Error_Service;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Nonce_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Id_Token_Service' ) ) {
    
        class Id_Token_Service {

            /**
             * Constructs the oauth authorize URL that is the end point where the user will be sent for authorization.
             * 
             * @since 4.0
             * 
             * @since 11.0 Dropped support for the v1 endpoint.
             * 
             * @param $login_hint string Login hint that will be added to Open Connect ID link
             * @param $redirect_to string Link where the user will be redirected to
             * 
             * @return string if everthing is configured OK a valid authorization URL
             */
            public static function get_openidconnect_url( $login_hint = null, $redirect_to = null ) {
                $redirect_to = !empty( $redirect_to )
                    ? $redirect_to
                    : ( isset( $_SERVER[ 'HTTP_REFERER' ] ) 
                        ? $_SERVER[ 'HTTP_REFERER' ]
                        : $GLOBALS[ 'WPO_CONFIG' ][ 'url_info' ][ 'wp_site_url' ] );

                $params = array( 
                    'client_id'     => Options_Service::get_global_string_var( 'application_id' ),
                    'response_type' => 'id_token code',
                    'redirect_uri'  => Options_Service::get_global_string_var( 'redirect_url' ),
                    'response_mode' => 'form_post',
                    'scope'         => 'openid email profile',
                    'state'         => $redirect_to,
                    'nonce'         => Nonce_Service::get_nonce(),
                );

                /**
                 * @since 9.4
                 * 
                 * Add ability to configure a domain hint to prevent Microsoft from
                 * signing in users that are already logged in to a different O365 tenant.
                 */
                $domain_hint = Options_Service::get_global_string_var( 'domain_hint' );

                if ( !empty( $domain_hint ) ) {
                    $params[ 'domain_hint' ] = $domain_hint;
                }
                
                if ( !empty( $login_hint ) ) {
                    $params[ 'login_hint' ] = $login_hint;
                }

                $directory_id = Options_Service::get_global_string_var( 'tenant_id' );
                $multi_tenanted = Options_Service::get_global_boolean_var( 'multi_tenanted' );
                
                if ( true === $multi_tenanted ) {
                    $directory_id = 'common';
                }

                $auth_url = 'https://login.microsoftonline.com/' 
                    . $directory_id 
                    . '/oauth2' 
                    . '/v2.0' 
                    . '/authorize?' 
                    . http_build_query( $params, '', '&' );
                
                    Log_Service::write_log( 'DEBUG', __METHOD__ . " -> Open ID Connect URL: $auth_url" );

                return $auth_url;
            }

            /**
             * 
             */
            public static function process_openidconnect_token() {
                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Processing incoming OpenID Connect id_token' );

                // Decode the id_token
                $id_token = self::decode_id_token();

                // Handle if token could not be processed
                if ( $id_token === false ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> ID token could not be processed and user will be redirected to default Wordpress login.' );
                    Authentication_Service::goodbye( Error_Service::ID_TOKEN_ERROR );
                    exit();
                }

                // Handle if nonce is invalid 
                if ( is_wp_error( $nonce_validation_result = Nonce_Service::validate_nonce( $id_token->nonce ) ) ) {
                    Log_Service::write_log( 'ERROR', $nonce_validation_result->get_error_message() );
                    Authentication_Service::goodbye( Error_Service::TAMPERED_WITH );
                    exit();
                }

                // Log id token if configured
                if ( true === Options_Service::get_global_boolean_var( 'debug_log_id_token' ) ) {
                    Log_Service::write_log( 'DEBUG', $id_token );
                }

                $request_service = Request_Service::get_instance();
                $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );

                // Store the Authorization Code in the Request
                if ( isset( $_POST[ 'code' ] ) ) {
                    // Session valid until
                    $authorization_code = new \stdClass();
                    $authorization_code->expiry = time() + 3480;
                    $authorization_code->code = $_POST[ 'code' ];

                    $request->set_item( 'authorization_code', $authorization_code );
                    unset( $_POST[ 'code' ] );
                    
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found authorization code and stored it temporarily as a request variable' );
                }

                $request->set_item( 'id_token', $id_token );

                /**
                 * @since 10.6
                 * 
                 * The wpo365_openid_token_processed action hook signals to its subscribers
                 * that a user has just signed in successfully with Microsoft. As arguments
                 * it provides the WordPress user ID and the user's Azure AD group IDs
                 * as an one-dimensional array of GUIDs (as strings).
                 */
                
                if ( defined( 'WPO_ALLOW_DEVELOPER_HOOKS' ) && true === constant( 'WPO_ALLOW_DEVELOPER_HOOKS' ) ) {
                    do_action( 'wpo365_openid_token_processed', $wp_usr->ID, $group_ids, $id_token );
                }
            }

            /**
             * Unraffles the incoming JWT id_token with the help of Firebase\JWT and the tenant specific public keys available from Microsoft.
             * 
             * @since   1.0
             *
             * @return  array|boolean 
             */
            public static function decode_id_token() {

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Processing an new id token' );

                // Get the token and get it's header for a first analysis
                if ( !isset( $_POST[ 'id_token' ] ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> ID token not found in posted data.' );
                    return false;
                }

                $id_token = $_POST[ 'id_token' ];
                $jwt_decoder = new JWT();
                $header = $jwt_decoder::header( $id_token );
                
                // Simple validation of the token's header
                if ( !isset( $header->kid ) || !isset( $header->alg ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> JWT header is missing so stop here.' );
                    return false;
                }

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Algorithm found ' . $header->alg );

                // Discover tenant specific public keys
                $keys = self::discover_ms_public_keys( false );

                if ( $keys == null ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Could not retrieve public keys from Microsoft.' );
                    return false;
                }

                // Find the tenant specific public key used to encode JWT token
                $key = self::retrieve_ms_public_key( $header->kid, $keys );
                
                if ( empty( $key ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Could not find expected key in keys retrieved from Microsoft. Will retry but this time retrieve the wellknown Open ID connect configuration for this specific application.' );

                    // Discover tenant and application specific public keys
                    $keys = self::discover_ms_public_keys( true, true );

                    if ( $keys == null ) {
                        Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Could not retrieve public keys from Microsoft using the application specific jwks_uri.' );
                        return false;
                    }

                    // Find the tenant specific public key used to encode JWT token
                    $key = self::retrieve_ms_public_key( $header->kid, $keys );

                    if ( empty( $key ) ) {
                        Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Could not find expected key in the tenant and application specific keys retrieved from Microsoft.' );
                        return false;
                    }
                }

                $pem_string = "-----BEGIN CERTIFICATE-----\n" . chunk_split( $key, 64, "\n" ) . "-----END CERTIFICATE-----\n";

                // Decode the id_token
                try {
                    $decoded_token = $jwt_decoder::decode( 
                        $id_token, 
                        $pem_string,
                        array( strtoupper( $header->alg ) )
                    );
                }
                catch( \Exception $e ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Could not decode ID token: ' . $e->getMessage() );
                    return false;
                }

                if ( !$decoded_token ) {

                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Failed to decode token ' . substr( $pem_string, 0, 35 ) . '...' . substr( $pem_string, -35 ) . ' using algorithm ' . $header->alg );
                    return false;
                }

                return $decoded_token;
            }

            /**
             * Discovers the public keys Microsoft used to encode the id_token
             *
             * @since   1.0
             *
             * @return  mixed(stdClass|null)    Cached keys if found and valid otherwise fresh new keys.
             */
            private static function discover_ms_public_keys( $refresh, $get_jwks_uri = false ) {

                if ( false === $refresh ) {
                    $cached_keys = get_site_option( 'wpo365_msft_keys' );

                    if ( !empty( $cached_keys ) ) {
                        $cached_keys_segments = explode( ',', $cached_keys, 2 );

                        if ( sizeof( $cached_keys_segments ) == 2 && intval( $cached_keys_segments[0] ) > time() ) {
                            $keys = json_decode( $cached_keys_segments[1] );
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Found cached MSFT public keys to decrypt the JWT token' );

                            if ( isset( $keys->keys ) )
                                return $keys->keys;

                            return $keys;
                        }
                    }
                }
                
                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Retrieving fresh MSFT public keys to decrypt the JWT token' );

                /**
                 * @since 10.10
                 * 
                 * Plugin can optionally try and read the jwks_uri (= public key endpoint) from the 
                 * wellknown Open ID configuration.
                 */

                if ( true === $get_jwks_uri ) {
                    $ms_keys_url = self::discover_jwks_uri();

                    if ( empty( $ms_keys_url ) ) {
                        return null; // Error is logged where it occurred.
                    }
                }
                else {
                    $ms_keys_url = "https://login.microsoftonline.com/common/discovery/v2.0/keys";
                }
                
                $curl = curl_init();
                curl_setopt( $curl, CURLOPT_URL, $ms_keys_url );
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
                
                if ( Options_Service::get_global_boolean_var( 'skip_host_verification' ) ) {
                        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); 
                        curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 ); 
                }

                if ( !empty( $curl_proxy = Options_Service::get_global_string_var( 'curl_proxy' ) ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Using curl proxy ' . $curl_proxy );
                    curl_setopt( $curl, CURLOPT_PROXY, $curl_proxy );
                }

                $result = curl_exec( $curl ); // result holds the keys
                if ( curl_error( $curl ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Error occured whilst getting MSFT decryption keys: ' . curl_error( $curl ) );
                    curl_close( $curl );
                    return null;
                }
                
                curl_close( $curl );
                update_site_option( 'wpo365_msft_keys', strval( time() + 21600 ) . ',' . $result );
                $keys = json_decode( $result );

                if ( isset( $keys->keys ) )
                    return $keys->keys;
                
                return $keys;
            }

            /**
             * Retrieve the Open Connect ID configuration for the tenant and application. Used as fallback when the common scenario is not working.
             * 
             * @since 10.10
             * 
             * @return The jwks_uri pointing to the endpoint where the plugin should look for keys.
             */
            private static function discover_jwks_uri() {
                $directory_id = Options_Service::get_global_string_var( 'tenant_id' );
                $application_id = Options_Service::get_global_string_var( 'application_id' );
                $open_id_config_url = "https://login.microsoftonline.com/$directory_id/.well-known/openid-configuration?appid=$application_id";

                Log_Service::write_log( 'DEBUG', __METHOD__ . " -> Trying to retrieve the well-known Open ID configuration from $open_id_config_url" );
                
                $curl = curl_init();
                curl_setopt( $curl, CURLOPT_URL, $open_id_config_url );
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
                
                if ( Options_Service::get_global_boolean_var( 'skip_host_verification' ) ) {
                    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); 
                    curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 ); 
                }

                if ( !empty( $curl_proxy = Options_Service::get_global_string_var( 'curl_proxy' ) ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Using curl proxy ' . $curl_proxy );
                    curl_setopt( $curl, CURLOPT_PROXY, $curl_proxy );
                }

                $result = curl_exec( $curl ); // result holds the keys

                if ( curl_error( $curl ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Error occured whilst getting JWKS URI for MSFT public keys: ' . curl_error( $curl ) );
                    curl_close( $curl );
                    return null;
                }
                
                curl_close( $curl );
                $open_id_config = json_decode( $result );

                if ( !isset( $open_id_config->jwks_uri ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> jwks_uri property not found. Enable debug log to see more details.' );
                    Log_Service::write_log( 'DEBUG', $open_id_config );
                    return null;
                }
                
                $jwks_uri = \str_replace( '/discovery/', "/discovery/v2.0/", $open_id_config->jwks_uri );
                return $jwks_uri;
            }
        
            /**
             * Retrieves the ( previously discovered ) public keys Microsoft used to encode the id_token
             *
             * @since   1.0
             *
             * @param   string  key-id to retrieve the matching keys
             * @param   array   keys previously discovered
             * @param   boolean whether or not to 
             * @return  void 
             */
            private static function retrieve_ms_public_key( $kid, $keys, $allow_refresh = true ) {
                foreach ( $keys as $key ) {
                    if ( $key->kid == $kid ) {
                        if ( is_array( $key->x5c ) ) {
                            return $key->x5c[0];
                        }
                        else {
                            return $key->x5c;
                        }
                    }
                }

                if ( true === $allow_refresh ) {
                    $new_keys = self::discover_ms_public_keys( true ); // Keys not found so lets refresh the cache
                    return self::retrieve_ms_public_key( $kid, $new_keys, false );
                }
 
                return false;
            }
        }
    }