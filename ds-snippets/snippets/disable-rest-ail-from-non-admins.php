<?php

/**
 * Plugin Name: Disable REST API from Non-Admins
 * Description: Way to secure the REST API endpoint so it is not viewable to non admin users. ** Not sure if this actually allows the REST API to be called externally. Need to verify this. **
 * Version: 1.0
 * Author: David Snow
 * Author URI: https://davidsnow.net
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

add_filter('rest_authentication_errors', function ($result) {
  if (! empty($result)) {
    return $result;
  }
  if (! is_user_logged_in()) {
    return new WP_Error('rest_not_logged_in', 'You are not currently logged in.', array('status' => 401));
  }
  if (! current_user_can('administrator')) {
    return new WP_Error('rest_not_admin', 'You are not an administrator.', array('status' => 401));
  }
  return $result;
});
