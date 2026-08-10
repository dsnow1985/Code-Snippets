<?php

/**
 * Plugin Name: DS Snippets
 * Description: Scans a target folder and includes PHP files based on settings toggles.
 * Version: 2.1
 * Author: David Snow
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/**
 * Get valid snippet files along with their header metadata.
 */
function ds_get_snippet_files()
{
  $dir_path = WP_CONTENT_DIR . '/plugins/ds-snippets/snippets/';
  $snippets = array();

  if (! is_dir($dir_path)) {
    return $snippets;
  }

  $files = scandir($dir_path);
  if (empty($files)) {
    return $snippets;
  }

  // Include WordPress core file needed for get_plugin_data()
  if (! function_exists('get_plugin_data')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
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

    $file_path = $dir_path . $file;

    // Retrieve file header metadata
    $header_data = get_plugin_data($file_path, false, false);

    // Fall back to filename if Plugin Name header is missing
    $display_name = ! empty($header_data['Name']) ? $header_data['Name'] : $file;

    $snippets[$file] = array(
      'file'        => $file,
      'name'        => $display_name,
      'description' => $header_data['Description'],
      'version'     => $header_data['Version'],
      'author'      => $header_data['Author'],
      'author_uri'  => $header_data['AuthorURI'],
    );
  }

  return $snippets;
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

  $available_snippets = ds_get_snippet_files();
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

    <?php if (empty($available_snippets)) : ?>
    <p>No valid snippet files found in <code>/wp-content/plugins/ds-snippets/snippets/</code>.</p>
    <?php else : ?>
    <table class="wp-list-table widefat fixed striped" style="margin-top: 15px;">
      <thead>
        <tr>
          <th scope="col" style="width: 80px;">Status</th>
          <th scope="col">Snippet</th>
          <th scope="col">Description</th>
          <th scope="col" style="width: 100px;">Version</th>
          <th scope="col">Author</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($available_snippets as $file => $info) :
              $is_checked = ! empty($enabled_snippets[$file]);
            ?>
        <tr>
          <td>
            <label for="snippet_<?php echo esc_attr(sanitize_key($file)); ?>">
              <input type="checkbox" id="snippet_<?php echo esc_attr(sanitize_key($file)); ?>"
                name="ds_enabled_snippets[<?php echo esc_attr($file); ?>]" value="1"
                <?php checked($is_checked, true); ?> />
            </label>
          </td>
          <td>
            <strong><?php echo esc_html($info['name']); ?></strong><br>
            <code style="font-size: 11px;"><?php echo esc_html($file); ?></code>
          </td>
          <td><?php echo esc_html($info['description']); ?></td>
          <td><?php echo esc_html($info['version']); ?></td>
          <td>
            <?php if (! empty($info['author_uri'])) : ?>
            <a href="<?php echo esc_url($info['author_uri']); ?>" target="_blank" rel="noopener noreferrer">
              <?php echo esc_html($info['author']); ?>
            </a>
            <?php else : ?>
            <?php echo esc_html($info['author']); ?>
            <?php endif; ?>
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