<?php 

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
