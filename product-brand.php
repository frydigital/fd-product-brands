<?php
/*
Plugin Name: Product Brands Plugin
Plugin URI: https://frydigital.com/plugins/product-brands
Description: A plugin that adds product brands taxonomy with images and shortcode to display the most used brands.
Version: 1.0.1
Author: Fry Digital
Author URI: https://frydigital.com/
*/


// Register the custom taxonomy
function pbp_register_product_brand_taxonomy()
{
    $labels = array(
        'name'                       => _x('Product Brands', 'taxonomy general name', 'pbp'),
        'singular_name'              => _x('Product Brand', 'taxonomy singular name', 'pbp'),
        'search_items'               => __('Search Product Brands', 'pbp'),
        'popular_items'              => __('Popular Product Brands', 'pbp'),
        'all_items'                  => __('All Product Brands', 'pbp'),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __('Edit Product Brand', 'pbp'),
        'update_item'                => __('Update Product Brand', 'pbp'),
        'add_new_item'               => __('Add New Product Brand', 'pbp'),
        'new_item_name'              => __('New Product Brand Name', 'pbp'),
        'separate_items_with_commas' => __('Separate product brands with commas', 'pbp'),
        'add_or_remove_items'        => __('Add or remove product brands', 'pbp'),
        'choose_from_most_used'      => __('Choose from the most used product brands', 'pbp'),
        'not_found'                  => __('No product brands found.', 'pbp'),
        'menu_name'                  => __('Product Brands', 'pbp'),
    );

    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array('slug' => 'product_brand'),
    );

    register_taxonomy('product_brand', array('product'), $args);
}
add_action('init', 'pbp_register_product_brand_taxonomy');

// Add custom field to the product_brand taxonomy
function pbp_add_product_brand_image_field()
{
?>
    <div class="form-field">
        <label for="product-brand-image"><?php esc_html_e('Brand Image', 'pbp'); ?></label>
        <input type="text" name="product_brand_image" id="product-brand-image" value="" class="pbp-media-upload">
        <button class="pbp-media-upload-button button"><?php esc_html_e('Select Image', 'pbp'); ?></button>
        <p class="description"><?php esc_html_e('Select an image for the product brand.', 'pbp'); ?></p>
    </div>
<?php
}
add_action('product_brand_add_form_fields', 'pbp_add_product_brand_image_field');

// Save custom field value
function pbp_save_product_brand_image_field($term_id, $taxonomy)
{
    if (isset($_POST['product_brand_image'])) {
        $image = $_POST['product_brand_image'];
        update_term_meta($term_id, 'product_brand_image', $image);
    }
}
add_action('edited_product_brand', 'pbp_save_product_brand_image_field', 10, 2);
add_action('created_product_brand', 'pbp_save_product_brand_image_field', 10, 2);

// Display custom field on the edit taxonomy page
function pbp_edit_product_brand_image_field($term)
{
    $image = get_term_meta($term->term_id, 'product_brand_image', true);
?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="product-brand-image"><?php esc_html_e('Brand Image', 'pbp'); ?></label></th>
        <td>
            <input type="text" name="product_brand_image" id="product-brand-image" value="<?php echo esc_attr($image); ?>" class="pbp-media-upload">
            <button class="pbp-media-upload-button button"><?php esc_html_e('Select Image', 'pbp'); ?></button>
            <p class="description"><?php esc_html_e('Select an image for the product brand.', 'pbp'); ?></p>
        </td>
    </tr>
    <?php
}
add_action('product_brand_edit_form_fields', 'pbp_edit_product_brand_image_field');

