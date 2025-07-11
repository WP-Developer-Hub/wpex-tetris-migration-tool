<?php
/**
 * Dashboard widget: "What's New" (remote readme) and media meta migration in one widget.
 */

if (!defined('ABSPATH')) {
    exit;
}

if ( ! class_exists('WPEX_Tetris_Migration_Widget') ) :
class WPEX_Tetris_Migration_Widget {

    public function __construct() {
        add_action('wp_dashboard_setup', array($this, 'register_widget'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_dashboard_scripts'));
    }

    /**
     * Enqueue Marked.js and custom CSS only on the dashboard.
     */
    public function enqueue_dashboard_scripts($hook) {
        if ( $hook !== 'index.php' ) return; // Only on Dashboard
        wp_enqueue_script('marked-js', 'https://cdn.jsdelivr.net/npm/marked/marked.min.js', array(), null, true);
        wp_enqueue_style('wpex-tetris-dashboard-css', plugin_dir_url(__FILE__) . 'css/dashboard-widget.css', array(), null);
    }

    public function register_widget() {
        wp_add_dashboard_widget(
            'wpex_tetris_migration_widget',
            'WPEX Tetris: What\'s New & Media Meta Migration',
            array($this, 'widget_output')
        );
    }

    public function widget_output() {
        $markdown_url = 'https://raw.githubusercontent.com/WP-Developer-Hub/wpex-tetris/refs/heads/master/readme.txt';
        ?>
        <h2 class="nav-tab-wrapper" id="wpex-nav-tabs">
            <a href="#wpex-tab-1" class="nav-tab nav-tab-active">What's New</a>
            <a href="#wpex-tab-2" class="nav-tab">Migrate Metabox</a>
        </h2>
        <div id="wpex-tab-1" class="wpex-tab-content" style="display: block;">
            <div id="wpex-tetris-remote-readme"><em>Loading...</em></div>
        </div>
        <div id="wpex-tab-2" class="wpex-tab-content" style="display: none;">
            <div class="wpex-migration-section">
                <h1>What This Tool Does:</h1>
                <ul>
                    <li><b>Download the new WPEX Tetris fork.</b><br>
                        Ensures you have the latest version of the WPEX Tetris theme with up-to-date features and security fixes.</li>
                    <li><b>Migrate old media meta fields to the new universal meta keys for the <b>wpex-tetris</b> theme.</b><br>
                        Scans all your posts, converts old media-related custom fields to the new format, and deletes the old meta keys for a cleaner database.</li>
                    <li>
                        <b>All child themes based on the original <code>wpex-tetris</code> theme (by WPExplorer) may break or lose some functionality.</b><br>
                        Please review and test any customizations or child themes before running the migration, as changes to meta keys could affect inherited or custom features.
                    </li>
                </ul>
                <em>
                    Use this tool only once. All posts are scanned automatically. Old meta keys are deleted after migration, so the process cannot be undone.
                </em>
                <h1>Migration Mapping Table:</h1>
                <!-- Migration Mapping Table -->
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
                <!-- End Migration Mapping Table -->

                <?php
                if ( isset($_POST['wpex_tetris_migrate_media_meta_nonce']) && wp_verify_nonce($_POST['wpex_tetris_migrate_media_meta_nonce'], 'wpex_tetris_migrate_media_meta') ) {
                    $result = $this->wpex_migrate_media_meta();
                    if ( $result['migrated'] > 0 ) {
                        echo '<div class="notice notice-success"><p>Migrated ' . esc_html($result['migrated']) . ' posts. Skipped ' . esc_html($result['skipped']) . ' posts.</p></div>';
                    } else {
                        echo '<div class="notice notice-warning"><p>No posts needed migration.</p></div>';
                    }
                    if ( !empty($result['errors']) ) {
                        echo '<div class="notice notice-error"><p>Errors:<br>' . implode('<br>', array_map('esc_html', $result['errors'])) . '</p></div>';
                    }
                }
                ?>
                <form method="post">
                    <?php wp_nonce_field('wpex_tetris_migrate_media_meta', 'wpex_tetris_migrate_media_meta_nonce'); ?>
                    <input type="submit" class="button button-primary" value="Run Media Meta Migration" onclick="return confirm('Are you sure you want to migrate all media meta? This cannot be undone.');">
                </form>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            var tabs = document.querySelectorAll('#wpex-nav-tabs .nav-tab');
            var contents = document.querySelectorAll('.wpex-tab-content');
            tabs.forEach(function(tab) {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    tabs.forEach(function(t) { t.classList.remove('nav-tab-active'); });
                    contents.forEach(function(c) { c.style.display = 'none'; });
                    tab.classList.add('nav-tab-active');
                    var target = tab.getAttribute('href');
                    document.querySelector(target).style.display = 'block';
                });
            });

            // Fetch and render Markdown (requires Marked.js to be enqueued)
            fetch('<?php echo esc_js($markdown_url); ?>')
                .then(response => response.text())
                .then(md => {
                    if (window.marked) {
                        document.getElementById('wpex-tetris-remote-readme').innerHTML = marked.parse(md);
                    } else {
                        document.getElementById('wpex-tetris-remote-readme').textContent = md;
                    }
                })
                .catch(() => {
                    document.getElementById('wpex-tetris-remote-readme').innerHTML = '<p>Could not load Markdown file.</p>';
                });
        });
        </script>
        <?php
    }

    /**
     * Migration logic: migrate only 'post' post type.
     */
    public function wpex_migrate_media_meta() {
        $post_ids = get_posts( array(
            'post_type'      => 'post', // Only 'post'
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ) );

        $migrated = 0;
        $skipped = 0;
        $errors = array();

        foreach ($post_ids as $post_id) {
            // Get old meta values
            $image_ids  = get_post_meta($post_id, '_easy_image_gallery', true);
            $audio_urls = get_post_meta($post_id, 'post_audio_mp3', true);
            $video_urls = get_post_meta($post_id, 'post_video', true);
            $post_url   = get_post_meta($post_id, 'post_url', true);

            // Check if any old meta has data (not empty)
            if (
                empty($image_ids) &&
                empty($audio_urls) &&
                empty($video_urls) &&
                empty($post_url)
            ) {
                $skipped++;
                continue;
            }

            // Convert URLs to IDs (for audio/video, this only works if $audio_urls/$video_urls is a single URL)
            $audio_ids = attachment_url_to_postid($audio_urls);
            $video_ids = attachment_url_to_postid($video_urls);

            // Determine value for universal_oembed_url
            $oembed_url = '';
            if ( !empty($video_urls) ) {
                $oembed_url = $video_urls;
            } elseif ( !empty($post_url) ) {
                $oembed_url = $post_url;
            }

            // Save to new meta keys
            update_post_meta($post_id, 'universal_local_audio_attachment_ids', $audio_ids);
            update_post_meta($post_id, 'universal_local_video_attachment_ids', $video_ids);
            update_post_meta($post_id, 'universal_local_image_attachment_ids', $image_ids);
            update_post_meta($post_id, 'universal_oembed_url', esc_url_raw($oembed_url));

            // Remove old meta keys
            delete_post_meta($post_id, '_easy_image_gallery');
            delete_post_meta($post_id, 'post_audio_mp3');
            delete_post_meta($post_id, 'post_video');
            delete_post_meta($post_id, 'post_url');

            $migrated++;
        }

        return array(
            'migrated' => $migrated,
            'skipped'  => $skipped,
            'errors'   => $errors,
        );
    }
}

endif;

if ( class_exists('WPEX_Tetris_Migration_Widget') ) {
    new WPEX_Tetris_Migration_Widget();
}
