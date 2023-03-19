<?php
/*
Plugin Name: WooCommerce Product Brand
Plugin URI: https://frydigital.com/plugins/fd-product-brands/
Description: A WooCommerce plugin that adds a custom taxonomy 'product_brand' to products and allows the attachment of images to the product_brand. Each image links to a search result for that taxonomy.
Version: 1.0.2
Author: Fry Digital
Author URI: https://frydigital.com/
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

/**
 * 
 * Plugin update checker
 * 
 * Check for Github version release and update
 * https://github.com/YahnisElsts/plugin-update-checker
 * 
 * @since	1.0.2
 */

 require 'plugin-update-checker/plugin-update-checker.php';
 use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
 
 $myUpdateChecker = PucFactory::buildUpdateChecker(
   'https://github.com/fryshaun/fd-product-brands/',
   __FILE__,
   'fd-product-brands'
 );
 //Github Releases
 $myUpdateChecker->getVcsApi()->enableReleaseAssets();
 
 //Set the branch that contains the stable release.
 $myUpdateChecker->setBranch('master');
 
 //Optional: If you're using a private repository, specify the access token like this:
 $myUpdateChecker->setAuthentication('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');


