<?php
/**
 * Plugin Name: SiloRider
 * Description: Runs SiloRider when a post is published
 * Version: 0.1
 * Author: Ludovic Chabant
 * Author URI: https://ludovic.chabant.com
 */

define( "SILORIDER_VERSION", "0.1" );
define( "SILORIDER_PATH", dirname(__FILE__) );

if ( ! defined( "ABSPATH" ) ) {
    exit;
}

require_once( SILORIDER_PATH . '/includes/admin-hooks.php' );
require_once( SILORIDER_PATH . '/includes/microformats-hooks.php' );
require_once( SILORIDER_PATH . '/includes/posse-hooks.php' );

function silorider_on_activate() { }
function silorider_on_deactivate() { }

register_activation_hook( __FILE__, 'silorider_on_activate' );
register_deactivation_hook( __FILE__, 'silorider_on_deactivate' );
