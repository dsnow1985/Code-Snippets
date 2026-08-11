<?php

/**
 * Plugin Name: Enable WooCommerce Subscriptions on Staging Sites
 * Description: Enables WooCommerce Subscriptions on staging sites.
 * Version: 1.0
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Requires Plugins: woocommerce, woocommerce-subscriptions
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
        <strong>Enable WooCommerce Subscriptions on Staging Sites Snippet:</strong> Requires <strong>WooCommerce</strong> to
        be installed and active
        for this code to work.
      </p>
    </div>
  <?php
  });

  return; // Stop running the rest of this snippet
}
if (! class_exists('WC_Subscriptions')) {
  add_action('admin_notices', function () {
    if (! current_user_can('activate_plugins')) {
      return;
    }
  ?>
    <div class="notice notice-error is-dismissible">
      <p>
        <strong>Enable WooCommerce Subscriptions on Staging Sites Snippet:</strong> Requires <strong>WooCommerce
          Subscriptions</strong> to be installed
        and active.
      </p>
    </div>
<?php
  });

  return; // Stop running the rest of this snippet
}

add_filter('wcs_is_site_staging', '__return_true');
