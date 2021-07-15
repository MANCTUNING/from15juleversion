<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Includes;

class WpApiChangeOrderStatusWebHookProcessing
{
    private static $instance = false;

    protected function __construct()
    {
        if ($this->isEnabled()) {
            add_action('rest_api_init', [$this, 'registerApiRoute'], 10);
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function registerApiRoute()
    {
        register_rest_route(
            'iwtbwcamo/v1',
            '/changeorderstatus',
            [
                'methods' => 'POST',
                'callback' => [$this, 'changeOrderStatus'],
                'permission_callback' => '__return_true'
            ]
        );
    }

    public function changeOrderStatus($request)
    {
        $param = $request->get_param('secret');
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        Helper::log('amo webhook change lead status request');

        // secret not coincided
        if ($param != $settings['client-id']) {
            Helper::log('amo webhook key not coincided');

            exit();
        }

        if (empty($settings['lead_statuses'])) {
            Helper::log('amo webhook - status mapping not configured');

            exit();
        }

        $leadRequest = $request->get_body_params();

        if (
            empty($leadRequest['leads']) ||
            empty($leadRequest['leads']['status'])
        ) {
            Helper::log('amo webhook - empty leads or empty status', $leadRequest);

            exit();
        }

        // // delay is required since this can create an incorrect behavior with a reverse change in order status
        sleep(5);

        $orderToCrmClass = OrderToCrm::getInstance();
        remove_action('woocommerce_order_status_changed', [$orderToCrmClass, 'actionProcessing']);
        remove_action('woocommerce_after_order_object_save', [$orderToCrmClass, 'afterSaveOrder']);

        foreach ($leadRequest['leads']['status'] as $lead) {
            $orders = get_posts(
                [
                    'post_type' => 'shop_order',
                    'post_status' => 'any',
                    'numberposts' => 1,
                    'meta_query' => [
                        [
                            'key' => '_wc_amo_lead_id',
                            'value' => $lead['id']
                        ]
                    ]
                ]
            );

            if (empty($orders)) {
                Helper::log('amo webhook - order not exists - ' . $lead['id']);

                continue;
            }

            Helper::log('amo webhook - order by lead exists - ' . $lead['id'] . ', order id - ' . $orders[0]->ID);

            $order = wc_get_order($orders[0]->ID);
            $currentAmoStatus = $lead['pipeline_id'] . '.' . $lead['status_id'];

            Helper::log('amo webhook - current amo status - ' . $currentAmoStatus);

            $resultStatus = false;

            foreach ($settings['lead_statuses'] as $orderStatus => $amoStatus) {
                if ($currentAmoStatus !== $amoStatus) {
                    continue;
                }

                $resultStatus = $orderStatus;
            }

            if (!$resultStatus) {
                Helper::log('amo webhook - empty result order status - ignore');

                continue;
            }

            if ($resultStatus !== $order->get_status()) {
                Helper::log('amo webhook - set new order status - ' . $resultStatus);

                $order->update_status(
                    $resultStatus,
                    esc_html__('Order status changed from CRM', 'wc-amocrm-integration')
                );
            } else {
                Helper::log('amo webhook - current order status = result status - ' . $resultStatus);
            }
        }

        exit();
    }

    private function isEnabled()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        return isset($settings['enabled'])
            && $settings['enabled'] == '1'
            && Helper::hasToken();
    }

    private function __clone()
    {
        // Nothing
    }
}
