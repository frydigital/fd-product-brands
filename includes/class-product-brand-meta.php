<?php
class Product_Brand_Meta extends WooCommerce_Product_Brand {
  
  public function __construct()
  {
      // Add custom fields to the term edit page
      add_action('product_brand_edit_form_fields', array($this, 'add_brand_fields'), 10, 2);

      // Save custom fields when the term is edited
      add_action('edited_product_brand', array($this, 'save_brand_fields'), 10, 2);
  }


  public function add_brand_fields($term, $taxonomy) {
    $brand_image_id = get_term_meta($term->term_id, 'brand_image_id', true);
    $brand_weight = get_term_meta($term->term_id, 'brand_weight', true);
    ?>
    <tr class="form-field">
      <th scope="row" valign="top">
        <label for="brand_image"><?php _e('Brand Image', 'my-text-domain'); ?></label>
      </th>
      <td>
        <?php
        // Display the brand image upload field
        echo '<div class="brand-image-wrapper">';
        if ($brand_image_id) {
          $brand_image = wp_get_attachment_image_src($brand_image_id, 'thumbnail');
          echo '<img src="' . esc_url($brand_image[0]) . '" alt="" style="max-width:100%;"/>';
        }
        echo '<input type="hidden" name="brand_image_id" id="brand_image_id" value="' . esc_attr($brand_image_id) . '" />';
        echo '<button class="upload-brand-image button">' . __('Upload/Add image', 'my-text-domain') . '</button>';
        echo '<button class="remove-brand-image button">' . __('Remove image', 'my-text-domain') . '</button>';
        echo '</div>';

        // Display the brand weight field
        echo '<br><br>';
        echo '<label for="brand_weight">' . __('Brand Weight', 'my-text-domain') . '</label>';
        echo '<input type="text" name="brand_weight" id="brand_weight" value="' . esc_attr($brand_weight) . '" />';
        ?>
      </td>
    </tr>
    <?php
  }

  public function save_brand_fields($term_id, $taxonomy) {
    if (isset($_POST['brand_image_id'])) {
      update_term_meta($term_id, 'brand_image_id', absint($_POST['brand_image_id']));
    }
    if (isset($_POST['brand_weight'])) {
      update_term_meta($term_id, 'brand_weight', sanitize_text_field($_POST['brand_weight']));
    }
  }
}
