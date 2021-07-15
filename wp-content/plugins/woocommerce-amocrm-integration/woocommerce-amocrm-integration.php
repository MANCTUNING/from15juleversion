<?php
/**
 * Plugin Name: WooCommerce - amoCRM - Integration
 * Plugin URI: https://codecanyon.net/item/woocommerce-amocrm-integration/21517442
 * Description: Allows you to integrate your WooCommerce and amoCRM
 * Version: 2.10.0
 * Author: itgalaxycompany
 * Author URI: https://codecanyon.net/user/itgalaxycompany
 * License: GPLv3
 * Text Domain: wc-amocrm-integration
 * Domain Path: /languages/
 */

use Itgalaxy\Wc\AmoCrm\Integration\Includes\Bootstrap;

if (!defined('ABSPATH')) {
    exit();
}

/*
 * Require for `is_plugin_active` function.
 */
require_once ABSPATH . 'wp-admin/includes/plugin.php';

/**
 * Not execute if WooCommerce not exists.
 *
 * @link https://developer.wordpress.org/reference/functions/is_plugin_active/
 */
if (!is_plugin_active('woocommerce/woocommerce.php')) {
    return;
}

define('WC_AMOCRM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WC_AMOCRM_PLUGIN_VERSION', '2.10.0');
define('WC_AMOCRM_PLUGIN_DIR', plugin_dir_path(__FILE__));

if (!defined('WC_AMOCRM_PLUGIN_LOG_FILE')) {
    define('WC_AMOCRM_PLUGIN_LOG_FILE', wp_upload_dir()['basedir'] . '/logs/wcamo.log');
}

/**
 * Registration and load of translations.
 *
 * @link https://developer.wordpress.org/reference/functions/load_theme_textdomain/
 */
load_theme_textdomain('wc-amocrm-integration', WC_AMOCRM_PLUGIN_DIR . 'languages');

/**
 * Use composer autoloader.
 */
require WC_AMOCRM_PLUGIN_DIR . 'vendor/autoload.php';

/**
 * Register plugin uninstall hook.
 *
 * @link https://developer.wordpress.org/reference/functions/register_uninstall_hook/
 */
register_uninstall_hook(__FILE__, ['Itgalaxy\Wc\AmoCrm\Integration\Includes\Bootstrap', 'pluginUninstall']);

/**
 * Load plugin.
 */
Bootstrap::getInstance(__FILE__);
