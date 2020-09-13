<?php
    // Register new Navigations
    function register_menu() {
        register_nav_menu('main',__( 'Main' ));
    }
    add_action( 'init', 'register_menu' );

    // Add custom admin page
    function admin_news_page() {
		add_menu_page(
			__( 'Nachrichten', 'my-textdomain' ),
			__( 'Nachrichten', 'my-textdomain' ),
			'manage_options',
			'news',
			'news_page_contents',
			'dashicons-lightbulb',
			26
		);
	}

	add_action( 'admin_menu', 'admin_news_page' );

    function news_page_contents() {
        ?>
            <h1>Nachrichten</h1>
            <h3>Nachricht 1</h3>
            <input type="text" name="news0">
        <?php
    }
    /*
    function admin_news_menu() {
		add_menu_page(
            'Nachrichten',
            'Nachrichten',
            'manage_option',
            'news_page_contents',
            'dashicons-lightbulb',
            26
		);
	}

	add_action( 'admin_menu', 'admin_news_menu' );

    function news_page_contents() {
        ?>
            <h1>Nachrichten</h1>
        <?php
    }
    */
?>