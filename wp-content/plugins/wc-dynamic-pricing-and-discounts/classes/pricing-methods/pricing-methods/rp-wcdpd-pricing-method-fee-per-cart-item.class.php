<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Pricing_Method')) {
    require_once('rp-wcdpd-pricing-method.class.php');
}

/**
 * Pricing Method Group: Fee Per Cart Item
 *
 * @class RP_WCDPD_Pricing_Method_Fee_Per_Cart_Item
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Pricing_Method_Fee_Per_Cart_Item extends RP_WCDPD_Pricing_Method
{

    protected $group_key        = 'fee_per_cart_item';
    protected $group_position   = 25;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook_group();
    }

    /**
     * Get group label
     *
     * @access public
     * @return string
     */
    public function get_group_label()
    {
        return __('Fee Per Cart Item', 'rp_wcdpd');
    }




}
