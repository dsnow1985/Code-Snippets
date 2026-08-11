<?php

/**
 * Plugin Name: Disable Product Zoom
 * Description: Disables the zoom on products when hover cursor over featured product image on single product pages.
 * Version: 1.0
 * Author: Unknown
 * Requires Plugins: woocommerce
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Fail-safe: Check if WooCommerce is active.
 */
if (! class_exists('WooCommerce')) {
  add_action('admin_notices', function () {
    if (! current_user_can('activate_plugins')) {
      return;
    }
?>
    <div class="notice notice-error is-dismissible">
      <p>
        <strong>Apply Tax Before Coupons Snippet:</strong> Requires <strong>WooCommerce</strong> to be installed and active
        for this code to work.
      </p>
    </div>
<?php
  });

  return; // Stop running the rest of this snippet
}

add_action('after_setup_theme', 'custom_remove_product_zoom', 11);

function custom_remove_product_zoom()
{
  remove_theme_support('wc-product-gallery-zoom');
}
