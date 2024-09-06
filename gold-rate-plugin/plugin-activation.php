<?php

function gold_rate_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gold_rate';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        image_url text NOT NULL,
        item_size varchar(255) DEFAULT '' NOT NULL,
        price varchar(255) DEFAULT '' NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'gold_rate_activate');
