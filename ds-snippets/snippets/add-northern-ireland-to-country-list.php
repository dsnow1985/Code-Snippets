<?php

/**
 * Plugin Name: Add Northern Ireland to Country List
 * Description: Adds Northern Ireland to the list of countries in WooCommerce.
 * Version: 1.0
 * Author: David Snow
 * Author URI: https://davidsnow.net
 */

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
