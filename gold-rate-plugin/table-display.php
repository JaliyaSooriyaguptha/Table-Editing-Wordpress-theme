<?php

function gold_rate_display_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gold_rate';
    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    if (!empty($results)) {
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row['id']) . '</td>';
            echo '<td>' . esc_html($row['item_size']) . '</td>';
            echo '<td>' . esc_html($row['price']) . '</td>';
            echo '<td><img src="' . esc_url($row['image_url']) . '" width="100" height="100"></td>';
            echo '<td><button class="edit-button" data-id="' . esc_attr($row['id']) . '">Edit</button> <button class="delete-button" data-id="' . esc_attr($row['id']) . '">Delete</button></td>';
            echo '</tr>';
        }
    }
}
