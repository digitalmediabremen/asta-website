<?php

    namespace Wpo\Services;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    use \Wpo\Core\User;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    use \Wpo\Services\Graph_Service;
    use \Wpo\Services\User_Service;

    if ( !class_exists( '\Wpo\Services\User_Custom_Fields_Service' ) ) {

        class User_Custom_Fields_Service { 

            /**
             * @since 11.0
             */
            public static function update_custom_fields( $wp_usr_id, $wpo_usr ) {

                if ( empty( $wpo_usr->graph_resource ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Cannot update custom user fields because the graph resource has not been retrieved' );
                    return;
                }
                
                // Check to see if expanded properties need to be loaded (currently only manager is supported)
                $extra_user_fields = Options_Service::get_global_list_var( 'extra_user_fields' );
                $expanded_fields = array();

                // Iterate over the configured graph fields and identify any supported expandable properties
                array_map( function ( $kv_pair  ) use ( &$expanded_fields ) {
                    if ( false !== stripos( $kv_pair[ 'key' ], 'manager' ) ) {
                        $expanded_fields[] = 'manager';
                    }
                }, $extra_user_fields );
                
                // Query to expand property
                if ( in_array( 'manager', $expanded_fields ) ) {
                    $upn = User_Service::try_get_user_principal_name( $wp_usr_id );

                    if ( !empty( $upn ) ) {
                        $user_manager = Graph_Service::fetch( '/users/' . \rawurlencode( $upn ) . '/manager', 'GET', false, array( 'Accept: application/json;odata.metadata=minimal' ) );

                        // Expand user details
                        if ( Graph_Service::is_fetch_result_ok( $user_manager, 'Could not retrieve user manager details for user ' . $upn, 'WARN' ) ) {
                            $wpo_usr->graph_resource[ 'manager' ] = $user_manager[ 'payload' ];
                        }
                    }
                }

                self::process_extra_user_fields( function( $name, $title ) use ( &$wpo_usr, &$wp_usr_id ) {

                    if ( isset( $wpo_usr->graph_resource[ $name ] ) && array_key_exists( $name, $wpo_usr->graph_resource ) ) {

                        $value = $name == 'manager'
                            ? self::parse_manager_details( $wpo_usr->graph_resource[ 'manager' ] )
                            : $wpo_usr->graph_resource[ $name ];

                        update_user_meta(
                            $wp_usr_id,
                            $name,
                            $value);
                            
                        
                        if ( function_exists( 'xprofile_set_field_data' ) && true === Options_Service::get_global_boolean_var( 'use_bp_extended' ) ) {
                            xprofile_set_field_data( $title, $wp_usr_id,  $value );
                        }
                    }
                } );
            }

            /**
             * 
             * @param function callback with signature ( $name, $title ) => void
             * 
             * @return void
             */
            public static function process_extra_user_fields( $callback ) {

                $extra_user_fields = Options_Service::get_global_list_var( 'extra_user_fields' );

                if ( sizeof( $extra_user_fields ) == 0 )
                    return;

                foreach( $extra_user_fields as $kv_pair )
                    $callback( $kv_pair[ 'key' ], $kv_pair[ 'value' ] );
            }

            /**
             * Adds an additional section to the bottom of the user profile page
             * 
             * @since 2.0
             * 
             * @param WP_User $user whose profile is being shown
             * @return void
             */
            public static function show_extra_user_fields( $user ) { 

                if ( false === Options_Service::get_global_boolean_var( 'graph_user_details' ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Extra user fields disabled as per configuration' );
                    return;
                }
                elseif ( true === Options_Service::get_global_boolean_var( 'use_bp_extended' ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Extra user fields will be display on BuddyPress Extended Profile instead' );
                    return;
                }
                else {

                    echo( "<h3>" . __( 'Office 365 Profile Information', 'wpo365-login' ) . "</h3>" );
                    echo( "<table class=\"form-table\">" );

                    self::process_extra_user_fields( function( $name, $title ) use ( &$user ) {

                        $value = get_user_meta( $user->ID, $name, true );

                        echo ( "<tr><th><label for=\"$name\">$title</label></th>" );

                        if( is_array( $value ) ) {

                            echo( "<td>" );

                            foreach( $value as $idx => $val ) {

                                if( empty( $val ) ) {
                                    continue;
                                }

                                echo "<input type=\"text\" name=\"$name". "__##__" ."$idx\" id=\"$name$idx\" value=\"$val\" class=\"regular-text\" /><br />";
                            }

                            echo( "</td>" );
                        }
                        else {

                            echo ( "<td><input type=\"text\" name=\"$name\" id=\"$name\" value=\"$value\" class=\"regular-text\" /><br/></td>" );
                        }

                        echo( "</tr>" );
                    } );

                    echo( '</table>' );
                }
            }

            /**
             * Allow users to save their updated extra user fields
             * 
             * @since 4.0
             * 
             * @return mixed(boolean|void)
             */
            public static function save_user_details( $user_id ) {

                if ( !current_user_can( 'edit_user', $user_id ) ) {
                    return false;
                }

                self::process_extra_user_fields( function( $name, $title ) use ( &$user_id ) {

                    if ( !empty( $_POST[ $name ] ) ) {

                        update_user_meta(
                            $user_id,
                            $name,
                            sanitize_text_field( $_POST[ $name ] ) );
                        return;
                    }

                    $flipped_post = array_flip( $_POST );

                    $array_of_user_meta = array_filter( $flipped_post, function( $key ) use ( &$name ) {
                        return ( false !== strpos( $key, $name . "__##__" ) );
                    } );

                    if ( false === empty( $array_of_user_meta ) ) {

                        $array_of_user_meta = array_flip( $array_of_user_meta );
                        $array_of_user_meta_values = array_values( $array_of_user_meta );
                        
                        update_user_meta(
                            $user_id,
                            $name,
                            $array_of_user_meta_values );
                        return;
                    }
                } );
            }

            /**
             * Parses the manager details fetched from Microsoft Graph.
             * 
             * @since 7.17
             * 
             * @return array Assoc. array with the most important manager details.
             */
            private static function parse_manager_details( $manager ) {
                if( empty( $manager ) ) {
                    return array();
                }
                $displayName = !empty( $manager[ 'displayName' ] )
                    ? $manager[ 'displayName' ]
                    : '';
                $mail = !empty( $manager[ 'mail' ] )
                    ? $manager[ 'mail' ]
                    : '';
                $officeLocation = !empty( $manager[ 'officeLocation' ] )
                    ? $manager[ 'officeLocation' ]
                    : '';
                $department = !empty( $manager[ 'department' ] )
                    ? $manager[ 'department' ]
                    : '';
                $businessPhones = !empty( $manager[ 'businessPhones' ] )
                    ? $manager[ 'businessPhones' ][0]
                    : '';
                $mobilePhone = !empty( $manager[ 'mobilePhone' ] ) 
                    ? $manager[ 'mobilePhone' ][0]
                    : '';
                return array(
                    'displayName' => $displayName,
                    'mail' => $mail,
                    'officeLocation' => $officeLocation,
                    'department' => $department,
                    'businessPhones' => $businessPhones,
                    'mobilePhone' => $mobilePhone,
                );
            }
        }
    }