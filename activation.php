<?php

global $wpdb;

$table_name = $wpdb->prefix . 'transact_transactions';
$sql        = "CREATE TABLE IF NOT EXISTS {$table_name} (
          `post_id` bigint(20) NOT NULL,
          `sales_id` bigint(20) NOT NULL,
          `timestamp` bigint(20) NOT NULL,
          PRIMARY KEY (`sale_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
