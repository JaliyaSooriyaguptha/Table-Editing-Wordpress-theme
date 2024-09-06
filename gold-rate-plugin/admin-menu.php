<?php

function gold_rate_menu() {
    add_menu_page(
        'Gold Rate',
        'Gold Rate',
        'manage_options',
        'gold-rate-page',
        'gold_rate_page_content',
        'dashicons-money',
        6
    );
}
add_action('admin_menu', 'gold_rate_menu');

function gold_rate_page_content() {
    ?>
    <div class="wrap">
        <h1>Gold Rate Manager</h1>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="gold_rate_save_details">
            <input type="hidden" id="edit_id" name="edit_id" value="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="item_size">Item Size</label></th>
                    <td><input type="text" id="item_size" name="item_size" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="price">Price</label></th>
                    <td><input type="text" id="price" name="price" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="image">Upload Image</label></th>
                    <td>
                        <img id="current_image" src="" alt="Current Image" style="display:none; width: 100px; height: 100px;">
                        <input type="hidden" id="existing_image" name="existing_image" value="">
                        <input type="file" id="image" name="image" accept="image/*">
                    </td>
                </tr>
            </table>
            <?php submit_button('Save'); ?>
        </form>


        <h2>Gold Rates</h2>
        <p>Use this shortcode to display the gold rates on your website: <code>[gold_rate_display]</code></p>

        <table id="gold-rates-table" class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Size</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'gold_rate';
                $results = $wpdb->get_results("SELECT * FROM $table_name");
                foreach ($results as $row) {
                    echo '<tr>';
                    echo '<td>' . $row->id . '</td>';
                    echo '<td>' . $row->item_size . '</td>';
                    echo '<td>' . $row->price . '</td>';
                    echo '<td><img src="' . $row->image_path . '" alt="Image" width="50" height="50"></td>';
                    echo '<td>
                            <a href="#" class="edit-rate" data-id="' . $row->id . '">Edit</a> |
                            <a href="#" class="delete-rate" data-id="' . $row->id . '">Delete</a>
                          </td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('.delete-rate').on('click', function(e) {
                e.preventDefault();
                var rateId = $(this).data('id');
                var row = $(this).closest('tr');

                if (confirm('Are you sure you want to delete this rate?')) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'delete_gold_rate',
                            id: rateId
                        },
                        success: function(response) {
                            if (response == 'success') {
                                row.fadeOut('slow', function() {
                                    $(this).remove();
                                });
                            } else {
                                alert('Failed to delete the rate.');
                            }
                        }
                    });
                }
            });

            $('.edit-rate').on('click', function(e) {
                e.preventDefault();
                var rateId = $(this).data('id');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_gold_rate',
                        id: rateId
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        $('#edit_id').val(data.id);
                        $('#item_size').val(data.item_size);
                        $('#price').val(data.price);
                        $('#existing_image').val(data.image_path);
                        if (data.image_path) {
                            $('#current_image').attr('src', data.image_path).show();
                        } else {
                            $('#current_image').hide();
                        }
                    }
                });
            });

        });
    </script>
    <?php
}

add_action('wp_ajax_delete_gold_rate', 'delete_gold_rate_callback');

function delete_gold_rate_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gold_rate';
    $rate_id = intval($_POST['id']);

    if ($wpdb->delete($table_name, array('id' => $rate_id))) {
        echo 'success';
    } else {
        echo 'error';
    }

    wp_die();
}

add_action('wp_ajax_get_gold_rate', 'get_gold_rate_callback');
function get_gold_rate_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gold_rate';
    $rate_id = intval($_POST['id']);
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $rate_id));
    
    if ($row) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'No data found']);
    }

    wp_die();
}

add_action('admin_post_gold_rate_save_details', 'gold_rate_save_details_callback');
function gold_rate_save_details_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gold_rate';

    $id = intval($_POST['edit_id']);
    $item_size = sanitize_text_field($_POST['item_size']);
    $price = sanitize_text_field($_POST['price']);
    $existing_image = sanitize_text_field($_POST['existing_image']);
    $image = $existing_image;

    if (!empty($_FILES['image']['name'])) {
        $uploaded_file = $_FILES['image'];
        $upload = wp_handle_upload($uploaded_file, ['test_form' => false]);

        if ($upload && !isset($upload['error'])) {
            $image = $upload['url'];
        }
    }

    if ($id > 0) {
        $wpdb->update(
            $table_name,
            [
                'item_size' => $item_size,
                'price' => $price,
                'image_path' => $image
            ],
            ['id' => $id],
            ['%s', '%s', '%s'],
            ['%d']
        );
    } else {
        $wpdb->insert(
            $table_name,
            [
                'item_size' => $item_size,
                'price' => $price,
                'image_path' => $image
            ],
            ['%s', '%s', '%s']
        );
    }

    wp_redirect(admin_url('admin.php?page=gold-rate-page'));
    exit();
}


?>
