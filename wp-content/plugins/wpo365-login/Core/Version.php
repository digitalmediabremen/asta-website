<?php

    namespace Wpo\Core;

    // prevent public access to this script
    defined( 'ABSPATH' ) or die();

	if ( !class_exists( '\Wpo\Core\Version' ) ) {

        class Version {

            public $current = '11.5';
            
    }
}