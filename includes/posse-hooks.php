<?php

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

