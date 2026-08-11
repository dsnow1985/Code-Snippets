<?php

/**
 * Plugin Name: Delay Sending Order Emails
 * Description: Delays the sending of order emails in WooCommerce.
 * Version: 1.0
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
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
        <strong>Delay Sending Order Emails Snippet:</strong> Requires <strong>WooCommerce</strong> to be installed and
        active
        for this code to work.
      </p>
    </div>
<?php
  });

  return; // Stop running the rest of this snippet
}

add_filter('woocommerce_defer_transactional_emails', '__return_true');
