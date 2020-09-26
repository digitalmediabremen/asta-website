<?php

    namespace Wpo\Tests;

    use \Wpo\Core\Plugin_Helpers;
    use \Wpo\Services\Access_Token_Service;
    use \Wpo\Services\Options_Service;

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    if ( !class_exists( '\Wpo\Tests\Test_Access_Tokens' ) ) {

        class Test_Access_Tokens {

            private $access_token = null;
            private $app_only_access_token = null;
            private $static_permissions = array();
            private $app_only_static_permissions = array();

            public function test_access_token() {

                if ( Options_Service::get_global_boolean_var( 'use_saml' ) ) {
                    return;
                }

                $test_result = new Test_Result( 'Can fetch access tokens', 'Access Tokens', Test_Result::SEVERITY_LOW );
                $test_result->passed = true;

                if ( empty( Options_Service::get_global_string_var( 'application_secret' ) ) ) {
                    $test_result->passed = false;
                    $test_result->message = "An 'Application secret' has not been configured (on the <a href=\"#integration\">Integration</a> tab). Please consult the online documentation using the link below and create an 'Application secret' and update the plugin's configuration.";
                    $test_result->more_info = 'https://www.wpo365.com/application-secret/';
                    return $test_result;
                }

                $this->access_token = Access_Token_Service::get_access_token( 'https://graph.microsoft.com/user.read' );

                if ( is_wp_error( $this->access_token ) || !property_exists( $this->access_token, 'access_token' ) ) {
                    $test_result->passed = false;
                    $test_result->message = 'Could not fetch access token. The following error occurred: ' . $this->access_token->get_error_message();
                    $test_result->more_info = '';
                }
                elseif ( property_exists( $this->access_token, 'scope' ) ) {
                    $this->static_permissions = explode( ' ', $this->access_token->scope );
                }

                return $test_result;
            }

            public function test_access_token_static_permissions_email() {
                
                return $this->check_static_permission( 
                    $this->access_token,
                    'openid', 
                    'delegated',
                    $this->static_permissions,
                    Test_Result::SEVERITY_BLOCKING,
                    'and as a result the plugin may not be able to obtain ID tokens from Azure AD' );
            }

            public function test_access_token_static_permissions_openid() {
                return $this->check_static_permission( 
                    $this->access_token,
                    'email', 
                    'delegated',
                    $this->static_permissions,
                    Test_Result::SEVERITY_CRITICAL, 
                    'and as a result the plugin will fail when trying to create a new WordPress user for this Office 365 / Azure AD user.' );
            }

            public function test_access_token_static_permissions_profile() {
                return $this->check_static_permission( 
                    $this->access_token,
                    'profile',
                    'delegated',
                    $this->static_permissions,
                    Test_Result::SEVERITY_CRITICAL );
            }

            public function test_access_token_static_permissions_user_read() {
                return $this->check_static_permission( 
                    $this->access_token,
                    'https://graph.microsoft.com/User.Read', 
                    'delegated',
                    $this->static_permissions,
                    Test_Result::SEVERITY_CRITICAL );
            }

            public function test_access_token_static_permissions_user_read_all() {
                return $this->check_static_permission( 
                    $this->access_token,
                    'https://graph.microsoft.com/User.Read.All', 
                    'delegated',
                    $this->static_permissions,
                    Test_Result::SEVERITY_LOW, 
                    'and as a result premium features such as User Synchronization may not work unless you have configured an app-only access token and assigned this permission to the Azure AD App registration used to obtain app-only tokens.', 
                    'https://docs.wpo365.com/article/101-app-only-integration' );
            }

            public function test_access_token_static_permissions_group_read_all() {
                return $this->check_static_permission( 
                    $this->access_token,
                    'https://graph.microsoft.com/Group.Read.All', 
                    'delegated',
                    $this->static_permissions,
                    Test_Result::SEVERITY_LOW, 
                    'and as a result premium features such as User Synchronization may not work unless you have configured an app-only access token and assigned this permission to the Azure AD App registration used to obtain app-only tokens.', 
                    'https://docs.wpo365.com/article/101-app-only-integration' );
            }

            public function test_access_token_static_permissions_sites_search_all() {
                return $this->check_static_permission( 
                    $this->access_token,
                    'https://graph.microsoft.com/Sites.Search.All', 
                    'delegated',
                    $this->static_permissions,
                    Test_Result::SEVERITY_LOW, 
                    'and as a result the <a href="https://www.wpo365.com/content-by-search/" target="_blank">SharePoint Content by Search app</a> will not work as expected.', 
                    'https://www.wpo365.com/content-by-search/' );
            }

            public function test_access_token_static_permissions_sites_read_all() {
                return $this->check_static_permission( 
                    $this->access_token,
                    'https://graph.microsoft.com/Sites.Read.All', 
                    'delegated',
                    $this->static_permissions,
                    Test_Result::SEVERITY_LOW, 
                    'and as a result the <a href="https://www.wpo365.com/documents/" target="_blank">SharePoint Documents app</a> will not work as expected.', 
                    'https://www.wpo365.com/documents/' );
            }

            public function test_refresh_token() {

                if ( Options_Service::get_global_boolean_var( 'use_saml' ) ) {
                    return;
                }

                $test_result = new Test_Result( 'Access token contains refresh token', 'Access Tokens', Test_Result::SEVERITY_CRITICAL );
                $test_result->passed = true;

                if ( empty( $this->access_token ) ) {
                    $test_result->passed = false;
                    $test_result->message = 'Could not fetch access token -> test skipped';
                    $test_result->more_info = '';
                    return $test_result;
                }

                if ( !property_exists( $this->access_token, 'refresh_token' ) ) {
                    $test_result->passed = false;
                    $test_result->message = 'Access token does not contain refresh token. As a result the plugin is not able to request more than 1 access token and therefore subsequent calls to Microsoft Graph for different scopes will fail.';
                    $test_result->more_info = '';
                    return $test_result;
                }

                return $test_result;
            }

            public function test_app_only_access_token() {
                
                $test_result = new Test_Result( 'Can fetch app-only access tokens', 'Access Tokens', Test_Result::SEVERITY_LOW );
                $test_result->passed = true;

                if ( !Options_Service::get_global_boolean_var( 'use_app_only_token' ) ) {
                    $test_result->passed = false;
                    $test_result->message = "It is recommended to register a secondary App in Azure Active Directory for more advanced scenarios e.g. User Synchronization to restrict permissions otherwise available to all users. Please consult the online documentation for more information.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/101-app-only-integration';
                }

                $application_id = Options_Service::get_global_string_var( 'app_only_application_id' );

                if ( empty( $application_id ) ) {
                    $test_result->passed = false;
                    $test_result->message = "It is recommended to register a secondary App in Azure Active Directory for more advanced scenarios e.g. User Synchronization to restrict permissions otherwise available to all users. Please consult the online documentation for more information.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/101-app-only-integration';
                }
                elseif ( !preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $application_id ) ) {
                    $test_result->passed = false;
                    $test_result->message = 'App-only Application ID is not a valid GUID';
                    $test_result->more_info = 'https://docs.wpo365.com/article/101-app-only-integration';
                }

                $application_secret = Options_Service::get_global_string_var( 'app_only_application_secret' );

                if ( empty( $application_secret ) ) {
                    $test_result->passed = false;
                    $test_result->message = "It is recommended to register a secondary App in Azure Active Directory for more advanced scenarios e.g. User Synchronization to restrict permissions otherwise available to all users. Please consult the online documentation for more information.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/101-app-only-integration';
                }

                $this->app_only_access_token = Access_Token_Service::get_app_only_access_token();

                if ( is_wp_error( $this->app_only_access_token ) || !property_exists( $this->app_only_access_token, 'access_token' ) ) {
                    $test_result->passed = false;
                    $test_result->message = 'Could not fetch app-only access token. The following error occurred: ' . $this->app_only_access_token->get_error_message();
                    $test_result->more_info = '';
                }
                else {
                    try {
                        $jwt_parts = explode( '.', $this->app_only_access_token->access_token );

                        if ( sizeof( $jwt_parts ) == 3 ) {
                            $payload = json_decode( \base64_decode( $jwt_parts[ 1 ] ) );
                            
                            if ( property_exists( $payload, 'roles' )  ) {
                                $this->app_only_static_permissions = $payload->roles;
                            }
                        }
                    }
                    catch ( \Exception $e ) { }
                }

                return $test_result;
            }

            public function test_app_only_access_token_static_permissions_user_read_all() {

                if ( !Options_Service::get_global_boolean_var( 'use_app_only_token' ) ) {
                    return;
                }
                
                return $this->check_static_permission( 
                    $this->app_only_access_token,
                    'User.Read.All', 
                    'application',
                    $this->app_only_static_permissions,
                    Test_Result::SEVERITY_LOW, 
                    'and as a result premium features such as User Synchronization may not work unless you have granted User.Read.All as a delegated permission.', 
                    'https://docs.wpo365.com/article/28-permissions-needed-by-the-plugin-by-workload' );
            }

            public function test_app_only_access_token_static_permissions_group_read_all() {

                if ( !Options_Service::get_global_boolean_var( 'use_app_only_token' ) ) {
                    return;
                }

                return $this->check_static_permission( 
                    $this->app_only_access_token,
                    'Group.Read.All', 
                    'application',
                    $this->app_only_static_permissions,
                    Test_Result::SEVERITY_LOW, 
                    'and as a result premium features such as User Synchronization may not work unless you have granted Group.Read.All as a delegated permission.', 
                    'https://docs.wpo365.com/article/28-permissions-needed-by-the-plugin-by-workload' );
            }

            private function check_static_permission( $access_token, $permission, $permission_type, $static_permissions, $severity, $additional_message = '', $more_info = 'https://docs.wpo365.com/article/23-integration' ) {
                
                if ( stripos( $permission_type, 'delegated' ) === 0 && Options_Service::get_global_boolean_var( 'use_saml' ) ) {
                    return;
                }

                $test_result = new Test_Result( "Static $permission_type permission (scope) '$permission' has been configured", 'Access Tokens', $severity );
                $test_result->passed = true;
                
                if ( empty( $access_token ) ) {
                    $test_result->passed = false;
                    $test_result->message = "Could not fetch $permission_type access token -> test skipped";
                    $test_result->more_info = '';
                    return $test_result;
                }

                if ( empty( $static_permissions ) ) {
                    $test_result->passed = false;
                    $test_result->message = "Could not determine static permissions of the current $permission_type access token -> test skipped";
                    $test_result->more_info = '';
                    return $test_result;
                }

                if ( !in_array( $permission, $static_permissions ) ) {
                    $test_result->passed = false;
                    $test_result->message = "Static permission '$permission' is not configured for current $permission_type access token $additional_message";
                    $test_result->more_info = $more_info;
                }

                return $test_result;
            }
        }
    }