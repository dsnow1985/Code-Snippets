<?php

/**
 * Plugin Name: Add Calendar to Checkout
 * Description: Adds a calendar datepicker to the WooCommerce checkout page.
 * Version: 1.1
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
        <strong>Add Calendar to Checkout Snippet:</strong> Requires <strong>WooCommerce</strong> to be installed and active
        for this code to work.
      </p>
    </div>
<?php
  });

  return; // Stop running the rest of this snippet
}

// --- Snippet Code Below ---

// Register main datepicker jQuery plugin script
add_action('wp_enqueue_scripts', 'enabling_date_picker');
function enabling_date_picker()
{
  // Only on front-end and checkout page
  if (is_admin() || ! function_exists('is_checkout') || ! is_checkout()) return;

  // Load the datepicker jQuery-ui plugin script
  wp_enqueue_script('jquery-ui-datepicker');
}

// Call datepicker functionality in your custom text field
add_action('woocommerce_after_order_notes', 'my_custom_checkout_field', 10, 1);
function my_custom_checkout_field($checkout)
{
  date_default_timezone_set('America/Los_Angeles');
  $mydateoptions = array('' => __('Select PickupDate', 'woocommerce'));

  echo '<div id="my_custom_checkout_field">
    <h3>' . __('Delivery Info', 'woocommerce') . '</h3>';

  // YOUR SCRIPT HERE BELOW
  echo '
    <script>
        jQuery(function($){
            $("#datepicker").datepicker();
        });
    </script>';

  woocommerce_form_field('order_pickup_date', array(
    'type'          => 'text',
    'class'         => array('my-field-class form-row-wide'),
    'id'            => 'datepicker',
    'required'      => true,
    'label'         => __('Delivery Date', 'woocommerce'),
    'placeholder'   => __('Select Date', 'woocommerce'),
    'options'       => $mydateoptions
  ), $checkout->get_value('order_pickup_date'));

  echo '</div>';
}
