<?php

    namespace Wpo\Tests;

    use \Wpo\Services\Id_Token_Service;
    use \Wpo\Services\Request_Service;

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    if ( !class_exists( '\Wpo\Tests\Test_OpenId_Connect' ) ) {

        class Test_OpenId_Connect {

            private $id_token = null;

            public function test_decode_id_token() {
                delete_site_option( 'wpo365_msft_keys' );

                $test_result = new Test_Result( 'Can decode the ID token <span style="color: rgb(0, 120, 212)">click here to view</span>', 'OpenID Connect', Test_Result::SEVERITY_BLOCKING );
                $test_result->passed = true;

                $this->id_token = Id_Token_Service::decode_id_token();

                if ( empty( $this->id_token ) ) {
                    $test_result->passed = false;
                    $test_result->message = 'Could not decode the ID token. Please check the <a href="#debug">debug log</a> for errors.';
                    $test_result->more_info = '';
                }
                else {
                    $test_result->data = $this->id_token;

                    $request_service = Request_Service::get_instance();
                    $request = $request_service->get_request( $GLOBALS[ 'WPO_CONFIG' ][ 'request_id' ] );

                    // Store the Authorization Code in the Request
                    if ( isset( $_POST[ 'code' ] ) ) {
                        // Session valid until
                        $authorization_code = new \stdClass();
                        $authorization_code->expiry = time() + 3480;
                        $authorization_code->code = $_POST[ 'code' ];

                        $request->set_item( 'authorization_code', $authorization_code );
                        unset( $_POST[ 'code' ] );
                    }

                    $request->set_item( 'id_token', $this->id_token );
                }

                return $test_result;
            }

            public function test_id_token_contains_email() {
                $test_result = new Test_Result( 'ID token contains email address', 'OpenID Connect', Test_Result::SEVERITY_BLOCKING );
                $test_result->passed = true;

                if ( empty( $this->id_token ) ) {
                    $test_result->passed = false;
                    $test_result->message = 'ID token missing -> test skipped';
                    $test_result->more_info = '';
                }
                elseif ( empty( $this->id_token->email ) ) {
                    $test_result->passed = false;
                    $test_result->message = "ID token does not contain email address. Please ensure that the user has a valid email address. If this is the case then please consult the online documentation and update the (Azure AD) App registration's manifest to include the optional 'email' claim.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/22-sso';
                }

                return $test_result;
            }

            public function test_id_token_contains_upn() {
                $test_result = new Test_Result( 'ID token contains user principal name', 'OpenID Connect', Test_Result::SEVERITY_CRITICAL );
                $test_result->passed = true;

                if ( empty( $this->id_token ) ) {
                    $test_result->passed = false;
                    $test_result->message = 'ID token missing -> test skipped';
                    $test_result->more_info = '';
                }
                elseif ( empty( $this->id_token->upn ) ) {
                    $test_result->passed = false;
                    $test_result->message = "ID token does not contain user principal name (upn). Please consult the online documentation and update the (Azure AD) App registration's manifest to include the optional 'upn' claim.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/22-sso';
                }

                return $test_result;
            }

            public function test_id_token_contains_given_name() {
                $test_result = new Test_Result( 'ID token contains first name', 'OpenID Connect', Test_Result::SEVERITY_CRITICAL );
                $test_result->passed = true;

                if ( empty( $this->id_token ) ) {
                    $test_result->passed = false;
                    $test_result->message = 'ID token missing -> test skipped';
                    $test_result->more_info = '';
                }
                elseif ( empty( $this->id_token->given_name ) ) {
                    $test_result->passed = false;
                    $test_result->message = "ID token does not contain first name (given_name). Please consult the online documentation and update the (Azure AD) App registration's manifest to include the optional 'given_name' claim.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/22-sso';
                }

                return $test_result;
            }

            public function test_id_token_contains_family_name() {
                $test_result = new Test_Result( 'ID token contains last name', 'OpenID Connect', Test_Result::SEVERITY_CRITICAL );
                $test_result->passed = true;

                if ( empty( $this->id_token ) ) {
                    $test_result->passed = false;
                    $test_result->message = 'ID token missing -> test skipped';
                    $test_result->more_info = '';
                }
                elseif ( empty( $this->id_token->family_name ) ) {
                    $test_result->passed = false;
                    $test_result->message = "ID token does not contain last name (family_name). Please consult the online documentation and update the (Azure AD) App registration's manifest to include the optional 'family_name' claim.";
                    $test_result->more_info = 'https://docs.wpo365.com/article/22-sso';
                }

                return $test_result;
            }

            
        }
    }