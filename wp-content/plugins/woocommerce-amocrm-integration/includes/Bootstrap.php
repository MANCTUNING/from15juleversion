<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Includes;

use Itgalaxy\Wc\AmoCrm\Integration\Admin\LogHelper;
use Itgalaxy\Wc\AmoCrm\Integration\Admin\ShopOrderTableColumn;
use Itgalaxy\Wc\AmoCrm\Integration\Admin\WcBulkOrderToCrm;
use Itgalaxy\Wc\AmoCrm\Integration\Admin\WcSettingsPage;

class Bootstrap
{
    const OPTIONS_KEY = 'wc-amocrm-integration-settings';
    const TOKEN_DATA_KEY = 'wc-amocrm-token-data';
    const PURCHASE_CODE_OPTIONS_KEY = 'wc-amocrm-purchase-code';

    const CRON_TASK_REMOVE_ORDERS = 'wc-amocrm-remove-orders-cron-task';
    const CRON_TASK_BULK_ORDERS = 'wc-amocrm-bulk-order-sent-to-crm';
    const CRON_TASK_SEND = 'wc-amocrm-send-cron-task';
    const CRON = 'wc-amocrm-cron';

    const OPTIONS_PIPELINES = 'wc-amocrm-pipelines';
    const OPTIONS_CUSTOM_FIELDS = 'wc-amocrm-custom-fields';
    const OPTIONS_USERS = 'wc-amocrm-users';

    const UTM_COOKIE = 'wc-amocrm-utm-cookie';

    const WEBHOOK_UPDATE_LEAD_STATUS_IS_SUBSCRIBED = 'wc-amocrm-webhook-update-lead';

    public static $plugin = '';

    public static $sendTypes = [
        'leads',
        'unsorted'
    ];

    private static $instance = false;

    protected function __construct($file)
    {
        self::$plugin = $file;

        register_activation_hook(
            self::$plugin,
            ['Itgalaxy\Wc\AmoCrm\Integration\Includes\Bootstrap', 'pluginActivation']
        );
        register_deactivation_hook(
            self::$plugin,
            ['Itgalaxy\Wc\AmoCrm\Integration\Includes\Bootstrap', 'pluginDeactivation']
        );

        self::fixedOldTokenData();

        Cron::getInstance();
        OrderToCrm::getInstance();
        CustomerToCrm::getInstance();
        WpApiChangeOrderStatusWebHookProcessing::getInstance();

        if (is_admin()) {
            add_action('plugins_loaded', function () {
                ShopOrderTableColumn::getInstance();
                WcBulkOrderToCrm::getInstance();
                WcSettingsPage::getInstance();
                LogHelper::getInstance();
            });
        }

        add_action('init', [$this, 'utmCookies']);
    }

    public static function getInstance($file)
    {
        if (!self::$instance) {
            self::$instance = new self($file);
        }

        return self::$instance;
    }

    public function utmCookies()
    {
        if (isset($_GET['utm_source'])) {
            setcookie(
                self::UTM_COOKIE,
                wp_json_encode([
                    'utm_source' => isset($_GET['utm_source']) ? wp_unslash($_GET['utm_source']) : '',
                    'utm_medium' => isset($_GET['utm_medium']) ? wp_unslash($_GET['utm_medium']) : '',
                    'utm_campaign' => isset($_GET['utm_campaign']) ? wp_unslash($_GET['utm_campaign']) : '',
                    'utm_content' => isset($_GET['utm_content']) ? wp_unslash($_GET['utm_content']) : '',
                    'utm_term' => isset($_GET['utm_term']) ? wp_unslash($_GET['utm_term']) : ''
                ]),
                time() + 86400,
                '/'
            );
        }
    }

    public static function pluginActivation()
    {
        PluginRequest::call('plugin_activate');

        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            wp_die(
                esc_html__(
                    'To run the plug-in, you must first install and activate the Woocommerce plugin.',
                    'wc-amocrm-integration'
                ),
                esc_html__(
                    'Error while activating the Woocommerce - amoCRM - Integration',
                    'wc-amocrm-integration'
                ),
                [
                    'back_link' => true
                ]
            );
            // Escape ok
        }

        $roles = new \WP_Roles();

        foreach (self::capabilities() as $capGroup) {
            foreach ($capGroup as $cap) {
                $roles->add_cap('administrator', $cap);

                if (is_multisite()) {
                    $roles->add_cap('super_admin', $cap);
                }
            }
        }
    }

    public static function pluginDeactivation()
    {
        PluginRequest::call('plugin_deactivate');
        \wp_clear_scheduled_hook(self::CRON_TASK_REMOVE_ORDERS);
        \wp_clear_scheduled_hook(self::CRON);
    }


    public static function pluginUninstall()
    {
        PluginRequest::call('plugin_uninstall');
    }

    public static function capabilities()
    {
        $capabilities = [];
        $capabilities['core'] = ['manage_' . self::OPTIONS_KEY];
        flush_rewrite_rules(true);

        return $capabilities;
    }

    private function fixedOldTokenData()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        if (empty($settings['refresh_token'])) {
            return;
        }

        $tokenData = [
            'access_token' => $settings['access-token'],
            'refresh_token' => $settings['refresh_token'],
            'expires_in' => time(),
        ];

        unset($settings['access-token']);
        unset($settings['refresh_token']);

        update_option(Bootstrap::OPTIONS_KEY, $settings);
        update_option(Bootstrap::TOKEN_DATA_KEY, $tokenData);
    }

    private function __clone()
    {
        // Nothing
    }
}
