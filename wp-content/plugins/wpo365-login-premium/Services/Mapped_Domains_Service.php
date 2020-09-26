<?php
    
    namespace Wpo\Services;

    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Mapped_Domains_Service' ) ) {

        class Mapped_Domains_Service {

            /**
             * @since 11.0
             */
            public static function domain_x_role( &$user_roles, $wpo_usr ) {
                $user_domain = '';
                $atpos = strpos( $wpo_usr->preferred_username, '@' );

                if ( false !== $atpos ) {
                    $user_domain = substr( $wpo_usr->preferred_username, ($atpos + 1) );
                }

                // Add new roles as per domain > WP role mapping
                $domain_role_settings = Options_Service::get_global_list_var( 'domains_x_roles' );

                foreach ( $domain_role_settings as $kv_pair ) {

                    if ( !empty( $user_domain ) && false !== stripos( $user_domain, $kv_pair[ 'key' ] ) ) {
                        $role_from_role_mapping = strtolower( $kv_pair[ 'value' ] );

                        // Check if the role exists (if not it is not added)
                        if ( null === get_role( $role_from_role_mapping ) ) {
                            Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Domain mapping for WordPress role ' . $role_from_role_mapping .' was found for user ' . $wpo_usr->preferred_username . ' but this role does not exist in WordPress' );
                            continue;
                        }

                        // Only add new WordPress role
                        if ( false === in_array( $role_from_role_mapping, $user_roles ) ) {
                            $user_roles[] = $role_from_role_mapping;
                            Log_Service::write_log( 'DEBUG', __METHOD__ . " -> Found domain mapping for WordPress role ' . $role_from_role_mapping .' and added it to the user's roles array" );
                        }                        
                    }
                }
            }
        }
    }
