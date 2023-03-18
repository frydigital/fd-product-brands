<?php



class WooCommerce_Product_Brand
{
  public function __construct()
  {
    add_action('init', array($this, 'register_product_brand_taxonomy'));
    add_action('add_meta_boxes', array($this, 'add_product_brand_meta_box'));
    add_action('save_post_product', array($this, 'save_product_brand_meta_data'));
    add_action('save_post_product', array($this, 'save_product_brand_weight_data'));
    add_filter('term_link', array($this, 'term_link'), 10, 2);
  }

  public function register_product_brand_taxonomy()
  {
    $args = array(
      'hierarchical' => true,
      'label' => __('Product Brands', 'woocommerce'),
      'show_ui' => true,
      'query_var' => true,
      'rewrite' => array('slug' => 'product_brand'),
      //'meta_box_cb' => array($this, 'product_brand_meta_box_html'),
    );

    register_taxonomy('product_brand', array('product'), $args);

    add_filter('manage_edit-product_brand_columns', array($this, 'product_brand_columns'));
    add_filter('manage_product_brand_custom_column', array($this, 'product_brand_column_content'), 10, 3);
    add_filter('manage_edit-product_brand_sortable_columns', array($this, 'product_brand_sortable_columns'));
    add_filter('request', array($this, 'product_brand_sort_request'));
  
  }


  // *** adjust this to be a selector of terms instead
  public function add_product_brand_meta_box()
  {
    add_meta_box(
      'product_brand_meta_box',
      __('Product Brand Image', 'woocommerce'),
      array($this, 'product_brand_meta_box_html'),
      'product',
      'side'
    );
  }

  public function product_brand_columns($columns)
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
        $content = '-';
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
    if (isset($vars['orderby']) && $vars['orderby'] === 'product_brand_weight') {
      $vars = array_merge($vars, array(
        'meta_key' => 'product_brand_weight',
        'orderby' => 'meta_value_num'
      ));
    }
    return $vars;
  }

  public function save_product_brand_weight_data($term_id, $tt_id, $taxonomy)
  {
    if (!isset($_POST['product_brand_weight'])) {
      return;
    }

    $weight = sanitize_text_field($_POST['product_brand_weight']);
    update_term_meta($term_id, 'product_brand_weight', $weight);
  }


  public function product_brand_meta_box_html($post)
  {
    wp_nonce_field('product_brand_image', 'product_brand_image_nonce');

    $product_brand_image_url = get_post_meta($post->ID, 'product_brand_image_url', true);
    $weight = get_term_meta($post->term_id, 'product_brand_weight', true);

?>
    <div class="product-brand-image-preview">
      <?php if (!empty($product_brand_image_url)) : ?>
        <img src="<?php echo esc_url($product_brand_image_url); ?>" alt="<?php echo esc_attr__('Product Brand Image', 'woocommerce'); ?>" />
      <?php endif; ?>
    </div>
    <div class="product-brand-image-upload">
      <label for="product_brand_image"><?php echo esc_html__('Upload Image', 'woocommerce'); ?></label>
      <input type="hidden" name="product_brand_image_url" id="product_brand_image_url" value="<?php echo esc_attr($product_brand_image_url); ?>" />
      <input type="button" id="product_brand_image_button" class="button" value="<?php echo esc_attr__('Select Image', 'woocommerce'); ?>" />
    </div>
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        var mediaUploader;

        $('#product_brand_image_button').click(function(e) {
          e.preventDefault();

          if (mediaUploader) {
            mediaUploader.open();
            return;
          }

          mediaUploader = wp.media.frames.file_frame = wp.media({
            title: '<?php echo esc_html__('Choose Image', 'woocommerce'); ?>',
            button: {
              text: '<?php echo esc_html__('Use Image', 'woocommerce'); ?>'
            },
            multiple: false
          });

          mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#product_brand_image_url').val(attachment.url);
            $('.product-brand-image-preview img').attr('src', attachment.url);
          });

          mediaUploader.open();
        });
      });
    </script>
    <div class="form-field term-group">
      <label for="product_brand_weight"><?php esc_html_e('Weight', 'woocommerce'); ?></label>
      <input type="text" name="product_brand_weight" id="product_brand_weight" value="<?php echo esc_attr($weight); ?>" />
    </div>
<?php
  }

  public function save_product_brand_meta_data(
    $post
  ) {

    if (!isset($_POST['product_brand_image_nonce']) || !wp_verify_nonce($_POST['product_brand_image_nonce'], 'product_brand_image')) {
      return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return;
    }

    if (!current_user_can('edit_post', $post->ID)) {
      return;
    }

    if (isset($_POST['product_brand_image_url'])) {
      update_post_meta($post->ID, 'product_brand_image_url', sanitize_text_field($_POST['product_brand_image_url']));
    }
  }

  public function term_link($url, $term)
  {
    if ($term->taxonomy === 'product_brand') {
      $url = add_query_arg(array('product_brand' => $term->slug), home_url('/'));
    }
    return $url;
  }
}

new WooCommerce_Product_Brand();


// This code creates a class called `WooCommerce_Product_Brand` and registers the custom taxonomy 'product_brand' using the `register_taxonomy()` function. It also adds a meta box to the product editing screen that allows the user to upload an image for the product_brand using the WordPress media uploader. The image is saved as post meta using the `update_post_meta()` function. Finally, the `term_link()` function filters the term link for the 'product_brand' taxonomy to include a query argument that links to a search result for that taxonomy.
