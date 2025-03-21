<?php
/*
Plugin Name: WooCommerce Product Brand
Plugin URI: https://frydigital.com/plugins/fd-product-brands/
Description: A WooCommerce plugin that adds a custom taxonomy 'product_brand' to products and allows the attachment of images to the product_brand. Each image links to a search result for that taxonomy.
Version: 1.1.2
Author: Fry Digital
Author URI: https://frydigital.com/
License: GPLv3
*/

require_once(plugin_dir_path(__FILE__) . 'includes/class-product-brand.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-product-brand-shortcode.php');

function my_plugin_activation_hook()
{
  if (! is_plugin_active('woocommerce/woocommerce.php') || version_compare(get_plugin_data(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php')['Version'], '9.4', '<')) {
    deactivate_plugins(plugin_basename(__FILE__));
    wp_die('Sorry, but this plugin requires WooCommerce to be installed and activated.');
  }
}
register_activation_hook(__FILE__, 'my_plugin_activation_hook');

add_action('plugins_loaded', function () {
  if (class_exists('WC_Brands_Admin')) {
    remove_action('product_brand_edit_form_fields', array('WC_Brands_Admin', 'edit_thumbnail_field'), 10);
  }
}, 20);

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
