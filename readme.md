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


# Future Development
* Admin options page to set sort order and flickity display options.
* Auto attach to footer location.