<?php

class WooCommerce_Product_Brand_Shortcode
{
  public function __construct()
  {
    add_shortcode('product_brand_display', array($this, 'product_brand_display_shortcode'));
  }

  public function product_brand_display_shortcode($atts)
  {
    $atts = shortcode_atts(array(
      'orderby' => 'product_brand_weight',
      'order' => 'asc',
      'scroll' => 'true',
      'limit' => '12',
      'width' => '200px',
      'image_class' => 'brand-image'
    ), $atts, 'product_brand_list');

    $brands_with_weight = get_terms('product_brand', array(
      'number' => $atts['limit'],
      'orderby' => 'meta_value_num',
      'order' => $atts['order'],
      'hide_empty' => false,
      'meta_query' => array(
        array(
          'key' => 'product_brand_weight',
          'compare' => 'EXISTS',
          'type' => 'NUMERIC'
        ),
        array(
          'key' => 'thumbnail_id',
          'compare' => 'EXISTS'
        ),
        array(
          'key' => 'display',
          'value' => '1',
          'compare' => '='
        )
      )
    ));

    $brands_without_weight = get_terms('product_brand', array(
      'number' => $atts['limit'],
      'orderby' => 'name',
      'order' => $atts['order'],
      'hide_empty' => false,
      'meta_query' => array(
        array(
          'key' => 'product_brand_weight',
          'compare' => 'NOT EXISTS'
        ),
        array(
          'key' => 'thumbnail_id',
          'compare' => 'EXISTS'
        ),
        array(
          'key' => 'display',
          'value' => '1',
          'compare' => '='
        )
      )
    ));

    $brands = array_merge($brands_with_weight, $brands_without_weight);

    $output = '';

    if (!empty($brands)) {
      $output .= '<div class="fd-product-brand brand-gallery main-carousel ">';

      if ($atts['scroll'] === 'false') {
        $output .= '<div class="row align-items-center row-cols-2 row-cols-md-4 row-cols-lg-5">';
      } else {
        $output .= '<div class="flickity-enabled">';
      }

      foreach ($brands as $brand) {
        $brand_id = $brand->term_id;
        $brand_name = $brand->name;
        $brand_slug = $brand->slug;
        $brand_image_url = get_brand_thumbnail_url($brand_id);

        $output .= '<div class="brand-item carousel-cell col">';
        $output .= '<a href="' . esc_url(add_query_arg('product_brand', $brand_slug, get_permalink(wc_get_page_id('shop')))) . '">';
        $output .= '<img class="' . $atts['image_class'] . '" src="' . $brand_image_url . '" alt="' . $brand_name . '" style="max-width: ' . $atts['width'] . ';" />';
        $output .= '</a>';
        $output .= '</div>';
      }

      $output .= '</div>';

      if ($atts['scroll'] === 'true') {

        $flickityOptions = [
          'groupCells' => true,
          'contain' => true,
          'draggable' => true
        ];

        $output .= '<script src="' .  plugin_dir_url(__DIR__) . 'public/js/flickity.pkgd.min.js"></script>';
        $output .= '<link rel="stylesheet" href="' . plugin_dir_url(__DIR__) . 'public/css/flickity.css" />';
        $output .= '<link rel="stylesheet" href="' . plugin_dir_url(__DIR__) . 'public/css/product-brands.css" />';
        $output .= '<script>jQuery(".brand-gallery").flickity(' . json_encode($flickityOptions) . ');</script>';
      }
      $output .= '</div>';
    }

    return $output;
  }
}
