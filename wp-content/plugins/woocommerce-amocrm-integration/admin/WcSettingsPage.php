<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Admin;

use Itgalaxy\Wc\AmoCrm\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\AmoCrm\Integration\Includes\CRM;
use AmoCRM\ClientOauthWc;
use Itgalaxy\Wc\AmoCrm\Integration\Includes\Helper;
use Itgalaxy\Wc\AmoCrm\Integration\Includes\PluginRequest;
use Itgalaxy\Wc\AmoCrm\Integration\Includes\AssetsHelper;

class WcSettingsPage
{
    private static $instance = false;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __construct()
    {
        add_action('admin_menu', [$this, 'addSubmenu'], 1000); // 1000 - fix priority for Admin Menu Editor
        add_action('wp_ajax_wcAmoAjaxValidate', [$this, 'wcAmoAjaxValidate']);
        add_action('wp_ajax_wcAmoAjaxSaveSettings', [$this, 'wcAmoAjaxSaveSettings']);
        add_action('wp_ajax_wcAmoAjaxAmoWebhook', [$this, 'wcAmoAjaxAmoWebhook']);
        add_action('wp_ajax_wcAmoAjaxRemoveLinksWithLead', [$this, 'wcAmoAjaxRemoveLinksWithLead']);

        if (isset($_GET['page']) && $_GET['page'] === Bootstrap::OPTIONS_KEY) {
            add_action('admin_enqueue_scripts', function () {
                wp_enqueue_style('wc-amocrm-admin', AssetsHelper::getPathAssetFile('/admin/css/app.css'), false, false);
                wp_enqueue_script('wc-amocrm-admin', AssetsHelper::getPathAssetFile('/admin/js/app.js'), false, false);
            });
        }

        // https://developer.wordpress.org/reference/hooks/admin_notices/
        add_action('admin_notices', [$this, 'notice']);
    }

    public function notice()
    {
        if (\get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY)) {
            return;
        }

