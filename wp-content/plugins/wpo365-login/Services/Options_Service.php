<?php
    
    namespace Wpo\Services;

    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Saml2_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Options_Service' ) ) {
    
        class Options_Service {

            /**
             * Same as get_global_var but will try and interpret the value of the
             * global variable as if it is a boolean. 
             * 
             * @since 4.6
             * 
             * @param   string  $name   Name of the global variable to get
             * @return  boolean         True in case value found equals 1, "1", "true" or true, otherwise false.
             */
            public static function get_global_boolean_var( $name, $log = true ) {
                $var = self::get_global_var( $name, $log );

                return (
                    $var === true 
                    || $var === "1" 
                    || $var === 1 
                    || ( is_string( $var ) && strtolower( $var ) == 'true' ) ) ? true : false;
            }

            /**
             * Same as get_global_var but will try and cast the value as a 1 dimensional array.
             * 
             * @since 7.0
             * 
             * @param   string $name    name of the global variable to get.
             * @return  array           Value of the global variable as an array or empty if not found.
             */
            public static function get_global_list_var( $name, $log = true ) {
                $var = self::get_global_var( $name, $log );
                return is_array( $var ) ? $var : array();
            }

            /**
             * Same as get_global_var but will try and cast the value as an integer.
             * 
             * @since 7.0
             * 
             * @param   string $name    name of the global variable to get.
             * @return  int             Value of the global variable as an integer or else -1.
             */
            public static function get_global_numeric_var( $name, $log = true ) {
                $var = self::get_global_var( $name, $log );
                return is_int( $var ) ? $var : 0;
            }

            /**
             * Same as get_global_var but will try and cast the value as a string.
             * 
             * @since 7.0
             * 
             * @param   string $name    name of the global variable to get.
             * @return  int             Value of the global variable as a string or else an empty string.
             */
            public static function get_global_string_var( $name, $log = true ) {
                $var = self::get_global_var( $name, $log );
                return is_string( $var ) ? trim( $var ) : '';
            }

            /**
             * Gets a global variable by its name.
             * 
             * @param   string  $name   Variable name as string
             * 
             * @return  object|null The global variable or WP_Error if not found
             */
            private static function get_global_var( $name, $log = true ) {
                self::ensure_options_cache();

                // Try return the requested option
                if ( isset( $GLOBALS[ 'WPO_CONFIG' ][ 'options' ][ $name ] ) 
                    && !empty( $GLOBALS[ 'WPO_CONFIG' ][ 'options' ][ $name ] ) ) {
                        $value = $GLOBALS[ 'WPO_CONFIG' ][ 'options' ][ $name ];
                        $value_for_log = in_array( $name, self::get_secret_options() ) && is_string( $value )
                            ? substr( $value, 0, strlen( $value ) / 3 ) . '[...]'
                            : ( is_array( $value ) || is_object( $value ) 
                                ? print_r( $value, true ) 
                                : $value );
                }
                else {
                    $value_for_log = "Global variable with name $name not configured.";
                }

                if ( true === $log ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . " -> Option: $name -> $value_for_log" );
                }

                return empty( $value )
                    ? null
                    : $value;
            }

            /**
             * Helper function to read the options into a global variable.
             * 
             * @since 7.3
             * 
             * @return void
             */
            private static function ensure_options_cache() {
                if ( empty( $GLOBALS[ 'WPO_CONFIG' ][ 'options' ] ) ) {
                    $mu_use_subsite_options = self::mu_use_subsite_options();
                    $options = $mu_use_subsite_options
                        ? get_option( 'wpo365_options', array() )
                        : get_site_option( 'wpo365_options', array() );

                    if ( empty( $options ) && is_multisite() && false === $mu_use_subsite_options ) {
                        $options = self::wpmu_copy_wpo365_options();
                    }
                    
                    if ( empty( $options ) ) {
                        $options = self::get_default_options();
                    }
                    
                    $GLOBALS[ 'WPO_CONFIG' ][ 'options' ] = $options;
                }
            }

            /**
             * Tries to get the wpo365_options for the main site (in a WordPress network)
             * 
             * - Changed with v7.3
             * 
             * @since 5.0 
             * 
             * @return Array 
             */
            private static function wpmu_copy_wpo365_options() {
                global $current_site;
                $main_site_blog_id = (int)$current_site->blog_id;

                return get_blog_option( $main_site_blog_id, 'wpo365_options', self::get_default_options() );
            }

            /**
             * Array with options that should be partially obscured when logged
             * 
             * @since 7.11
             * 
             * @return array with option names that should be obscured when logged
             */
            private static function get_secret_options() {
                return array(
                    'tenant_id',
                    'application_id',
                    'application_secret',
                    'nonce_secret',
                    'app_only_application_id',
                    'app_only_application_secret',
                    'saml_x509_cert',
                );
            }

            /**
             * Helper to check if this WordPress instance is multisite and 
             * a global boolean constant WPO_MU_USE_SUBSITE_OPTIONS has been 
             * configured
             * 
             * @since 7.3
             * 
             * @return boolean True if subsite options should be used 
             */
            public static function mu_use_subsite_options() {
                if ( is_multisite()
                    && defined( 'WPO_MU_USE_SUBSITE_OPTIONS' ) 
                    && true === constant( 'WPO_MU_USE_SUBSITE_OPTIONS' ) ) {
                        return true;
                }

                return false;
            }

            /**
             * Convert keys in an associative array from php style with underscore to camel case.
             * 
             * @since 5.4
             * 
             * @param $assoc_array string Associative options array with keys following PHP camel case naming convention.
             * 
             * @return string Updated (associative) options array with keys following JSON naming convention.
             */
            public static function to_camel_case( $assoc_array ) {
                $result = array();

                if ( !is_array( $assoc_array ) )
                    return $result;

                foreach ( $assoc_array as $key => $value ) {
                    $key = str_replace( '-', '', $key );
                    $key = strtolower( $key );
                    $cc_key = preg_replace_callback('/_([a-z])/', function( $match ) {
                        return strtoupper($match[1]);
                    }, $key);
                    $result[ $cc_key ] = $value;
                }

                return $result;
            }

            /**
             * Convert keys in an associative array from camel case to php style with underscore.
             * 
             * @since 5.4
             * 
             * @param $assoc_array string Associative options array with keys following JSON camel case naming convention.
             * 
             * @return string Updated (associative) options array with keys following PHP naming convention.
             */
            public static function from_camel_case( $assoc_array ) {
                $result = array();

                foreach ( $assoc_array as $key => $value ) {
                    $php_key = preg_replace_callback('/([A-Z])/', function( $match ) {
                       return "_".strtolower($match[1]);
                    }, $key);
                    $result[ $php_key ] = $value;
                }

                return $result;
            }

            /**
             * Helper to get an initial options array.
             * 
             * @since   7.0
             * 
             * @return  array   Array with snake cased options
             */
            public static function get_default_options() {
                $default_login_url_path = parse_url( wp_login_url(), PHP_URL_PATH );

                $pages_blacklist = array(
                    '/login/',
                    'admin-ajax.php', 
                    'wp-cron.php', 
                    'xmlrpc.php',
                    $default_login_url_path,
                );

                $default_options = array(
                    'auth_scenario' => 'internet',
                    'pages_blacklist' => $pages_blacklist,
                    'session_duration' => 3600,
                    'version' => '2019',
                );

                return $default_options;
            }

            /**
             * Helper to get the cached WPO365 options to the Wizard.
             * 
             * @since   7.0
             * 
             * @return  array   Array with camel cased options or an empty one if an error occurred.
             */
            public static function get_options() {
                self::ensure_options_cache();
                
                try {
                    return self::to_camel_case( $GLOBALS[ 'WPO_CONFIG' ][ 'options' ] );
                }
                catch(\Exception $e) {
                    return array();
                }
            }

            /**
             * Helper to update the cached WPO365 options with options sent
             * from the Wizard.
             * 
             * @since   7.0
             * 
             * @param   array   $updated_options    camelcased options sent by Wizard
             * @param   boolean $is_assoc           argument is a PHP assoc array
             * @return  bool    True if successfully updated otherwise false
             */
            public static function update_options( $updated_options, $is_assoc = false ) {
                self::ensure_options_cache();

                try {    
                    if ( !$is_assoc ) {
                        $camel_case_options = json_decode( utf8_encode( base64_decode( $updated_options ) ), true );
                        $snake_case_options = self::from_camel_case( $camel_case_options );
                    }
                    else {
                        $snake_case_options = $updated_options;
                    }

                    $options = $GLOBALS[ 'WPO_CONFIG' ][ 'options' ];

                    if ( !empty( $options ) ) {
                        foreach ( $snake_case_options as $key => $value )        // add to existing options
                            $options[ $key ] = $value;
                    }
                    else {
                        $options = $snake_case_options;                         // or replace all options
                    }

                    ksort( $options);

                    if ( self::mu_use_subsite_options() )
                        update_option( 'wpo365_options', $options );
                    else 
                        update_site_option( 'wpo365_options', $options );

                    $GLOBALS[ 'WPO_CONFIG' ][ 'options' ] = array();
                }
                catch(\Exception $e) {
                    return false;
                }
                return true;
            }

            /**
             * Simple helper to add or update an option.
             * 
             * @since 10.0
             * 
             * @param $name     string  Name of the option to add
             * @param $value    mixed   Value of the option to add
             * 
             * @return void
             */
            public static function add_update_option( $name, $value ) {
                self::ensure_options_cache();
                $options = $GLOBALS[ 'WPO_CONFIG' ][ 'options' ];
                $options[ $name ] = $value;
                ksort( $options);

                if ( self::mu_use_subsite_options() ) {
                    update_option( 'wpo365_options', $options );
                }
                else { 
                    update_site_option( 'wpo365_options', $options );
                }

                $GLOBALS[ 'WPO_CONFIG' ][ 'options' ] = array();
            }

            /**
             * Converts a string based key and value pair to an object style key value pair
             * e.g. from "c93f6d7c-1a8b-4421-b87b-bbc67ed396a3,author;" to 
             * { key: "c93f6d7c-1a8b-4421-b87b-bbc67ed396a3", value="author" }
             * 
             * @since 5.4
             * 
             * @param $option string Key value pairs as string e.g. key1,value1;key2,value2;...
             * 
             * @return Array associative array in the form of array( 'key1' => 'value2', 'key2' => 'value2' )...
             */
            private static function to_keyvalues( $option ) {
                if ( false === is_string( $option ) )
                    return array();
                
                $kv_str_arr = array_filter( explode( ';', trim( $option ) ) );
                $kv_arr = array_map( function( $kv_str ) {
                    $kv = array_filter( explode( ',', $kv_str ) );
                    return array( 'key' => $kv[0], 'value' => $kv[1] );
                }, $kv_str_arr );
                $kv_arr = is_array( $kv_arr ) ? $kv_arr : array();

                return $kv_arr;
            }

            /**
             * Inspects the array provided whether tenant, application and redirect url have been
             * specified.
             * 
             * @since 7.3
             * 
             * @return boolean True if wpo365 is configured otherwise false
             */
            public static function is_wpo365_configured() {
                $tentant_id_ok = !empty( self::get_global_string_var( 'tenant_id' ) );

                if ( !$tentant_id_ok ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> WPO365 is not configured -> Tenant ID is missing.' );
                }

                if ( true === self::get_global_boolean_var( 'use_saml' ) ) {
                    return Saml2_Service::saml_settings( true );
                }

                $application_id_ok = !empty( self::get_global_string_var( 'application_id' ) );

                if ( !$application_id_ok ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> WPO365 is not configured -> Application ID is missing.' );
                }

                $redirect_url_ok = !empty( self::get_global_string_var( 'redirect_url' ) );

                if ( !$redirect_url_ok ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> WPO365 is not configured -> Redirect URL is missing.' );
                }

                return $tentant_id_ok 
                    && $application_id_ok 
                    && $redirect_url_ok;
            }
        }
    }