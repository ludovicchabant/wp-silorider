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

// Run SiloRider on post publish.
function silorider_posse_on_publish( $new_status, $old_status, $post ) {
    if ( $new_status == 'publish' && $old_status != 'publish' && $post->post_type == 'post' ) {
        // Save the current working dir to restore it afterwards.
        $old_cwd = getcwd();
        $new_cwd = get_option( 'workingdir' );
        try {
            // If we have a custom working directory, change to it now.
            if ( $new_cwd !== '' ) {
                chdir( $new_cwd );
            }

            // Get the command line, or 'silorider' if it's not defined.
            $cmdline = get_option( 'commandline' );
            if ( $cmdline == '' ) {
                $cmdline = 'silorider';
            }

            // Add redirection for the logfile, if needed.
            $logfile = get_option( 'logfile' );
            if ( $logfile !== '' ) {
                $cmdline .= ' >> ' . $logfile . ' 2>&1';
            }

            // Run the command in the background.
            $cmdline .= ' &';

            // Do it!
            error_log( "Running SiloRider on publish: '" . $cmdline . "' (in '" . $new_cwd . "')" );
            shell_exec( $cmdline );
        } finally {
            chdir( $old_cwd );
        }
    }
}
add_action( 'transition_post_status', 'silorider_posse_on_publish', 10, 3 );

// Register admin settings.
function silorider_register_settings() {
    register_setting(
        'silorider',
        'commandline', 
        array(
            'type' => 'string',
            'description' => "The command line to run SiloRider",
            'default' => 'silorider'
        )
    );
    register_setting(
        'silorider',
        'workingdir',
        array(
            'type' => 'string',
            'description' => "The working directory for running SiloRider",
            'default' => ''
        )
    );
    register_setting(
        'silorider',
        'logfile',
        array(
            'type' => 'string',
            'description' => "The filename to capture the output into",
            'default' => ''
        )
    );

    add_settings_section(
        'silorider_settings_general',
        __( "General", 'silorider' ),
        '', // No custom html to add for this section.
        'silorider'
    );

    add_settings_field(
        'silorider_commandline',
        __( 'Command Line', 'silorider' ),
        'silorider_echo_string_setting_field',
        'silorider',
        'silorider_settings_general',
        array( 'name' => 'commandline', 'label_for' => 'commandline' )
    );
    add_settings_field(
        'silorider_workingdir',
        __( 'Working Directory', 'silorider' ),
        'silorider_echo_string_setting_field',
        'silorider',
        'silorider_settings_general',
        array( 'name' => 'workingdir', 'label_for' => 'workingdir' )
    );
    add_settings_field(
        'silorider_logfile',
        __( 'Log File', 'silorider' ),
        'silorider_echo_string_setting_field',
        'silorider',
        'silorider_settings_general',
        array( 'name' => 'logfile', 'label_for' => 'logfile' )
    );
}

function silorider_echo_string_setting_field( $args ) {
    $name = $args['name'];
    $value = get_option( $name );
    $name_attr = esc_attr( $name );
    $html = '<input type="text" id="' . $name_attr . '" name="' . $name_attr . '" '
        . 'value="' . esc_attr( $value ) . '"/>';
    echo $html;
}

add_action( 'admin_init', 'silorider_register_settings' );

// Reggister admin panel option page.
function silorider_options_page() {
    add_menu_page(
        'SiloRider',
        'SiloRider',
        'manage_options',
        'silorider',
        'silorider_options_page_html'
    );
}

function silorider_options_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Show important messages.
    if ( isset( $_GET['settings-updated'] ) ) {
        add_settings_error(
            'silorider',
            'update-confirmed',
            __( "Settings saved", 'silorider' ),
            'success'
        );
    }
    settings_errors( 'silorider' );

    require_once( SILORIDER_PATH . '/includes/options-page.php' );
}

add_action( 'admin_menu', 'silorider_options_page' );

function silorider_on_activate() {
}

function silorider_on_deactivate() {
}

register_activation_hook( __FILE__, 'silorider_on_activate' );
register_deactivation_hook( __FILE__, 'silorider_on_deactivate' );
