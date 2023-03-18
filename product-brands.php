<?php
/*
Plugin Name: WooCommerce Product Brand
Description: A WooCommerce plugin that adds a custom taxonomy 'product_brand' to products and allows the attachment of images to the product_brand. Each image links to a search result for that taxonomy.
Version: 1.0.1
Author: Fry Digital
*/

// Define the class that extends WooCommerce_Product_Brand
require_once(plugin_dir_path(__FILE__) . 'includes/class-product-brand.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-product-brand-shortcode.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-product-brand-admin.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-product-brand-meta.php');


// Define the function that registers the plugin
function my_product_brand_plugin() {
  // Create an instance of the My_Product_Brand class
  new WooCommerce_Product_Brand();
  new Product_Brand_Meta();
  new WooCommerce_Product_Brand_Shortcode();
  new Product_Brand_Admin();
}

// Hook the my_product_brand_plugin function to the plugins_loaded hook
add_action('plugins_loaded', 'my_product_brand_plugin');




