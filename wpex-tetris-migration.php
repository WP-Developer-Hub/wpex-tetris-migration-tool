<?php
/*
Plugin Name: WPEX Tetris Migration
Description: Add the updater to download the new WPEX Tetris fork & migrate old media meta fields to new universal meta keys for the wpex-tetris theme via a dashboard widget.
Version: 1.2.0
Author: DJABHipHop
Author URI: https://github.com/WP-Developer-Hub
Plugin URI: https://github.com/WP-Developer-Hub/wpex-tetris-migration-tool
*/

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WPEX_TETRIS_CLASSES_DIR')) {
    define('WPEX_TETRIS_CLASSES_DIR', plugin_dir_path(__FILE__) . 'classes/');
}

class WPEX_Tetris_Migration_Plugin {

    public function __construct() {
        require_once WPEX_TETRIS_CLASSES_DIR . 'wpex-tetris-migration-widget.php';

        // Get the current theme version
        $theme     = wp_get_theme();
        $curr_ver  = $theme->get('Version');
        // Get the last checked version from the database
        $last_ver  = get_option('wpex_tetris_last_checked_version');

        // Only require the updater if the versions do not match
        if ($curr_ver !== $last_ver) {
            require_once WPEX_TETRIS_CLASSES_DIR . 'wpex-tetris-migration-updater.php';
        }

        // Properly hook the meta box registration
        add_action('add_meta_boxes', [$this, 'register_old_embed_media_metabox']);

        add_action('admin_notices', function() {
            if ($curr_ver !== $last_ver) {
                global $pagenow;
                // Show only on 'update-core.php' (Updates) and 'themes.php' (Appearance > Themes)
                if (in_array($pagenow, array('update-core.php', 'themes.php'))) {
                    $plugin_search_url = admin_url('plugin-install.php?s=Download+Plugins+and+Themes+from+Dashboard&tab=search&type=term');
                    ?>
                    <div class="notice notice-warning">
                        <p>
                            <strong>Before updating:</strong> For your safety, please
                            <a href="<?php echo esc_url($plugin_search_url); ?>" target="_blank">
                                install the "Download Plugins and Themes from Dashboard" plugin
                            </a>
                            to easily back up your current theme as a ZIP file from your dashboard.
                        </p>
                    </div>
                    <?php
                }
            }
        });
    }

    public function register_old_embed_media_metabox() {
        add_meta_box(
            'old_embed_media',
            'Old Embed Media (for reference only)',
            [$this, 'render_old_embed_media_metabox'],
            'post', // Change to your custom post type if needed
            'side'
        );
    }

    public function render_old_embed_media_metabox($post) {
        $old_value = get_post_meta($post->ID, '_old_embed_media', true);
        echo '<label>Previous embed value:</label><br>';
        echo '<input type="text" value="' . esc_attr($old_value) . '" class="widefat">';
        echo '<p style="color:#888;font-size:smaller;">This field is for reference only. You can copy the value, but it will not be saved or updated.</p>';
    }
}

// Initialize the plugin
new WPEX_Tetris_Migration_Plugin();
