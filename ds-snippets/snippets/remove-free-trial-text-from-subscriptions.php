<?php

/**
 * Plugin Name: Remove Free Trial Text from Subscriptions
 * Description: Removes the free trial text from subscription products in WooCommerce.
 * Version: 1.0
 * Author: David Snow
 * Author URI: https://davidsnow.net
 * Requires Plugins: woocommerce, woocommerce-subscriptions
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
        <strong>Remove Free Trial Text Snippet:</strong> Requires <strong>WooCommerce</strong> to be installed and active.
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
        <strong>Remove Free Trial Text Snippet:</strong> Requires <strong>WooCommerce Subscriptions</strong> to be installed
        and active.
      </p>
    </div>
<?php
  });

  return; // Stop running the rest of this snippet
}

// --- Snippet Code Below ---

add_filter('woocommerce_subscription_price_string', 'dynamic_subscription_price_string', 10, 2);

function dynamic_subscription_price_string($price_string, $product)
{
  // Ensure the product is a valid WooCommerce product object
  if (!is_object($product) || !method_exists($product, 'is_type')) {
    return $price_string; // Return the original price string if the product is not valid
  }

  // Check if the product is a subscription
  if ($product->is_type('subscription')) {
    $signup_fee = (float) $product->get_sign_up_fee(); // Sign-up fee
    $trial_length = (int) $product->get_trial_length(); // Trial period length
    $trial_period = $product->get_trial_period(); // Trial period unit (e.g., days, weeks, months)
    $subscription_price = (float) $product->get_regular_price(); // Monthly price

    // Only modify the price string if there is a trial period in months
    if ($trial_length > 0 && $trial_period === 'month') {
      $signup_text = $signup_fee > 0 ? wc_price($signup_fee) : 'free';
      $price_string = sprintf(
        '%s / month after initial %d months for %s',
        wc_price($subscription_price),
        $trial_length,
        $signup_text
      );
    }
  }

  return $price_string;
}
