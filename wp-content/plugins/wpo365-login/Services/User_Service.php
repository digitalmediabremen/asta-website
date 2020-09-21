<?php

    namespace Wpo\Services;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    use \Wpo\Core\Domain_Helpers;
    use \Wpo\Core\User;
    use \Wpo\Services\Authentication_Service;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    use \Wpo\Services\Saml2_Service;
    use \Wpo\Services\User_Create_Service;

    if ( !class_exists( '\Wpo\Services\User_Service' ) ) {

        class User_Service { 

            const USER_NOT_LOGGED_IN = 0;
            const IS_NOT_O365_USER = 1;
            const IS_O365_USER = 2;
            
            /**
             * Transform ID token in to internally used User represenation.
             * 
             * @since 7.17
             * 
             * @param $id_token string The open ID connect token received.
             * @return mixed(User|WP_Error) A new User object created from the id_token or WP_Error if the ID token could not be parsed
             */
            public static function user_from_id_token( $id_token ) {
                $preferred_username = isset( $id_token->preferred_username ) 
                    ? trim( strtolower( $id_token->preferred_username ) )
                    : '';
                
                $upn = isset( $id_token->upn ) 
                    ? trim( strtolower( $id_token->upn ) )
                    : '';

                $email = isset( $id_token->email ) 
                    ? trim( strtolower( $id_token->email ) )
                    : '';
                
                $first_name = isset( $id_token->given_name ) 
                    ? trim( $id_token->given_name )
                    : '';

                $last_name = isset( $id_token->family_name ) 
                    ? trim( $id_token->family_name )
                    : '';

                $full_name = isset( $id_token->name ) 
                    ? trim( $id_token->name )
                    : '';
                
                $tid = isset( $id_token->tid )
                    ? trim( $id_token->tid )
                    : '';
                
                $groups = property_exists( $id_token, 'groups' ) && is_array( $id_token->groups )
                    ? array_flip( $id_token->groups ) 
                    : array();

                $wpo_usr = new User();
                $wpo_usr->from_idp_token = true;
				$wpo_usr->first_name = $first_name;
				$wpo_usr->last_name = $last_name;
				$wpo_usr->full_name = $full_name;
                $wpo_usr->email = $email;
                $wpo_usr->preferred_username = $preferred_username;
				$wpo_usr->upn = $upn;
                $wpo_usr->name = $upn;
                $wpo_usr->tid = $tid;
                $wpo_usr->groups = $groups;

                // Enrich -> Graph resource for user
                if ( \class_exists( '\Wpo\Services\User_Details_Service' ) && \method_exists( '\Wpo\Services\User_Details_Service', 'get_graph_user' ) ) {
                    $graph_resource = \Wpo\Services\User_Details_Service::get_graph_user( $wpo_usr->upn, true );
                    $wpo_usr->graph_resource = $graph_resource;
                }

                // Enrich -> Azure AD groups
                if ( empty( $wpo_usr->groups ) && \class_exists( '\Wpo\Services\User_Aad_Groups_Service' ) && \method_exists( '\Wpo\Services\User_Aad_Groups_Service', 'get_aad_groups' ) ) {
                    \Wpo\Services\User_Aad_Groups_Service::get_aad_groups( $wpo_usr, true );
                }

                // Improve quality of the data with graph resource
                if ( \class_exists( '\Wpo\Services\User_Details_Service' ) && \method_exists( '\Wpo\Services\User_Details_Service', 'try_improve_core_fields' ) ) {
                    \Wpo\Services\User_Details_Service::try_improve_core_fields( $wpo_usr );
                }

                // Store for later e.g. custom (BuddyPress) fields
                $request_service = Request_Service::get_instance();
                $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );
                $request->set_item( 'wpo_usr', $wpo_usr );

                return $wpo_usr;
            }
            
			/**
			 * Parse graph user response received and return User object. This method may return a user
			 * without an email address.
			 *
			 * @since 2.2
			 *
			 * @param string 	$user  received from Microsoft Graph
			 * @return User  	A new User Object created from the graph response
			 */
			public static function user_from_graph_user( $graph_resource ) {

                $usr = new User();

                if ( empty( $graph_resource ) ) {
                    return $usr;
                }
                
                $is_aad_guest = isset( $graph_resource[ 'userPrincipalName' ] ) && false !== stripos( $graph_resource[ 'userPrincipalName' ], '#ext#' );

				$usr->first_name = isset( $graph_resource[ 'givenName' ] ) ?  $graph_resource[ 'givenName' ] : '';
				$usr->last_name = isset( $graph_resource[ 'surname' ] ) ? $graph_resource[ 'surname' ] : '';
				$usr->full_name = isset( $graph_resource[ 'displayName' ] ) ? $graph_resource[ 'displayName' ] : '';
				$usr->email = isset( $graph_resource[ 'mail' ] ) ? $graph_resource[ 'mail' ] : '';
                $usr->upn = isset( $graph_resource[ 'userPrincipalName' ] ) ? $graph_resource[ 'userPrincipalName' ] : '';
                $usr->preferred_username = $is_aad_guest && isset( $graph_resource[ 'mail' ] ) ? $graph_resource[ 'mail' ] : $usr->upn;
                $usr->name = !empty( $usr->full_name )
                    ? $usr->full_name
                    : $usr->preferred_username;
                $usr->graph_resource = $graph_resource;

                // Enrich -> Azure AD groups
                if ( \class_exists( '\Wpo\Services\User_Aad_Groups_Service' ) && \method_exists( '\Wpo\Services\User_Aad_Groups_Service', 'get_aad_groups' ) ) {
                    \Wpo\Services\User_Aad_Groups_Service::get_aad_groups( $usr );
                }
                
				return $usr;
            }

            /**
             * Transform ID token in to internally used User represenation.
             * 
             * @since 7.17
             * 
             * @param $id_token string The open ID connect token received.
             * @return mixed(User|WP_Error) A new User object created from the id_token or WP_Error if the ID token could not be parsed
             */
            public static function user_from_saml_response( $name_id, $saml_attributes ) {
                
                $preferred_username = Saml2_Service::get_attribute( 'preferred_username', $saml_attributes, true );                
                $upn = !empty( $name_id ) ? $name_id : $preferred_username;
                $email = Saml2_Service::get_attribute( 'email', $saml_attributes, true );
                $first_name = Saml2_Service::get_attribute( 'first_name', $saml_attributes );
                $last_name = Saml2_Service::get_attribute( 'last_name', $saml_attributes );
                $full_name = Saml2_Service::get_attribute( 'full_name', $saml_attributes );
                $tid = Saml2_Service::get_attribute( 'tid', $saml_attributes );

                $wpo_usr = new User();
                $wpo_usr->from_idp_token = true;
				$wpo_usr->first_name = $first_name;
				$wpo_usr->last_name = $last_name;
				$wpo_usr->full_name = $full_name;
                $wpo_usr->email = $email;
                $wpo_usr->preferred_username = $preferred_username;
				$wpo_usr->upn = $upn;
                $wpo_usr->name = $upn;
                $wpo_usr->tid = $tid;

                // Enrich -> Graph resource for user
                if ( \class_exists( '\Wpo\Services\User_Details_Service' ) && \method_exists( '\Wpo\Services\User_Details_Service', 'get_graph_user' ) ) {
                    $graph_resource = \Wpo\Services\User_Details_Service::get_graph_user( $wpo_usr->upn );
                    $wpo_usr->graph_resource = $graph_resource;
                }

                // Enrich -> Azure AD groups
                if ( \class_exists( '\Wpo\Services\User_Aad_Groups_Service' ) && \method_exists( '\Wpo\Services\User_Aad_Groups_Service', 'get_aad_groups' ) ) {
                    \Wpo\Services\User_Aad_Groups_Service::get_aad_groups( $wpo_usr );
                }

                // Improve quality of the data with graph resource
                if ( \class_exists( '\Wpo\Services\User_Details_Service' ) && \method_exists( '\Wpo\Services\User_Details_Service', 'try_improve_core_fields' ) ) {
                    \Wpo\Services\User_Details_Service::try_improve_core_fields( $wpo_usr );
                }

                // Store for later e.g. custom (BuddyPress) fields
                $request_service = Request_Service::get_instance();
                $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );
                $request->set_item( 'wpo_usr', $wpo_usr );

                return $wpo_usr;
            }

            /**
             * @since 11.0
             */
            public static function ensure_user( $wpo_usr ) {
                $wp_usr = self::try_get_user_by( $wpo_usr->preferred_username, $wpo_usr->email );

                if ( !empty( $wp_usr ) ) {
                    Authentication_Service::is_deactivated( $wp_usr->user_login, true );
                    $wp_usr_id = $wp_usr->ID;
                }
                else {
                    if ( \class_exists( '\Wpo\Services\User_Create_Update_Service' ) && \method_exists( '\Wpo\Services\User_Create_Update_Service', 'create_user' ) ) {
                        $wp_usr_id = \Wpo\Services\User_Create_Update_Service::create_user( $wpo_usr );
                    }
                    else {
                        $wp_usr_id = User_Create_Service::create_user( $wpo_usr );
                    }
                }

                if ( !( !$wpo_usr->created && $wpo_usr->from_idp_token && Options_Service::get_global_boolean_var( 'express_login' ) ) 
                    && class_exists( '\Wpo\Services\User_Create_Update_Service' ) && \method_exists( '\Wpo\Services\User_Create_Update_Service', 'update_user' ) ) {
                    \Wpo\Services\User_Create_Update_Service::update_user( $wp_usr_id, $wpo_usr );
                }

                $wp_usr = \get_user_by( 'ID', $wp_usr_id );

                return $wp_usr;
            }

            /**
             * Tries to find the user by upn, accountname or email.
             * 
             * @since 9.4
             * 
             * @param $needle
             * 
             * @return WP_User or null
             */
            public static function try_get_user_by( $login = '', $email = '' ) {

                // Check arguments
                if ( empty( $login ) && empty( $email ) ) {
                    return null;
                }

                // 1st - Try find by upn
                if ( is_string( $login ) ) {
                    $wp_usr = \get_user_by( 'login', $login );

                    if ( !empty( $wp_usr ) ) {
                        return $wp_usr;
                    }                    
                }
                
                
                // 2nd - Try find by email
                if ( is_string( $email ) ) {
                    $wp_usr = \get_user_by( 'email', $email );

                    if ( !empty( $wp_usr ) ) {
                        return $wp_usr;
                    }
                }

                // 3rd - Try find by accountname
                if ( is_string( $login ) ) { 

                    $atpos = strpos( $login, '@' );

                    if ( false !== $atpos ) {
                        $accountname = substr( $login, 0, $atpos );
                        $wp_usr = \get_user_by( 'login', $accountname );

                        if ( !empty( $wp_usr ) ) {
                            return $wp_usr;
                        }
                    }

                }

                return null;
            }

            /**
             * @since 11.0
             */
            public static function try_get_user_principal_name( $wp_usr_id ) {

                if ( empty( $wp_usr_id ) ) {
                    $request_service = Request_Service::get_instance();
                    $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );
                    $wpo_usr = $request->get_item( 'wpo_usr' );

                    if ( !empty( $wpo_usr ) && !empty( $wpo_usr->upn ) ) {
                        return $wpo_usr->upn;
                    }
                }

                $upn = get_user_meta( $wp_usr_id, 'userPrincipalName', true );

                if ( empty( $upn ) ) {
                    $wp_usr = \get_user_by( 'ID', $wp_usr_id );
                    $upn = $wp_usr->user_login;
                    $smtp_domain = Domain_Helpers::get_smtp_domain_from_email_address( $upn );

                    // User's login cannot be used to identify the user resource
                    if ( empty( $smtp_domain ) || !Domain_Helpers::is_tenant_domain( $smtp_domain ) ) {
                        $upn = $wp_usr->user_email;
                        $smtp_domain = Domain_Helpers::get_smtp_domain_from_email_address( $upn );

                        if ( empty( $smtp_domain ) || !Domain_Helpers::is_tenant_domain( $smtp_domain ) ) {
                            return null;
                        }
                    }
                }

                return $upn;
            }

            /**
             * @since 11.0
             */
            public static function save_user_principal_name( $upn ) {
                $wp_usr_id = get_current_user_id();

                if ( $wp_usr_id > 0 && !empty( $upn ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Successfully saved upn ' . $upn );
                    update_user_meta( $wp_usr_id, 'userPrincipalName', $upn );
                }
            }

            /**
             * @since 11.0
             */
            public static function try_get_user_tenant_id( $wp_usr_id ) {

                if ( empty( $wp_usr_id ) ) {
                    $request_service = Request_Service::get_instance();
                    $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );
                    $wpo_usr = $request->get_item( 'wpo_usr' );

                    if ( !empty( $wpo_usr ) && !empty( $wpo_usr->tid ) ) {
                        return $wpo_usr->tid;
                    }
                }

                $tid = get_user_meta( $wp_usr_id, 'aadTenantId', true );

                if ( empty( $tid ) ) {
                    $tid = Options_Service::get_global_string_var( 'tenant_id' );
                }

                return $tid;
            }

            /**
             * @since 11.0
             */
            public static function save_user_tenant_id( $tid ) {
                $wp_usr_id = get_current_user_id();

                if ( $wp_usr_id > 0 && !empty( $tid ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Successfully saved user tenant id ' . $tid );
                    update_user_meta( $wp_usr_id, 'aadTenantId', $tid );
                }
            }

            /**
             * Checks whether current user is O365 user
             *
             * @since   1.0
             * @return  int One of the following User Service class constants 
             *              USER_NOT_LOGGED_IN, IS_O365_USER or IS_NOT_O365_USER
             */
            public static function user_is_o365_user( $user_id, $email = '' ) {
                $wp_usr = get_user_by( 'ID', intval( $user_id ) );
                
                if ( !empty( $email ) && false === $wp_usr ) {
                    $wp_usr = get_user_by( 'email', $email );
                }

                if( $wp_usr === false ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Checking whether user is O365 user -> Not logged on' );
                    return self::USER_NOT_LOGGED_IN;
                }

                $email_domain = Domain_Helpers::get_smtp_domain_from_email_address( $wp_usr->user_email );

                if( Domain_Helpers::is_tenant_domain( $email_domain ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Checking whether user is O365 user -> YES' );
                    return self::IS_O365_USER;
                }

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Checking whether user is O365 user -> NO' );
                return self::IS_NOT_O365_USER;
            }
            
            /**
             * Helper to get a property value of an object or otherwise return a default value.
             * 
             * @param   $resource       object  Object that is the parent of the property
             * @param   $prop           string  Name of the property
             * @param   $default        mixed   Default value if property does not exist
             * @param   $tolower        boolean Whether or not to change the casing of the return value to lower
             * @param   $log_message    string  Message to write to the log if the property does not exist
             */
            private static function get_property_or_default( 
                $resource, 
                $prop, 
                $default = '', 
                $tolower = false, 
                $log_message = '' ) {
                    
                    if ( isset( $resource->$prop )  && !empty( $resource->$prop) ) {
                        return $tolower && is_string( $resource->$prop )
                            ? strtolower( trim( $resource->$prop ) )
                            : ( is_string( $resource->$prop ) 
                                ? trim( $resource->$prop )
                                : $resource->$prop );
                    }
                    
                    if ( !empty( $log_message ) ) {
                        Log_Service::write_log( 'WARN', __METHOD__ . " -> $log_message" );
                    }

                    return $default;
            }

            /**
             * Helper to get a property value of an object or otherwise return a default value.
             * 
             * @param   $resource       array   (Associative) array that is the parent of the property
             * @param   $prop           string  Name of the property
             * @param   $default        mixed   Default value if property does not exist
             * @param   $tolower        boolean Whether or not to change the casing of the return value to lower
             * @param   $log_message    string  Message to write to the log if the property does not exist
             */
            private static function get_arr_property_or_default( 
                $resource, 
                $prop, 
                $default = '', 
                $tolower = false, 
                $log_message = '' ) {
                    if ( isset( $resource[ $prop ] ) && !empty( $resource[ $prop ]) ) {
                        return $tolower && is_string( $resource[ $prop ] )
                            ? strtolower( trim( $resource[ $prop ] ) )
                            : ( is_string( $resource[ $prop ] ) 
                                ? trim( $resource[ $prop ] )
                                : $resource[ $prop ] );
                    }
                    Log_Service::write_log( 'WARN', __METHOD__ . " -> $log_message" );
                    return $default;
            }
        }
    }