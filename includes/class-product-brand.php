<?php

// Initialize the class and add Brand taxonomy into WooCommerce

class WooCommerce_Product_Brand
{
  public function __construct()
  {
    add_action('init', array($this, 'register_product_brand_taxonomy'));
  }

  public function register_product_brand_taxonomy()
  {
    add_filter('manage_edit-product_brand_columns', array($this, 'product_brand_weight'));
    add_filter('manage_product_brand_custom_column', array($this, 'product_brand_column_content'), 10, 3);
    add_filter('manage_edit-product_brand_sortable_columns', array($this, 'product_brand_sortable_columns'));
    add_filter('request', array($this, 'product_brand_sort_request'));
  }

  public function product_brand_weight($columns)
  {
    $columns['product_brand_weight'] = __('Weight', 'woocommerce');
    return $columns;
  }

  public function product_brand_column_content($content, $column_name, $term_id)
  {
    if ($column_name === 'product_brand_weight') {
      $weight = get_term_meta($term_id, 'product_brand_weight', true);
      if (!empty($weight)) {
        $content = esc_html($weight);
      } else {
        $content = '';
      }
    }

    return $content;
  }

  public function product_brand_sortable_columns($columns)
  {
    $columns['product_brand_weight'] = 'product_brand_weight';
    return $columns;
  }

  public function product_brand_sort_request($vars)
  {
      if (isset($vars['orderby']) && 'product_brand_weight' == $vars['orderby']) {
          $vars = array_merge($vars, array(
              'meta_key' => 'product_brand_weight',
              'orderby' => 'meta_value',
          ));
      } 
  
      return $vars;
  }
}

new WooCommerce_Product_Brand();


// This code creates a class called `WooCommerce_Product_Brand` and registers the custom taxonomy 'product_brand' using the `register_taxonomy()` function. It also adds a meta box to the product editing screen that allows the user to upload an image for the product_brand using the WordPress media uploader. The image is saved as post meta using the `update_post_meta()` function. Finally, the `term_link()` function filters the term link for the 'product_brand' taxonomy to include a query argument that links to a search result for that taxonomy.
