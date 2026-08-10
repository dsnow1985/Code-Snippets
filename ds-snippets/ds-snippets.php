<?php

/**
 * Plugin Name: DS Snippets
 * Description: Scans a target folder and includes PHP files based on settings toggles.
 * Version: 2.3
 * Author: David Snow
 * Author URI: https://davidsnow.net
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/**
 * Get the absolute path to the snippets directory.
 */
function ds_get_snippets_dir(): string
{
  return plugin_dir_path(__FILE__) . 'snippets/';
}

/**
 * Get valid snippet files along with their header metadata.
 */
function ds_get_snippet_files(): array
{
  $dir_path = ds_get_snippets_dir();
  $snippets = [];

  if (! is_dir($dir_path)) {
    return $snippets;
  }

  $files = scandir($dir_path);
  if (empty($files)) {
    return $snippets;
  }

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
    $header_data = get_plugin_data($file_path, false, false);
    $display_name = ! empty($header_data['Name']) ? $header_data['Name'] : $file;

    $snippets[$file] = [
      'file'             => $file,
      'name'             => $display_name,
      'description'      => $header_data['Description'],
      'version'          => $header_data['Version'],
      'author'           => $header_data['Author'],
      'author_uri'       => $header_data['AuthorURI'],
      'requires_plugins' => $header_data['RequiresPlugins'],
    ];
  }

  return $snippets;
}

/**
 * Run enabled snippets safely.
 */
function ds_run_enabled_snippets(): void
{
  $dir_path = ds_get_snippets_dir();
  $enabled_snippets = get_option('ds_enabled_snippets', []);

  if (! is_array($enabled_snippets) || empty($enabled_snippets)) {
    return;
  }

  // Fetch valid snippets to ensure we only include actual existing snippet files
  $valid_snippets = array_keys(ds_get_snippet_files());

  foreach ($enabled_snippets as $file => $is_enabled) {
    if ($is_enabled && in_array($file, $valid_snippets, true)) {
      $file_path = realpath($dir_path . sanitize_file_name($file));

      // Ensure realpath stays strictly within the designated snippets directory
      if ($file_path && strpos($file_path, realpath($dir_path)) === 0 && file_exists($file_path)) {
        include_once $file_path;
      }
    }
  }
}
add_action('plugins_loaded', 'ds_run_enabled_snippets');

/**
 * Register settings page.
 */
function ds_snippets_add_admin_menu(): void
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
 * Sanitize submitted settings before saving.
 */
function ds_sanitize_enabled_snippets($input): array
{
  $output = [];
  if (! is_array($input)) {
    return $output;
  }

  $valid_files = array_keys(ds_get_snippet_files());

  foreach ($input as $file => $value) {
    $clean_file = sanitize_file_name($file);
    if (in_array($clean_file, $valid_files, true)) {
      $output[$clean_file] = (int) $value === 1 ? 1 : 0;
    }
  }

  return $output;
}

/**
 * Register setting field with sanitization callback.
 */
function ds_snippets_settings_init(): void
{
  register_setting('ds_snippets_group', 'ds_enabled_snippets', [
    'type'              => 'array',
    'sanitize_callback' => 'ds_sanitize_enabled_snippets',
    'default'           => [],
  ]);
}
add_action('admin_init', 'ds_snippets_settings_init');

/**
 * Render settings page.
 */
function ds_snippets_options_page(): void
{
  if (! current_user_can('manage_options')) {
    return;
  }

  $available_snippets = ds_get_snippet_files();
  $enabled_snippets = get_option('ds_enabled_snippets', []);
  if (! is_array($enabled_snippets)) {
    $enabled_snippets = [];
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
              <th scope="col">Required Plugins</th>
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
                <td><?php echo esc_html($info['requires_plugins']); ?></td>
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
