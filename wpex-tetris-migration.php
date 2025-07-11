<?php
/*
Plugin Name: WPEX Tetris Migration Tool
Description: Add the updater to download the new WPEX Tetris fork & migrate old media meta fields to new universal meta keys for the wpex-tetris theme via a dashboard widget.
Version: 1.0
Author: DJABHipHop
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
        require_once WPEX_TETRIS_CLASSES_DIR . 'wpex-tetris-migration-updater.php';
    }
}

// Initialize the plugin
new WPEX_Tetris_Migration_Plugin();
