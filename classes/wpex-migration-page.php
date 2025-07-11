<?php
/**
 * Admin Page: WPEX Migration (Welcome, Migrate Customizer, Migrate Metabox)
 */

if (!defined('ABSPATH')) exit;

if ( ! class_exists('WPEX_Migration_Page') ) :
class WPEX_Migration_Page {

    public function __construct() {
        add_action('admin_menu', array($this, 'register_admin_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function register_admin_page() {
        add_menu_page(
            'WPEX Migration',
            'WPEX Migration',
            'manage_options',
            'wpex-migration',
            array($this, 'render_admin_page'),
            'dashicons-migrate',
            3
        );
    }

    public function enqueue_admin_scripts($hook) {
        if ( $hook !== 'toplevel_page_wpex-migration' ) return;
        wp_enqueue_style('wpex-migration-admin-css', plugin_dir_url(__FILE__) . 'css/dashboard-widget.css', array(), null);
    }

    public function render_admin_page() {
        // Completion flags
        $updater_done     = get_option('wpex_migration_updater_complete');
        $backup_done      = get_option('wpex_migration_backup_complete');
        $customizer_done  = get_option('wpex_migration_customizer_complete');
        $metabox_done     = get_option('wpex_migration_metabox_complete');

        // Determine which tab is active (default: welcome)
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'welcome';
        ?>
        <div class="wrap">
            <h1>WPEX Migration</h1>

            <!-- Tabs -->
            <h2 class="nav-tab-wrapper wpex-guide-tabs" id="wpex-nav-tabs">
                <a href="?page=wpex-migration&tab=welcome" class="nav-tab<?php if ($active_tab == 'welcome') echo ' nav-tab-active'; ?>">
                    <span class="dashicons dashicons-admin-home"></span> Welcome
                </a>
                <a href="?page=wpex-migration&tab=customizer" class="nav-tab<?php if ($active_tab == 'customizer') echo ' nav-tab-active'; ?><?php if ($customizer_done) echo ' wpex-tab-disabled'; ?>">
                    <span class="dashicons dashicons-admin-customizer"></span> Migrate Customizer
                    <?php if ($customizer_done): ?>
                        <span class="wpex-completed-badge">Completed</span>
                    <?php endif; ?>
                </a>
                <a href="?page=wpex-migration&tab=metabox" class="nav-tab<?php if ($active_tab == 'metabox') echo ' nav-tab-active'; ?><?php if ($metabox_done) echo ' wpex-tab-disabled'; ?>">
                    <span class="dashicons dashicons-database"></span> Migrate Metabox
                    <?php if ($metabox_done): ?>
                        <span class="wpex-completed-badge">Completed</span>
                    <?php endif; ?>
                </a>
            </h2>

            <!-- Welcome Tab -->
            <?php if ($active_tab == 'welcome'): ?>
            <div id="wpex-tab-welcome" class="wpex-tab-content" style="display: block;">
                <div class="wpex-migration-checklist">
                    <h2 style="font-size: 1.2em; margin-bottom: 12px;">Migration Checklist</h2>
                    <ul>
                        <li>
                            <span class="dashicons dashicons-<?php echo $backup_done ? 'yes' : 'marker'; ?> wpex-check-icon<?php if ($backup_done) echo ' wpex-check-complete'; ?>"></span>
                            Backup theme
                            <?php if ($backup_done): ?>
                                <span class="wpex-check-label">Completed</span>
                            <?php endif; ?>
                        </li>
                        <li>
                            <span class="dashicons dashicons-<?php echo $updater_done ? 'yes' : 'marker'; ?> wpex-check-icon<?php if ($updater_done) echo ' wpex-check-complete'; ?>"></span>
                            Updater
                            <?php if ($updater_done): ?>
                                <span class="wpex-check-label">Completed</span>
                            <?php endif; ?>
                        </li>
                        <li>
                            <span class="dashicons dashicons-<?php echo $customizer_done ? 'yes' : 'marker'; ?> wpex-check-icon<?php if ($customizer_done) echo ' wpex-check-complete'; ?>"></span>
                            Customizer Migration
                            <?php if ($customizer_done): ?>
                                <span class="wpex-check-label">Completed</span>
                            <?php endif; ?>
                        </li>
                        <li>
                            <span class="dashicons dashicons-<?php echo $metabox_done ? 'yes' : 'marker'; ?> wpex-check-icon<?php if ($metabox_done) echo ' wpex-check-complete'; ?>"></span>
                            Metabox Migration
                            <?php if ($metabox_done): ?>
                                <span class="wpex-check-label">Completed</span>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
                <p>Welcome to the WPEX Migration tool. Please complete each step in order. Tabs will deactivate after each migration step is finished.</p>
            </div>
            <?php endif; ?>

            <!-- Migrate Customizer Tab -->
            <?php if ($active_tab == 'customizer'): ?>
            <div id="wpex-tab-customizer" class="wpex-tab-content" style="display: block;">
                <div class="wpex-guide-step">
                    <h2><span class="dashicons dashicons-admin-customizer"></span> Migrate Theme Customizer Settings
                        <?php if ($customizer_done): ?>
                            <span class="wpex-completed-badge">Completed</span>
                        <?php endif; ?>
                    </h2>
                    <p>This will move your logo and copyright info to the new theme options.</p>
                    <div class="wpex-migration-table">
                        <h3>Customizer Migration Mapping Table</h3>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Setting</th>
                                    <th>Old Key</th>
                                    <th></th>
                                    <th>New Key</th>
                                    <th>Icon</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Logo</td>
                                    <td><code>wpex_logo</code></td>
                                    <td><span class="dashicons dashicons-arrow-right-alt"></span></td>
                                    <td><code>custom_logo</code></td>
                                    <td><span class="dashicons dashicons-format-image"></span></td>
                                    <td>Theme mod moved to WP core logo</td>
                                </tr>
                                <tr>
                                    <td>Copyright</td>
                                    <td><code>wpex_copyright</code></td>
                                    <td><span class="dashicons dashicons-arrow-right-alt"></span></td>
                                    <td><code>universal_copyright_layout</code></td>
                                    <td><span class="dashicons dashicons-admin-site"></span></td>
                                    <td>Theme mod moved to new key</td>
                                </tr>
                                <tr>
                                    <td>Social Links</td>
                                    <td><code>wpex_social_*</code></td>
                                    <td><span class="dashicons dashicons-dismiss" style="color:#dc3232"></span></td>
                                    <td><em>Dropped</em></td>
                                    <td><span class="dashicons dashicons-share"></span></td>
                                    <td><b>Dropped</b>: No longer supported</td>
                                </tr>
                                <tr>
                                    <td>Header Aside</td>
                                    <td><code>wpex_header_aside</code></td>
                                    <td><span class="dashicons dashicons-dismiss" style="color:#dc3232"></span></td>
                                    <td><em>Dropped</em></td>
                                    <td><span class="dashicons dashicons-editor-alignright"></span></td>
                                    <td><b>Dropped</b>: No longer supported</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <form method="post" style="margin-top: 1em;" <?php if ($customizer_done) echo 'class="wpex-disabled"'; ?>>
                        <?php wp_nonce_field('wpex_tetris_migrate_customizer', 'wpex_tetris_migrate_customizer_nonce'); ?>
                        <input type="submit" class="button button-primary" value="Migrate Customizer Settings"
                            <?php if ($customizer_done) echo 'disabled'; ?>
                            onclick="return confirm('Migrate customizer settings? This cannot be undone.');">
                    </form>
                    <?php
                    if (isset($_POST['wpex_tetris_migrate_customizer_nonce']) && wp_verify_nonce($_POST['wpex_tetris_migrate_customizer_nonce'], 'wpex_tetris_migrate_customizer') && !$customizer_done) {
                        $result = $this->wpex_migrate_customizer();
                        if ($result['migrated'] > 0) {
                            echo '<div class="notice notice-success wpex-guide-notice"><p>Migrated ' . esc_html($result['migrated']) . ' settings. Skipped ' . esc_html($result['skipped']) . '.</p></div>';
                        } else {
                            echo '<div class="notice notice-warning wpex-guide-notice"><p>No settings needed migration.</p></div>';
                        }
                        if (!empty($result['errors'])) {
                            echo '<div class="notice notice-error wpex-guide-notice"><p>Errors:<br>' . implode('<br>', array_map('esc_html', $result['errors'])) . '</p></div>';
                        }
                        update_option('wpex_migration_customizer_complete', 1);
                        echo '<script>location.href="?page=wpex-migration&tab=customizer";</script>';
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Migrate Metabox Tab -->
            <?php if ($active_tab == 'metabox'): ?>
            <div id="wpex-tab-metabox" class="wpex-tab-content" style="display: block;">
                <div class="wpex-guide-step">
                    <h2><span class="dashicons dashicons-database"></span> Migrate Post Metabox Fields
                        <?php if ($metabox_done): ?>
                            <span class="wpex-completed-badge">Completed</span>
                        <?php endif; ?>
                    </h2>
                    <p>This will migrate all post media fields to the new format.</p>
                    <div class="wpex-migration-table">
                        <h3>Migration Mapping Table</h3>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Old Meta Key</th>
                                    <th></th>
                                    <th>New Meta Key</th>
                                    <th>Icon</th>
                                    <th>Migration Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Image</td>
                                    <td><code>_easy_image_gallery</code></td>
                                    <td><span class="dashicons dashicons-arrow-right-alt"></span></td>
                                    <td><code>universal_local_image_attachment_ids</code></td>
                                    <td><span class="dashicons dashicons-format-image"></span></td>
                                    <td>1:1 (IDs stay IDs)</td>
                                </tr>
                                <tr>
                                    <td>Audio</td>
                                    <td><code>post_audio_mp3</code></td>
                                    <td><span class="dashicons dashicons-arrow-right-alt"></span></td>
                                    <td><code>universal_local_audio_attachment_ids</code></td>
                                    <td><span class="dashicons dashicons-format-audio"></span></td>
                                    <td>URL → attachment ID</td>
                                </tr>
                                <tr>
                                    <td>Video / Link / Embed</td>
                                    <td><code>post_video</code> or <code>post_url</code></td>
                                    <td><span class="dashicons dashicons-arrow-right-alt"></span></td>
                                    <td><code>universal_oembed_url</code></td>
                                    <td>
                                        <span class="dashicons dashicons-format-video"></span>
                                        <span class="dashicons dashicons-admin-links"></span>
                                    </td>
                                    <td>
                                        <b>Dropped for now</b><br>
                                        (May be added back later if demand is high. Previously: URL would stay URL.)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p>
                            <strong>Keys dropped after migration:</strong>
                            <code>_easy_image_gallery</code>,
                            <code>post_audio_mp3</code>,
                            <code>post_video</code>,
                            <code>post_url</code>
                        </p>
                    </div>
                    <form method="post" <?php if ($metabox_done) echo 'class="wpex-disabled"'; ?>>
                        <?php wp_nonce_field('wpex_tetris_migrate_metabox', 'wpex_tetris_migrate_metabox_nonce'); ?>
                        <input type="submit" class="button button-primary" value="Migrate Metabox Fields"
                            <?php if ($metabox_done) echo 'disabled'; ?>
                            onclick="return confirm('Migrate all media meta? This cannot be undone.');">
                    </form>
                    <?php
                    if (isset($_POST['wpex_tetris_migrate_metabox_nonce']) && wp_verify_nonce($_POST['wpex_tetris_migrate_metabox_nonce'], 'wpex_tetris_migrate_metabox') && !$metabox_done) {
                        $result = $this->wpex_migrate_metabox();
                        if ($result['migrated'] > 0) {
                            echo '<div class="notice notice-success wpex-guide-notice"><p>Migrated ' . esc_html($result['migrated']) . ' posts. Skipped ' . esc_html($result['skipped']) . '.</p></div>';
                        } else {
                            echo '<div class="notice notice-warning wpex-guide-notice"><p>No posts needed migration.</p></div>';
                        }
                        if (!empty($result['errors'])) {
                            echo '<div class="notice notice-error wpex-guide-notice"><p>Errors:<br>' . implode('<br>', array_map('esc_html', $result['errors'])) . '</p></div>';
                        }
                        update_option('wpex_migration_metabox_complete', 1);
                        echo '<script>location.href="?page=wpex-migration&tab=metabox";</script>';
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
        <?php
    }

    // Migrate only theme customizer settings (theme mods)
    public function wpex_migrate_customizer() {
        $migrated = 0;
        $skipped = 0;
        $errors = [];

        $wpex_logo = get_theme_mod('wpex_logo');
        if (!empty($wpex_logo)) {
            set_theme_mod('custom_logo', $wpex_logo);
            remove_theme_mod('wpex_logo');
            $migrated++;
        } else {
            $skipped++;
        }

        $wpex_copyright = get_theme_mod('wpex_copyright');
        if (!empty($wpex_copyright)) {
            set_theme_mod('universal_copyright_layout', $wpex_copyright);
            remove_theme_mod('wpex_copyright');
            $migrated++;
        } else {
            $skipped++;
        }

        return [
            'migrated' => $migrated,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ];
    }

    // Migrate post meta fields (metaboxes)
    public function wpex_migrate_metabox() {
        $post_ids = get_posts([
            'post_type'      => 'post',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]);

        $migrated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($post_ids as $post_id) {
            $image_ids  = get_post_meta($post_id, '_easy_image_gallery', true);
            $audio_urls = get_post_meta($post_id, 'post_audio_mp3', true);
            $video_urls = get_post_meta($post_id, 'post_video', true);
            $post_url   = get_post_meta($post_id, 'post_url', true);

            if (
                empty($image_ids) &&
                empty($audio_urls) &&
                empty($video_urls) &&
                empty($post_url)
            ) {
                $skipped++;
                continue;
            }

            $audio_ids = attachment_url_to_postid($audio_urls);
            $video_ids = attachment_url_to_postid($video_urls);

            $oembed_url = '';
            if (!empty($video_urls)) {
                $oembed_url = $video_urls;
            } elseif (!empty($post_url)) {
                $oembed_url = $post_url;
            }

            update_post_meta($post_id, 'universal_local_audio_attachment_ids', $audio_ids);
            update_post_meta($post_id, 'universal_local_video_attachment_ids', $video_ids);
            update_post_meta($post_id, 'universal_local_image_attachment_ids', $image_ids);
            update_post_meta($post_id, 'universal_oembed_url', esc_url_raw($oembed_url));

            delete_post_meta($post_id, '_easy_image_gallery');
            delete_post_meta($post_id, 'post_audio_mp3');
            delete_post_meta($post_id, 'post_video');
            delete_post_meta($post_id, 'post_url');

            $migrated++;
        }

        return [
            'migrated' => $migrated,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ];
    }
}
endif;

if ( class_exists('WPEX_Migration_Page') ) {
    new WPEX_Migration_Page();
}
