<?php

/**
 * Plugin Name: Disable Variation
 * Description: Remove bits of variation data from WooCommerce cart & emails we don’t want to show.
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
    <strong>Disable Variation Snippet:</strong> Requires <strong>WooCommerce</strong> to be installed and active
    for this code to work.
  </p>
</div>
<?php
  });

  return; // Stop running the rest of this snippet
}

add_filter( 'woocommerce_get_item_data', 'ds_filter_variable_item_data', 10, 2 );
function ds_filter_variable_item_data( $item_data, $cart_item ) {

    // Labels we want to remove
    $items_to_remove = [ 'logo', 'color' ];

    // Remove any items where the label matches
    foreach( $item_data as $key => $data ) {
        if( in_array( $data['name'], $items_to_remove ) ) {
            unset( $item_data[ $key ] );
        }
    }

    return $item_data;
}