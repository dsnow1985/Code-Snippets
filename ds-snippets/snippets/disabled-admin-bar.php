<?php

/**
 * Plugin Name: Disable Admin Bar for Non-Admins
 * Description: Disables the admin bar for non-admin users.
 * Version: 1.0
 * Author: Unknown
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

add_action('wp', function () {
  if (! current_user_can('manage_options')) {
    show_admin_bar(false);
  }
});
