<?php
/*
Plugin Name: Product Brands Plugin
Plugin URI: https://example.com/
Description: A plugin that adds product brands taxonomy with images and shortcode to display the most used brands.
Version: 1.0
Author: Your Name
Author URI: https://example.com/
*/

// Register the custom taxonomy for product brands
function pbp_register_product_brand_taxonomy() {
    $labels = array(
        'name'                       => __( 'Product Brands', 'textdomain' ),
        'singular_name'              => __( 'Product Brand', 'textdomain' ),
        'menu_name'                  => __( 'Product Brands', 'textdomain' ),
        'all_items'                  => __( 'All Product Brands', 'textdomain' ),
        'parent_item'                => __( 'Parent Product Brand', 'textdomain' ),
        'parent_item_colon'          => __( 'Parent Product Brand:', 'textdomain' ),
        'new_item_name'              => __( 'New Product Brand Name', 'textdomain' ),
        'add_new_item'               => __( 'Add New Product Brand', 'textdomain' ),
        'edit_item'                  => __( 'Edit Product Brand', 'textdomain' ),
        'update_item'                => __( 'Update Product Brand', 'textdomain' ),
        'view_item'                  => __( 'View Product Brand', 'textdomain' ),
        'separate_items_with_commas' => __( 'Separate product brands with commas', 'textdomain' ),
        'add_or_remove_items'        => __( 'Add or remove product brands', 'textdomain' ),
        'choose_from_most_used'      => __( 'Choose from the most used product brands', 'textdomain' ),
        'popular_items'              => __( 'Popular Product Brands', 'textdomain' ),
        'search_items'               => __( 'Search Product Brands', 'textdomain' ),
        'not_found'                  => __( 'Not Found', 'textdomain' ),
        'no_terms'                   => __( 'No product brands', 'textdomain' ),
        'items_list'                 => __( 'Product Brands list', 'textdomain' ),
        'items_list_navigation'      => __( 'Product Brands list navigation', 'textdomain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'query_var'                  => true,
        'rewrite'                    => array( 'slug' => 'product_brands' ),
    );
    register_taxonomy( 'product_brands', array( 'product' ), $args );
}
add_action( 'init', 'pbp_register_product_brand_taxonomy' );

// Add an image field to the product brand
function pbp_add_product_brand_image_field() {
    add_meta_box(
        'product_brand_image',
        'Product Brand Image',
        'pbp_product_brand_image_field_callback',
        'product_brands',
        'side'
    );
}
add_action( 'add_meta_boxes', 'pbp_add_product_brand_image_field' );

function pbp_product_brand_image_field_callback( $term ) {
    $image_id = get_term_meta( $term->term_id, 'product_brand_image_id', true
    function pbp_product_brand_image_field_callback( $term ) {
        $image_id = get_term_meta( $term->term_id, 'product_brand_image_id', true );
        $image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
        ?>
        <div class="form-field">
            <label for="product_brand_image"><?php _e( 'Product Brand Image', 'textdomain' ); ?></label>
            <input type="hidden" name="product_brand_image_id" id="product_brand_image_id" value="<?php echo esc_attr( $image_id ); ?>">
            <div id="product_brand_image_preview">
                <?php if ( $image_url ) : ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>">
                <?php endif; ?>
            </div>
            <button type="button" class="button" id="product_brand_image_button"><?php _e( 'Upload/Add Image', 'textdomain' ); ?></button>
            <button type="button" class="button" id="product_brand_image_remove_button"><?php _e( 'Remove Image', 'textdomain' ); ?></button>
            <script>
                jQuery(function($) {
                    var frame,
                        imageIDInput = $('#product_brand_image_id'),
                        preview = $('#product_brand_image_preview'),
                        button = $('#product_brand_image_button'),
                        removeButton = $('#product_brand_image_remove_button');
                    button.on('click', function() {
                        if (frame) {
                            frame.open();
                            return;
                        }
                        frame = wp.media({
                            title: 'Select or Upload Product Brand Image',
                            button: {
                                text: 'Use this Image',
                            },
                            multiple: false,
                        });
                        frame.on('select', function() {
                            var attachment = frame.state().get('selection').first().toJSON();
                            imageIDInput.val(attachment.id);
                            preview.html('<img src="' + attachment.sizes.thumbnail.url + '" alt="' + attachment.alt + '">');
                        });
                        frame.open();
                    });
                    removeButton.on('click', function() {
                        imageIDInput.val('');
                        preview.html('');
                    });
                });
            </script>
        </div>
        <?php
    }
    function pbp_most_used_brands_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'limit' => 10,
        ), $atts, 'pbp_most_used_brands' );
    
        $brands = get_terms( array(
            'taxonomy' => 'product_brands',
            'orderby' => 'count',
            'order' => 'DESC',
            'hide_empty' => true,
        ) );
    
        $sorted_brands = array();
    
        foreach ( $brands as $brand ) {
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_brands',
                        'field' => 'term_id',
                        'terms' => $brand->term_id,
                    ),
                ),
                'meta_query' => array(
                    array(
                        'key' => '_stock',
                        'compare' => '>',
                        'value' => '0',
                        'type' => 'NUMERIC',
                    ),
                ),
                'fields' => 'ids',
            );
    
            $products = get_posts( $args );
    
            $total_value = 0;
    
            foreach ( $products as $product_id ) {
                $product = wc_get_product( $product_id );
                $total_value += $product->get_stock_quantity() * $product->get_price();
            }
    
            $sorted_brands[$brand->name] = $total_value;
        }
    
        arsort( $sorted_brands );
    
        $output = '<ul>';
    
        $count = 0;
    
        foreach ( $sorted_brands as $brand_name => $total_value ) {
            $output .= '<li><a href="' . esc_url( get_term_link( $brand_name, 'product_brands' ) ) . '">' . $brand_name . '</a> (' . wc_price( $total_value ) . ')</li>';
    
            $count++;
    
            if ( $count == $atts['limit'] ) {
                break;
            }
        }
    
        $output .= '</ul>';
    
        return $output;
    }
    add_shortcode( 'pbp_most_used_brands', 'pbp_most_used_brands_shortcode' );
    
    // Add a weight field to the product_brands taxonomy
