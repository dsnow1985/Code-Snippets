<?php

/**
 * Plugin Name: Limit Zip Code to Five Characters
 * Description: Limits the zip code to five characters in WooCommerce.
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

function wtwh_wc_custom_billing_fields($fields)
{
  // restricting the number of allowed digits to 5 for the billing postcode field
  $fields['billing_postcode']['maxlength'] = 5;
  return $fields;
}
add_filter('woocommerce_billing_fields', 'wtwh_wc_custom_billing_fields');

function wtwh_wc_custom_shipping_fields($fields)
{
  // restricting the number of allowed digits to 5 for the shipping postcode field
  $fields['shipping_postcode']['maxlength'] = 5;
  return $fields;
}
add_filter('woocommerce_shipping_fields', 'wtwh_wc_custom_shipping_fields');
