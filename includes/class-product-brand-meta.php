<?php
class Product_Brand_Meta extends WooCommerce_Product_Brand
{

  public function __construct()
  {
    add_action('product_brand_add_form_fields', array($this, 'add_brand_fields'));
    add_action('product_brand_edit_form_fields', array($this, 'edit_brand_fields'));
    
    // Enqueue script for WP upload box
    add_action('admin_enqueue_scripts', array($this, 'load_media_files'));
    
    // Hook into the saving of the 'product_brand' taxonomy term
    add_action('create_product_brand', array($this, 'save_brand_image_and_weight'));
    add_action('edited_product_brand', array($this, 'save_brand_image_and_weight'));
  }

  public function load_media_files()
  {
    wp_enqueue_media();
  }

  public function save_brand_image_and_weight($term_id)
  {
    if (isset($_POST['product_brand_image'])) {
      $image_id = $_POST['product_brand_image'];
      if (!empty($image_id)) {
        update_term_meta($term_id, 'product_brand_image', $image_id);
      }
    }
    if (isset($_POST['product_brand_weight'])) {
      $weight = $_POST['product_brand_weight'];
      if (!empty($weight)) {
        update_term_meta($term_id, 'product_brand_weight', $weight);
      }
    }
    // Upload image and set as term meta
    if (!empty($_FILES['product_brand_image_file']['name'])) {
      $uploaded_image = media_handle_upload('product_brand_image_file', $term_id);
      if (!is_wp_error($uploaded_image)) {
        update_term_meta($term_id, 'product_brand_image', $uploaded_image);
      }
    }
  }

  public function add_brand_fields()
  {
    // Add the image and weight fields to the product_brand taxonomy form
?>
    <div class="form-field term-group">
      <label for="product_brand_image"><?php esc_html_e('Brand Image', 'text-domain'); ?></label>
      <input type="hidden" id="product_brand_image" name="product_brand_image" value="">
      <img id="brand-image-preview" src="" style="max-width: 100%; display: none;">
      <input type="button" id="brand-image-upload-button" class="button" value="<?php esc_attr_e('Upload Image', 'text-domain'); ?>">
      <script>
        jQuery(document).ready(function($) {
          // Set up the media uploader
          var mediaUploader;
          $('#brand-image-upload-button').click(function(e) {
            e.preventDefault();
            // If the uploader object has already been created, reopen the dialog
            if (mediaUploader) {
              mediaUploader.open();
              return;
            }
            // Create the media uploader
            mediaUploader = wp.media.frames.file_frame = wp.media({
              title: '<?php esc_attr_e('Select Image', 'text-domain'); ?>',
              button: {
                text: '<?php esc_attr_e('Choose Image', 'text-domain'); ?>',
              },
              multiple: false
            });
            // When a file is selected, grab the URL and set it as the value of the image field
            mediaUploader.on('select', function() {
              var attachment = mediaUploader.state().get('selection').first().toJSON();
              $('#brand-image-preview').attr('src', attachment.url);
              $('#product_brand_image').val(attachment.id);
              $('#brand-image-preview').show();
            });
            // Open the uploader dialog
            mediaUploader.open();
          });
        });
      </script>
    </div>
    <div class="form-field term-group">
      <label for="product_brand_weight"><?php esc_html_e('Brand Weight', 'text-domain'); ?></label>
      <input type="text" id="product_brand_weight" name="product_brand_weight" value="">
    </div>
  <?php
  }

  public function edit_brand_fields($term)
  {
    // Retrieve current values of fields
    $image_id = get_term_meta($term->term_id, 'product_brand_image', true);
    $weight = get_term_meta($term->term_id, 'product_brand_weight', true);

    // Output the fields
  ?>
    <tr class="form-field">
      <th scope="row" valign="top"><label for="product_brand_image"><?php _e('Brand Image'); ?></label></th>
      <td>
        <?php if (!empty($image_id)) { ?>
          <img src="<?php echo wp_get_attachment_url($image_id); ?>" style="max-width: 200px;" />
          <br /><br />
        <?php } ?>
        <input type="hidden" id="product_brand_image" name="product_brand_image" value="<?php echo esc_attr($image_id); ?>" />
        <button id="upload_image_button" class="button"><?php _e('Upload Image'); ?></button>
        <script>
          jQuery(document).ready(function($) {
            var custom_uploader;
            $('#upload_image_button').click(function(e) {
              e.preventDefault();
              if (custom_uploader) {
                custom_uploader.open();
                return;
              }
              custom_uploader = wp.media({
                title: '<?php _e("Choose an image"); ?>',
                button: {
                  text: '<?php _e("Use this image"); ?>'
                },
                multiple: false
              });
              custom_uploader.on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#product_brand_image').val(attachment.id);
                $('img').attr('src', attachment.url);
              });
              custom_uploader.open();
            });
          });
        </script>
      </td>
    </tr>
    <tr class="form-field">
      <th scope="row" valign="top"><label for="product_brand_weight"><?php _e('Brand Weight'); ?></label></th>
      <td><input type="number" step="0.01" name="product_brand_weight" id="product_brand_weight" value="<?php echo esc_attr($weight); ?>" /></td>
    </tr>
<?php
  }
}
