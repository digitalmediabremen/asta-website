<?php
    
    namespace Wpo\Services;

    use \Wpo\Core\Plugin_Helpers;
    use \Wpo\Core\Wpmu_Helpers;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Notifications_Service' ) ) {
    
        class Notifications_Service {

            /**
             * Shows admin notices when the plugin is not configured correctly
             * 
             * @since 2.3
             * 
             * @return void
             */
            public static function show_admin_notices( ) {

                if ( !is_admin() && !is_network_admin() ) {
                    return;
                }

                if ( false === Options_Service::is_wpo365_configured( ) ) {
                    if ( Options_Service::get_global_boolean_var( 'use_saml' ) ) {
                        printf( '<div class="notice notice-error" style="margin-left: 2px;"><p>' . __( 'Click <strong><a href="%s?page=wpo365-wizard">here</a></strong> to configure WordPress + Microsoft 365 / Azure AD Single Sign-on. The configuration must - at the very least - provide a valid <strong>Directory (tenant) ID</strong>, <strong>Service Provider configuration</strong>, <strong>Identity Provider configuration</strong> and an <strong>X509 certificate</strong>. Please review <a target="_blank" href="https://docs.wpo365.com/article/100-configure-single-sign-on-with-saml-2-0">this article</a> for details.', 'wpo365-login') . '</p></div>', ( get_admin_url() . ( is_network_admin() ? 'network/admin.php' : 'admin.php' ) ) );
                    }
                    else {
                        printf( '<div class="notice notice-error" style="margin-left: 2px;"><p>' . __( 'Click <strong><a href="%s?page=wpo365-wizard">here</a></strong> to configure WordPress + Microsoft 365 / Azure AD Single Sign-on. The configuration must - at the very least - provide a valid <strong>Directory (tenant) ID</strong>, <strong>Application ID</strong> and a so-called <strong>Redirect URI</strong>. Please review <a target="_blank" href="https://docs.wpo365.com/article/22-sso">this article</a> for details.', 'wpo365-login') . '</p></div>', ( get_admin_url() . ( is_network_admin() ? 'network/admin.php' : 'admin.php' ) ) );
                    }                    
                }
                
                if ( is_super_admin() ) {

                    if ( false === Options_Service::get_global_boolean_var( 'hide_error_notice' ) ) { 
                        $cached_errors = Wpmu_Helpers::mu_get_transient( 'wpo365_errors' );

                        if ( is_array( $cached_errors ) ) {
                            $wpo365_errors = '';
                            array_map( function ( $log_item ) use ( &$wpo365_errors ) {
                                $wpo365_errors .= '<li><strong>[' . date( 'Y-m-d H:i:s', $log_item[ 'time' ] ) . ']</strong> ' . $log_item[ 'body' ] . '</li>';
                            } , $cached_errors );
                            echo '<div class="notice notice-error" style="margin-left: 2px;"><p>The <strong>WordPress + Microsoft Office 365 / Azure AD</strong> plugin detected the following (last three) errors that you should address.</p><ul style="list-style: initial; padding: inherit;">' . $wpo365_errors . '</ul><p>Please take the time to review those errors. Once errors have been addressed you can safely <strong>dismiss</strong> this notice for now or check <strong>Hide error notice</strong> on the <strong>Debug</strong> tab of the <strong>WPO365</strong> wizard to hide this notice permanently. </p><p><a class="button button-primary" href="./?wpo365_errors_dismissed">' . __( 'Dismiss', 'wpo365-login' ) . '</a></p><p>Please check the <a href="https://docs.wpo365.com/" target="_blank">online documentation</a> for help or alternatively <a href="https://www.wpo365.com/contact/" target="_blank">contact WPO365 support</a> whenever you are unable to resolve the error reported.</p></div>';
                        }
                    }

                    if ( is_multisite() && !defined( 'WPO_MU_USE_SUBSITE_OPTIONS' ) ) {
                        echo '<div class="notice notice-warning" style="margin-left: 2px;"><p>You can add <strong>define( \'WPO_MU_USE_SUBSITE_OPTIONS\', false );</strong> to your wp-config.php file to force all subsites to authenticate their users via the main site and hide the WPO365 configuration link on all subsites. Vice versa you can change its value to <strong>true</strong> and configure Single Sign-on for each subsite separately.</p></div>';
                    }

                    // review
                    if ( $GLOBALS[ 'WPO_CONFIG' ][ 'plugin' ] == 'wpo365-login/wpo365-login.php' 
                        && true === Options_Service::is_wpo365_configured()
                        && false === Wpmu_Helpers::mu_get_transient( 'wpo365_review_dismissed' ) 
                        && false === Wpmu_Helpers::mu_get_transient( 'wpo365_user_created' ) 
                        && !Plugin_Helpers::is_premium_edition_active() ) {
                            echo( '<div class="notice notice-info" style="margin-left: 2px;"><p>' 
                                . sprintf( __( 'Many thanks for using the %s plugin! Could you please spare a minute and give it a review over at WordPress.org?', 'wpo365-login' ), '<strong>WPO365 | LOGIN</strong>' )
                                . '</p><p><a class="button button-primary" href="http://wordpress.org/support/view/plugin-reviews/wpo365-login?filter=5#postform" target="_blank">' . __( 'Yes, here we go!', 'wpo365-login' ) . '</a> <a class="button" href="./?wpo365_review_dismissed">' . __( 'Remind me later', 'wpo365-login' ) . '</a></p>'
                                . '<p>- Marco van Wieren | Downloads by van Wieren | <a href="https://www.wpo365.com/">https://www.wpo365.com/</a></p></div>' );
                    }

                    // upgrade
                    if ( $GLOBALS[ 'WPO_CONFIG' ][ 'plugin' ] == 'wpo365-login/wpo365-login.php' 
                        && true === Options_Service::is_wpo365_configured()
                        && false !== Wpmu_Helpers::mu_get_transient( 'wpo365_user_created' ) 
                        && false === Wpmu_Helpers::mu_get_transient( 'wpo365_upgrade_dismissed' ) 
                        && !Plugin_Helpers::is_premium_edition_active() ) {
                            echo( '<div class="notice notice-info" style="margin-left: 2px;"><p>' 
                                . __( 'The <strong>WPO365 | LOGIN</strong> plugin just created a new WordPress user for you! Check out the <strong>premium editions</strong> for more control over the single sign-on experience, user profile and avatar synchronization, integration with Microsoft 365 services such as Power BI, SharePoint Online, Microsoft Graph and Yammer and support for SCIM based Azure AD User provisioning.', 'wpo365-login' )
                                . '</p><p><a class="button button-primary" href="https://www.wpo365.com/downloads/" target="_blank">' . __( 'Yes, take me there!', 'wpo365-login' ) . '</a> <a class="button" href="./?wpo365_upgrade_dismissed">' . __( 'Remind me later', 'wpo365-login' ) . '</a></p>'
                                . '<p><strong>PS</strong> ' . __( 'You can save 10% when you enter the discount code <strong>UPGRADE2020</strong> when you checkout of the website!', 'wpo365-login' ) . '</p>'
                                . '<p>- Marco van Wieren | Downloads by van Wieren | <a href="https://www.wpo365.com/">https://www.wpo365.com/</a></p></div>' );
                    }
                }
            }

            /**
             * Helper to configure a transient to surpress admoin notices when the user clicked dismiss.
             * 
             * @since 7.18
             * 
             * @return void
             */
            public static function dismiss_admin_notices() {
                
                if ( isset( $_GET[ 'wpo365_errors_dismissed' ] ) ) {
                    Wpmu_Helpers::mu_delete_transient( 'wpo365_errors' );
                }
                
                if ( isset( $_GET[ 'wpo365_review_dismissed' ] ) ) {
                    Wpmu_Helpers::mu_set_transient( 'wpo365_review_dismissed', date( 'd' ), 1209600 );
                }

                if ( isset( $_GET[ 'wpo365_upgrade_dismissed' ] ) ) {
                    Wpmu_Helpers::mu_delete_transient( 'wpo365_user_created' );
                    Wpmu_Helpers::mu_set_transient( 'wpo365_upgrade_dismissed', date( 'd' ), 1209600 );
                }
            }
        }
    }