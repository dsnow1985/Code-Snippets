<?php

/**
 * Plugin Name: Disable Admin Bar for Non-Admins
 * Description: Disables the admin bar for non-admin users.
 * Version: 1.0
 * Author: David Snow
 * Author URI: https://davidsnow.net
 */

add_action('wp', function () {
  if (! current_user_can('manage_options')) {
    show_admin_bar(false);
  }
});