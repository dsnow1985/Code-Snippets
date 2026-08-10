<?php

// Register main datepicker jQuery plugin script
add_action('wp_enqueue_scripts', 'enabling_date_picker');
function enabling_date_picker()
{

  // Only on front-end and checkout page
  if (is_admin() || ! is_checkout()) return;

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
    <h3>' . __('Delivery Info') . '</h3>';

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
    'label'         => __('Delivery Date'),
    'placeholder'       => __('Select Date'),
    'options'     =>   $mydateoptions
  ), $checkout->get_value('order_pickup_date'));

  echo '</div>';
}
