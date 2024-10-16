<?php
define('DB_NAME', 'wordpress');
define('DB_USER', 'wordpress');
define('DB_PASSWORD', 'wordpress_password');
define('DB_HOST', 'db');

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// 他のWordPress設定をここに追加...

$table_prefix = 'wp_';

if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

require_once(ABSPATH . 'wp-settings.php');