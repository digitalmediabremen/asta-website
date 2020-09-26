<?php

    namespace Wpo\Services;

    use \Wpo\Services\Graph_Service;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    use \Wpo\Services\User_Service;

    if ( !class_exists( '\Wpo\Services\Avatar_Service' ) ) {
    
        class Avatar_Service  {

            const USR_META_AVATAR           = 'wpo_avatar';
            const USR_META_AVATAR_UPDATED   = 'wpo_avatar_updated';

            /**
             * WordPress filter hook to replace the default WordPress avatar with the Office 365 user image. When the 
             * requested avatar is for the currently logged in user it will check to see whether the avatar was previously
             * loaded and if not or if it needs to be refreshed, it will try and get it using Microsoft Graph.
             * 
             * @since 1.0
             * 
             * @param $avatar Image tag for the user's avatar.
             * @param $id_or_email A user ID, email address, or comment object.
             * @param $size Square avatar width and height in pixels to retrieve.
             * @param $default (Optional) URL for the default image or a default type.
             * @param $alt Alternative text to use in the avatar image tag.
             * @param $args @since 4.2.0 (Optional) Extra arguments to retrieve the avatar.
             * @return string Image tag for the user's avatar
             */
            public static function get_O365_avatar( $wp_avatar, $id_or_email, $size, $for_other_user = false ) {

                // Check if O365 avatar is enabled
                if ( false === Options_Service::get_global_boolean_var( 'use_avatar' ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Avatar function not enabled so returning default WordPress' );
                    return $wp_avatar;
                }

                $wp_usr = null;

                $id_or_email = is_object( $id_or_email ) ? $id_or_email->comment_author_email : $id_or_email;

                // If provided an email and a corresponding WP_User cannot be found, return default avatar
                if ( is_email( $id_or_email ) ) {

                    if ( !email_exists( $id_or_email ) ) {
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Email not found therefore falling back to default avatar for ' . $id_or_email );
                        return $wp_avatar; // Avatar for "email" user requested but user "unknown"
                    }
                    else {
                        $wp_usr = \get_user_by( 'email', $id_or_email ); // Avatar for "known" user by "email"
                    }
                }
                else {
                    $wp_usr = \get_user_by( 'ID', $id_or_email ); // Assume we have received a user ID
                }

                // Can not resolve a WP_User return default avatar

                if ( empty( $wp_usr ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> User with ID or email ' . $id_or_email . ' not found therefore falling back to default avatar' );
                    return $wp_avatar;
                }

                // Ensure the avatar template is available

                $user_avatar = Options_Service::get_global_string_var( 'avatar_template' );
                
                if ( empty( $user_avatar ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Template for O365 avatar not found therefore using default avatar template' );
                    $user_avatar = '<img src="__##AVATAR_URL##__" width="__##AVATAR_SIZE##__" height="__##AVATAR_SIZE##__" class="ui avatar image">';
                }

                // Ensure we can use the WP_Filesystem

                $files = Files_Service::get_instance();

                if ( !$files->configure_wpo365_profile_images() ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Could not configure Files service and therefore returning default WP avatar' );
                    return $wp_avatar;
                }

                // Ensure the profile images directory can be determined

                $profile_images_dir = $files->get_wpo365_profile_images_dir();
                $profile_images_url = $files->get_wpo365_profile_images_url();

                $profile_image_file_name = $wp_usr->ID . '.png';
                $profile_image_path = $profile_images_dir . $profile_image_file_name;
                $profile_image_url =  $profile_images_url . $profile_image_file_name;

                $profile_image_exists = file_exists( $profile_image_path );

                $user_avatar = str_replace( '__##AVATAR_URL##__', $profile_image_url, $user_avatar );
                $user_avatar = str_replace( '__##AVATAR_SIZE##__', $size, $user_avatar );

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Plugin will look for the following O365 avatar -> ' . $profile_image_path );

                // Check if avatar requires updating

                $last_updated = $profile_image_exists
                    ? filemtime( $profile_image_path )
                    : 0;

                $avatar_refresh = Options_Service::get_global_numeric_var( 'avatar_updated' );
                $avatar_refresh = empty( $avatar_refresh ) ? 1296000 : $avatar_refresh;
                $avatar_expired = time() - $last_updated > $avatar_refresh;

                if ( !$avatar_expired ) {
                    Log_Service::write_log( 'DEBUG', 'Returning cached O365 avatar' );
                    return $user_avatar;
                }

                // At this point requested avatar 
                // 1. Either does not exist
                // 2. Or should be refreshed

                $upn = User_Service::try_get_user_principal_name( $wp_usr->ID );

                if ( empty( $upn ) ) {
                    Log_Service::write_log( 'DEBUG', 'Cannot determine the user principal name to obtain a user photo from Microsoft Graph for user with ID ' . $wp_usr->user_login );
                    return $wp_avatar;
                }

                /**
                 * When getting the avatar for an other user ensure the access token has the
                 * scope for doing so.
                 */

                $scope = 'https://graph.microsoft.com/user.read';

                if ( \get_current_user_id() !== $wp_usr->ID ) {
                    $scope = 'https://graph.microsoft.com/user.read.all';

                    // Apply some throttling
                    $request_service = Request_Service::get_instance();
                    $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );
                    $nr_of_avatars_refreshed = $request->get_item( 'nr_of_avatars_refreshed' );
                    $nr_of_avatars_refreshed = empty( $nr_of_avatars_refreshed ) ? 1 : $nr_of_avatars_refreshed + 1;
                    $request->set_item( 'nr_of_avatars_refreshed', $nr_of_avatars_refreshed );
                    
                    if ( $nr_of_avatars_refreshed > 5 ) {
                        
                        if ( $profile_image_exists ) {
                            return $user_avatar;
                        }

                        return $wp_avatar;
                    }
                }

                /** 
                 * The beta endpoint will return the profile picture from Exchange OR AAD 
                 * whereas the v1.0 only takes the profile picture from Exchange.
                 */

                $graph_version = Options_Service::get_global_string_var( 'graph_version' );

                if ( $graph_version != 'beta' ) {
                    $GLOBALS[ 'WPO_CONFIG' ][ 'options' ][ 'graph_version' ] = 'beta';
                }
                
                $raw = Graph_Service::fetch( '/users/' . \rawurlencode( $upn ) . '/photo/$value', 'GET', true, array( 'Accept: application/json;odata.metadata=minimal' ), false, false, '', $scope );

                $GLOBALS[ 'WPO_CONFIG' ][ 'options' ][ 'graph_version' ] = $graph_version;

                // Take the default WordPress avatar because something went wrong
                if ( is_wp_error( $raw ) 
                    || $raw === false 
                    || $raw[ 'response_code' ] != 200 ) {

                        if ( is_wp_error( $raw ) ) {
                            // Something wrong with the acces token
                            $error_level = $raw->get_error_code() == '1025' ? 'WARN' : 'ERROR';
                            Log_Service::write_log( $error_level, __METHOD__ . ' -> Could not retrieve O365 avatar therefore returning default avatar [Error: ' . $raw->get_error_message() . ']');    
                        }
                        else {
                            // Most likely no profile picture available - therefore not an error
                            Log_Service::write_log( 'WARN', __METHOD__ . ' -> Could not retrieve O365 avatar therefore returning default avatar [See log for details]');
                            Log_Service::write_log( 'WARN', $raw );
                        }

                        // The refresh was not successful, most likely due to missing permissions.
                        // Wait for the next time the user logs on and keep using the expired avatar.
                        if ( $profile_image_exists && $avatar_expired ) {
                            return $user_avatar;
                        }

                        // Most likely the user does not have a picture
                        $gravatar_url = \get_avatar_url( $wp_usr->ID );

                        if ( $gravatar_url === false ) {
                            Log_Service::write_log( 'WARN', __METHOD__ . ' -> Could not retrieve default gravatar URL' );
                            return $wp_avatar;
                        }

                        $gravatar_url = \html_entity_decode( $gravatar_url );

                        if ( stripos( $gravatar_url, '//' ) === 0 ) {
                            $gravatar_url = "https:$gravatar_url";   
                        }

                        $curl = curl_init();
                        curl_setopt( $curl, CURLOPT_URL, $gravatar_url );
                        curl_setopt( $curl, CURLOPT_HEADER, 0);
                        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
                        curl_setopt( $curl, CURLOPT_BINARYTRANSFER,1);
                        
                        if ( Options_Service::get_global_boolean_var( 'skip_host_verification' ) ) {
                            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); 
                            curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 ); 
                        }

                        if ( !empty( $curl_proxy = Options_Service::get_global_string_var( 'curl_proxy' ) ) ) {
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Using curl proxy ' . $curl_proxy );
                            curl_setopt( $curl, CURLOPT_PROXY, $curl_proxy );
                        }

                        $gravatar = curl_exec( $curl ); // result holds the image data

                        if ( curl_error( $curl ) ) {
                            Log_Service::write_log( 'WARN', __METHOD__ . ' -> Avatar::get_O365_avatar_V2 cURL error -> ' . curl_error( $curl ) );
                            Log_Service::write_log( 'WARN', __METHOD__ . ' -> Avatar::get_O365_avatar_V2 Could not retrieve default gravatar from URL ' . $gravatar_url );
                            curl_close( $curl );
                            return $wp_avatar;
                        }
                        
                        curl_close( $curl );

                        if ( $files->save_wpo365_profile_image( $profile_image_path, $gravatar ) === false ) {
                            Log_Service::write_log( 'WARN', __METHOD__ . ' -> Could not write gravatar image to the file system' );
                            return $wp_avatar;
                        }

                        // Default gravatar saved for next time
                        return $wp_avatar;
                }

                if ( !$files->save_wpo365_profile_image( $profile_image_path, $raw[ 'payload' ] ) ) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Could not write profile image to the file system' );
                    return $wp_avatar;
                }

                // Return the default wp avatar when the file couldn't be saved

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> O365 avatar saved successfully' );

                return $user_avatar;
            }

            /**
             * Helper method to get the O365 profile image URL when profile image is saved in wp-content/uploads.
             * 
             * @since 10.0
             * 
             * @return string O365 profile image URL otherwise empty.
             */
            public static function get_o365_avatar_url( $wp_usr_id ) {
                $upload_dir = wp_upload_dir();
                $profile_image_path = '/wpo365/profile-images';
                $profile_image_file_name = $wp_usr_id . '.png';

                $profile_image = $upload_dir[ 'basedir' ] . $profile_image_path . '/' . $profile_image_file_name;
                $profile_image_url = $upload_dir[ 'baseurl' ] . $profile_image_path . '/' . $profile_image_file_name;
                $profile_image_exists = file_exists( $profile_image );
                
                if ( false === $profile_image_exists ) {
                    return '';
                }

                return $profile_image_url;
            }
        }
    }