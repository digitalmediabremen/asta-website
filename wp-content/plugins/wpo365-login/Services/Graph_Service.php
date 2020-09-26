<?php
    
    namespace Wpo\Services;

    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Access_Token_Service;
    use \Wpo\Services\Options_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Graph_Service' ) ) {
    
        class Graph_Service {

            const REST_API = "https://graph.microsoft.com/";
            const GRAPH_VERSION = "v1.0";
            const GRAPH_VERSION_BETA = "beta";

            /**
             * Connects to Microsoft Graph REST api to get retrieve data on the basis of the query presented
             *
             * @since 0.1
             *
             * @param   string  $query  query part of the Graph query e.g. '/me/photo/$'
             * @param   string  $method HTTP Method (default GET)
             * @param   boolean $binary Get binary data e.g. when getting user profile image
             * @param   array   $headers
             * @param   boolean $use_delegated
             * @param   boolean $prefetch Is deprecated since 11.0 when the method will figure out what it can use to obtain a delegated token
             * @param   string  $post_fields
             * @param   string  $scope
             * @return  mixed(object|WP_Error) JSON string as PHP object or false
             *
             */
            public static function fetch( $query, $method = 'GET', $binary = false, $headers = array(), $use_delegated = false, $prefetch = false, $post_fields = "", $scope = 'https://graph.microsoft.com/user.read' ) {

                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Requesting data from Microsoft Graph using query: ' . $query );

                /**
                 * @since v10 it is possible to request data from Microsoft Graph
                 * using an app-only context.
                 */

                if ( !$use_delegated && Options_Service::get_global_boolean_var( 'use_app_only_token' ) ) {
                    $access_token = Access_Token_Service::get_app_only_access_token();
                }
                else {
                    $access_token = Access_Token_Service::get_access_token( $scope );
                }
                
                if ( is_wp_error( $access_token ) ) {
                    $warning = 'Could not retrieve an access token for (scope|query) ' . $scope . '|' . $query . '.  Error details: ' . $access_token->get_error_message();
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> ' . $warning );
                    return new \WP_Error( $access_token->get_error_code(), $warning );
                }

                $bearer = 'Authorization: Bearer ' . $access_token->access_token;
                $headers[] = $bearer;
                
                $graph_version = Options_Service::get_global_string_var( 'graph_version' );
                $graph_version = empty( $graph_version) || $graph_version == 'current' 
                    ? self::GRAPH_VERSION 
                    : ( $graph_version == 'beta' ? self::GRAPH_VERSION_BETA : self::GRAPH_VERSION );

                $url = self::REST_API . $graph_version . $query;

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url );
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );

                if ( $method == 'POST' )
                    curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_fields );

                if ( $binary )
                    curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);

                if ( Options_Service::get_global_boolean_var( 'skip_host_verification' ) ) {
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); 
                }

                if ( !empty( $curl_proxy = Options_Service::get_global_string_var( 'curl_proxy' ) ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Using curl proxy ' . $curl_proxy );
                    curl_setopt( $curl, CURLOPT_PROXY, $curl_proxy );
                }

                $raw = curl_exec( $curl );
                $curl_response_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

                if ( curl_error( $curl ) ) {
                    $warning = 'Error occured whilst fetching from Microsoft Graph: ' . curl_error( $curl );
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> ' . $warning );
                    curl_close( $curl );
                    return new \WP_Error( '7020', $warning );
                }

                curl_close( $curl );

                if ( !$binary) {
                    $raw = json_decode( $raw, true );
                }

                return array( 'payload' => $raw, 'response_code' => $curl_response_code );
            }

            /**
             * Quick test to see if the result fetched from Microsoft Graph is valid.
             * 
             * @since 7.17
             * 
             * @param $fetch_result mixed(array|wp_error)
             * 
             * @return bool True if valid otherwise false
             */
            public static function is_fetch_result_ok( $fetch_result, $message, $level = 'ERROR' ) {
                
                if ( is_wp_error( $fetch_result ) ) {
                    Log_Service::write_log( $level, __METHOD__ . ' -> ' . $message . ' [Error: ' . $fetch_result->get_error_message() . ']' );
                    return false;
                }

                if ( $fetch_result[ 'response_code' ] != 200 ) {

                    if ( is_array( $fetch_result ) && isset( $fetch_result[ 'payload' ] ) && isset( $fetch_result[ 'payload' ][ 'error' ] ) && isset( $fetch_result[ 'payload' ][ 'error' ][ 'message' ] ) ) {
                        Log_Service::write_log( $level, __METHOD__ . ' -> ' . $message . ' [Error: ' . $fetch_result[ 'payload' ][ 'error' ][ 'message' ] . ']' );
                        return false;
                    }

                    Log_Service::write_log( $level, __METHOD__ . ' -> ' . $message . ' [See log for details]' );
                    Log_Service::write_log( 'WARN', $fetch_result );
                    return false;
                }

                return true;
            }
        }
    }