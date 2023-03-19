<?php
class Product_Brand_Admin extends WooCommerce_Product_Brand {
  
  public function __construct() {
    parent::__construct();
    add_action('admin_menu', array($this, 'add_admin_menu'));
  }

  public function add_admin_menu() {
    add_submenu_page(
      'woocommerce',
      'Product Brand Settings',
      'Product Brand Settings',
      'manage_options',
      'product-brands',
      array($this, 'product_brands_page')
    );
  }

  public function product_brands_page() {
    // Handle form submissions here
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $orderby = sanitize_text_field($_POST['orderby']);
      $order = sanitize_text_field($_POST['order']);
      update_option('product_brand_list_orderby', $orderby);
      update_option('product_brand_list_order', $order);
    }
    
    // Display product brands list and configuration form
    $orderby = get_option('product_brand_list_orderby', 'product_brand_weight');
    $order = get_option('product_brand_list_order', 'asc');
    // ...
  }
}
