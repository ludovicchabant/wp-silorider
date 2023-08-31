<div class="wrap">
    <h1><?= esc_html( get_admin_page_title() ) ?></h1>
    <form method="post" action="<?= esc_url( self_admin_url( 'options.php' ) ) ?>" enctype="multipart/form-data">
        <?php 
            settings_fields( 'silorider' );
            do_settings_sections( 'silorider' );
            submit_button( __( "Save Settings", 'silorider' ) );
        ?>
    </form>
</div>
