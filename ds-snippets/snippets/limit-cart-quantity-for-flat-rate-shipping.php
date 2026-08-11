<?php

/**
 * Plugin Name: Limit Cart Quantity for Flat Rate Shipping
 * Description: Limits the quantity of items in the cart for flat rate shipping in WooCommerce.
 * Version: 1.0
 * Author: David Snow
 * Author URI: https://davidsnow.net
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
        <strong>Limit Cart Quantity for Flat Rate Shipping Snippet:</strong> Requires <strong>WooCommerce</strong> to be
        installed and active
        for this code to work.
      </p>
    </div>
<?php
  });

  return; // Stop running the rest of this snippet
}

add_action('woocommerce_check_cart_items', function () {
  $max_quantity = 5; // Set your desired limit
  $flat_rate_selected = false;

  // Check if Flat Rate is the selected shipping method
  $chosen_methods = WC()->session->get('chosen_shipping_methods', []);
  if (!empty($chosen_methods)) {
    foreach ($chosen_methods as $method) {
      if (strpos($method, 'flat_rate') !== false) {
        $flat_rate_selected = true;
        break;
      }
    }
  }

  // If Flat Rate is selected and the cart quantity exceeds the limit, show a notice
  if ($flat_rate_selected && WC()->cart->get_cart_contents_count() > $max_quantity) {
    if (!wc_has_notice("Flat rate shipping is limited to$max_quantity items per order.", 'error')) {
      wc_add_notice("Flat rate shipping is limited to$max_quantity items per order.", 'error');
    }
  }
});
