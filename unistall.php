<?php

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_transient( SETTING_VALIDATION_TRANSIENT );

global $wpdb;

$query  = "DROP TABLE `wp_transact_transactions`;";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($query);
