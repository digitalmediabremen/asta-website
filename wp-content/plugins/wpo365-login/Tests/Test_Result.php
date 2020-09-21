<?php

    namespace Wpo\Tests;

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    if ( !class_exists( '\Wpo\Test\Test_Result' ) ) {

        class Test_Result {

            const SEVERITY_LOW       = 'low';
            const SEVERITY_CRITICAL  = 'critical';
            const SEVERITY_BLOCKING  = 'blocking';

            public $timestamp = null;

            public $category = null;

            public $severity = null;
            
            public $title = null;

            public $sequence = 0;

            public $passed = false;
            
            public $message = null;

            public $more_info = null;

            public $data = null;

            public function __construct( $title, $category = 'default', $severity = self::SEVERITY_LOW ) {
                $this->timestamp = date( "j F Y H:i:s", time() );
                $this->title = $title;
                $this->category = $category;
                $this->severity = $severity;
            }
        }
    }