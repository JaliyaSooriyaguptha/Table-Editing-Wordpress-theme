<?php
// Add the shortcode
function gold_rate_display_shortcode() {
    ob_start();
    global $wpdb;
    $gold_rate = $wpdb->prefix . 'gold_rate';
    $results = $wpdb->get_results("SELECT * FROM $gold_rate");

    if (empty($results)) {
        return '<p>No gold rate details found.</p>';
    }

    echo '<style>
        .gold-rates {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .gold-rate-item {
            width: 200px;
            text-align: center;
            background: #fff;
            padding: 15px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .gold-rate-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
        }
        .gold-rate-item p {
            margin: 0;
            font-size: 16px;
        }
        .gold-rate-item .item-size {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .gold-rate-item .price {
            color: #999;
        }
    </style>';

    $default_image_url = plugins_url('path/to/default/image.jpg', __FILE__); 

    echo '<div class="gold-rates">';
    foreach ($results as $row) {
        $image_url = !empty($row->image_path) ? esc_url($row->image_path) : $default_image_url;
        echo '<div class="gold-rate-item">';
        echo '<img src="' . $image_url . '" alt="Gold Image">';
        echo '<p class="item-size">' . esc_html($row->item_size) . '</p>';
        echo '<p class="price">' . esc_html($row->price) . '</p>';
        echo '</div>';
    }
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('gold_rate_display', 'gold_rate_display_shortcode');
?>
