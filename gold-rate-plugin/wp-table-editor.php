<?php

function gold_rate_save_details() {
    if (isset($_POST['action']) && $_POST['action'] === 'gold_rate_save_details') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gold_rate';

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploaded_file = $_FILES['image'];
            $upload = wp_handle_upload($uploaded_file, array('test_form' => false));
            if (isset($upload['file'])) {
                $image_path = $upload['file'];
                $image_url = $upload['url'];
            } else {
                wp_die('Failed to upload image.');
            }
        } else {
            wp_die('No image file uploaded.');
        }

        $item_size = sanitize_text_field($_POST['item_size']);
        $price = sanitize_text_field($_POST['price']);

        $wpdb->insert(
            $table_name,
            array(
                'image_path' => $image_path,
                'item_size' => $item_size,
                'price' => $price,
            )
        );

        wp_redirect(admin_url('admin.php?page=gold-rate-page'));
        exit;
    }
}
add_action('admin_post_gold_rate_save_details', 'gold_rate_save_details');
