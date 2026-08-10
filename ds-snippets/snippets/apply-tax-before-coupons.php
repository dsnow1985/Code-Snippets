<?

/**
 * Plugin Name: Apply Tax Before Coupons
 * Description: Ensures tax is applied before coupon discounts in WooCommerce.
 * Version: 1.0
 * Author: David Snow
 * Author URI: https://davidsnow.net
 */

/* Fail-safe check: Verify WooCommerce is active. If not, display an admin warning banner and stop snippet execution.*/
function ds_check_woocommerce_dependency()
{
  // Check if WooCommerce class exists or if the function is_plugin_active is available
  if (! class_exists('WooCommerce')) {
    add_action('admin_notices', 'ds_woocommerce_missing_admin_notice');
    return false;
  }
  return true;
}

/**
 * Display an admin notice if WooCommerce is inactive.
 */
function ds_woocommerce_missing_admin_notice()
{
  if (! current_user_can('activate_plugins')) {
    return;
  }
?>
  <div class="notice notice-error is-dismissible">
    <p>
      <strong>DS Snippet Disabled:</strong> The custom <strong>Apply Tax Before Coupons</strong> snippet requires
      <strong>WooCommerce</strong> to be installed and activated.
    </p>
  </div>
<?php
}

// Bail early if WooCommerce is not available
if (! ds_check_woocommerce_dependency()) {
  return;
}

add_filter('woocommerce_product_get_tax_class', 'wc_handle_coupon_tax_rates', 1, 2);
function wc_handle_coupon_tax_rates($tax_class, $product)
{
  if (WC()->cart->subtotal <= 110) // This condition determines if the 'Zero Rate' tax class is applied.
    $tax_class = 'Zero Rate'; // Replace 'Zero Rate' with the desired tax class.
  return $tax_class;
}