// Enqueue necessary scripts and styles
function pbp_enqueue_scripts()
{
    wp_enqueue_media();
    wp_enqueue_script('pbp-product-brand-image-upload', plugin_dir_url(__FILE__) . 'js/pbp-product-brand-image-upload.js', array('jquery'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'pbp_enqueue_scripts');

// Shortcode to display a list of product brands sorted by the total inventory value of the products they are associated with
function pbp_list_product_brands_sorted_by_inventory($atts)
{
    $product_brands = get_terms(array(
        'taxonomy' => 'product_brand',
        'hide_empty' => false,
    ));

    // Initialize an empty array to hold the inventory values
    $inventory_values = array();

    foreach ($product_brands as $product_brand) {
        // Get all products associated with this product brand
        $products = get_posts(array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_brand',
                    'field'    => 'term_id',
                    'terms'    => $product_brand->term_id,
                ),
            ),
        ));

        // Initialize a variable to hold the total inventory value for this product brand
        $total_inventory_value = 0;

        foreach ($products as $product) {
            // Get the product price and quantity
            $price    = get_post_meta($product->ID, '_price', true);
            $quantity = get_post_meta($product->ID, '_stock', true);

            // Calculate the product inventory value and add it to the total inventory value for this product brand
            $product_inventory_value = $price * $quantity;
            $total_inventory_value   += $product_inventory_value;
        }

        // Add the total inventory value to the array
        $inventory_values[$product_brand->term_id] = $total_inventory_value;
    }

    // Sort the product brands by inventory value in descending order
    arsort($inventory_values);

    // Initialize an empty string to hold the output
    $output = '';

    // Loop through the sorted product brands and add them to the output string
    foreach ($inventory_values as $term_id => $inventory_value) {
        $term       = get_term($term_id, 'product_brand');
        $term_image = get_term_meta($term_id, 'product_brand_image', true);

        $output .= '<div class="product-brand">';
        // Display the brand image if one exists
        if ($term_image) {
            $output .= '<div class="product-brand-image"><img src="' . esc_url($term_image) . '"></div>';
        }

        // Display the product brand name
        $output .= '<h3 class="product-brand-name">' . esc_html($term->name) . '</h3>';

        // Display the total inventory value for the product brand
        $output .= '<div class="product-brand-inventory-value">' . esc_html__('Inventory Value:', 'pbp') . ' ' . wc_price($inventory_value) . '</div>';

        // Close the product-brand div
        $output .= '</div>';
    }

    return $output;
}
add_shortcode('pbp_list_product_brands_sorted_by_inventory', 'pbp_list_product_brands_sorted_by_inventory');

// Add a custom column for the product_brand weight
function pbp_product_brand_weight_column($columns)
{
    $columns['product_brand_weight'] = esc_html__('Weight', 'pbp');
    return $columns;
}
add_filter('manage_edit-product_brand_columns', 'pbp_product_brand_weight_column');

// Add the weight value to the custom column
function pbp_product_brand_weight_column_value($content, $column_name, $term_id)
{
    if ($column_name === 'product_brand_weight') {
        $weight = get_term_meta($term_id, 'product_brand_weight', true);
        $content .= '<div class="product-brand-weight">' . esc_html($weight) . '</div>';
    }
    return $content;
}
add_filter('manage_product_brand_custom_column', 'pbp_product_brand_weight_column_value', 10, 3);

// Make the custom column sortable by weight
function pbp_product_brand_weight_column_sortable($columns)
{
    $columns['product_brand_weight'] = 'product_brand_weight';
    return $columns;
}
add_filter('manage_edit-product_brand_sortable_columns', 'pbp_product_brand_weight_column_sortable');