        echo sprintf(
            '<div class="notice notice-error"><p><strong>%1$s</strong>: %2$s <a href="%3$s">%4$s</a></p></div>',
            esc_html__('WooCommerce - amoCRM - Integration', 'wc-amocrm-integration'),
            esc_html__(
                'Please verify the purchase code on the plugin settings page - ',
                'wc-amocrm-integration'
            ),
            esc_url(admin_url() . 'admin.php?page=wc-amocrm-integration-settings#wcamo-license-verify'),
            esc_html__('open', 'wc-amocrm-integration')
        );
    }

    public function wcAmoAjaxValidate()
    {
        parse_str(trim(wp_unslash($_POST['form'])), $data);

        $redirectUrl = isset($data['redirect-url']) ? wp_unslash($data['redirect-url']) : '';
        $domain = isset($data['domain']) ? trim(wp_unslash($data['domain']), '/') : '';
        $clientID = isset($data['client-id']) ? trim(wp_unslash($data['client-id'])) : '';
        $clientSecret = isset($data['client-secret']) ? trim(wp_unslash($data['client-secret'])) : '';
        $authCode = isset($data['authorization-code']) ? trim(wp_unslash($data['authorization-code'])) : '';

        if (!empty($clientID) && !empty($clientSecret) && !empty($domain) && empty($authCode)) {
            $setting = (array) get_option(Bootstrap::OPTIONS_KEY);

            $setting['enabled'] = isset($data['enabled']) ? wp_unslash($data['enabled']) : '';
            $setting['enabled_contact'] = isset($data['enabled_contact']) ? wp_unslash($data['enabled_contact']) : '';
            $setting['do_not_update_contact'] = isset($data['do_not_update_contact']) ? wp_unslash($data['do_not_update_contact']) : '';
            $setting['enabled_logging'] = isset($data['enabled_logging']) ? wp_unslash($data['enabled_logging']) : '';
            $setting['send_type'] = isset($data['send_type']) ? wp_unslash($data['send_type']) : '';

            update_option(Bootstrap::OPTIONS_KEY, $setting);

            $response = sprintf(
                '<div data-ui-component="wcamonotice" class="updated notice notice-success is-dismissible"><p>%s</p></div>',
                esc_html__('Integration settings check is successfully.', 'wc-amocrm-integration')
            );
        } elseif (empty($clientID) || empty($clientSecret) || empty($domain) || empty($authCode)) {
            $response = sprintf(
                '<div data-ui-component="wcamonotice" class="error notice notice-error is-dismissible"><p><strong>%1$s</strong>: %2$s</p></div>',
                esc_html__('ERROR', 'wc-amocrm-integration'),
                esc_html__('To integrate with amoCRM, your must fill domain, login and API key field.', 'wc-amocrm-integration')
            );
        } else {
            $setting = (array) get_option(Bootstrap::OPTIONS_KEY);

            $setting['redirect-url'] = $redirectUrl;
            $setting['domain'] = $domain;
            $setting['client-id'] = $clientID;
            $setting['client-secret'] = $clientSecret;
            $setting['authorization-code'] = $authCode;
            $setting['enabled'] = isset($data['enabled']) ? wp_unslash($data['enabled']) : '';
            $setting['enabled_contact'] = isset($data['enabled_contact']) ? wp_unslash($data['enabled_contact']) : '';
            $setting['do_not_update_contact'] = isset($data['do_not_update_contact']) ? wp_unslash($data['do_not_update_contact']) : '';
            $setting['enabled_logging'] = isset($data['enabled_logging']) ? wp_unslash($data['enabled_logging']) : '';
            $setting['send_type'] = isset($data['send_type']) ? wp_unslash($data['send_type']) : '';

            update_option(Bootstrap::OPTIONS_KEY, $setting);

            $response = CRM::checkConnection();

            if (empty($response)) {
                CRM::updateInformation();

                $response = sprintf(
                    '<div data-ui-component="wcamonotice" class="updated notice notice-success is-dismissible"><p>%s</p></div>',
                    esc_html__('Integration settings check is successfully.', 'wc-amocrm-integration')
                );
            }
        }

        echo $response;

        exit();
    }

    public function wcAmoAjaxSaveSettings()
    {
        parse_str(trim(wp_unslash($_POST['form'])), $data);

        $setting = (array) get_option(Bootstrap::OPTIONS_KEY);
        $data['domain'] = isset($setting['domain']) ? $setting['domain'] : '';
        $data['client-id'] = isset($setting['client-id']) ? $setting['client-id'] : '';
        $data['client-secret'] = isset($setting['client-secret']) ? $setting['client-secret'] : '';
        $data['authorization-code'] = isset($setting['authorization-code']) ? $setting['authorization-code'] : '';

        $data['enabled'] = isset($setting['enabled']) ? $setting['enabled'] : '';
        $data['enabled_contact'] = isset($setting['enabled_contact']) ? $setting['enabled_contact'] : '';
        $data['do_not_update_contact'] = isset($setting['do_not_update_contact']) ? $setting['do_not_update_contact'] : '';
        $data['enabled_logging'] = isset($setting['enabled_logging']) ? $setting['enabled_logging'] : '';
        $data['send_type'] = isset($setting['send_type']) ? $setting['send_type'] : '';

        update_option(Bootstrap::OPTIONS_KEY, $data);

        $response = sprintf(
            '<div data-ui-component="wcamonotice" class="updated notice notice-success is-dismissible"><p>%s</p></div>',
            esc_html__('Settings successfully updated.', 'wc-amocrm-integration')
        );

        echo $response;

        exit();
    }

    public function wcAmoAjaxAmoWebhook()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        switch ($_POST['whtype']) {
            case 'status_lead':
                $amo = new ClientOauthWc($settings['domain'], 'empty', $settings);

                if ($_POST['whaction'] == 'subscribe') {
                    $amo->webhooks->apiSubscribe(
                        get_site_url()
                        . '/wp-json/iwtbwcamo/v1/changeorderstatus/?secret='
                        . $settings['client-id'],
                        'status_lead'
                    );

                    update_option(Bootstrap::WEBHOOK_UPDATE_LEAD_STATUS_IS_SUBSCRIBED, true);
                } else {
                    $amo->webhooks->apiUnsubscribe(
                        get_site_url()
                        . '/wp-json/iwtbwcamo/v1/changeorderstatus/?secret='
                        . $settings['client-id'],
                        'status_lead'
                    );

                    update_option(Bootstrap::WEBHOOK_UPDATE_LEAD_STATUS_IS_SUBSCRIBED, '');
                }

                break;
        }

        if ($_POST['whaction'] == 'subscribe') {
            $response = sprintf(
                '<div data-ui-component="wcamonotice" class="updated notice notice-success is-dismissible"><p>%s</p></div>',
                esc_html__('Successfully subscribe.', 'wc-amocrm-integration')
            );
        } else {
            $response = sprintf(
                '<div data-ui-component="wcamonotice" class="updated notice notice-success is-dismissible"><p>%s</p></div>',
                esc_html__('Successfully unsubscribe.', 'wc-amocrm-integration')
            );
        }

        echo $response;

        exit();
    }

    public function wcAmoAjaxRemoveLinksWithLead()
    {
        if (!current_user_can('manage_woocommerce')) {
            exit();
        }

        global $wpdb;

        $wpdb->delete(
            $wpdb->postmeta,
            [
                'meta_key' => '_wc_amo_lead_id'
            ]
        );

        echo sprintf(
            '<div data-ui-component="wcamonotice" class="updated notice notice-success is-dismissible"><p>%s</p></div>',
            esc_html__('The link between orders and lead in amoCRM has been successfully deleted.', 'wc-amocrm-integration')
        );

        exit();
    }

    public function addSubmenu()
    {
        add_submenu_page(
            'woocommerce',
            esc_html__('amoCRM', 'wc-amocrm-integration'),
            esc_html__('amoCRM', 'wc-amocrm-integration'),
            'manage_woocommerce',
            Bootstrap::OPTIONS_KEY,
            [$this, 'settingsPage']
        );
    }

    public function settingsPage()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (isset($_POST['wcAmoCrmReloadFieldsCache'])) {
            CRM::updateInformation();

            echo sprintf(
                '<div data-ui-component="wcamonotice" class="updated notice notice-success is-dismissible"><p>%s</p></div>',
                esc_html__('Fields cache updated successfully.', 'wc-amocrm-integration')
            );
        }
        ?>
        <div id="poststuff" class="woocommerce-reports-wrap halved">
            <h1><?php esc_html_e('Integration settings', 'wc-amocrm-integration'); ?></h1>
            <p>
                <?php
                echo sprintf(
                    '%1$s <a href="%2$s" target="_blank">%3$s</a>. %4$s.',
                    esc_html__('Plugin documentation: ', 'wc-amocrm-integration'),
                    esc_url(WC_AMOCRM_PLUGIN_URL . 'documentation/index.html#step-1'),
                    esc_html__('open', 'wc-amocrm-integration'),
                    esc_html__(
                        'Or open the folder `documentation` in the plugin and open index.html',
                        'wc-amocrm-integration'
                    )
                );
                ?>
            </p>

            <form data-ui-component="integration-settings">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="enabled">
                                <?php esc_html_e('Enable send orders', 'wc-amocrm-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="hidden" value="0" id="enabled" name="enabled">
                            <input type="checkbox"
                                value="1"
                                <?php echo isset($settings['enabled']) && $settings['enabled'] == '1' ? 'checked' : ''; ?>
                                id="enabled"
                                name="enabled">
                            <small>
                                <?php
                                esc_html_e(
                                    'Create / update the status of the lead, and create / update the contact according to the order data..',
                                    'wc-amocrm-integration');
                                ?>
                            </small>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="enabled_contact">
                                <?php esc_html_e('Enable send users', 'wc-amocrm-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="hidden" value="0" id="enabled_contact" name="enabled_contact">
                            <input type="checkbox"
                                value="1"
                                <?php echo isset($settings['enabled_contact']) && $settings['enabled_contact'] == '1' ? 'checked' : ''; ?>
                                id="enabled_contact"
                                name="enabled_contact">
                            <small>
                                <?php
                                esc_html_e(
                                    'Create / update a contact when user registering / updates profile information.',
                                    'wc-amocrm-integration');
                                ?>
                            </small>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="do_not_update_contact">
                                <?php esc_html_e('Do not update contacts', 'wc-amocrm-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="hidden" value="0" id="do_not_update_contact" name="do_not_update_contact">
                            <input type="checkbox"
                                value="1"
                                <?php echo isset($settings['do_not_update_contact']) && $settings['do_not_update_contact'] == '1' ? 'checked' : ''; ?>
                                id="do_not_update_contact"
                                name="do_not_update_contact">
                            <small>
                                <?php
                                esc_html_e(
                                    'If there is an existing contact, do not change its data, just associate it with a new lead.',
                                    'wc-amocrm-integration');
                                ?>
                            </small>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e('Redirect URL', 'wc-amocrm-integration'); ?><span
                        </th>
                        <td>
                            <input type="url"
                                aria-required="true"
                                value="<?php
                                echo esc_url(Helper::getRedirectUrl());
                                ?>"
                                id="redirect-url"
                                name="redirect-url"
                                required
                                class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="domain">
                                <?php esc_html_e('Domain Name', 'wc-amocrm-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                aria-required="true"
                                value="<?php
                                echo isset($settings['domain'])
                                    ? esc_attr($settings['domain'])
                                    : '';
                                ?>"
                                required
                                id="domain"
                                placeholder="example.amocrm.ru"
                                name="domain"
                                class="regular-text">
                            <br>
                            <small>
                                <?php
                                esc_html_e(
                                    'domain of your amoCRM account (without http:// or https://).',
                                    'wc-amocrm-integration'
                                );
                                ?>
                            </small>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="client-secret">
                                <?php esc_html_e('Secret key', 'wc-amocrm-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                aria-required="true"
                                value="<?php
                                echo isset($settings['client-secret'])
                                    ? esc_attr($settings['client-secret'])
                                    : '';
                                ?>"
                                id="client-secret"
                                name="client-secret"
                                class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="client-id">
                                <?php esc_html_e('Integration ID', 'wc-amocrm-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                aria-required="true"
                                value="<?php
                                echo isset($settings['client-id'])
                                    ? esc_attr($settings['client-id'])
                                    : '';
                                ?>"
                                id="client-id"
                                name="client-id"
                                class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="authorization-code">
                                <?php esc_html_e('Authorization code', 'wc-amocrm-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                aria-required="true"
                                value="<?php
                                echo isset($settings['authorization-code'])
                                    ? esc_attr($settings['authorization-code'])
                                    : '';
                                ?>"
                                id="authorization-code"
                                name="authorization-code"
                                class="regular-text">
                            <br>
                            <small><?php esc_html_e('Authorization code - will remain empty after saving the '
                                    . 'settings, as it is used once to get the first token', 'wc-amocrm-integration');
                                ?></small>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="send_type">
                                <?php esc_html_e('Send type', 'wc-amocrm-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <select name="send_type" id="send_type">
                                <option value="immediately" <?php
                                echo empty($settings['send_type']) || $settings['send_type'] == 'immediately' ? 'selected' : '';
                                ?>>
                                    <?php esc_html_e('Immediately upon checkout', 'wc-amocrm-integration'); ?>
                                </option>
                                <option value="wp_cron" <?php
                                echo isset($settings['send_type']) && $settings['send_type'] == 'wp_cron' ? 'selected' : '';
                                ?>>
                                    <?php esc_html_e('WP Cron', 'wc-amocrm-integration'); ?>
                                </option>
                            </select>
                            <?php
                            if (
                                Helper::hasToken() &&
                                !empty($settings['send_type']) &&
                                $settings['send_type'] === 'wp_cron'
                            ) {
                                ?>
                                <p class="descripton">
                                    <?php esc_html_e('The number of registered order submit events pending', 'wc-amocrm-integration'); ?>:
                                    <strong>
                                        <?php echo (int) $this->getCountEvents(); ?>
                                    </strong>
                                </p>
                            <?php } ?>

                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="enabled_logging">
                                <?php esc_html_e('Enable logging', 'wc-amocrm-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="hidden" value="0" name="enabled_logging">
                            <input type="checkbox"
                                value="1"
                                <?php echo isset($settings['enabled_logging']) && $settings['enabled_logging'] == '1' ? 'checked' : ''; ?>
                                id="enabled_logging"
                                name="enabled_logging">
                            <br>
                            <small><?php echo esc_html(WC_AMOCRM_PLUGIN_LOG_FILE); ?></small>
                            <hr>
                            <a href="<?php echo esc_url(admin_url() . '?' . Bootstrap::OPTIONS_KEY . '-logs-get'); ?>"
                                class="button"
                                target="_blank">
                                <?php echo esc_html__('Download log', 'wc-amocrm-integration'); ?>
                            </a>
                            <a href="<?php echo esc_url(admin_url()); ?>admin.php?page=<?php echo Bootstrap::OPTIONS_KEY; ?>&<?php echo Bootstrap::OPTIONS_KEY; ?>-logs-clear"
                                class="button">
                                <?php echo esc_html__('Clear log', 'wc-amocrm-integration'); ?>
                            </a>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit"
                        class="button button-primary"
                        data-ui-component="save-integration-settings"
                        value="<?php esc_attr_e('Save', 'wc-amocrm-integration'); ?>"
                        name="submit">
                </p>
            </form>

            <?php if (Helper::hasToken()) { ?>
                <hr>
                <form action="" method="post">
                    <input
                        type="submit"
                        class="button button-primary"
                        name="wcAmoCrmReloadFieldsCache"
                        value="<?php esc_html_e('Reload fields data from CRM', 'wc-amocrm-integration'); ?>">
                </form>
                <hr>
                <h1><?php esc_html_e('Fields mapping', 'wc-amocrm-integration'); ?></h1>
                <form>
                    <strong>
                    <?php
                    esc_html_e(
                        'In the following fields, you can use these tags:',
                        'wc-amocrm-integration'
                    );

                    // compatibility with `WP Crowdfunding`
                    if (class_exists('Wpneo_Crowdfunding')) {
                        remove_filter(
                            'woocommerce_checkout_fields',
                            [\Wpneo_Crowdfunding::instance(), 'wpneo_override_checkout_fields']
                        );
                    }

                    // fix Woocommerce Poor Guys Swiss Knife
                    if (class_exists( 'WCPGSK_Main')) {
                        global $wcpgsk;

                        remove_action(
                            'woocommerce_checkout_init',
                            [$wcpgsk, 'wcpgsk_checkout_init']
                        );
                    }

                    // compatibility with `WC Fields Factory`
                    global $wcff;

                    if (is_object($wcff) && !empty($wcff->checkout) && is_object($wcff->checkout)) {
                        add_filter(
                            'woocommerce_checkout_fields',
                            [$wcff->checkout, 'wcccf_filter_checkout_fields']
                        );
                    }

                    // compatibility with `TcoWooCheckout WooCommerce Checkout Manager`
                    if (class_exists('Tco_Woo_Hooks')) {
                        remove_filter(
                            'woocommerce_checkout_fields',
                            ['Tco_Woo_Hooks', 'other_fields'],
                            99,
                            1
                        );
                    }

                    // compatibility with `Checkout Field Editor for WooCommerce`
                    if (class_exists('THWCFE_Public_Checkout')) {
                        $plugin = new \THWCFE_Public_Checkout(1, 1);
                        $plugin->define_public_hooks();

                        add_filter('thwcfe_show_field', function () {
                            return true;
                        });
                    }

                    if (class_exists('WC_Customer')) {
                        WC()->customer = new \WC_Customer();
                    }

                    // compatibility with `WooCommerce Shiptor`
                    if (class_exists('WC_Session_Handler')) {
                        WC()->session = new \WC_Session_Handler();
                    }

                    // compatibility with `Woo Checkout for Digital Goods`
                    if (class_exists('WC_Cart')) {
                        WC()->frontend_includes();
                        WC()->cart = new \WC_Cart();
                    }
                    ?>
                    </strong>
                    <table border="1" cellpadding="10" cellspacing="0">
                        <tbody>
                            <tr>
                                <td>
                                    <?php
                                    foreach (\WC()->checkout()->get_checkout_fields('billing') as $value => $field) {
                                        echo esc_html('[' . $value . '] - ' . $field['label']) . '<br>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    foreach (\WC()->checkout()->get_checkout_fields('shipping') as $value => $field) {
                                        echo esc_html('[' . $value . '] - ' . $field['label']) . '<br>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $orderCheckoutFields = \WC()->checkout()->get_checkout_fields('order');

                                    if ($orderCheckoutFields) {
                                        foreach ($orderCheckoutFields as $value => $field) {
                                            echo esc_html('[' . $value . '] - ' . $field['label']) . '<br>';
                                        }
                                    }

                                    // Supports `WooCommerce Checkout Add-Ons` custom checkout fields
                                    foreach ((array) get_option('wc_checkout_add_ons') as $id => $field) {
                                        if (!isset($field['name'])) {
                                            continue;
                                        }

                                        echo esc_html('[wc_checkout_add_on_' . $id . '] - ' . $field['name']) . '<br>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    Utm-fields: <span class="mailtag code">[utm_source]</span>
                                    <span class="mailtag code">[utm_medium]</span>
                                    <span class="mailtag code">[utm_campaign]</span>
                                    <span class="mailtag code">[utm_term]</span>
                                    <span class="mailtag code">[utm_content]</span>
                                    <br>
                                    Roistat-fields: <span class="mailtag code">[roistat_visit]</span>
                                    <br>
                                    GA fields: <span class="mailtag code">[gaClientID]</span>
                                    <br>
                                    Yandex fields: <span class="mailtag code">[yandexClientID]</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <?php
                                    esc_html_e(
                                        'Additional tags',
                                        'wc-amocrm-integration'
                                    );
                                    ?>:
                                    <br>
                                    <span class="mailtag code">[order_number]</span>
                                    <span class="mailtag code">[order_currency]</span>
                                    <span class="mailtag code">[order_total_without_shipping_and_tax]</span>
                                    <span class="mailtag code">[order_create_date]</span>
                                    <span class="mailtag code">[order_status_id]</span>
                                    <span class="mailtag code">[order_status_title]</span>
                                    <span class="mailtag code">[order_total_weight]</span>
                                    <span class="mailtag code">[first_product_title]</span>
                                    <span class="mailtag code">[payment_method_title]</span>
                                    <span class="mailtag code">[payment_method_id]</span>
                                    <span class="mailtag code">[shipping_method_title]</span>
                                    <span class="mailtag code">[shipping_method_id]</span>
                                    <span class="mailtag code">[shipping_price]</span>
                                    <span class="mailtag code">[order_admin_edit_link]</span>
                                    <span class="mailtag code">[order_product_sku_list]</span>
                                    <span class="mailtag code">[order_product_titles_list]</span>
                                    <span class="mailtag code">[order_coupon_list]</span>
                                    <?php if (class_exists('order_delivery_date_lite')) { ?>
                                        <span class="mailtag code">[order_delivery_date_lite]</span>
                                    <?php } ?>
                                    <?php if (class_exists('order_delivery_date')) { ?>
                                        <span class="mailtag code">[order_delivery_date]</span>
                                    <?php } ?>
                                    <span class="mailtag code">[order_product_cat_name_list]</span>
                                    <br>
                                    <br>
                                    <span class="mailtag code">[order_product_titles_by_product_cat_:cat_id]</span>
                                    - :cat_id this is `id` product category, for example [order_product_titles_by_product_cat_16]
                                    <hr>
                                    <?php
                                    esc_html_e(
                                        'You can use any meta key if you need custom meta data',
                                        'wc-amocrm-integration'
                                    );
                                    ?>:
                                    <br><br>
                                    For example: you somehow write in the meta data of the order any meta value on the meta key `_key1`, so that the value has been processed, specify the following short code [meta_key__key1].
                                    <br><br>
                                    Another couple of examples:<br>
                                    meta key = my_custom_key - shortcode = [meta_key_my_custom_key]<br>
                                    meta key = _my_custom_key - shortcode = [meta_key__my_custom_key]
                                    <br><br>
                                    That is, to form the correct shortcode, you need to add `meta_key_` to the name of the meta key, and put square brackets at the beginning and at the end.
                                    <br>Meta value must be written before the order is sent to crm.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <div id="tabs" data-ui-component="wc-amocrm-setting-tabs">
                        <ul>
                            <li>
                                <a href="#lead-fields">
                                    <?php esc_html_e('Lead fields', 'wc-amocrm-integration'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#task-fields">
                                    <?php esc_html_e('Task fields', 'wc-amocrm-integration'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#contact-fields">
                                    <?php esc_html_e('Contact fields', 'wc-amocrm-integration'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#company-fields">
                                    <?php esc_html_e('Company fields', 'wc-amocrm-integration'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#status-mapping">
                                    <?php esc_html_e('Status mapping', 'wc-amocrm-integration'); ?>
                                </a>
                            </li>
                        </ul>
                        <div id="lead-fields">
                            <strong><?php esc_html_e('Lead price - auto set', 'wc-amocrm-integration'); ?></strong>
                            <hr>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <label for="add_admin_order_link_note">
                                                <?php
                                                $value = isset($settings['add_admin_order_link_note']) ? $settings['add_admin_order_link_note'] : '';
                                                ?>
                                                <input type="hidden" name="add_admin_order_link_note" value="false">
                                                <input id="add_admin_order_link_note"
                                                    type="checkbox"
                                                    title="<?php esc_html_e('Add a link to the order on the site in the note', 'wc-amocrm-integration'); ?>"
                                                    <?php checked($value, 'true'); ?>
                                                    name="add_admin_order_link_note"
                                                    value="true">
                                                <?php esc_html_e('Add a link to the order on the site in the note', 'wc-amocrm-integration'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="create_task_to_deal">
                                                <?php
                                                $value = isset($settings['create_task_to_deal']) ? $settings['create_task_to_deal'] : '';
                                                ?>
                                                <input type="hidden" name="create_task_to_deal" value="false">
                                                <input id="create_task_to_deal"
                                                    type="checkbox"
                                                    <?php checked($value, 'true'); ?>
                                                    name="create_task_to_deal"
                                                    value="true">
                                                <?php esc_html_e('Create a task to deal', 'wc-amocrm-integration'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="resend_product_list">
                                                <?php
                                                $value = isset($settings['resend_product_list']) ? $settings['resend_product_list'] : '';
                                                ?>
                                                <input type="hidden" name="resend_product_list" value="false">
                                                <input id="resend_product_list"
                                                       type="checkbox"
                                                    <?php checked($value, 'true'); ?>
                                                       name="resend_product_list"
                                                       value="true">
                                                <?php
                                                esc_html_e(
                                                    'Re-send products from the order to the amoCRM. It will be useful '
                                                    . 'if you edit the order. Only if "Products" is supported and '
                                                    . 'enabled in your amoCRM.',
                                                    'wc-amocrm-integration'
                                                );
                                                ?>
                                            </label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <?php LeadSettings::getInstance()->render($settings); ?>
                            <hr>
                            <table class="form-table">
                                <tbody>
                                    <tr>
                                        <th style="width: 150px;">
                                            <?php esc_html_e('Additional note', 'wc-amocrm-integration'); ?>
                                        </th>
                                        <td>
                                            <?php
                                            $value = isset($settings['additional_lead_note'])
                                                ? $settings['additional_lead_note']
                                                : '';
                                            ?>
                                            <textarea id="additional_lead_note"
                                                class="large-text code"
                                                title="<?php esc_html_e('Additional note', 'wc-amocrm-integration'); ?>"
                                                name="additional_lead_note"
                                                rows="4"><?php echo esc_attr($value); ?></textarea>
                                            <p class="description">
                                                <?php
                                                esc_html_e(
                                                    'Please note that the plugin sends the standard order note '
                                                    . 'automatically.',
                                                    'wc-amocrm-integration'
                                                );
                                                ?>
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="task-fields">
                            <?php TaskSettings::getInstance()->render($settings); ?>
                        </div>
                        <div id="contact-fields">
                            <?php ContactSettings::getInstance()->render($settings); ?>
                        </div>
                        <div id="company-fields">
                            <?php CompanySettings::getInstance()->render($settings); ?>
                        </div>
                        <div id="status-mapping">
                            <table>
                                <tbody>
                                <tr>
                                    <td>
                                        <label for="do_not_post_status_changes">
                                            <?php
                                            $value = isset($settings['do_not_post_status_changes']) ? $settings['do_not_post_status_changes'] : '';
                                            ?>
                                            <input type="hidden" name="do_not_post_status_changes" value="false">
                                            <input id="do_not_post_status_changes"
                                                   type="checkbox"
                                                <?php checked($value, 'true'); ?>
                                                   name="do_not_post_status_changes"
                                                   value="true">
                                            <?php
                                            esc_html_e(
                                                'Do not post status changes',
                                                'wc-amocrm-integration'
                                            );
                                            ?>
                                        </label>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <hr>
                            <input type="hidden" name="lead_check_statuses" value="">
                            <table class="form-table">
                                <?php
                                $leadStatuses = isset($settings['lead_statuses']) ? $settings['lead_statuses'] : [];
                                $leadCheckStatuses = isset($settings['lead_check_statuses']) ? (array) $settings['lead_check_statuses'] : [];

                                foreach (wc_get_order_statuses() as $status => $label) {
                                    $value = str_replace('wc-', '', $status);
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo esc_html($label); ?>
                                        </td>
                                        <td>
                                            <select id="_lead_statuses_<?php echo esc_attr($value); ?>"
                                                name="lead_statuses[<?php echo esc_attr($value); ?>]">
                                                <option value=""><?php esc_html_e('Not chosen', 'wc-amocrm-integration'); ?></option>
                                                <?php
                                                $pipelines = get_option(Bootstrap::OPTIONS_PIPELINES);
                                                $currentValue = isset($leadStatuses[$value]) ? $leadStatuses[$value] : '';

                                                foreach ($pipelines as $pipelineID => $pipeline) {
                                                    if (empty($pipeline['statuses'])) {
                                                        continue;
                                                    }

                                                    echo '<optgroup label="' . esc_attr($pipeline['label']) . '">';

                                                    foreach ($pipeline['statuses'] as $statusID => $status) {
                                                        // show only deal stages
                                                        if ($status['type'] !== 0) {
                                                            continue;
                                                        }

                                                        $statusValue = $pipelineID . '.' . $statusID;
                                                        ?>
                                                        <option value="<?php echo esc_attr($statusValue); ?>"
                                                            <?php
                                                            echo !isset($settings['lead_statuses']) &&
                                                            $pipeline === reset($pipelines) &&
                                                            $status === array_values($pipeline['statuses'])[1]
                                                                ? ' selected '
                                                                : '';
                                                            ?>
                                                            <?php selected($currentValue, $statusValue); ?>>
                                                            <?php echo esc_attr($status['name']); ?>
                                                        </option>
                                                        <?php
                                                    }

                                                    echo '</optgroup>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <label>
                                                <input type="checkbox"
                                                    name="lead_check_statuses[]"
                                                    <?php echo in_array($value, $leadCheckStatuses) ? 'checked' : ''; ?>
                                                    value="<?php echo esc_attr($value); ?>">
                                                <?php esc_html_e('Check the availability of the transaction and if not found, then delete the order', 'wc-amocrm-integration'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                    <p class="submit">
                        <input type="submit"
                            class="button button-primary"
                            data-ui-component="wc-amo-save-settings"
                            value="<?php esc_attr_e('Save settings', 'wc-amocrm-integration'); ?>"
                            name="submit">
                    </p>
                </form>
                <hr>
                <h1><?php esc_html_e('Use amoCRM webhooks', 'wc-amocrm-integration'); ?></h1>
                <?php if (!get_option(Bootstrap::WEBHOOK_UPDATE_LEAD_STATUS_IS_SUBSCRIBED)) { ?>
                    <input type="submit"
                        class="button button-secondary"
                        data-action="subscribe"
                        data-type="status_lead"
                        data-ui-component="wc-amo-webhook"
                        value="<?php esc_attr_e('Subscribe lead status changes', 'wc-amocrm-integration'); ?>"
                        name="submit">
                <?php } else { ?>
                    <input type="submit"
                        class="button button-secondary"
                        data-action="unsubscribe"
                        data-type="status_lead"
                        data-ui-component="wc-amo-webhook"
                        value="<?php esc_attr_e('Unsubscribe lead status changes', 'wc-amocrm-integration'); ?>"
                        name="submit">
                <?php } ?>
                <br>
                <small><?php esc_html_e('You can use this if you want to accept changes in the status of a order, when the status changes in amoCRM', 'wc-amocrm-integration'); ?>.</small>
                <hr>
                <h1><?php esc_html_e('Additionally', 'wc-amocrm-integration'); ?></h1>
                <input type="submit"
                    class="button button-secondary"
                    data-alert-text="<?php esc_attr_e('Are you sure you want to do this? The action cannot be undone.', 'wc-amocrm-integration'); ?>"
                    data-ui-component="wc-amo-remove-links-with-lead"
                    value="<?php esc_attr_e('Remove from all orders information about lead ID from amoCRM', 'wc-amocrm-integration'); ?>"
                    name="submit">
                <br>
                <small>
                    <?php esc_html_e(
                        'You can use this if, for some reason, you want to remove information '
                        . 'about the created lead in amoCRM from all orders on the site, so you '
                        . 'get a situation as if orders have not yet been sent to amoCRM.',
                        'wc-amocrm-integration'
                    ); ?>.
                </small>
            <?php } ?>
            <hr>
            <?php
            if (isset($_POST['purchase-code'])) {
                $code = trim(wp_unslash($_POST['purchase-code']));

                $response = PluginRequest::call(
                    isset($_POST['verify']) ? 'code_activate' : 'code_deactivate',
                    $code
                );

                if (is_wp_error($response)) {
                    // fix network connection problems
                    if ($response->get_error_code() === 'http_request_failed') {
                        if (isset($_POST['verify'])) {
                            $messageContent = 'Success verify.';
                            update_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, $code);
                        } else {
                            $messageContent = 'Success unverify.';
                            update_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, '');
                        }

                        $message = 'successCheck';
                    } else {
                        $messageContent = '(Code - '
                            . $response->get_error_code()
                            . ') '
                            . $response->get_error_message();

                        $message = 'failedCheck';
                    }
                } else {
                    if ($response->status === 'successCheck' && isset($_POST['verify'])) {
                        update_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, $code);
                    } else {
                        update_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, '');
                    }

                    $messageContent = $response->message;
                    $message = $response->status;
                }

                if ($message == 'successCheck') {
                    echo sprintf(
                        '<div class="updated notice notice-success is-dismissible"><p>%s</p></div>',
                        esc_html($messageContent)
                    );
                } elseif ($messageContent) {
                    echo sprintf(
                        '<div class="error notice notice-error is-dismissible"><p>%s</p></div>',
                        esc_html($messageContent)
                    );
                }
            }

            $code = get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY);
            ?>
            <h1>
                <?php esc_html_e('License verification', 'wc-amocrm-integration'); ?>
                <?php if ($code) { ?>
                    - <small style="color: green;">
                        <?php esc_html_e('verified', 'wc-amocrm-integration'); ?>
                    </small>
                <?php } else { ?>
                    - <small style="color: red;">
                        <?php esc_html_e('please verify your purchase code', 'wc-amocrm-integration'); ?>
                    </small>
                <?php } ?>
            </h1>
            <form method="post" id="wcamo-license-verify" action="#wcamo-license-verify">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="purchase-code">
                                <?php esc_html_e('Purchase code', 'wc-amocrm-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                aria-required="true"
                                required
                                value="<?php
                                echo !empty($code)
                                    ? esc_attr($code)
                                    : '';
                                ?>"
                                id="purchase-code"
                                name="purchase-code"
                                class="large-text">
                            <small>
                                <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"
                                    target="_blank">
                                    <?php esc_html_e('Where Is My Purchase Code?', 'wc-amocrm-integration'); ?>
                                </a>
                            </small>
                        </td>
                    </tr>
                </table>
                <p>
                    <input type="submit"
                        class="button button-primary"
                        value="<?php esc_attr_e('Verify', 'wc-amocrm-integration'); ?>"
                        name="verify">
                    <?php if ($code) { ?>
                        <input type="submit"
                            class="button button-primary"
                            value="<?php esc_attr_e('Unverify', 'wc-amocrm-integration'); ?>"
                            name="unverify">
                    <?php } ?>
                </p>
            </form>
        </div>
        <?php
    }

    private function getCountEvents()
    {
        $cronJobs = get_option('cron', []);
        $count = 0;

        foreach ($cronJobs as $time => $cron) {
            if (empty($cron[Bootstrap::CRON_TASK_SEND])) {
                continue;
            }

            $count += count($cron[Bootstrap::CRON_TASK_SEND]);
        }

        return $count;
    }
}
