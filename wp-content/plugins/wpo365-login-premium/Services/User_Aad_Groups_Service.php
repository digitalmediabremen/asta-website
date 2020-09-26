<?php

    namespace Wpo\Services;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    use \Wpo\Core\User;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    use \Wpo\Services\Graph_Service;

    if ( !class_exists( '\Wpo\Services\User_Aad_Groups_Service' ) ) {

        class User_Aad_Groups_Service { 

            /**
             * Retrieves the user's AAD group memberships and adds them to the internally used User.
             * 
             * @since 11.0
             * 
             * @param   $wpo_usr    \Wpo\Core\User (by reference)
             * 
             * @return  void
             */
            public static function get_aad_groups( &$wpo_usr, $use_me = false ) {

                $allowed_groups = Options_Service::get_global_list_var( 'groups_whitelist' );
                $groups_x_roles = Options_Service::get_global_list_var( 'groups_x_roles' );
                $groups_x_itthinx_groups = Options_Service::get_global_list_var( 'groups_x_groups_groups' );
                $groups_x_goto_after = Options_Service::get_global_list_var( 'groups_x_goto_after' );

                // No aad group info is needed for this user
                if ( empty( $allowed_groups ) && empty( $groups_x_roles ) && empty( $groups_x_itthinx_groups ) && empty( $groups_x_goto_after ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> No need to retrieve Azure AD groups' );
                    return;
                }

                if ( empty( $wpo_usr->upn ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> User principal name not found for user when retrieving Azure AD group memberships for user ' . $wpo_usr->preferred_username );
                    return;
                }

                $security_enabled_groups_only = false === Options_Service::get_global_boolean_var( 'all_group_memberships' );

                $data = json_encode( array( 'securityEnabledOnly' => $security_enabled_groups_only ) );
                $content_length = strlen( $data);
                $headers = array(
                    'Accept: application/json;odata.metadata=minimal',
                    'Content-Type: application/json',
                    'Content-Length: ' . $content_length,
                );

                $raw = Graph_Service::fetch( '/users/' . \rawurlencode( $wpo_usr->upn ) . '/getMemberGroups', 'POST', false, $headers, false, true, $data );

                if ( Graph_Service::is_fetch_result_ok( $raw, 'Could not retrieve Azure AD group memberships' ) ) {
                    $wpo_usr->groups = array_flip( $raw[ 'payload' ][ 'value' ] );
                }
            }
        }
    }