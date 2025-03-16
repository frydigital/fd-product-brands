<?php
class Product_Brand_Meta extends WooCommerce_Product_Brand
{

  public function __construct()
  {
    add_action('product_brand_add_form_fields', array($this, 'add_brand_fields'));
    add_action('product_brand_edit_form_fields', array($this, 'edit_brand_fields'));
    add_action('create_product_brand', array($this, 'save_brand_image_and_weight'));
    add_action('edited_product_brand', array($this, 'save_brand_image_and_weight'));
  }

  public function save_brand_image_and_weight($term_id)
  {
    if (isset($_POST['product_brand_weight'])) {
      $weight = $_POST['product_brand_weight'];
      if (!empty($weight)) {
        update_term_meta($term_id, 'product_brand_weight', $weight);
      }
    }
  }

  public function add_brand_fields()
  {
    // Add the image and weight fields to the product_brand taxonomy form
?>
    <div class="form-field term-group">
      <label for="product_brand_weight"><?php esc_html_e('Brand Weight', 'text-domain'); ?></label>
      <input type="text" id="product_brand_weight" name="product_brand_weight" value="">
    </div>
  <?php
  }

  public function edit_brand_fields($term)
  {
    // Retrieve current values of fields
    $weight = get_term_meta($term->term_id, 'product_brand_weight', true);

    // Output the fields
  ?>
    <tr class="form-field">
      <th scope="row" valign="top"><label for="product_brand_weight"><?php _e('Brand Weight'); ?></label></th>
      <td><input type="number" step="0.01" name="product_brand_weight" id="product_brand_weight" value="<?php echo esc_attr($weight); ?>" /></td>
    </tr>
<?php
  }
}
