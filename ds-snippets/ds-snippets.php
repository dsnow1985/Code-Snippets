<?php

/**
 * Plugin Name: DS Snippets
 * Description: Scans a target folder and includes PHP files that do not contain an underscore in their filename to run on a WordPress site.
 * Version: 1.0
 * Author: David Snow
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/**
 * WARNING: Automatically including and executing PHP files from a directory 
 * can pose significant security risks. Ensure the target directory is 
 * properly secured and only accessible by authorized administrators.
 */
function scp_scan_and_run_directory()
{
  // Set the target directory path
  $dir_path = WP_CONTENT_DIR . '/ds-snippets/snippets';

  // Check if directory exists
  if (! is_dir($dir_path)) {
    return;
  }

  // Open the directory
  $handle = opendir($dir_path);
  if (! $handle) {
    return;
  }

  // Read files in a loop
  while (false !== ($file = readdir($handle))) {
    // Skip system pointer files
    if ($file === '.' || $file === '..') {
      continue;
    }

    // Check if it is a .php file
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if (strtolower($ext) !== 'php') {
      continue;
    }

    // Skip the file if it has an underscore '_' in the filename
    if (strpos($file, '_') !== false) {
      continue;
    }

    // Full path to the file
    $file_full_path = $dir_path . $file;

    // Include and run the file
    if (file_exists($file_full_path)) {
      include $file_full_path;
    }
  }

  closedir($handle);
}

// Hook the function into WordPress (runs on init)
add_action('init', 'scp_scan_and_run_directory');
