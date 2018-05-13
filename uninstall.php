<?php

    // if uninstall.php is not called by WordPress, die
    if ( ! defined('WP_UNINSTALL_PLUGIN' ) ) {
        die;
    }

    // drop table
    if ( false == get_option( 'acfedu_preserve_settings' ) ) {
	    global $wpdb;
	    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}faculty");

	    $target_folder = wp_upload_dir()['basedir'] . '/acfedu';
	    rmdir( $target_folder );
    }

?>
