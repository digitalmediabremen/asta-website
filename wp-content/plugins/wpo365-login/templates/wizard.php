<?php

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    use \Wpo\Services\Nonce_Service;

    global $wp_roles;

    $plugin_version = 'wpo365Login';

    if ( class_exists( '\Wpo\Plus' ) ) $plugin_version = 'wpo365LoginPlus';
    if ( class_exists( '\Wpo\Professional' ) ) $plugin_version = 'wpo365LoginProfessional';
    if ( class_exists( '\Wpo\Premium' ) ) $plugin_version = 'wpo365LoginPremium';
    if ( class_exists( '\Wpo\Intranet' ) ) $plugin_version = 'wpo365LoginIntranet';

    $itthinx_groups = class_exists( '\Wpo\Services\Mapped_Itthinx_Groups_Service' ) ? \Wpo\Services\Mapped_Itthinx_Groups_Service::get_groups_groups() : array();

    $props = array( 
        'siteUrl'           => get_site_url(), 
        'adminUrl'          => get_site_url( null, '/wp-admin' ),
        'nonce'             => Nonce_Service::get_nonce(),
        'pluginVersion'     => $plugin_version,
        'availableRoles'    => json_encode( $wp_roles->roles ),
        'availableGroups'   => json_encode( $itthinx_groups ),
    );
    
    ?>
    
        <!-- Main -->
        <div>
            <script src="<?php echo $GLOBALS[ 'WPO_CONFIG' ][ 'plugin_url' ] ?>apps/dist/wizard.js?cb=<?php echo $GLOBALS[ 'WPO_CONFIG' ][ 'version' ] ?>" 
                data-nonce="<?php echo wp_create_nonce( 'wpo365_fx_nonce' ) ?>"
                data-wpajaxadminurl="<?php echo admin_url() . 'admin-ajax.php' ?>"
                data-props="<?php echo htmlspecialchars( json_encode( $props ) ) ?>">
            </script>
            <!-- react root element will be added here -->
        </div>
        <script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if (e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>
        <script type="text/javascript">window.Beacon('init', 'ff044b87-9fe1-42f8-85d3-7b894533f00b')</script>