function pbp_add_weight_field_to_taxonomy( $args, $taxonomy ) {
    if ( $taxonomy === 'product_brands' ) {
        $args['meta_box_cb'] = 'pbp_product_brand_weight_meta_box';
    }
    return $args;
}
add_filter( 'register_taxonomy_args', 'pbp_add_weight_field_to_taxonomy', 10, 2 );

// Display the weight meta box
function pbp_product_brand_weight_meta_box() {
    $screens = array( 'product_brands' );

    foreach ( $screens as $screen ) {

        // Add the meta box
        add_meta_box(
            'pbp_product_brand_weight',
            __( 'Brand Weight', 'pbp' ),
            'pbp_product_brand_weight_callback',
            $screen,
            'side',
            'core'
        );
    }
}

// Callback function to display the weight field
function pbp_product_brand_weight_callback( $term ) {
    $weight = get_term_meta( $term->term_id, 'product_brand_weight', true );
    ?>
    <div class="form-field">
        <label for="product-brand-weight"><?php esc_html_e( 'Brand Weight', 'pbp' ); ?></label>
        <input type="number" step="0.01" name="product_brand_weight" id="product-brand-weight" value="<?php echo esc_attr( $weight ); ?>">
        <p class="description"><?php esc_html_e( 'Enter the brand weight here', 'pbp' ); ?></p>
    </div>
    <?php
}

// Save the weight value
function pbp_save_product_brand_weight( $term_id, $taxonomy ) {
    if ( isset( $_POST['product_brand_weight'] ) ) {
        $weight = floatval( $_POST['product_brand_weight'] );
        update_term_meta( $term_id, 'product_brand_weight', $weight );
    }
}
add_action( 'edited_product_brands', 'pbp_save_product_brand_weight', 10, 2 );
add_action( 'created_product_brands', 'pbp_save_product_brand_weight', 10, 2 );

function pbp_get_top_brands_by_weight() {
    $args = array(
        'taxonomy' => 'product_brands',
        'meta_query' => array(
            array(
                'key' => 'product_brand_weight',
                'type' => 'DECIMAL',
            ),
        ),
        'orderby' => array(
            'meta_value_num' => 'DESC',
            'name' => 'ASC',
        ),
        'number' => 10,
    );
    $brands = get_terms( $args );
    return $brands;
}