// Modify the product_brand query to allow sorting by weight
function pbp_product_brand_query($query)
{
    if (!is_admin()) {
        return;
    }
    $orderby = $query->get('orderby');

    if ($orderby === 'product_brand_weight') {
        $query->set('meta_key', 'product_brand_weight');
        $query->set('orderby', 'meta_value_num');
    }
    // Add a weight field to the product_brand term
    function pbp_product_brand_weight_field()
    {
        $taxonomy = 'product_brand';
    ?>
        <div class="form-field term-group">
            <label for="product_brand_weight"><?php esc_html_e('Weight', 'pbp'); ?></label>
            <input type="number" name="product_brand_weight" id="product_brand_weight" step="0.01" value="">
            <p class="description"><?php esc_html_e('Enter the weight for this product brand. This can be used to sort the product brands by weight in the product brand list.', 'pbp'); ?></p>
        </div>
    <?php
    }
    add_action('product_brand_add_form_fields', 'pbp_product_brand_weight_field');

    // Save the weight field value
    function pbp_save_product_brand_weight_field($term_id)
    {
        if (isset($_POST['product_brand_weight'])) {
            update_term_meta($term_id, 'product_brand_weight', sanitize_text_field($_POST['product_brand_weight']));
        }
    }
    add_action('created_product_brand', 'pbp_save_product_brand_weight_field');
    add_action('edited_product_brand', 'pbp_save_product_brand_weight_field');

    // Modify the product_brand list table query to include the weight meta field
    function pbp_product_brand_list_table_query($query)
    {
        if ($query->query['taxonomy'] === 'product_brand' && !empty($_GET['orderby']) && $_GET['orderby'] === 'product_brand_weight') {
            $query->query_vars['meta_key'] = 'product_brand_weight';
            $query->query_vars['orderby'] = 'meta_value_num';
        }
    }
    add_action('pre_get_terms', 'pbp_product_brand_list_table_query', 10, 1);

    // Display the weight field value in the product_brand list table
    function pbp_product_brand_list_table_columns($columns)
    {
        $columns['product_brand_weight'] = esc_html__('Weight', 'pbp');
        return $columns;
    }
    add_filter('manage_edit-product_brand_columns', 'pbp_product_brand_list_table_columns');

    function pbp_product_brand_list_table_column_content($content, $column_name, $term_id)
    {
        if ($column_name === 'product_brand_weight') {
            $weight = get_term_meta($term_id, 'product_brand_weight', true);
            if (!empty($weight)) {
                $content .= esc_html($weight);
            } else {
                $content .= '-';
            }
        }
        return $content;
    }
    add_filter('manage_product_brand_custom_column', 'pbp_product_brand_list_table_column_content', 10, 3);

    // Modify the product_brand list table columns to include a weight sorting link
    function pbp_product_brand_list_table_sortable_columns($columns)
    {
        $columns['product_brand_weight'] = 'product_brand_weight';
        return $columns;
    }
    add_filter('manage_edit-product_brand_sortable_columns', 'pbp_product_brand_list_table_sortable_columns');

    // Modify the product_brand list table query to sort by weight if requested
    function pbp_product_brand_list_table_sorting($query)
    {
        if ($query->is_main_query() && $query->get('orderby') === 'product_brand_weight') {
            $query->set('meta_key', 'product_brand_weight');
            $query->set('orderby', 'meta_value_num');
        }
    }
    add_action('pre_get_posts', 'pbp_product_brand_list_table_sorting');

    // Add the inventory value to the product_brand object
    function pbp_add_product_brand_inventory_value($term)
    {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_brand',
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                ),
            ),
            'fields' => 'ids',
        );
        $products = get_posts($args);
        $total_inventory_value = 0;
        foreach ($products as $product_id) {
            $product = wc_get_product($product_id);
            $total_inventory_value += $product->get_stock_quantity() * $product->get_price();
        }
        $term->inventory_value = $total_inventory_value;
        return $term;
    }
    add_filter('get_term', 'pbp_add_product_brand_inventory_value', 10, 2);

    // Sort product brands by inventory value
    function pbp_sort_product_brands_by_inventory_value($terms)
    {
        usort($terms, function ($a, $b) {
            if ($a->inventory_value === $b->inventory_value) {
                return 0;
            }
            return ($a->inventory_value > $b->inventory_value) ? -1 : 1;
        });
        return $terms;
    }

    // Display the most used brands with a shortcode sorted by inventory value
    function pbp_most_used_product_brands_by_inventory_value_shortcode()
    {
        $args = array(
            'taxonomy' => 'product_brand',
            'orderby' => 'count',
            'order' => 'desc',
            'number' => 10,
        );
        $terms = get_terms($args);
        $terms = pbp_sort_product_brands_by_inventory_value($terms);
        ob_start();
    ?>
        <ul class="pbp-most-used-product-brands">
            <?php foreach ($terms as $term) : ?>
                <li>
                    <a href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a>
                    <span class="pbp-most-used-product-brands-inventory-value"><?php echo esc_html(wc_price($term->inventory_value)); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
<?php
        return ob_get_clean();
    }
    add_shortcode('pbp_most_used_product_brands_by_inventory_value', 'pbp_most_used_product_brands_by_inventory_value_shortcode');
}


// Add the plugin menu to the WooCommerce menu
add_action( 'admin_menu', 'pbp_add_admin_menu' );
function pbp_add_admin_menu() {
    add_submenu_page(
        'woocommerce',
        'Product Brands Report',
        'Product Brands Report',
        'manage_options',
        'product-brands-report',
        'pbp_render_admin_page'
    );
}

// Render the admin page
function pbp_render_admin_page() {
    ?>
    <div class="wrap">
        <h1>Product Brands Report</h1>
        <form method="get" action="">
            <label for="sort-by">Sort by:</label>
            <select name="sort-by" id="sort-by">
                <option value="value">Total Value</option>
                <option value="quantity">Total Quantity</option>
                <option value="weight">Weight</option>
            </select>
            <input type="submit" value="Sort" class="button">
        </form>
        <?php
        $sort_by = isset( $_GET['sort-by'] ) ? sanitize_text_field( $_GET['sort-by'] ) : 'value';
        $terms = get_terms( array(
            'taxonomy' => 'product_brand',
            'hide_empty' => false,
        ) );
        if ( ! empty( $terms ) ) {
            switch ( $sort_by ) {
                case 'value':
                    $terms = pbp_sort_product_brands_by_inventory_value( $terms );
                    break;
                case 'quantity':
                    $terms = pbp_sort_product_brands_by_inventory_quantity( $terms );
                    break;
                case 'weight':
                    $terms = pbp_sort_product_brands_by_weight( $terms );
                    break;
            }
            ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Product Brand</th>
                        <th>Total Value</th>
                        <th>Total Quantity</th>
                        <th>Weight</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $terms as $term ) : ?>
                        <tr>
                            <td><?php echo $term->name; ?></td>
                            <td><?php echo pbp_get_product_brand_inventory_value( $term ); ?></td>
                            <td><?php echo pbp_get_product_brand_inventory_quantity( $term ); ?></td>
                            <td><?php echo pbp_get_product_brand_weight( $term ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
        } else {
            echo 'No product brands found.';
        }
        ?>
    </div>
    <?php
}