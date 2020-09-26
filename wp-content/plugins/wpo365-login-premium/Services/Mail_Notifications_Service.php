<?php
    
    namespace Wpo\Services;

    use \Wpo\Core\Url_Helpers;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Mail_Notifications_Service' ) ) {
    
        class Mail_Notifications_Service {
            /**
             * Overriding the OOTB new_user_notification function due to errors caused by switching the local (possibly too early).
             * 
             * @since 7.5
             * 
             * See online docs for parameter and return information.
             */
            public static function new_user_notification( $user_id, $deprecated = null, $notify = 'both' ) {
                if ( $deprecated !== null ) {
                    _deprecated_argument( __FUNCTION__, '4.3.1' );
                }
            
                // Accepts only 'user', 'admin' , 'both' or default '' as $notify
                if ( ! in_array( $notify, array( 'user', 'admin', 'both', '' ), true ) ) {
                    return;
                }
            
                global $wpdb, $wp_hasher;
                $user = get_userdata( $user_id );
            
                // The blogname option is escaped with esc_html on the way into the database in sanitize_option
                // we want to reverse this for the plain text arena of emails.
                $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
            
                if ( 'user' !== $notify ) {
                    // $switched_locale = switch_to_locale( get_locale() );
            
                    /* translators: %s: site title */
                    $message = sprintf( __( 'New user registration on your site %s:', 'wpo365-login' ), $blogname ) . "\r\n\r\n";
                    /* translators: %s: user login */
                    $message .= sprintf( __( 'Username: %s', 'wpo365-login' ), $user->user_login ) . "\r\n\r\n";
                    /* translators: %s: user email address */
                    $message .= sprintf( __( 'Email: %s', 'wpo365-login' ), $user->user_email ) . "\r\n";
            
                    $wp_new_user_notification_email_admin = array(
                        'to'      => get_option( 'admin_email' ),
                        /* translators: Password change notification email subject. %s: Site title */
                        'subject' => __( '[%s] New User Registration', 'wpo365-login' ),
                        'message' => $message,
                        'headers' => '',
                    );
            
                    /**
                    * Filters the contents of the new user notification email sent to the site admin.
                    *
                    * @since 4.9.0
                    *
                    * @param array   $wp_new_user_notification_email {
                    *     Used to build wp_mail().
                    *
                    *     @type string $to      The intended recipient - site admin email address.
                    *     @type string $subject The subject of the email.
                    *     @type string $message The body of the email.
                    *     @type string $headers The headers of the email.
                    * }
                    * @param WP_User $user     User object for new user.
                    * @param string  $blogname The site title.
                    */
                    $wp_new_user_notification_email_admin = apply_filters( 'wp_new_user_notification_email_admin', $wp_new_user_notification_email_admin, $user, $blogname );

                    @wp_mail(
                        $wp_new_user_notification_email_admin['to'],
                        wp_specialchars_decode( sprintf( $wp_new_user_notification_email_admin['subject'], $blogname ) ),
                        $wp_new_user_notification_email_admin['message'],
                        $wp_new_user_notification_email_admin['headers']
                    );
            
                    /* if ( $switched_locale ) {
                        restore_previous_locale();
                    } */
                }
            
                // `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
                if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
                    return;
                }
            
                // Generate something random for a password reset key.
                $key = wp_generate_password( 20, false );
            
                /** This action is documented in wp-login.php */
                do_action( 'retrieve_password_key', $user->user_login, $key );
            
                // Now insert the key, hashed, into the DB.
                if ( empty( $wp_hasher ) ) {
                    require_once ABSPATH . WPINC . '/class-phpass.php';
                    $wp_hasher = new PasswordHash( 8, true );
                }
                $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
                $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );
            
                /* $switched_locale = switch_to_locale( get_user_locale( $user ) ); */
            
                /* translators: %s: user login */
                $message  = sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
                $message .= __( 'To set your password, visit the following address:', 'wpo365-login' ) . "\r\n\r\n";
                $message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' ) . ">\r\n\r\n";
            
                $message .= Url_Helpers::get_preferred_login_url() . "\r\n";
            
                $wp_new_user_notification_email = array(
                    'to'      => $user->user_email,
                    /* translators: Password change notification email subject. %s: Site title */
                    'subject' => __( '[%s] Your username and password info', 'wpo365-login' ),
                    'message' => $message,
                    'headers' => '',
                );
            
                /**
                * Filters the contents of the new user notification email sent to the new user.
                *
                * @since 4.9.0
                *
                * @param array   $wp_new_user_notification_email {
                *     Used to build wp_mail().
                *
                *     @type string $to      The intended recipient - New user email address.
                *     @type string $subject The subject of the email.
                *     @type string $message The body of the email.
                *     @type string $headers The headers of the email.
                * }
                * @param WP_User $user     User object for new user.
                * @param string  $blogname The site title.
                */
                $wp_new_user_notification_email = apply_filters( 'wp_new_user_notification_email', $wp_new_user_notification_email, $user, $blogname );

                wp_mail(
                    $wp_new_user_notification_email['to'],
                    wp_specialchars_decode( sprintf( $wp_new_user_notification_email['subject'], $blogname ) ),
                    $wp_new_user_notification_email['message'],
                    $wp_new_user_notification_email['headers']
                );
            
                /* if ( $switched_locale ) {
                    restore_previous_locale();
                } */
            }

            /**
             * Custom filter that hooks up with the wp_new_user_notification_email filter.
             * 
             * @since 7.5
             * 
             * @param   array   New user registration email info: to, subject, message and headers
             * @param   WP_user The WP_User that was just registered as a new user
             * @param   string  The current blog's title
             * 
             * @return array The (possibly updated) new user registration mail info.
             */
            public static function new_user_notification_email($email_info, $wp_user, $blogname ) {
                // Do nothing if custom template is not explicitely configured
                if( false === Options_Service::get_global_boolean_var( 'new_usr_send_mail_custom' ) ) {
                    return $email_info;
                }
                
                // e.g. $subject_template = '[__##BLOG_NAME##__] New user registration';
                $subject_template = Options_Service::get_global_string_var( 'new_usr_mail_subject' );
                $subject = self::replace_template_tags( $subject_template, $wp_user, $blogname );
                
                // e.g. $title_template = 'USER REGISTRATION';
                $title_template = Options_Service::get_global_string_var( 'new_usr_mail_title' );
                $title = self::replace_template_tags( $title_template, $wp_user, $blogname );

                // e.g. $subtitle_template = ''; but currently not in use
                $subtitle = '';

                // e.g. $salutation_template = 'Dear __##USER_DISPLAY_NAME##__';
                $salutation_template = Options_Service::get_global_string_var( 'new_usr_mail_salutation' );
                $salutation = self::replace_template_tags( $salutation_template, $wp_user, $blogname );

                // e.g. $body_template = 'The website <strong><a href="__##BLOG_URL##__">__##BLOG_NAME##__</a></strong> has been shared with you.'
                // . 'You can now sign in using your Office 365 account <strong>__##USER_LOGIN_NAME##__</strong>.';
                $body_template = Options_Service::get_global_string_var( 'new_usr_mail_body' );
                $body = self::replace_template_tags( $body_template, $wp_user, $blogname );

                // e.g. $footer_template = '<strong>WPO365 - Connecting Wordpress and Office 365</strong><br/>'
                //  . 'Zurich, Switzerland<br/><br/>'
                //  . 'Stay informed on Twitter <a href="https://twitter.com/WPO365" target="_blank">https://twitter.com/WPO365</a><br/>';
                $footer_template = Options_Service::get_global_string_var( 'new_usr_mail_footer' );
                $footer = self::replace_template_tags( $footer_template, $wp_user, $blogname );

                ob_start();
                include( $GLOBALS[ 'WPO_CONFIG' ][ 'extension_dir' ]. '/templates/new-user.php' );    
                $message = ob_get_clean();
                
                $email_info[ 'message' ] = $message;
                $email_info[ 'subject' ] = $subject;
                $email_info[ 'headers' ] = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=ISO-8859-1\r\n";

                return $email_info;
            }

            /**
             * Triggered by the wp_mail_failed hook.
             * 
             * @since 11.0
             * 
             * @param $mail_error mixed The PHP Mailer error
             * 
             * @return void
             */
            public static function log_mail_error( $mail_error ) {
                
                if ( class_exists( '\Wpo\Services\Log_Service' ) ) {
                    \Wpo\Services\Log_Service::write_log( 'WARN', $mail_error );
                }
            }

            /**
             * Replaces various new user email template tags with their corresponding values.
             * 
             * @since 7.5
             * 
             * @param $template string  The template with the tags to be replaced
             * @param $wp_user  WP_User The WP_User object used to obtain diplay and login name plus email
             * @param $blogname string  The blog's title
             * 
             * @return string Template with tags replaced with their corresponding instance values.
             */
            private static function replace_template_tags( $template, $wp_user, $blogname ) {
                $template = str_replace( '__##USER_DISPLAY_NAME##__', $wp_user->display_name, $template );
                $template = str_replace( '__##USER_LOGIN_NAME##__', $wp_user->user_login, $template );
                $template = str_replace( '__##USER_EMAIL##__', $wp_user->user_email, $template );
                $template = str_replace( '__##BLOG_NAME##__', $blogname, $template );
                $template = str_replace( '__##BLOG_URL##__', network_site_url(), $template );
                return $template;
            }
        }
    }