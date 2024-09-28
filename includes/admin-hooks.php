<?php

function silorider_admin_init() {
    silorider_register_settings();
    silorider_add_setting_fields();
}

// Register admin settings.
function silorider_register_settings() {
    // Microblogging settings
    register_setting(
        'silorider',
        'generate_micropost_titles',
        array(
            'type' => 'boolean',
            'description' => "Generate titles for microposts",
            'default' => true
        )
    );
    register_setting(
        'silorider',
        'micropost_title_word_count',
        array(
            'type' => 'integer',
            'description' => "The number of words to include in the title",
            'default' => 10
        )
    );
    register_setting(
        'silorider',
        'generate_micropost_slugs',
        array(
            'type' => 'boolean',
            'description' => "Generate date-based slugs for microposts",
            'default' => true
        )
    );
    register_setting(
        'silorider',
        'micropost_slug_format',
        array(
            'type' => 'string',
            'description' => "The data format for micropost slugs",
            'default' => 'His'
        )
    );

    // POSSE settings
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
}

function silorider_add_setting_fields() {
    // Microblogging settings.
    add_settings_section(
        'silorider_settings_microblogging',
        __( "Microblogging", 'silorider' ),
        '', // No custom html to add for this section.
        'silorider'
    );

    add_settings_field(
        'silorider_generate_micropost_titles',
        __( 'Generate Micropost Titles', 'silorider' ),
        'silorider_echo_checkbox_setting_field',
        'silorider',
        'silorider_settings_microblogging',
        array( 'name' => 'generate_micropost_titles', 'label_for' => 'generate_micropost_titles' )
    );
    add_settings_field(
        'silorirder_micropost_title_word_count',
        __( 'Word Count to Generate Title', 'silorider' ),
        'silorider_echo_integer_setting_field',
        'silorider',
        'silorider_settings_microblogging',
        array( 'name' => 'micropost_title_word_count', 'label_for' => 'micropost_title_word_count', 'min' => 1 )
    );
    add_settings_field(
        'silorider_generate_micropost_slugs',
        __( 'Generate Micropost Slugs', 'silorider' ),
        'silorider_echo_checkbox_setting_field',
        'silorider',
        'silorider_settings_microblogging',
        array( 'name' => 'generate_micropost_slugs', 'label_for' => 'generate_micropost_slugs' )
    );
    add_settings_field(
        'silorider_micropost_slug_format',
        __( 'Micropost Slug Format', 'silorider' ),
        'silorider_echo_string_setting_field',
        'silorider',
        'silorider_settings_microblogging',
        array( 'name' => 'micropost_slug_format', 'label_for' => 'micropost_slug_format' )
    );

    // POSSE settings.
    add_settings_section(
        'silorider_settings_posse',
        __( "POSSE", 'silorider' ),
        '', // No custom html to add for this section.
        'silorider'
    );

    add_settings_field(
        'silorider_commandline',
        __( 'Command Line', 'silorider' ),
        'silorider_echo_string_setting_field',
        'silorider',
        'silorider_settings_posse',
        array( 'name' => 'commandline', 'label_for' => 'commandline' )
    );
    add_settings_field(
        'silorider_workingdir',
        __( 'Working Directory', 'silorider' ),
        'silorider_echo_string_setting_field',
        'silorider',
        'silorider_settings_posse',
        array( 'name' => 'workingdir', 'label_for' => 'workingdir' )
    );
    add_settings_field(
        'silorider_logfile',
        __( 'Log File', 'silorider' ),
        'silorider_echo_string_setting_field',
        'silorider',
        'silorider_settings_posse',
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

function silorider_echo_integer_setting_field( $args ) {
    $name = $args['name'];
    $value = get_option( $name );
    $name_attr = esc_attr( $name );
    $html = '<input type="number" id="' . $name_attr . '" name="' . $name_attr . '"'
        . 'value="' . esc_attr( $value ) . '" ';
    if ( isset( $args['min'] ) ) $html .= ' min="' . $args['min'] . '"';
    if ( isset( $args['max'] ) ) $html .= ' max="' . $args['max'] . '"';
    $html .= '/>';
    echo $html;
}

function silorider_echo_checkbox_setting_field( $args ) {
    $name = $args['name'];
    $value = get_option( $name );
    $name_attr = esc_attr( $name );
    $html = '<input type="checkbox" id="' . $name_attr . '" name="' . $name_attr . '" '
        . ($value ? 'checked="true" ' : '') . '"/>';
    echo $html;
}

add_action( 'admin_init', 'silorider_admin_init' );

// Register admin panel option page.
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

    require_once( 'options-page.php' );
}

add_action( 'admin_menu', 'silorider_options_page' );

