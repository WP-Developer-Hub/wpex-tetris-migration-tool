<?php
/*
Plugin Name: WPEX Tetris Migration
Description: Add the updater to download the new WPEX Tetris fork & migrate old media meta fields to new universal meta keys for the wpex-tetris theme via a dashboard widget.
Version: 1.3.0
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
        require_once WPEX_TETRIS_CLASSES_DIR . 'wpex-migration-page.php';

        if (!get_option('wpex_migration_updater_complete')) {
            require_once WPEX_TETRIS_CLASSES_DIR . 'wpex-tetris-migration-updater.php';
        }

        // Properly hook the meta box registration
        add_action('add_meta_boxes', [$this, 'register_old_embed_media_metabox']);

        add_action('admin_notices', function() {
            // Only show if the backup is not done
            if ( get_option('wpex_migration_backup_complete') ) {
                return;
            }

            global $pagenow;
            // Only show on Updates and Themes screens
            if ( ! in_array($pagenow, array('update-core.php', 'themes.php')) ) {
                return;
            }

            // Handle the "Mark as Done" button click
            if (
                isset($_GET['wpex_mark_backup_done']) &&
                $_GET['wpex_mark_backup_done'] === '1' &&
                check_admin_referer('wpex_mark_backup_done')
            ) {
                update_option('wpex_migration_backup_complete', 1);
                // Redirect to remove the GET parameter and prevent resubmission
                wp_safe_redirect( remove_query_arg(array('wpex_mark_backup_done', '_wpnonce')) );
                exit;
            }

            $plugin_search_url = admin_url('plugin-install.php?s=Download+Plugins+and+Themes+from+Dashboard&tab=search&type=term');
            $mark_done_url = wp_nonce_url(
                add_query_arg('wpex_mark_backup_done', '1'),
                'wpex_mark_backup_done'
            );
            ?>
            <div class="notice notice-warning">
                <p>
                    <strong>Before updating:</strong> For your safety, please
                    <a href="<?php echo esc_url($plugin_search_url); ?>" target="_blank">
                        install the "Download Plugins and Themes from Dashboard" plugin
                    </a>
                    to easily back up your current theme as a ZIP file from your dashboard.<br>
                    <em>Once you have completed your backup, click the button below to mark this step as done and dismiss this message.</em>
                </p>
                <p>
                    <a href="<?php echo esc_url($mark_done_url); ?>" class="button button-primary">
                        Mark Backup as Done
                    </a>
                </p>
            </div>
            <?php
        });
        add_action('init', ['WPEX_Tetris_Migration_Plugin', 'store_logo_on_activation']);
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

    public static function store_logo_on_activation() {
        $logo = get_theme_mod('wpex_logo');
        if ($logo) {
            add_option('wpex_temp_logo', $logo); // Unique option name!
        }
    }
}

// Initialize the plugin
new WPEX_Tetris_Migration_Plugin();

