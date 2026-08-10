<?php

/**
 * Plugin Name: Add Northern Ireland to Country List
 * Description: Adds Northern Ireland to the list of countries in WooCommerce.
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
        <strong>Add Northern Ireland to Country List Snippet:</strong> Requires <strong>WooCommerce</strong> to be installed
        and active.
      </p>
    </div>
<?php
  });

  return; // Stop running the rest of this snippet
}

add_filter('woocommerce_countries',  'rs_add_my_country');
function rs_add_my_country($countries)
{
  $new_countries = array(
    'NIRE'  => __('Northern Ireland', 'woocommerce'),
    'SCO'  => __('Scotland', 'woocommerce'),
    'WAL'  => __('Wales', 'woocommerce'),
    'ENG'  => __('England', 'woocommerce'),
  );

  return array_merge($countries, $new_countries);
}

add_filter('woocommerce_continents', 'rs_add_my_country_to_continents');
function rs_add_my_country_to_continents($continents)
{
  $continents['UK']['countries'][] = 'NIRE';
  $continents['UK']['countries'][] = 'SCO';
  $continents['UK']['countries'][] = 'WAL';
  $continents['UK']['countries'][] = 'ENG';
  return $continents;
}
