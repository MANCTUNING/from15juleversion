<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Admin;

class ShopOrderTableColumn
{
    private static $instance = false;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        // https://developer.wordpress.org/reference/hooks/manage_post-post_type_posts_custom_column/
        add_action('manage_shop_order_posts_custom_column', [$this, 'addLeadIdToNameValue'], 11, 2);
    }

    public function addLeadIdToNameValue($columnName, $postID)
    {
        if ($columnName === 'order_number') {
            $id = get_post_meta($postID, '_wc_amo_lead_id', true);

            echo '<br><strong>'
                . esc_html__('amo lead ID: ', 'wc-amocrm-integration')
                . '</strong>'
                . esc_html($id ? $id : esc_html__('no data', 'wc-amocrm-integration'));
        }
    }
}
