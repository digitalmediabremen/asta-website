<?php
    
    namespace Wpo\Services;

    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Mapped_Aad_Groups_Service' ) ) {

        class Mapped_Aad_Groups_Service {

            /**
             * @since 11.0
             */
            public static function aad_group_x_role( &$user_roles, $wpo_usr ) {
                // Add new roles as per AD Group > WP role mapping
                $group_role_settings = Options_Service::get_global_list_var( 'groups_x_roles' );

                foreach ( $group_role_settings as $kv_pair ) {
                    if ( array_key_exists( $kv_pair[ 'key' ], $wpo_usr->groups ) ) {
                        $role_from_role_mapping = strtolower( $kv_pair[ 'value' ] );

                        // Check if the role exists (if not it is not added)
                        if ( null === get_role( $role_from_role_mapping ) ) {
                            Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Group mapping for WordPress role ' . $role_from_role_mapping .' was found for user ' . $wpo_usr->preferred_username . ' but this role does not exist in WordPress' );
                            continue;
                        }

                        // Only add new WordPress role
                        if ( false === in_array( $role_from_role_mapping, $user_roles ) ) {
                            $user_roles[] = $role_from_role_mapping;
                            Log_Service::write_log( 'DEBUG', __METHOD__ . " -> Found group mapping for WordPress role ' . $role_from_role_mapping .' and added it to the user's roles array" );
                        }                        
                    }
                }
            }
        }
    }
