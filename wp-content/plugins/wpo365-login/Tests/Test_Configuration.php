<?php

    namespace Wpo\Tests;

    use \Wpo\Services\Options_Service;

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    if ( !class_exists( '\Wpo\Tests\Test_Configuration' ) ) {

        class Test_Configuration {

            public function test_tenant_id() {
                $test_result = new Test_Result( 'Tenant ID has been configured', 'Configuration', Test_Result::SEVERITY_BLOCKING );
                $test_result->passed = true;

                $tenant_id = Options_Service::get_global_string_var( 'tenant_id' );

                if ( empty( $tenant_id ) ) {
                    $test_result->passed = false;
                    $test_result->message = "Tenant ID is not configured. Please copy the 'Directory (tenant) ID' from your Azure AD App registration's 'Overview' page and paste it into the corresponding field on the <a href=\"#singleSignOn\">'Single Sign-on' tab</a>.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/22-sso';
                }
                elseif ( !preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $tenant_id ) ) {
                    $test_result->passed = false;
                    $test_result->message = "Tenant ID is not a valid GUID. Please copy the 'Directory (tenant) ID' from your Azure AD App registration's 'Overview' page and paste it into the corresponding field on the <a href=\"#singleSignOn\">'Single Sign-on' tab</a>.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/22-sso';
                }

                return $test_result;
            }

            public function test_application_id() {

                if ( Options_Service::get_global_boolean_var( 'use_saml' ) ) {
                    return;
                }

                $test_result = new Test_Result( 'Application ID has been configured', 'Configuration', Test_Result::SEVERITY_BLOCKING );
                $test_result->passed = true;

                $application_id = Options_Service::get_global_string_var( 'application_id' );

                if ( empty( $application_id ) ) {
                    $test_result->passed = false;
                    $test_result->message = "Application ID is not configured. Please copy the 'Application (client) ID' from your Azure AD App registration's 'Overview' page and paste it into the corresponding field on the <a href=\"#singleSignOn\">'Single Sign-on' tab</a>.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/22-sso';
                }
                elseif ( !preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $application_id ) ) {
                    $test_result->passed = false;
                    $test_result->message = "Application ID is not a valid GUID. Please copy the 'Application (client) ID' from your Azure AD App registration's 'Overview' page and paste it into the corresponding field on the <a href=\"#singleSignOn\">'Single Sign-on' tab</a>.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/22-sso';
                }

                return $test_result;
            }

            public function test_redirect_url() {

                if ( Options_Service::get_global_boolean_var( 'use_saml' ) ) {
                    return;
                }

                $test_result = new Test_Result( 'Redirect URL has been configured', 'Configuration', Test_Result::SEVERITY_BLOCKING );
                $test_result->passed = true;

                $redirect_url = Options_Service::get_global_string_var( 'redirect_url' );

                if ( empty( $redirect_url ) ) {
                    $test_result->passed = false;
                    $test_result->message = "Please copy the 'Redirect URI' from your Azure AD App registration's 'Authentication' page and paste it into the corresponding field on the <a href=\"#singleSignOn\">'Single Sign-on' tab</a>.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/22-sso';
                }

                return $test_result;
            }

            public function test_auth_scenario_internet_optimization() {
                $is_optimized = defined( 'WPO_AUTH_SCENARIO' ) && constant( 'WPO_AUTH_SCENARIO' ) == 'internet';

                if ( !$is_optimized ) {
                    return;
                }

                $test_result = new Test_Result( '"Internet" mode optimization', 'Configuration', Test_Result::SEVERITY_CRITICAL );
                $test_result->passed = true;
                
                $redirect_url = Options_Service::get_global_string_var( 'redirect_url' );

                if ( $is_optimized && ( empty( $redirect_url ) || false === stripos( $redirect_url, '/wp-admin' ) )  ) {
                    $test_result->passed = false;
                    $test_result->message = "Since you configured <i>define( 'WPO_AUTH_SCENARIO', 'internet' );</i> you must ensure that the Redirect URI ends with '/wp-admin/'. Please update the Redirect URI first in <strong>Azure AD</strong> for your <i>App registration</i> and then subsequently on the <a href=\"#singleSignOn\">'Single Sign-on' tab</a> .";
                    $test_result->more_info = 'https://docs.wpo365.com/article/36-authentication-scenario';
                }

                return $test_result;
            }

            public function test_debug_mode_enabled() {
                $test_result = new Test_Result( 'Debug log disabled', 'Configuration', Test_Result::SEVERITY_CRITICAL );
                $test_result->passed = true;

                $debug_log = Options_Service::get_global_boolean_var( 'debug_log' );

                if ( true === $debug_log ) {
                    $test_result->passed = false;
                    $test_result->message = 'Please disable debug log to improve overall performance of your website. Goto <a href="#debug">Debug</a> to disable the debug log.';
                    $test_result->more_info = 'https://docs.wpo365.com/article/19-enable-debug-log';
                }

                return $test_result;
            }

            public function test_using_https() {
                $test_result = new Test_Result( 'Correct use of HTTPS', 'Configuration', Test_Result::SEVERITY_CRITICAL );
                $test_result->passed = true;

                $aad_redirect_url = Options_Service::get_global_string_var( 'redirect_url' );
                $wp_home = $GLOBALS[ 'WPO_CONFIG' ][ 'url_info' ][ 'wp_site_url' ];
                
                if ( stripos( $aad_redirect_url, 'http://' ) === 0  && stripos( $aad_redirect_url, 'localhost' ) === false ) {
                    $test_result->passed = false;
                    $test_result->message = '(Azure AD) Redirect URL must start with https://. Please goto <a href="#singleSignOn">Single Sign-on</a> and update the RedirectURL and make sure that the Redirect URI that you entered for your Azure AD App registration also starts with https://. If your website does not support SSL then please purchase an SSL certificate and configure this for your website. You can only use an insecure website address for development purposes that use "localhost".';
                    $test_result->more_info = '';
                }
                elseif ( stripos( $wp_home, 'http://' ) === 0 && stripos( $wp_home, 'localhost' ) === false ) {
                    $test_result->passed = false;
                    $test_result->message = '(WordPress) Home URL must start with https://. Please goto WordPress Admin > Settings > General and update WordPress Address (URL) and Site Address (URL).';
                    $test_result->more_info = '';
                }

                return $test_result;
            }

            public function test_domain_hint() {

                if ( Options_Service::get_global_boolean_var( 'use_saml' ) ) {
                    return;
                }
                
                $test_result = new Test_Result( 'Domain hint configured', 'Configuration', Test_Result::SEVERITY_CRITICAL );
                $test_result->passed = true;

                $domain_hint = Options_Service::get_global_string_var( 'domain_hint' );

                if ( empty( $domain_hint ) ) {
                    $test_result->passed = false;
                    $test_result->message = 'Consider configuring a domain hint to avoid users with multiple Azure AD / Office 365 accounts signing in with the wrong account. Please goto <a href="#singleSignOn">Single Sign-on</a> and add a domain hint.';
                    $test_result->more_info = 'https://docs.wpo365.com/article/35-domain-hint';
                }

                return $test_result;
            }

            public function test_custom_domain() {
                $test_result = new Test_Result( 'Custom domain names configured', 'Configuration', Test_Result::SEVERITY_BLOCKING );
                $test_result->passed = true;

                $custom_domain = array_flip( Options_Service::get_global_list_var( 'custom_domain' ) );

                if ( empty( $custom_domain ) ) {
                    $test_result->passed = false;
                    $test_result->message = "You have not configured at least one custom domain. Please check your <a href=\"https://portal.azure.com/#blade/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/Domains\" target=\"_blank\">Custom domain names</a> in Azure Portal and add the domain names on the <a href=\"#userRegistration\">User registration</a> tab accordingly. Please press '+' after each entry to add the custom domain name to the list.";
                    $test_result->more_info = 'http://www.wpo365.com/domain-hint/';
                }

                return $test_result;
            }
        }
    }