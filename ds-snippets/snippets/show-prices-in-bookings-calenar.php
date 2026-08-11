<?php

/**
 * Plugin Name: Show Prices in Bookings Calendar
 * Description: Display daily accommodation price in WooCommerce Bookings calendar. (Specifically for WooCommerce Bookings and WooCommerce Accommodation Bookings)
 * Version: 1.0
 * Author: David Snow
 * Author URI: https://davidsnow.net
 * Requires Plugins: woocommerce, woocommerce-bookings, woocommerce-accommodation-bookings
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
    <strong>Show Prices in Bookings Calendar Snippet:</strong> Requires <strong>WooCommerce</strong> to be installed and
    active
    for this code to work.
  </p>
</div>
<?php
  });

  return; // Stop running the rest of this snippet
}
if (! class_exists('WC_Bookings')) {
  add_action('admin_notices', function () {
    if (! current_user_can('activate_plugins')) {
      return;
    }
?>
<div class="notice notice-error is-dismissible">
  <p>
    <strong>Show Prices in Bookings Calendar Snippet:</strong> Requires <strong>WooCommerce</strong> to be installed and
    active
    for this code to work.
  </p>
</div>
<?php
  });

  return; // Stop running the rest of this snippet
}