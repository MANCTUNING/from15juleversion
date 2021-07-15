<?php

namespace Itgalaxy\Wc\AmoCrm\Integration\Admin;

use Itgalaxy\Wc\AmoCrm\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\AmoCrm\Integration\Includes\Helper;

class WcBulkOrderToCrm
{
    private static $instance = false;

    protected function __construct()
    {
        if ($this->isEnabled()) {
            add_filter('bulk_actions-edit-shop_order', [$this, 'addItemInActionList'], 10, 1);
            add_filter('handle_bulk_actions-edit-shop_order', [$this, 'handleAction'], 10, 3);
            add_action('admin_notices', [$this, 'adminNotice']);
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function addItemInActionList($actions)
    {
        $actions['send_order_to_amocrm'] = esc_html__(
            'Send to amoCRM',
            'wc-amocrm-integration'
        );

        return $actions;
    }

    public function handleAction($redirectTo, $action, $ids)
    {
        if ($action !== 'send_order_to_amocrm') {
            return $redirectTo;
        }

        $ids = array_map('absint', $ids);

        Helper::log('register mass send', [$ids]);

        wp_schedule_single_event(time() + 15, Bootstrap::CRON_TASK_BULK_ORDERS, [$ids]);

        $redirectTo = add_query_arg(
            [
                'post_type' => 'shop_order',
                'send' => count($ids),
                'msg_status' => 1,
                'ids' => join(',', $ids),
            ],
            $redirectTo
        );

        return esc_url_raw($redirectTo);
    }

    public function adminNotice()
    {
        global $post_type, $pagenow;

        if ($pagenow !== 'edit.php' || $post_type !== 'shop_order') {
            return;
        }

        $messageStatus = isset($_GET['msg_status']) ? (int) $_GET['msg_status'] : '';

        if (empty($messageStatus)) {
            return;
        }

        $number = isset($_GET['send']) ? (int) $_GET['send'] : 0;

        $message = '';

        if ($messageStatus === 1) {
            $message = sprintf(
                esc_html__('%s orders registered for sending to CRM, the dispatch time depends on the number of orders.', 'wc-amocrm-integration'),
                number_format_i18n($number)
            );
        }

        echo '<div class="updated"><p>' . $message . '</p></div>';
    }

    private function isEnabled()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        return isset($settings['enabled'])
            && $settings['enabled'] == '1'
            && Helper::hasToken();
    }
}
