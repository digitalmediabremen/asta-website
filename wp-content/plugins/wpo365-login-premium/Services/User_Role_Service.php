<?php

    namespace Wpo\Services;

    use \Wpo\Core\Permissions_Helpers;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    if ( !class_exists( '\Wpo\Services\User_Role_Service' ) ) {

        class User_Role_Service {

            public static function update_user_roles( $wp_usr_id, $wpo_usr ) {

                if ( class_exists( '\Wpo\Services\Mapped_Itthinx_Groups_Service' ) && method_exists( '\Wpo\Services\Mapped_Itthinx_Groups_Service', 'aad_group_x_itthinx_group' ) ) {
                    // Optionally update itthinx group assignments
                    \Wpo\Services\Mapped_Itthinx_Groups_Service::aad_group_x_itthinx_group( $wp_usr_id, $wpo_usr );
                }

                if ( class_exists( '\Wpo\Services\Mapped_Itthinx_Groups_Service' ) && method_exists( '\Wpo\Services\Mapped_Itthinx_Groups_Service', 'custom_field_x_itthinx_group' ) ) {
                    // Optionally update itthinx group assignments
                    \Wpo\Services\Mapped_Itthinx_Groups_Service::custom_field_x_itthinx_group( $wp_usr_id, $wpo_usr );
                }

                // Get all possible roles for user
                $user_roles = self::get_user_roles( $wp_usr_id, $wpo_usr );

                // If no roles are found then return
                if ( empty( $user_roles ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Target role for user could not be determined e.g. because of user not in AD group or default role for main site unconfigured' );
                    return;
                }

                $wp_usr = \get_user_by( 'ID', $wp_usr_id );

                if( \in_array( 'administrator', $wp_usr->roles ) || is_super_admin( $wp_usr_id ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Not updating the role for a user that is already an administrator.' );
                    return;
                }

                // Empty any existing roles when configured to do so
                if ( strtolower( Options_Service::get_global_string_var( 'replace_or_update_user_roles' ) ) == 'replace' ) {
                    foreach ( $wp_usr->roles as $current_user_role ) {
                        $wp_usr->remove_role( $current_user_role );
                    }

                    // refresh the user meta for
                    $wp_usr = \get_user_by( 'ID', $wp_usr_id );
                }

                // Add from new roles if not already added
                foreach ( $user_roles as $user_role ) {
                    if ( false === in_array( $user_role, $wp_usr->roles ) ) {
                        $wp_usr->add_role( $user_role );
                    }
                }
            }

            /**
             * Gets the user's default role or if a mapping exists overrides that default role 
             * and returns the role according to the mapping.
             * 
             * @since 3.2
             * 
             * 
             * @return mixed(array|WP_Error) user's role as string or an WP_Error if not defined
             */
            private static function get_user_roles( $wp_usr_id, $wpo_usr ) {
                // Start with an empty array
                $user_roles = [];

                // Graph user resource property x WP role
                if ( class_exists( '\Wpo\Services\Mapped_Custom_Fields_Service' ) && method_exists( '\Wpo\Services\Mapped_Custom_Fields_Service', 'custom_field_x_role' ) ) {
                    \Wpo\Services\Mapped_Custom_Fields_Service::custom_field_x_role( $user_roles, $wpo_usr );
                }

                // AAD group x WP role
                if ( class_exists( '\Wpo\Services\Mapped_Aad_Groups_Service' ) && method_exists( '\Wpo\Services\Mapped_Aad_Groups_Service', 'aad_group_x_role' ) ) {
                    \Wpo\Services\Mapped_Aad_Groups_Service::aad_group_x_role( $user_roles, $wpo_usr );
                }
                
                // Logon Domain x WP role
                if ( class_exists( '\Wpo\Services\Mapped_Domains_Service' ) && method_exists( '\Wpo\Services\Mapped_Domains_Service', 'domain_x_role' ) ) {
                    \Wpo\Services\Mapped_Domains_Service::domain_x_role( $user_roles, $wpo_usr );
                }
                
                // Add default role if needed / configured
                if ( empty( $user_roles ) || ( !empty( $user_roles ) && false === Options_Service::get_global_boolean_var( 'default_role_as_fallback' ) ) ) {
                    $usr_default_role = Options_Service::get_global_string_var( 'new_usr_default_role' );

                    if ( !empty( $usr_default_role ) ) {
                        $usr_default_role = strtolower( $usr_default_role );
                        $wp_role = get_role( $usr_default_role );

                        if ( empty( $wp_role ) ){
                            Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Trying to add the default role but it appears undefined' );
                        }
                        else {
                            $user_roles[] = $usr_default_role;
                        }
                    }
                }
                return $user_roles;
            }
        }
    }