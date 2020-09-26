<?php
    
    namespace Wpo\Services;

    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Mapped_Custom_Fields_Service' ) ) {

        class Mapped_Custom_Fields_Service {

            /**
             * @since 11.0
             */
            public static function custom_field_x_role( &$user_roles, $wpo_usr ) {
                // Add new roles as per custom field > WP role mapping
                $custom_field_role_settings = Options_Service::get_global_list_var( 'custom_fields_x_roles' );

                foreach ( $custom_field_role_settings as $kv_pair ) {
                    // If mapped custom field's name is indeed a property of the Graph User Resource
                    $custom_field_setting = \explode( ':', $kv_pair[ 'key' ] );

                    if ( sizeof( $custom_field_setting ) !== 2 ) {
                        Log_Service::write_log( 'WARN', __METHOD__ . ' -> Custom field mapping for WordPress role is not correctly entered: ' . $custom_field_setting . ' and should be of the form propertyName:value e.g. department:Communications');
                        continue;
                    }

                    if ( !empty( $wpo_usr->graph_resource ) && \array_key_exists( $custom_field_setting[0], $wpo_usr->graph_resource ) && $wpo_usr->graph_resource[ $custom_field_setting[0] ] == $custom_field_setting[1] ) {
                        $role_from_role_mapping = strtolower( $kv_pair[ 'value' ] );

                        // Check if the role exists (if not it is not added)
                        if ( null === get_role( $role_from_role_mapping ) ) {
                            Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Custom field mapping for WordPress role ' . $role_from_role_mapping .' was found for user ' . $wpo_usr->preferred_username . ' but this role does not exist in WordPress' );
                            continue;
                        }

                        // Only add new WordPress role
                        if ( false === in_array( $role_from_role_mapping, $user_roles ) ) {
                            $user_roles[] = $role_from_role_mapping;
                            Log_Service::write_log( 'DEBUG', __METHOD__ . " -> Found custom field mapping for WordPress role ' . $role_from_role_mapping .' and added it to the user's roles array" );
                        }                        
                    }
                }
            }
        }
    }
