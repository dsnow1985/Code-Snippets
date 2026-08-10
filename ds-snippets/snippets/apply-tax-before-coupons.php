<?

/**
 * Plugin Name: Apply Tax Before Coupons
 * Description: Ensures tax is applied before coupon discounts in WooCommerce.
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
        <strong>Apply Tax Before Coupons Snippet:</strong> Requires <strong>WooCommerce</strong> to be installed and active
        for this code to work.
      </p>
    </div>
<?php
  });

  return; // Stop running the rest of this snippet
}

add_filter('woocommerce_product_get_tax_class', 'wc_handle_coupon_tax_rates', 1, 2);
function wc_handle_coupon_tax_rates($tax_class, $product)
{
  if (WC()->cart->subtotal <= 110) // This condition determines if the 'Zero Rate' tax class is applied.
    $tax_class = 'Zero Rate'; // Replace 'Zero Rate' with the desired tax class.
  return $tax_class;
}
