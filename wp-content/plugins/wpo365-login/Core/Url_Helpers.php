<?php
    
    namespace Wpo\Core;

    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Core\Url_Helpers' ) ) {
    
        class Url_Helpers {

            /**
             * Helper method to (try) help ensure that the path segment given ends with a trailing slash.
             * 
             * @since 1.0
             * 
             * @param $url string Path that should end with a slash
             * @return string Path with trailing slash if appropriate
             */
            public static function ensure_trailing_slash_path( $path ) {
                $path = trim( $path, '/' );
                $path_segments = explode( '/', $path );
                $segments_count = count( $path_segments );
                if ( $segments_count > 0 && false === stripos( $path_segments[ $segments_count -1 ], '.' ) ) {
                    $is_root = empty( $path );
                    return $is_root 
                        ? '/' 
                        : '/' . implode( '/', $path_segments ) . '/';
                } 
                return '/' . $path;
            }

            /**
             * Helper method to (try) help ensure that the url given ends with a trailing slash.
             * 
             * @since 1.0
             * 
             * @param $url string Url that should end with a slash
             * @return string Url with trailing slash if appropriate
             */
            public static function ensure_trailing_slash_url( $url ) {

                if ( empty( $url ) || !is_string( $url ) ) {
                    return null;
                }

                $parsed_url = parse_url( $url );
                $resulting_url = '';
                
                if ( !empty( $parsed_url[ 'scheme' ] ) ) {
                    $resulting_url .= $parsed_url[ 'scheme' ];
                }
                else {
                    return null;
                }

                $resulting_url .= ( '://' );

                if ( !empty( $parsed_url[ 'user' ] ) && !empty( $parsed_url[ 'pass' ] ) ) {
                    $resulting_url .= ( $parsed_url[ 'user' ] . ':' . $parsed_url[ 'pass' ] . '@' );
                }

                if ( !empty( $parsed_url[ 'host' ] ) ) {
                    $resulting_url .= $parsed_url[ 'host' ];
                }
                else {
                    return null;
                }

                if ( !empty( $parsed_url[ 'port' ] ) ) {
                    $resulting_url .= ( ':' . $parsed_url[ 'port' ] );
                }

                if ( !empty( $parsed_url[ 'path' ] ) ) {
                    $resulting_url .= self::ensure_trailing_slash_path( $parsed_url[ 'path' ] );
                }
                else {
                    $resulting_url .= '/';
                }

                if ( !empty( $parsed_url[ 'query' ] ) ) {
                    $resulting_url .= ( '?' . $parsed_url[ 'query' ] );
                }

                if ( !empty( $parsed_url[ 'fragment' ] ) ) {
                    $resulting_url .= ( '#' . $parsed_url[ 'fragment' ] );
                }

                return $resulting_url;
            }

            /**
             * Helper method to determine whether the current URL is the WP REST API.
             * 
             * @since 7.12
             * 
             * @return boolean true if the current URL is for the WP REST API otherwise false.
             */
            public static function is_wp_rest_api() {
                $rest_prefix = \rest_get_url_prefix();

                if ( empty( $rest_prefix ) ) {
                    $rest_prefix = 'wp-json';
                }

                $path_without_subdir = str_replace( 
                    $GLOBALS[ 'WPO_CONFIG' ][ 'url_info' ][ 'wp_site_path' ], 
                    '',
                    $GLOBALS[ 'WPO_CONFIG' ][ 'url_info' ][ 'request_uri' ] );
                
                if ( stripos( $path_without_subdir, $rest_prefix ) === 0 ) {

                    return true;
                }

                return false;
            }

            /**
             * Will check whether request is for WP REST API and if yes
             * if a basic authentication header is present (without proofing it).
             * 
             * @since 7.12
             * 
             * @return boolean true if found, otherwise false
             */
            public static function is_basic_auth_api_request() {
                
                if ( false === self::is_wp_rest_api() ) {
                    return false;
                }

                $headers = getallheaders();
                $headers_to_lower = array_change_key_case( $headers, CASE_LOWER );
                
                return ( isset( $headers_to_lower[ 'authorization' ] ) && stripos( $headers_to_lower[ 'authorization' ], 'basic' ) === 0 );
            }

            /**
             * Adds custom wp query vars
             * 
             * @since 3.6
             * 
             * @param Array $vars existing wp query vars
             * @return Array updated $vars that now includes custom wp query vars
             */
            public static function add_query_vars_filter( $vars ) {

                $vars[] = 'login_errors';
                $vars[] = 'stnu'; // show table new users
                $vars[] = 'stne'; // show table existing users
                $vars[] = 'stou'; // show table old users
                $vars[] = 'sjs';  // sync job status
                $vars[] = 'redirect_to';  // redirect to after successfull authentication
                return $vars;
            }

            /**
             * Get's WordPress default (and possibly custom) login URLs.
             * 
             * @since 7.17
             * 
             * @return array Assoc. array with custom login url (possibly empty string) and default login url. 
             */
            public static function get_login_urls() {
                $default_login_url = \wp_login_url();
                $custom_login_url = Options_Service::get_global_string_var( 'custom_login_url' );
                
                // Custom login url must be an absolute URL
                if ( stripos( $custom_login_url, 'http' ) !== 0 ) {

                    return array( 
                        'custom_login_url' => '',
                        'default_login_url' => $default_login_url,
                    );
                }

                // Custom login url should not accept a query string
                if ( false !== stripos( $custom_login_url, '?' ) ) {
                    $custom_login_url_arr = explode( '?', $custom_login_url );
                    $custom_login_url = $custom_login_url_arr[0];
                }

                // Custom login url should not accept a hash
                if ( false !== stripos( $custom_login_url, '#' ) ) {
                    $custom_login_url_arr = explode( $custom_login_url, '#' );
                    $custom_login_url = $custom_login_url_arr[0];
                }

                $custom_login_url = self::ensure_trailing_slash_url( $custom_login_url );

                return array( 
                    'custom_login_url' => $custom_login_url,
                    'default_login_url' => $default_login_url,
                );
            }

            /**
             * Gets the custom login url if configured and otherwise the default login URL is returned.
             * 
             * @since 7.17
             * 
             * @return string Returns custom login url if configured and otherwise the default login URL.
             */
            public static function get_preferred_login_url() {
                $login_urls = self::get_login_urls();
                
                return !empty( $login_urls[ 'custom_login_url' ] ) 
                    ? $login_urls[ 'custom_login_url' ]
                    : $login_urls[ 'default_login_url' ];
            }

            /**
             * Helper method to determine whether the current URL is the login form.
             * 
             * @since 7.11
             * 
             * @return boolean true if the current form is the wp login form.
             */
            public static function is_wp_login( $uri = NULL ) {
                if ( empty( $uri ) ) {
                    $uri = $GLOBALS[ 'WPO_CONFIG' ][ 'url_info' ][ 'request_uri' ];
                }

                $login_urls = self::get_login_urls();

                array_walk( $login_urls, function( &$value, $key ) {
                    rtrim( $value, '/' );
                } );

                $custom_login_url_path = !empty( $login_urls[ 'custom_login_url' ] )
                    ? parse_url( $login_urls[ 'custom_login_url' ], PHP_URL_PATH )
                    : '';
                $custom_login_url_detected = !empty( $custom_login_url_path ) 
                    &&  false !== stripos( $uri,  $custom_login_url_path );
                
                $default_login_url_path = parse_url( $login_urls[ 'default_login_url' ], PHP_URL_PATH );
                $default_login_url_detected = false !== stripos( $uri,  $default_login_url_path );

                return ( $custom_login_url_detected || $default_login_url_detected );
            }

            /**
             * Checks whether headers are sent before trying to redirect and if sent falls
             * back to an alternative method
             * 
             * @since 4.3
             * 
             * @param string $url URL to redirect to
             * @return void
             */
            public static function force_redirect( $url ) {

                $location = wp_sanitize_redirect( $url );
                $location = self::ensure_trailing_slash_url( $location );

                if ( headers_sent() ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Headers sent when trying to redirect user to ' . $url );
                    echo '<script type="text/javascript">';
                    echo 'window.location.href="'. $location . '";';
                    echo '</script>';
                    echo '<noscript>';
                    echo '<meta http-equiv="refresh" content="0;url=' . $location . '" />';
                    echo '</noscript>';                    
                    exit();
                }

                wp_redirect( $url ); // Will call wp_sanitize_redirect
                exit();
            }

            /**
             * Helper method to determine the redirect URL which can either be the last page
             * the user visited before authentication stored in the posted state property, or
             * if configured the goto_after_signon_url or in case none of these apply the WordPress
             * home URL. This method can be called from the wpo_redirect_url filter.
             * 
             * @since 7.1
             * 
             * @return string URL to send the user once authentication completed
             */
            public static function get_redirect_url( $site_url ) {
                
                // Initially set to state but make sure it's not the login URL and if it is then 
                // take the goto_after_signon_url if configured at all
                if ( isset( $_POST[ 'state' ] ) ) {
                    $state_url = wp_sanitize_redirect( $_POST[ 'state' ] );
                    $redirect_url = false === self::is_wp_login( $state_url ) 
                        ? $state_url
                        : ( !empty( $goto_after_signon_url ) 
                        ? $goto_after_signon_url
                        : $site_url );
                }
                elseif ( isset( $_POST[ 'RelayState' ] ) ) {
                    $state_url = wp_sanitize_redirect( $_POST[ 'RelayState' ] );
                    $redirect_url = false === self::is_wp_login( $state_url ) 
                        ? $state_url
                        : ( !empty( $goto_after_signon_url ) 
                        ? $goto_after_signon_url
                        : $site_url );
                }
                else {
                    $redirect_url = $site_url;
                }

                return $redirect_url;
            }

            public static function goto_after( $wpo_usr ) {
                // Get URL and redirect user (default is the WordPress homepage)
                $redirect_url = $GLOBALS[ 'WPO_CONFIG' ][ 'url_info' ][ 'wp_site_url' ];

                if ( \class_exists( '\Wpo\Services\Redirect_Service' ) && \method_exists( '\Wpo\Services\Redirect_Service', 'get_redirect_url' ) ) {
                    $redirect_url = \Wpo\Services\Redirect_Service::get_redirect_url( $redirect_url, $wpo_usr->groups, $wpo_usr->created );
                }
                else {
                    $redirect_url = self::get_redirect_url( $redirect_url );
                }

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Redirecting to ' . $redirect_url );

                /**
                 * @since 9.0
                 * 
                 * Enforce the same scheme as AAD redirect uri to avoid infite loops.
                 */ 
                $aad_redirect_url = Options_Service::get_global_string_var( 'redirect_url' );
                
                if ( stripos( $aad_redirect_url, 'https://' ) !== false && stripos( $redirect_url, 'http://' ) === 0 ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Please update your htaccess or similar and ensure that users can only access your website via https:// (detected state: ' . $redirect_url . ').' );
                    $redirect_url = str_replace( 'http://', 'https://', $redirect_url );
                }

                /**
                 * @since 10.10
                 */
                if( stripos( $aad_redirect_url, 'www' ) !== stripos( $redirect_url, 'www' ) ) {

                    if( !\is_multisite() || \is_multisite() && Options_Service::mu_use_subsite_options() ) {
                        $short_url_host = false !== stripos( $redirect_url, 'www' ) ? parse_url( $aad_redirect_url, PHP_URL_HOST ) : parse_url( $redirect_url, PHP_URL_HOST );
                        Log_Service::write_log( 'ERROR', "Short URL detected. User will be sent to $aad_redirect_url instead of the page he / she requested ($redirect_url). Edit your wp-config.php file and add the line <strong>define('COOKIE_DOMAIN', '$short_url_host');</strong> just below the line that reads <em>/* That's all, stop editing! Happy publishing. */</em>. See <a href=\"https://docs.wpo365.com/article/5-infinite-loop\" target=\"_blank\">https://docs.wpo365.com/article/5-infinite-loop</a>." );
                        $redirect_url = $aad_redirect_url;
                    }
                }

                self::force_redirect( $redirect_url );
            }
        }
    }