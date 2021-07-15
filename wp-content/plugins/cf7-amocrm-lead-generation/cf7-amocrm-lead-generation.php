<?php
/**
 * Plugin Name: Contact Form 7 - amoCRM - Integration
 * Plugin URI: https://codecanyon.net/item/contact-form-7-amocrm-lead-generation/20129763
 * Description: Allows you to integrate your forms and amoCRM
 * Version: 2.4.8
 * Author: itgalaxycompany
 * Author URI: https://codecanyon.net/user/itgalaxycompany
 * License: GPLv3
 * Text Domain: cf7-amocrm-integration
 * Domain Path: /languages/
 */

use Itgalaxy\Cf7\AmoCRM\Integration\Includes\Bootstrap;

if (!defined('ABSPATH')) {
    exit();
}

/*
 * Require for `is_plugin_active` function.
 */
require_once ABSPATH . 'wp-admin/includes/plugin.php';

load_theme_textdomain('cf7-amocrm-integration', __DIR__ . '/languages');

define('CF7_AMOCRM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CF7_AMOCRM_PLUGIN_DIR', __DIR__);
define('CF7_AMOCRM_PLUGIN_VERSION', '2.4.8');

if (!defined('CF7_AMOCRM_PLUGIN_LOG_FILE')) {
    define('CF7_AMOCRM_PLUGIN_LOG_FILE', wp_upload_dir()['basedir'] . '/logs/.cf7amo.log');
}

require __DIR__ . '/vendor/autoload.php';

register_uninstall_hook(
    __FILE__,
    ['Itgalaxy\Cf7\AmoCRM\Integration\Includes\Bootstrap', 'pluginUninstall']
);

Bootstrap::getInstance(__FILE__);
