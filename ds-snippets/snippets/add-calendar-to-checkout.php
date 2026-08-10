<?php

/**
 * Fail-safe check: Verify WooCommerce is active.
 * If not, display an admin warning banner and stop snippet execution.
 */
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
      <strong>DS Snippet Disabled:</strong> The custom checkout datepicker snippet requires <strong>WooCommerce</strong>
      to be installed and activated.
    </p>
  </div>
<?php
}

// Bail early if WooCommerce is not available
if (! ds_check_woocommerce_dependency()) {
  return;
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
