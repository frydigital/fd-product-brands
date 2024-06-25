# FD Product Brands
A WooCommerce plugin that adds a custom taxonomy 'product_brand' to products and allows the attachment of images to the product_brand. Each image links to a search result for that taxonomy. Utilized flickity.js for front end display (https://flickity.metafizzy.co/).

### Requirements
* WooCommerce v7.5.0 (Tested)
  
## Shortcode Usage
To use this shortcode, you can simply add `[product_brand_list]` to any post or page. You can also pass the `orderby` and `order` attributes to sort the list by weight, total quantity, or total retail value. For example, `[product_brand_list orderby="product_brand_weight" order="desc"]` would display the list of product brands sorted by weight in descending order.

## Changelog
* 1.0.1 - Initial testing and functionality. Integration of flickity JS and shortcode.
* 1.0.2 - Added autoupdate functionality, reorganisation of folder structure.
* 1.0.3 - Reassign flickity file location
* 1.0.4 - Update display styles and default options
* 1.0.5 - Remove private key for updates (push to public repo)
* 1.0.6 - Responsive CSS update, enable draggable
* 1.0.7 - shortcode url link correction, link to slug instead of brand name.
* 1.0.8 - Bug Fix - class WooCommerce_Product_Brand does not have a method "save_product_brand_meta_data"
* 1.0.9 - Updated CSS rules to isolate styling to product brands only.
* 1.0.10 - Added shortcode attributes for limit and scroll.  Scroll=0 disables flickity functionality & scripts. Limit=12 sets the number of brands to display (default 12).
* 1.0.11 - Introduce 'width' atts, default 200px. Accepts any valid CSS width value.


# Future Development
* Admin options page to set sort order and flickity display options.
* Auto attach to footer location.