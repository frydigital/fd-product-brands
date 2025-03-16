<?php
/*
Plugin Name: WooCommerce Product Brand
Plugin URI: https://frydigital.com/plugins/fd-product-brands/
Description: A WooCommerce plugin that adds a custom taxonomy 'product_brand' to products and allows the attachment of images to the product_brand. Each image links to a search result for that taxonomy.
Version: 1.1
Author: Fry Digital
Author URI: https://frydigital.com/
License: GPLv3
*/


require_once(plugin_dir_path(__FILE__) . 'includes/class-product-brand.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-product-brand-meta.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-product-brand-shortcode.php');


function my_plugin_activation_hook()
{
  if (! is_plugin_active('woocommerce/woocommerce.php') || version_compare(get_plugin_data(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php')['Version'], '9.4', '<')) {
    deactivate_plugins(plugin_basename(__FILE__));
    wp_die('Sorry, but this plugin requires WooCommerce to be installed and activated.');
  }
}

register_activation_hook(__FILE__, 'my_plugin_activation_hook');

// Define the function that registers the plugin
function my_product_brand_plugin()
{
  new WooCommerce_Product_Brand();
  new WooCommerce_Product_Brand_Shortcode();
}
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
  'https://github.com/arbutusroutes/fd-product-brands/',
  __FILE__,
  'fd-product-brands'
);
//Github Releases
$myUpdateChecker->getVcsApi()->enableReleaseAssets();

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');
