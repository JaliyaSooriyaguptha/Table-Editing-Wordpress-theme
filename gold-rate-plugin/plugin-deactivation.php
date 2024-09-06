<?php

function gold_rate_deactivate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gold_rate';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
register_deactivation_hook(__FILE__, 'gold_rate_deactivate');
