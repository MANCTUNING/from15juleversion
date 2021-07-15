<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Includes;

use AmoCRM\Client;
use AmoCRM\Exception;

class Cron
{
    private static $instance = false;

    protected function __construct()
    {
        add_action('init', [$this, 'createCron']);

        // not bind if run not cron mode
        if (!defined('DOING_CRON') || !DOING_CRON) {
            return;
        }

        add_action(Bootstrap::CRON_TASK_REMOVE_ORDERS, [$this, 'checkRemovedLeads']);
        add_action(Bootstrap::CRON_TASK_BULK_ORDERS, [$this, 'bulkSentCron'], 10, 1);
        add_action(Bootstrap::CRON_TASK_SEND, [$this, 'sendCronAction'], 10, 1);
        add_action(Bootstrap::CRON, [$this, 'cronAction']);
        add_filter('woocommerce_order_data_store_cpt_get_orders_query', [$this, 'handleCustomQueryVar'], 10, 2);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function createCron()
    {
        if (!wp_next_scheduled(Bootstrap::CRON_TASK_REMOVE_ORDERS)) {
            wp_schedule_event(time(), 'hourly', Bootstrap::CRON_TASK_REMOVE_ORDERS);
        }

        if (!wp_next_scheduled(Bootstrap::CRON)) {
            wp_schedule_event(time(), 'weekly', Bootstrap::CRON);
        }
    }

    public function checkRemovedLeads()
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY);

        $leadCheckStatuses = isset($settings['lead_check_statuses']) ? $settings['lead_check_statuses'] : [];

        if (empty($leadCheckStatuses)) {
            return false;
        }

        $orders = wc_get_orders(
            [
                'limit'  => -1,
                'return' => 'ids',
                'status'   => $leadCheckStatuses,
                'wc_amo_lead_id' => true
            ]
        );

        if (empty($orders)) {
            return false;
        }

        $leadIDs = [];

        foreach ($orders as $orderID) {
            $leadIDs[get_post_meta($orderID, '_wc_amo_lead_id', true)] = $orderID;
        }

        try {
            $amo = new Client($settings['domain'], $settings['login'], $settings['hash']);

            $entity = $amo->lead;

            $amoLeads = $entity->apiList(['id' => array_keys($leadIDs)]);

            foreach ($amoLeads as $amoLead) {
                if (isset($leadIDs[$amoLead['id']])) {
                    unset($leadIDs[$amoLead['id']]);
                }
            }

            if (!empty($leadIDs)) {
                foreach ($leadIDs as $orderID) {
                    \wp_trash_post($orderID);
                }
            }
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG === true) {
                printf(
                    'Error (%d): %s' . "\n",
                    (int) $e->getCode(),
                    esc_html($e->getMessage())
                );
            }
        }
    }

    public function handleCustomQueryVar($query, $query_vars)
    {
        if (isset($query_vars['wc_amo_lead_id'])) {
            $query['meta_query'][] = [
                'key' => '_wc_amo_lead_id',
                'value' => '',
                'compare' => '!='
            ];
        }

        return $query;
    }

    public function bulkSentCron($orderIds)
    {
        Helper::log('execute task mass send - start');

        if (!empty($orderIds)) {
            $orderSender = OrderToCrm::getInstance();

            $count = 0;
            $nextOrderSendIds = $orderIds;

            foreach ($orderIds as $key => $id) {
                if ($count >= 5) {
                    continue;
                }

                Helper::log('execute task mass send, send order - ', $id);

                $orderSender->orderSendCrm($id);
                unset($nextOrderSendIds[$key]);

                $count++;

                if ($count == 5 && !empty($nextOrderSendIds)) {
                    wp_schedule_single_event(time() + 15, Bootstrap::CRON_TASK_BULK_ORDERS, [$nextOrderSendIds]);
                }
            }
        }
    }

    public function sendCronAction($id)
    {
        $orderSender = OrderToCrm::getInstance();
        $orderSender->orderSendCrm($id);
    }

    public function cronAction()
    {
        $response = PluginRequest::call('cron_code_check');

        if (is_wp_error($response)) {
            return;
        }

        if ($response->status === 'stop') {
            update_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, '');
        }
    }

    private function isEnabled()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        return isset($settings['enabled'])
            && $settings['enabled'] == '1'
            && !empty($settings['domain'])
            && !empty($settings['login'])
            && !empty($settings['hash']);
    }

    private function __clone()
    {
        // Nothing
    }
}
