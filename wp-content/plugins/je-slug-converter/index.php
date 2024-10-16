<?php
/**
 * Plugin Name: Japanese to English Slug Converter
 * Plugin URI: http://example.com/plugin-name/
 * Description: Automatically converts Japanese slugs to English using Gemini API Pro.
 * Version: 1.0.0
 * Author: ShoheiTai
 * Author URI: https://wp.tech-style.info
 * License: GPL2
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('JE_SLUG_CONVERTER_VERSION', '1.0.0');
define('JE_SLUG_CONVERTER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JE_SLUG_CONVERTER_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the main plugin class
require_once JE_SLUG_CONVERTER_PLUGIN_DIR . 'includes/class-je-slug-converter.php';
require_once JE_SLUG_CONVERTER_PLUGIN_DIR . 'includes/class-plugin-settings-page.php';

if (is_admin()) {
    $je_slug_converter_settings = new JE_Slug_Converter_Settings();
}

// Instantiate the main plugin class
function run_je_slug_converter() {
    $plugin = new JE_Slug_Converter();
    $plugin->run();
}


// Run the plugin
run_je_slug_converter();
