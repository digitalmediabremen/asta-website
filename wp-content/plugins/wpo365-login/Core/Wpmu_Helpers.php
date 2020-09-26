<?php
    
    namespace Wpo\Core;

    use \Wpo\Services\Options_Service;
    use \Wpo\Services\Log_Service;

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Core\Wpmu_Helpers' ) ) {
    
        class Wpmu_Helpers {

            /**
             * @since 9.2
             * 
             * enumeration of possible multisite configs
             */
            const WPMU_NOT_CONFIGURED = 1;
            const WPMU_SHARED = 2;
            const WPMU_NOT_SHARED = 3;

            /**
             * Helper to determine whether WordPress has been configured as
             * multisite and if yes whether it is configured to use a 
             * separate WPO365 config per subsite.
             * 
             * @since 9.2
             * 
             * @return int One of the class const WPMU_NOT_CONFIGURED, WPMU_SHARED or WPMU_NOT_SHARED
             */
            public static function get_type_of_multisite() {
                
                if ( is_multisite() ) {
                    if ( Options_Service::mu_use_subsite_options() ) {
                        return self::WPMU_NOT_SHARED;
                    }
                    return self::WPMU_SHARED;
                }

                return self::WPMU_NOT_CONFIGURED;
            }

            /**
             * Helper to get the global or local transient based on the
             * WPMU configuration.
             * 
             * @since 9.2
             * 
             * @return mixed Returns the value of transient or false if not found
             */
            public static function mu_get_transient( $name ) {
                $type_of_multisite = self::get_type_of_multisite();

                return $type_of_multisite === self::WPMU_NOT_CONFIGURED 
                    || $type_of_multisite === self::WPMU_NOT_SHARED
                        ? get_transient( $name )
                        : get_site_transient( $name );
            }

            /**
             * Helper to set the global or local transient based on the
             * WPMU configuration.
             * 
             * @since 9.2
             * 
             * @param $name string Name of transient
             * @param $value mixed Value of transient
             * @param $duration int Time transient should be cached in seconds
             * 
             * @return void
             */
            public static function mu_set_transient( $name, $value, $duration = 0 ) {
                $type_of_multisite = self::get_type_of_multisite();

                if ( $type_of_multisite === self::WPMU_NOT_CONFIGURED 
                    || $type_of_multisite === self::WPMU_NOT_SHARED) {
                        set_transient( $name, $value, $duration );
                    }
                    else {
                        set_site_transient( $name, $value, $duration );
                    }
            }

            /**
             * Helper to delete the global or local transient based on the
             * WPMU configuration.
             * 
             * @since 10.9
             * 
             * @param $name string Name of transient
             * 
             * @return void
             */
            public static function mu_delete_transient( $name ) {
                $type_of_multisite = self::get_type_of_multisite();

                if ( $type_of_multisite === self::WPMU_NOT_CONFIGURED 
                    || $type_of_multisite === self::WPMU_NOT_SHARED ) {
                        delete_transient( $name );
                    }
                    else {
                        delete_site_transient( $name );
                    }
            }

            /**
             * Helper to switch the current blog from the main site to a subsite in case
             * of a multisite installation (shared scenario) when the user is redirected 
             * back to the main site whereas the state URL indicates that the target is
             * a subsite.
             * 
             * @since   11.0
             * 
             * @param   $state_url  string  The (Relay) state URL
             * 
             * @return  void
             */
            public static function switch_blog( $state_url ) {

                if ( self::get_type_of_multisite() === self::WPMU_SHARED && isset( $state_url ) ) {
                    $redirect_url = Options_Service::get_global_string_var( 'redirect_url' );
                    $redirect_host = parse_url( $redirect_url, PHP_URL_HOST );
                    
                    $state_host = parse_url( $state_url, PHP_URL_HOST );

                    if ( defined( 'SUBDOMAIN_INSTALL' ) && constant( 'SUBDOMAIN_INSTALL' ) === true ) {
                        $state_blog_id = get_blog_id_from_url( $state_host, '/' );
                        $redirect_blog_id = get_blog_id_from_url( $redirect_host, '/' );
                    }
                    else {
                        $redirect_path = parse_url( $redirect_url, PHP_URL_PATH );
                        $state_path = parse_url( $state_url, PHP_URL_PATH );

                        $state_blog_id = get_blog_id_from_url( $state_host, $state_path );
                        $redirect_blog_id = get_blog_id_from_url( $redirect_host, $redirect_path );
                    }

                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Detected WPMU with state blog id ' . $state_blog_id . ' and redirect blog id ' . $redirect_blog_id );

                    if ( $state_blog_id !== $redirect_blog_id ) {
                        switch_to_blog( $state_blog_id );
                    }
                }
            }
        }
    }