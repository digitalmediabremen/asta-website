<?php

    namespace Wpo\Services;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    use \Wpo\Core\User;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    use \Wpo\Services\Graph_Service;
    use \Wpo\Services\User_Service;

    if ( !class_exists( '\Wpo\Services\User_Details_Service' ) ) {

        class User_Details_Service { 

            /**
             * @since 11.0
             */
            public static function try_improve_core_fields( &$wpo_usr ) {

                if ( empty( $wpo_usr->graph_resource ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Cannot improve user fields because the graph resource has not been retrieved' );
                    return;
                }

                if ( isset( $wpo_usr->graph_resource[ 'userPrincipalName' ] ) ) {
                    $wpo_usr->upn = $wpo_usr->graph_resource[ 'userPrincipalName' ];
                }

                if ( isset( $wpo_usr->graph_resource[ 'mail' ] ) ) {
                    $wpo_usr->email = $wpo_usr->graph_resource[ 'mail' ];
                }

                if ( isset( $wpo_usr->graph_resource[ 'displayName' ] ) ) {
                    $wpo_usr->full_name = $wpo_usr->graph_resource[ 'displayName' ];
                }

                if ( isset( $wpo_usr->graph_resource[ 'givenName' ] ) ) {
                    $wpo_usr->first_name = $wpo_usr->graph_resource[ 'givenName' ];
                }

                if ( isset( $wpo_usr->graph_resource[ 'surname' ] ) ) {
                    $wpo_usr->last_name = $wpo_usr->graph_resource[ 'surname' ];
                }
            }

            /**
             * Retrieves the user's AAD group memberships and adds them to the internally used User.
             * 
             * @since 11.0
             * 
             * @param   $wpo_usr    \Wpo\Core\User (by reference)
             * 
             * @return  void
             */
            public static function get_graph_user( $upn = null, $use_me = false ) {
                
                if ( !empty( $upn ) ) {
                    $query = '/users/' . \rawurlencode( $upn );
                }
                elseif ( $use_me ) {
                    $query = '/me';
                }
                else {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> User principal name not found for user when retrieving Azure AD user resource for user ' . $upn );
                    return null;
                }

                if ( $use_me && empty( Options_Service::get_global_string_var( 'application_secret' ) ) && !Options_Service::get_global_boolean_var( 'use_app_only_token' ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Cannot retrieve details for user ' . $upn . ' because the administrator has not configured the integration portion of the WPO365 plugin. Please check the documentation for detailed step-by-step instructions on how to configure integration with Microsoft Graph and other 365 services' );
                    return null;
                }

                $headers = array(
                    'Accept: application/json;odata.metadata=minimal',
                    'Content-Type: application/json',
                );

                $graph_resource = Graph_Service::fetch( $query, 'GET', false, $headers );

                if ( Graph_Service::is_fetch_result_ok( $graph_resource, 'Could not retrieve user details' ) ) {
                    return $graph_resource[ 'payload' ];
                }

                return null;
            }
        }
    }