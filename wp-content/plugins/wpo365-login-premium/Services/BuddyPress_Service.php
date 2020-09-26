<?php

    namespace Wpo\Services;

    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\BuddyPress_Service' ) ) {
    
        class BuddyPress_Service  {

            /**
             * Adds an additional section to the bottom of the user profile page
             * 
             * @since 5.3
             * 
             * @param WP_User $user whose profile is being shown
             * @return void
             */
            public static function bp_show_extra_user_fields( $user ) { 

                if ( false === Options_Service::get_global_boolean_var( 'graph_user_details' ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Extra user fields disabled as per configuration' );
                    return;
                }
                elseif ( true === Options_Service::get_global_boolean_var( 'use_bp_extended' ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Extra user fields will be display on BuddyPress Extended Profile instead' );
                    return;
                }
                elseif ( !class_exists( '\Wpo\Services\User_Details_Service' ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Cannot show extra BuddyPress fields because of missing dependency' );
                    return;
                }
                else {

                    echo( '<div class="bp-widget base">' );
                    echo( '<h3 class="screen-heading profile-group-title">' . __( 'Directory Info', 'wpo365-login' ) . '</h3>' );
                    echo( '<table class="profile-fields bp-tables-user"><tbody>' );

                    \Wpo\Services\User_Custom_Fields_Service::process_extra_user_fields( function( $name, $title ) use ( &$user ) {

                        $value = get_user_meta( bp_displayed_user_id(), $name, true );
                        echo( '<tr class="field_1 field_name required-field visibility-public field_type_textbox"><td class="label">' . $title . '</td>' );
                        
                        if( is_array( $value ) ) {
                            echo( '<td class="data">' );

                            foreach( $value as $idx => $val )
                                echo( '<p>' . $val . '</p>' );

                            echo( '</td>' );
                        }
                        else
                            echo ( '<td class="data"><p>' . $value . '</p></td>' );

                        echo( "</tr>" );
                    } );
                    
                    echo( '</tbody></table></div>' );
                }
            }

            /**
             * Helper method that returns the O365 avatar for Buddy Press.
             * 
             * @since 9.0
             * 
             * @param $avatar Image tag for the user's avatar.
             * @return string Image tag for the user's avatar possibly with img URL replaced with O365 profile image URL.
             */
            public static function fetch_buddy_press_avatar( $bp_avatar, $params ) {

                if ( false === Options_Service::get_global_boolean_var( 'use_bp_avatar' ) ) {
                    return $bp_avatar;
                }

                if ( !is_array( $params ) || !isset( $params[ 'item_id' ] ) ) {
                    return $bp_avatar;
                }

                if ( !class_exists( '\Wpo\Services\Avatar_Service' ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Cannot BuddyPress avatar because of missing dependency' );
                    return $bp_avatar;
                }

                /**
                 * @since 10.5
                 * 
                 * Don't return avatar if objec is not a user (e.g. but a group)
                 */
                if ( is_array( $params ) && isset( $params[ 'object' ] ) && false === stripos( $params[ 'object' ], 'user' ) ) {
                    return $bp_avatar;
                }

                $o365_avatar_url = \Wpo\Services\Avatar_Service::get_o365_avatar_url( intval( $params[ 'item_id' ] ) );

                return empty( $o365_avatar_url )
                    ? $bp_avatar
                    : \preg_replace( '/src=".+?"/', 'src="' . $o365_avatar_url . '"', $bp_avatar );
            }
        }
    }
