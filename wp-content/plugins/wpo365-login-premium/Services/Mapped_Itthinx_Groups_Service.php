<?php
    
    namespace Wpo\Services;

    use \Wpo\Services\Log_Service;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Services\Mapped_Itthinx_Groups_Service' ) ) {

        class Mapped_Itthinx_Groups_Service {

            /**
             * Helper to get all Groups if Groups plugin is installed.
             * 
             * @since 10.9
             * 
             * @return array Flattened groups tree, alphabetically sorted
             */
            public static function get_groups_groups() {
                $groups_groups = array();
                
                if ( class_exists( 'Groups_Utility' ) 
                    && method_exists( 'Groups_Utility', 'get_group_tree' ) 
                    && class_exists( 'Groups_Group' )
                    && method_exists( 'Groups_Group', 'read' ) ) {
                        $groups_groups_tree = \Groups_Utility::get_group_tree();
                        $get_groups = function( $group_id, $nodes, $callback ) use ( &$groups_groups ) {
                            $group_name = \Groups_Group::read( $group_id )->name;
                            $groups_groups[] = \Groups_Group::read( $group_id )->name;
                            
                            foreach ( $nodes as $key => $value ) {
                                $callback( $key, $value, $callback );
                            }
                        };

                        foreach ( $groups_groups_tree as $group_id => $nodes ) {
                            $get_groups( $group_id, $nodes, $get_groups );
                        }

                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> ' . \implode( ', ', $groups_groups ) );
                }
                else {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Groups plugin not installed' );
                }

                sort( $groups_groups );
                return $groups_groups;
            }

            /**
             * @since 11.0
             */
            public static function aad_group_x_itthinx_group( $wp_usr_id, $wpo_usr ) {

                if ( ( !empty( $aad_group_itthinx_group_settings = Options_Service::get_global_list_var( 'groups_x_groups_groups' ) ) ) && class_exists( 'Groups_Group' ) && method_exists( 'Groups_Group', 'read_by_name' ) ) {
                    
                    foreach ( $aad_group_itthinx_group_settings as $kv_pair ) {

                        if ( array_key_exists( $kv_pair[ 'key' ], $wpo_usr->groups ) ) {
                            $itthinx_group = $kv_pair[ 'value' ];
                            $group = \Groups_Group::read_by_name( $itthinx_group );
                            
                            if ( !$group ) {
                                Log_Service::write_log( 'ERROR', __METHOD__ . ' -> AAD Group mapping for itthinx group ' . $itthinx_group .' was found for user ' . $wpo_usr->preferred_username . ' but this group does not exist in WordPress' );
                                continue;
                            } else {
                                $group_id = $group->group_id;
                            }
                            
                            if ( $group_id ) {

                                if ( !\Groups_User_Group::read( $wp_usr_id, $group_id ) ) {
                                    \Groups_User_Group::create( 
                                        array(
                                            'user_id' => $wp_usr_id,
                                            'group_id' => $group_id
                                        ) 
                                    );
                                }

                                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> AAD Group mapping for itthinx group ' . $itthinx_group .' was found for user ' . $wpo_usr->preferred_username . ' and user has been added' );
                            }                                              
                        }
                    }
                } 
                else {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Itthinx group configuration not detected' );
                }
            }

            /**
             * @since 11.0
             */
            public static function custom_field_x_itthinx_group( $wp_usr_id, $wpo_usr ) {

                if ( !empty( $custom_field_group_settings = Options_Service::get_global_list_var( 'custom_fields_x_itthinx_groups' ) ) && class_exists( 'Groups_Group' ) && method_exists( 'Groups_Group', 'read_by_name' ) ) {
                    // Add new roles as per AD Group > WP role mapping
                    
                    foreach ( $custom_field_group_settings as $kv_pair ) {

                        $custom_field_setting = \explode( ':', $kv_pair[ 'key' ] );

                        if ( sizeof( $custom_field_setting ) !== 2 ) {
                            Log_Service::write_log( 'WARN', __METHOD__ . ' -> Custom field mapping for itthinx group is not correctly entered: ' . $custom_field_setting . ' and should be of the form propertyName:value e.g. department:Communications');
                            continue;
                        }

                        if ( !empty( $wpo_usr->graph_resource ) && \array_key_exists( $custom_field_setting[0], $wpo_usr->graph_resource ) && $wpo_usr->graph_resource[ $custom_field_setting[0] ] == $custom_field_setting[1] ) {
                            $itthinx_group_name = $kv_pair[ 'value' ];
                            $group = \Groups_Group::read_by_name( $itthinx_group_name );
                            
                            if ( !$group ) {
                                Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Custom field mapping for itthinx group ' . $itthinx_group_name .' was found for user ' . $wpo_usr->preferred_username . ' but this group does not exist in WordPress' );
                                continue;
                            } else {
                                $group_id = $group->group_id;
                            }
                            
                            if ( $group_id ) {

                                if ( !\Groups_User_Group::read( $wp_usr_id, $group_id ) ) {
                                    \Groups_User_Group::create( 
                                        array(
                                            'user_id' => $wp_usr_id,
                                            'group_id' => $group_id
                                        ) 
                                    );
                                }

                                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Custom field mapping for itthinx group ' . $itthinx_group_name .' was found for user ' . $wpo_usr->preferred_username . ' and user has been added' );
                            }                                              
                        }
                    }
                } 
                else {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Itthinx group configuration not detected' );
                }
            }
        }
    }
