<?php

class WooCommerce_Product_Brand
{
  public function __construct()
  {
    add_action('product_brand_add_form_fields', array($this, 'add_display_field'));
    add_action('product_brand_edit_form_fields', array($this, 'edit_display_field'));
    add_action('edited_product_brand', array($this, 'save_display_field'));
    add_action('created_product_brand', array($this, 'save_display_field'));
    add_filter('manage_edit-product_brand_columns', array($this, 'add_display_column'));
    add_filter('manage_product_brand_custom_column', array($this, 'display_column_content'), 10, 3);
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    add_action('wp_ajax_toggle_display', array($this, 'toggle_display'));
    add_filter('manage_edit-product_brand_columns', array($this, 'product_brand_weight'));
    add_filter('manage_product_brand_custom_column', array($this, 'product_brand_column_content'), 10, 3);
    add_filter('manage_edit-product_brand_sortable_columns', array($this, 'product_brand_sortable_columns'));
    add_filter('request', array($this, 'product_brand_sort_request'));
  }

  public function add_display_field()
  {
    ?>
    <div class="form-field term-display-wrap">
      <label for="display"><?php _e('Display', 'text_domain'); ?></label>
      <input type="checkbox" name="display" id="display" value="1">
      <p class="description"><?php _e('Enable this to display the brand in the shortcode output.', 'text_domain'); ?></p>
    </div>
    <?php
  }

  public function edit_display_field($term)
  {
    $display = get_term_meta($term->term_id, 'display', true);
    ?>
    <tr class="form-field term-display-wrap">
      <th scope="row"><label for="display"><?php _e('Display', 'text_domain'); ?></label></th>
      <td>
        <input type="checkbox" name="display" id="display" value="1" <?php checked($display, '1'); ?>>
        <p class="description"><?php _e('Enable this to display the brand in the shortcode output.', 'text_domain'); ?></p>
      </td>
    </tr>
    <?php
  }

  public function save_display_field($term_id)
  {
    $display = isset($_POST['display']) ? '1' : '';
    update_term_meta($term_id, 'display', $display);
  }

  public function add_display_column($columns)
  {
    $columns['display'] = __('Display', 'text_domain');
    return $columns;
  }

  public function display_column_content($content, $column_name, $term_id)
  {
    if ($column_name === 'display') {
      $display = get_term_meta($term_id, 'display', true);
      $checked = $display ? 'checked' : '';
      $content = '<input type="checkbox" class="toggle-display" data-term-id="' . $term_id . '" ' . $checked . '>';
    }
    return $content;
  }

  public function enqueue_admin_scripts($hook)
  {
    if ($hook === 'edit-tags.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'product_brand') {
      wp_enqueue_script('toggle-display', plugin_dir_url(__DIR__) . 'public/js/toggle-display.js', array('jquery'), null, true);
      wp_localize_script('toggle-display', 'toggleDisplay', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('toggle_display_nonce')
      ));
    }
  }

  public function toggle_display()
  {
    check_ajax_referer('toggle_display_nonce', 'nonce');

    $term_id = intval($_POST['term_id']);
    $display = get_term_meta($term_id, 'display', true) ? '' : '1';
    update_term_meta($term_id, 'display', $display);

    wp_send_json_success(array('display' => $display));
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
