<?php

/**
 * Plugin Name: DS Snippets
 * Description: Scans a target folder and includes PHP files based on settings toggles.
 * Version: 2.0
 * Author: David Snow
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/**
 * Get valid snippet files from the snippets directory.
 */
function ds_get_snippet_files()
{
  $dir_path = WP_CONTENT_DIR . '/plugins/ds-snippets/snippets/';
  $snippet_files = array();

  if (! is_dir($dir_path)) {
    return $snippet_files;
  }

  $files = scandir($dir_path);
  if (empty($files)) {
    return $snippet_files;
  }

  foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
      continue;
    }

    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if (strtolower($ext) !== 'php') {
      continue;
    }

    // Skip files containing an underscore
    if (strpos($file, '_') !== false) {
      continue;
    }

    $snippet_files[] = $file;
  }

  return $snippet_files;
}

/**
 * Run enabled snippets.
 */
function ds_run_enabled_snippets()
{
  $dir_path = WP_CONTENT_DIR . '/plugins/ds-snippets/snippets/';
  $enabled_snippets = get_option('ds_enabled_snippets', array());

  if (! is_array($enabled_snippets) || empty($enabled_snippets)) {
    return;
  }

  foreach ($enabled_snippets as $file => $is_enabled) {
    if ($is_enabled && strpos($file, '_') === false && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
      $file_path = $dir_path . sanitize_file_name($file);
      if (file_exists($file_path)) {
        include_once $file_path;
      }
    }
  }
}
add_action('plugins_loaded', 'ds_run_enabled_snippets');

/**
 * Register settings page.
 */
function ds_snippets_add_admin_menu()
{
  add_options_page(
    'DS Snippets Settings',
    'DS Snippets',
    'manage_options',
    'ds-snippets',
    'ds_snippets_options_page'
  );
}
add_action('admin_menu', 'ds_snippets_add_admin_menu');

/**
 * Register setting field.
 */
function ds_snippets_settings_init()
{
  register_setting('ds_snippets_group', 'ds_enabled_snippets');
}
add_action('admin_init', 'ds_snippets_settings_init');

/**
 * Render settings page.
 */
function ds_snippets_options_page()
{
  if (! current_user_can('manage_options')) {
    return;
  }

  $available_files = ds_get_snippet_files();
  $enabled_snippets = get_option('ds_enabled_snippets', array());
  if (! is_array($enabled_snippets)) {
    $enabled_snippets = array();
  }
?>
  <div class="wrap">
    <h1>DS Snippets Settings</h1>
    <form action="options.php" method="post">
      <?php
      settings_fields('ds_snippets_group');
      do_settings_sections('ds_snippets_group');
      ?>

      <?php if (empty($available_files)) : ?>
        <p>No valid snippet files found in <code>/wp-content/plugins/ds-snippets/snippets/</code>.</p>
      <?php else : ?>
        <table class="form-table" role="presentation">
          <tbody>
            <?php foreach ($available_files as $file) :
              $is_checked = ! empty($enabled_snippets[$file]);
            ?>
              <tr>
                <th scope="row"><?php echo esc_html($file); ?></th>
                <td>
                  <label for="snippet_<?php echo esc_attr(sanitize_key($file)); ?>">
                    <input type="checkbox" id="snippet_<?php echo esc_attr(sanitize_key($file)); ?>"
                      name="ds_enabled_snippets[<?php echo esc_attr($file); ?>]" value="1"
                      <?php checked($is_checked, true); ?> />
                    Enable this snippet
                  </label>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php submit_button('Save Snippet Settings'); ?>
      <?php endif; ?>
    </form>
  </div>
<?php
}
