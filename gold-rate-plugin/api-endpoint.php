<?php

add_action('rest_api_init', function () {
    // Endpoint for adding new gold rate details
    register_rest_route('daily-updater/v1', '/add', array(
        'methods' => 'POST',
        'callback' => 'gold_rate_add_item',
        'permission_callback' => '__return_true',
    ));
    
    // Endpoint for uploading images
    register_rest_route('daily-updater/v1', '/upload', array(
        'methods' => 'POST',
        'callback' => 'gold_rate_handle_image_upload',
        'permission_callback' => '__return_true',
    ));
    
    // Endpoint for fetching gold rate details
    register_rest_route('daily-updater/v1', '/details', array(
        'methods' => 'GET',
        'callback' => 'gold_rate_get_items',
        'permission_callback' => '__return_true',
    ));

    // Endpoint for updating gold rate details
    register_rest_route('daily-updater/v1', '/details/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'gold_rate_update_item',
        'permission_callback' => '__return_true',
    ));

    // Endpoint for deleting gold rate details
    register_rest_route('daily-updater/v1', '/details/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => 'gold_rate_delete_item',
        'permission_callback' => '__return_true',
    ));
});

function gold_rate_handle_image_upload(WP_REST_Request $request) {
    if (!isset($_FILES['file'])) {
        return new WP_Error('no_file', 'No file uploaded.', array('status' => 400));
    }

    $uploaded_file = $_FILES['file'];
    $upload = wp_handle_upload($uploaded_file, array('test_form' => false));

    if (isset($upload['file'])) {
        // Optionally, you can save the image URL or path in your database
        return array('message' => 'File uploaded successfully', 'image_url' => $upload['url']);
    } else {
        return new WP_Error('upload_error', 'Failed to upload file.', array('status' => 500));
    }
}


function gold_rate_add_item(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gold_rate';

    $item_size = sanitize_text_field($request->get_param('item_size'));
    $price = sanitize_text_field($request->get_param('price'));
    $image_path = sanitize_text_field($request->get_param('image_path'));

    $result = $wpdb->insert(
        $table_name,
        array(
            'image_path' => $image_path,
            'item_size' => $item_size,
            'price' => $price,
        )
    );

    if ($result === false) {
        return new WP_Error('db_insert_error', 'Failed to insert record into database.', array('status' => 500));
    }

    return array('message' => 'Item added successfully.');
}

function gold_rate_get_items(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gold_rate';

    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    return array('details' => $results);
}

function gold_rate_update_item(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gold_rate';
    $id = $request->get_param('id');

    $item_size = sanitize_text_field($request->get_param('item_size'));
    $price = sanitize_text_field($request->get_param('price'));
    $image_path = sanitize_text_field($request->get_param('image_path'));

    $result = $wpdb->update(
        $table_name,
        array(
            'image_path' => $image_path,
            'item_size' => $item_size,
            'price' => $price,
        ),
        array('id' => $id)
    );

    if ($result === false) {
        return new WP_Error('db_update_error', 'Failed to update record in database.', array('status' => 500));
    }

    return array('message' => 'Item updated successfully.');
}

function gold_rate_delete_item(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gold_rate';
    $id = $request->get_param('id');

    $result = $wpdb->delete($table_name, array('id' => $id));

    if ($result === false) {
        return new WP_Error('db_delete_error', 'Failed to delete record from database.', array('status' => 500));
    }

    return array('message' => 'Item deleted successfully.');
}
?>
