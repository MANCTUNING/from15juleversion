<?php if (!defined('ABSPATH')) {exit;} // Защита от прямого вызова скрипта
function yfym_export_page() { 
 $numFeed = '1'; // (string)
 if (isset($_REQUEST['yfym_submit_send_select_feed'])) {
  if (!empty($_POST) && check_admin_referer('yfym_nonce_action_send_select_feed', 'yfym_nonce_field_send_select_feed')) {
	$numFeed = $_POST['yfym_num_feed'];
  } 
 }

 if (isset($_GET['numFeed'])) {
	$numFeed = sanitize_text_field($_GET['numFeed']);
 }

 $status_sborki = (int)yfym_optionGET('yfym_status_sborki', $numFeed);

 if (isset($_REQUEST['yfym_submit_action'])) {
  if (!empty($_POST) && check_admin_referer('yfym_nonce_action', 'yfym_nonce_field')) {
	do_action('yfym_prepend_submit_action', $numFeed);  
	
	$numFeed = sanitize_text_field($_POST['yfym_num_feed_for_save']);
	
	$unixtime = current_time('timestamp', 1); // 1335808087 - временная зона GMT (Unix формат)
	yfym_optionUPD('yfym_date_save_set', $unixtime, $numFeed, 'yes', 'set_arr');

	if (isset($_POST['yfym_skip_missing_products'])) {
		yfym_optionUPD('yfym_skip_missing_products', sanitize_text_field($_POST['yfym_skip_missing_products']), $numFeed, 'yes', 'set_arr');
	} else {
		yfym_optionUPD('yfym_skip_missing_products', '0', $numFeed, 'yes', 'set_arr');
	}
	if (isset($_POST['yfym_skip_backorders_products'])) {
		yfym_optionUPD('yfym_skip_backorders_products', sanitize_text_field($_POST['yfym_skip_backorders_products']), $numFeed, 'yes', 'set_arr');
	} else {
		yfym_optionUPD('yfym_skip_backorders_products', '0', $numFeed, 'yes', 'set_arr');
	}
	if (isset($_POST['yfym_no_default_png_products'])) {
		yfym_optionUPD('yfym_no_default_png_products', sanitize_text_field($_POST['yfym_no_default_png_products']), $numFeed, 'yes', 'set_arr');
	} else {
		yfym_optionUPD('yfym_no_default_png_products', '0', $numFeed, 'yes', 'set_arr');
	}
	if (isset($_POST['yfym_skip_products_without_pic'])) {
		yfym_optionUPD('yfym_skip_products_without_pic', sanitize_text_field($_POST['yfym_skip_products_without_pic']), $numFeed, 'yes', 'set_arr');
	} else {
		yfym_optionUPD('yfym_skip_products_without_pic', '0', $numFeed, 'yes', 'set_arr');
	}
	if (isset($_POST['yfym_ufup'])) {
		yfym_optionUPD('yfym_ufup', sanitize_text_field($_POST['yfym_ufup']), $numFeed, 'yes', 'set_arr');
	} else {
		yfym_optionUPD('yfym_ufup', '0', $numFeed, 'yes', 'set_arr');
	} 
	yfym_optionUPD('yfym_desc', sanitize_text_field($_POST['yfym_desc']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_the_content', sanitize_text_field($_POST['yfym_the_content']), $numFeed, 'yes', 'set_arr');
	if (isset($_POST['yfym_var_desc_priority'])) {
		yfym_optionUPD('yfym_var_desc_priority', sanitize_text_field($_POST['yfym_var_desc_priority']), $numFeed, 'yes', 'set_arr');
	} else {
		yfym_optionUPD('yfym_var_desc_priority', '0', $numFeed, 'yes', 'set_arr');
	}	
	yfym_optionUPD('yfym_clear_get', sanitize_text_field($_POST['yfym_clear_get']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_barcode', sanitize_text_field($_POST['yfym_barcode']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_barcode_post_meta', sanitize_text_field($_POST['yfym_barcode_post_meta']), $numFeed, 'yes', 'set_arr');

	yfym_optionUPD('yfym_vendorcode', sanitize_text_field($_POST['yfym_vendorcode']), $numFeed, 'yes', 'set_arr');

	if (isset($_POST['yfym_enable_auto_discounts'])) {
		yfym_optionUPD('yfym_enable_auto_discounts', sanitize_text_field($_POST['yfym_enable_auto_discounts']), $numFeed, 'yes', 'set_arr');
		} else {
		yfym_optionUPD('yfym_enable_auto_discounts', '0', $numFeed, 'yes', 'set_arr');
	}
	yfym_optionUPD('yfym_expiry', sanitize_text_field($_POST['yfym_expiry']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_downloadable', sanitize_text_field($_POST['yfym_downloadable']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_age', sanitize_text_field($_POST['yfym_age']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_country_of_origin', sanitize_text_field($_POST['yfym_country_of_origin']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_source_id', sanitize_text_field($_POST['yfym_source_id']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_source_id_post_meta', sanitize_text_field($_POST['yfym_source_id_post_meta']), $numFeed, 'yes', 'set_arr');
	if (isset($_POST['yfym_ebay_stock'])) {
		yfym_optionUPD('yfym_ebay_stock', sanitize_text_field($_POST['yfym_ebay_stock']), $numFeed, 'yes', 'set_arr');
	} else {
		yfym_optionUPD('yfym_ebay_stock', '0', $numFeed, 'yes', 'set_arr');
	} 
	yfym_optionUPD('yfym_manufacturer_warranty', sanitize_text_field($_POST['yfym_manufacturer_warranty']), $numFeed, 'yes', 'set_arr');
  
	yfym_optionUPD('yfym_whot_export', sanitize_text_field($_POST['yfym_whot_export']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_feed_assignment', sanitize_text_field($_POST['yfym_feed_assignment']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_file_extension', sanitize_text_field($_POST['yfym_file_extension']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_yml_rules', sanitize_text_field($_POST['yfym_yml_rules']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_pickup', sanitize_text_field($_POST['yfym_pickup']), $numFeed, 'yes', 'set_arr');
	
	yfym_optionUPD('yfym_sales_notes_cat', sanitize_text_field($_POST['yfym_sales_notes_cat']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_sales_notes', sanitize_text_field($_POST['yfym_sales_notes']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_delivery', sanitize_text_field($_POST['yfym_delivery']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_store', sanitize_text_field($_POST['yfym_store']), $numFeed, 'yes', 'set_arr');

	yfym_optionUPD('yfym_order_before', sanitize_text_field($_POST['yfym_order_before']), $numFeed, 'yes', 'set_arr');	
	$yfym_delivery_cost = (int)sanitize_text_field($_POST['yfym_delivery_cost']);
	if ($yfym_delivery_cost >= 0 ) {
		yfym_optionUPD('yfym_delivery_cost', $yfym_delivery_cost, $numFeed, 'yes', 'set_arr'); 
	}	
	yfym_optionUPD('yfym_delivery_days', sanitize_text_field($_POST['yfym_delivery_days']), $numFeed, 'yes', 'set_arr');
	if (isset($_POST['yfym_delivery_options'])) {
		yfym_optionUPD('yfym_delivery_options', sanitize_text_field($_POST['yfym_delivery_options']), $numFeed, 'yes', 'set_arr');
	} else {
		yfym_optionUPD('yfym_delivery_options', '0', $numFeed, 'yes', 'set_arr');
	}

	yfym_optionUPD('yfym_order_before2', sanitize_text_field($_POST['yfym_order_before2']), $numFeed, 'yes', 'set_arr');	
	$yfym_delivery_cost2 = (int)sanitize_text_field($_POST['yfym_delivery_cost2']);
	if ($yfym_delivery_cost2 >= 0 ) {
		yfym_optionUPD('yfym_delivery_cost2', $yfym_delivery_cost2, $numFeed, 'yes', 'set_arr'); 
	}	
	yfym_optionUPD('yfym_delivery_days2', sanitize_text_field($_POST['yfym_delivery_days2']), $numFeed, 'yes', 'set_arr');
	if (isset($_POST['yfym_delivery_options2'])) {
		yfym_optionUPD('yfym_delivery_options2', sanitize_text_field($_POST['yfym_delivery_options2']), $numFeed, 'yes', 'set_arr');
	} else {
		yfym_optionUPD('yfym_delivery_options2', '0', $numFeed, 'yes', 'set_arr');
	}	
	
	yfym_optionUPD('yfym_shop_name', $_POST['yfym_shop_name'], $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_company_name', $_POST['yfym_company_name'], $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_currencies', $_POST['yfym_currencies'], $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_shop_sku', $_POST['yfym_shop_sku'], $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_count', sanitize_text_field($_POST['yfym_count']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_auto_disabled', sanitize_text_field($_POST['yfym_auto_disabled']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_market_sku_status', sanitize_text_field($_POST['yfym_market_sku_status']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_amount', sanitize_text_field($_POST['yfym_amount']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_manufacturer', $_POST['yfym_manufacturer'], $numFeed, 'yes', 'set_arr');

	yfym_optionUPD('yfym_main_product', sanitize_text_field($_POST['yfym_main_product']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_adult', sanitize_text_field($_POST['yfym_adult']), $numFeed, 'yes', 'set_arr');
	if (isset($_POST['yfym_wooc_currencies'])) {
		yfym_optionUPD('yfym_wooc_currencies', sanitize_text_field($_POST['yfym_wooc_currencies']), $numFeed, 'yes', 'set_arr');
	}
	yfym_optionUPD('yfym_vendor', sanitize_text_field($_POST['yfym_vendor']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_model', sanitize_text_field($_POST['yfym_model']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_separator_type', sanitize_text_field($_POST['yfym_separator_type']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_behavior_onbackorder', sanitize_text_field($_POST['yfym_behavior_onbackorder']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_behavior_stip_symbol', sanitize_text_field($_POST['yfym_behavior_stip_symbol']), $numFeed, 'yes', 'set_arr');

	if (isset($_POST['yfym_params_arr'])) {
		yfym_optionUPD('yfym_params_arr', serialize($_POST['yfym_params_arr']), $numFeed);
	} else {yfym_optionUPD('yfym_params_arr', serialize(array()), $numFeed);}
	if (isset($_POST['yfym_add_in_name_arr'])) {
		yfym_optionUPD('yfym_add_in_name_arr', serialize($_POST['yfym_add_in_name_arr']), $numFeed);
	} else {yfym_optionUPD('yfym_add_in_name_arr', serialize(array()), $numFeed);}
	if (isset($_POST['yfym_no_group_id_arr'])) {
		yfym_optionUPD('yfym_no_group_id_arr', serialize($_POST['yfym_no_group_id_arr']), $numFeed);
	} else {yfym_optionUPD('yfym_no_group_id_arr', serialize(array()), $numFeed);}
	yfym_optionUPD('yfym_price_from', sanitize_text_field($_POST['yfym_price_from']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_oldprice', sanitize_text_field($_POST['yfym_oldprice']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_vat', sanitize_text_field($_POST['yfym_vat']), $numFeed, 'yes', 'set_arr');
	yfym_optionUPD('yfym_step_export', sanitize_text_field($_POST['yfym_step_export']), $numFeed, 'yes', 'set_arr');
	
	$arr_maybe = array("off", "five_min", "hourly", "six_hours", "twicedaily", "daily");
	$yfym_run_cron = sanitize_text_field($_POST['yfym_run_cron']);
	if (in_array($yfym_run_cron, $arr_maybe)) {		
		yfym_optionUPD('yfym_status_cron', $yfym_run_cron, $numFeed, 'yes', 'set_arr');
		if ($yfym_run_cron === 'off') {
			// отключаем крон
			wp_clear_scheduled_hook('yfym_cron_period', array($numFeed));
			yfym_optionUPD('yfym_status_cron', 'off', $numFeed, 'yes', 'set_arr');
			
			wp_clear_scheduled_hook('yfym_cron_sborki', array($numFeed));
			yfym_optionUPD('yfym_status_sborki', '-1', $numFeed);
		} else {
			$recurrence = $yfym_run_cron;
			wp_clear_scheduled_hook('yfym_cron_period', array($numFeed));
			wp_schedule_event(time(), $recurrence, 'yfym_cron_period', array($numFeed));
			yfym_error_log('FEED № '.$numFeed.'; yfym_cron_period внесен в список заданий; Файл: export.php; Строка: '.__LINE__, 0);
		}
	} else {
		yfym_error_log('Крон '.$yfym_run_cron.' не зарегистрирован. Файл: export.php; Строка: '.__LINE__, 0);
	}
  }
 } 

 $yfym_status_cron = yfym_optionGET('yfym_status_cron', $numFeed, 'set_arr');
 $yfym_whot_export = yfym_optionGET('yfym_whot_export', $numFeed, 'set_arr'); 
 $yfym_feed_assignment = yfym_optionGET('yfym_feed_assignment', $numFeed, 'set_arr');
 $yfym_file_extension = yfym_optionGET('yfym_file_extension', $numFeed, 'set_arr');
 $yfym_yml_rules = yfym_optionGET('yfym_yml_rules', $numFeed, 'set_arr');
 $yfym_desc = yfym_optionGET('yfym_desc', $numFeed, 'set_arr');
 $yfym_the_content = yfym_optionGET('yfym_the_content', $numFeed, 'set_arr');
 $yfym_var_desc_priority = yfym_optionGET('yfym_var_desc_priority', $numFeed, 'set_arr');
 $yfym_clear_get = yfym_optionGET('yfym_clear_get', $numFeed, 'set_arr');
 $yfym_shop_name = stripslashes(htmlspecialchars(yfym_optionGET('yfym_shop_name', $numFeed, 'set_arr')));
 $yfym_company_name = stripslashes(htmlspecialchars(yfym_optionGET('yfym_company_name', $numFeed, 'set_arr')));
 $yfym_currencies = yfym_optionGET('yfym_currencies', $numFeed, 'set_arr');
 $yfym_shop_sku = yfym_optionGET('yfym_shop_sku', $numFeed, 'set_arr');
 $yfym_count = yfym_optionGET('yfym_count', $numFeed, 'set_arr');
 $yfym_auto_disabled = yfym_optionGET('yfym_auto_disabled', $numFeed, 'set_arr');
 $yfym_market_sku_status = yfym_optionGET('yfym_market_sku_status', $numFeed, 'set_arr');
 $yfym_amount = yfym_optionGET('yfym_amount', $numFeed, 'set_arr');
 $yfym_main_product = yfym_optionGET('yfym_main_product', $numFeed, 'set_arr');  
 $yfym_adult = yfym_optionGET('yfym_adult', $numFeed, 'set_arr'); 	
 $yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $numFeed, 'set_arr');
 $yfym_step_export = yfym_optionGET('yfym_step_export', $numFeed, 'set_arr'); 
 $yfym_no_group_id_arr = unserialize(yfym_optionGET('yfym_no_group_id_arr', $numFeed)); 
 $yfym_skip_missing_products = yfym_optionGET('yfym_skip_missing_products', $numFeed, 'set_arr');
 $yfym_skip_backorders_products = yfym_optionGET('yfym_skip_backorders_products', $numFeed, 'set_arr'); 
 $yfym_no_default_png_products = yfym_optionGET('yfym_no_default_png_products', $numFeed, 'set_arr');
 $yfym_skip_products_without_pic = yfym_optionGET('yfym_skip_products_without_pic', $numFeed, 'set_arr'); 
 $yfym_ufup = yfym_optionGET('yfym_ufup', $numFeed, 'set_arr');
 $yfym_delivery = yfym_optionGET('yfym_delivery', $numFeed, 'set_arr'); 
 $yfym_delivery_options = yfym_optionGET('yfym_delivery_options', $numFeed, 'set_arr');
 $yfym_delivery_cost = yfym_optionGET('yfym_delivery_cost', $numFeed, 'set_arr'); 
 $yfym_delivery_days = yfym_optionGET('yfym_delivery_days', $numFeed, 'set_arr'); 
 $yfym_order_before = yfym_optionGET('yfym_order_before', $numFeed, 'set_arr');
 $yfym_delivery_options2 = yfym_optionGET('yfym_delivery_options2', $numFeed, 'set_arr');
 $yfym_delivery_cost2 = yfym_optionGET('yfym_delivery_cost2', $numFeed, 'set_arr'); 
 $yfym_delivery_days2 = yfym_optionGET('yfym_delivery_days2', $numFeed, 'set_arr');  
 $yfym_order_before2 = yfym_optionGET('yfym_order_before2', $numFeed, 'set_arr');
 $yfym_pickup = yfym_optionGET('yfym_pickup', $numFeed, 'set_arr'); 
 $yfym_price_from = yfym_optionGET('yfym_price_from', $numFeed, 'set_arr'); 
 $yfym_oldprice = yfym_optionGET('yfym_oldprice', $numFeed, 'set_arr'); 
 $yfym_vat = yfym_optionGET('yfym_vat', $numFeed, 'set_arr');
 $yfym_barcode = yfym_optionGET('yfym_barcode', $numFeed, 'set_arr');
 $yfym_barcode_post_meta = yfym_optionGET('yfym_barcode_post_meta', $numFeed, 'set_arr');
 $yfym_vendorcode = yfym_optionGET('yfym_vendorcode', $numFeed, 'set_arr');

 $yfym_enable_auto_discounts = yfym_optionGET('yfym_enable_auto_discounts', $numFeed, 'set_arr'); 
 $expiry = yfym_optionGET('yfym_expiry', $numFeed, 'set_arr'); 
 $downloadable = yfym_optionGET('yfym_downloadable', $numFeed, 'set_arr');
 $age = yfym_optionGET('yfym_age', $numFeed, 'set_arr');
 $model = yfym_optionGET('yfym_model', $numFeed, 'set_arr'); 
 $yfym_separator_type = yfym_optionGET('yfym_separator_type', $numFeed, 'set_arr'); 
 $yfym_behavior_onbackorder = yfym_optionGET('yfym_behavior_onbackorder', $numFeed, 'set_arr'); 
 $yfym_behavior_stip_symbol = yfym_optionGET('yfym_behavior_stip_symbol', $numFeed, 'set_arr'); 
 $vendor = yfym_optionGET('yfym_vendor', $numFeed, 'set_arr'); 
 $country_of_origin = yfym_optionGET('yfym_country_of_origin', $numFeed, 'set_arr');
 $yfym_source_id = yfym_optionGET('yfym_source_id', $numFeed, 'set_arr');
 $yfym_source_id_post_meta = yfym_optionGET('yfym_source_id_post_meta', $numFeed, 'set_arr');
 $yfym_ebay_stock = yfym_optionGET('yfym_ebay_stock', $numFeed, 'set_arr'); 
 $yfym_manufacturer = yfym_optionGET('yfym_manufacturer', $numFeed, 'set_arr'); 
 $manufacturer_warranty = yfym_optionGET('yfym_manufacturer_warranty', $numFeed, 'set_arr'); 
 $sales_notes_cat = yfym_optionGET('yfym_sales_notes_cat', $numFeed, 'set_arr');
 $sales_notes = htmlspecialchars(yfym_optionGET('yfym_sales_notes', $numFeed, 'set_arr'));
 $params_arr = unserialize(yfym_optionGET('yfym_params_arr', $numFeed));
 $add_in_name_arr = unserialize(yfym_optionGET('yfym_add_in_name_arr', $numFeed)); 
 $yfym_store = yfym_optionGET('yfym_store', $numFeed, 'set_arr');
 $yfym_file_url = urldecode(yfym_optionGET('yfym_file_url', $numFeed, 'set_arr'));
 $yfym_date_sborki = yfym_optionGET('yfym_date_sborki', $numFeed, 'set_arr');
?>
<div class="wrap">
 <h1><?php _e('Exporter Yandex Market', 'yfym'); ?></h1>
 <?php $woo_version = yfym_get_woo_version_number();
	if ($woo_version <= 3.0 ) {
		print '<div class="notice notice-error is-dismissible"><p>'. __('For the plugin to function correctly, you need a version of WooCommerce 3.0 and higher! You have version ', 'yfym'). $woo_version . __(' installed. Please, update WooCommerce', 'yfym'). '! <a href="https://icopydoc.ru/minimalnye-trebovaniya-dlya-raboty-yml-for-yandex-market/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=update-woocommerce">'. __('Learn More', 'yfym'). '</a>.</p></div>';
	}
	if (defined('DISABLE_WP_CRON')) {
	 if (DISABLE_WP_CRON == true) {
		print '<div class="notice notice-error is-dismissible"><p>'. __('Most likely, the plugin does not work correctly because you turned off the CRON with the help of the ', 'yfym'). 'DISABLE_WP_CRON.</p></div>';
	 }
	}
	
    $check_global_attr_count = wc_get_attribute_taxonomies();
    if (count($check_global_attr_count) < 1) {
		print '<div class="notice notice-warning is-dismissible"><p>'. __('Your site has no global attributes! This may affect the quality of the YML feed. This can also cause difficulties when setting up the plugin', 'yfym'). '. <a href="https://icopydoc.ru/globalnyj-i-lokalnyj-atributy-v-woocommerce/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=no-global-attributes">'. __('Please read the recommendations', 'yfym'). '</a>.</p></div>';
		yfym_error_log('WARNING: Cайт не имеет глобальных атрибутов! Это может повлиять на качество YML-фида; Подробности см. тут: https://icopydoc.ru/globalnyj-i-lokalnyj-atributy-v-woocommerce/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=logs&utm_term=no-global-attributes Файл: export.php; Строка: '.__LINE__, 0);
	}
	unset($check_global_attr_count); 
	/* <a href="<?php echo ADMIN_COOKIE_PATH; ?>/admin.php?page=yfymextensions" target="_blank"></a> */
 ?> 
 <div class="notice notice-info">
  <p><span class="yfym_bold">YML for Yandex Market Pro</span> - <?php _e('a necessary extension for those who want to', 'yfym'); ?> <span class="yfym_bold" style="color: green;"><?php _e('save on advertising budget', 'yfym'); ?></span> <?php _e('on Yandex', 'yfym'); ?>! <a href="https://icopydoc.ru/product/yml-for-yandex-market-pro/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=about-yml-pro"><?php _e('Learn More', 'yfym'); ?></a>.</p>
  <p><span class="yfym_bold">Promos Export</span> - <?php _e('is an extension that allows you to increase sales by unloading Promotions (promos, gifts) on the Yandex Market', 'yfym'); ?>. <a href="https://icopydoc.ru/product/yml-for-yandex-market-promos-export/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=about-yml-promos"><?php _e('Learn More', 'yfym'); ?></a>.</p>
  <p><span class="yfym_bold">Rozetka Export</span> - <?php _e('a necessary extension for those who sell products on', 'yfym'); ?> Rozetka. <a href="https://icopydoc.ru/product/plagin-yml-for-yandex-market-rozetka-export/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=about-yml-rozetka"><?php _e('Learn More', 'yfym'); ?></a>.</p>
  <p><span class="yfym_bold">Prom Export</span> - <?php _e('extension for exporting products to', 'yfym'); ?> <a href="//prom.ua" target="_blank">prom.ua</a>. <a href="https://icopydoc.ru/product/plagin-yml-for-yandex-market-prom-export/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=about-yml-prom"><?php _e('Learn More', 'yfym'); ?></a>.</p>
  <p><span class="yfym_bold">Aliexpress Export</span> - <?php _e('extension for exporting products to', 'yfym'); ?> <a href="//sell.aliexpress.com/ru/__pc/YML_AE_Assistant.htm" target="_blank">Aliexpress</a>. <a href="https://icopydoc.ru/product/plagin-yml-for-yandex-market-aliexpress-export/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=about-yml-aliexpress"><?php _e('Learn More', 'yfym'); ?></a>.</p>
  <p><span class="yfym_bold">Book Export</span> - <?php _e('adds YML support for books to the plugin', 'yfym'); ?>. <a href="https://icopydoc.ru/product/yml-for-yandex-market-book-export/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=about-yml-book"><?php _e('Learn More', 'yfym'); ?></a>.</p>
 </div>
 <?php do_action('yfym_before_poststuff', $numFeed); ?>
 <div id="poststuff"><?php /* $yfymListTable = new YmlforYandexMarket_WP_List_Table(); ?><form method="get"><input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/><input type="hidden" name="yfym_form_id" value="yfym_wp_list_table" /><?php $yfymListTable->prepare_items(); $yfymListTable->display(); ?></form>
 */ ?>
 <div id="post-body" class="columns-2">
  <div id="postbox-container-1" class="postbox-container"><div class="meta-box-sortables">
  	<?php do_action('yfym_prepend_container_1', $numFeed); ?>
	<div class="postbox"> 
	 <div class="inside">	
	  <p style="text-align: center;"><strong style="color: green;"><?php _e('Instruction', 'yfym'); ?>:</strong> <a href="https://icopydoc.ru/kak-sozdat-woocommerce-yml-instruktsiya/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=main-instruction" target="_blank"><?php _e('How to create a YML-feed', 'yfym'); ?></a>.</p>
	  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">	
		<select style="width: 100%" name="yfym_num_feed" id="yfym_num_feed">
			<?php if (is_multisite()) {$cur_blog_id = get_current_blog_id();} else {$cur_blog_id = '0';}		
			$allNumFeed = (int)yfym_ALLNUMFEED; $ii = '1';
			for ($i = 1; $i<$allNumFeed+1; $i++) : ?>
			<option value="<?php echo $i; ?>" <?php selected($numFeed, $i); ?>><?php _e('Feed', 'yfym'); ?> <?php echo $i; ?>: feed-yml-<?php echo $cur_blog_id; ?>.<?php $yfym_fe = yfym_optionGET('yfym_file_extension', (string)$i, 'set_arr'); if ($yfym_fe == '') {$yfym_fe = 'xml';} echo $yfym_fe; ?> <?php $assignment = yfym_optionGET('yfym_feed_assignment', $ii, 'set_arr'); if ($assignment === '') {} else {echo '('.$assignment.')';} ?></option>
			<?php $ii++; endfor; ?>
		</select>
		<?php wp_nonce_field('yfym_nonce_action_send_select_feed', 'yfym_nonce_field_send_select_feed'); ?>
		<input style="width: 100%; margin: 10px 0 10px 0;" class="button" type="submit" name="yfym_submit_send_select_feed" value="<?php _e('Select feed', 'yfym'); ?>" />
	  </form>
  	 </div>
	</div>
	<?php do_action('yfym_before_support_project'); ?>
	<div class="postbox">
	 <h2 class="hndle"><?php _e('Please support the project', 'yfym'); ?>!</h2>
	 <div class="inside">	  
		<p><?php _e('Thank you for using the plugin', 'yfym'); ?> <strong>Yml for Yandex Market</strong></p>
		<p><?php _e('Please help make the plugin better', 'yfym'); ?> <a href="https://docs.google.com/forms/d/e/1FAIpQLSdmEXYIQzW-_Hj2mwvVbzKT8UUKaScJWQjDwcgI7Y5D0Xmchw/viewform" target="_blank" ><?php _e('answering 6 questions', 'yfym'); ?>!</a></p>
		<p><?php _e('If this plugin useful to you, please support the project one way', 'yfym'); ?>:</p>
		<ul class="yfym_ul">
			<li><a href="//wordpress.org/support/plugin/yml-for-yandex-market/reviews/" target="_blank"><?php _e('Leave a comment on the plugin page', 'yfym'); ?></a>.</li>
			<li><?php _e('Support the project financially', 'yfym'); ?>. <a href="//sobe.ru/na/plugin_yml_for_yandex_market" target="_blank"> <?php _e('Donate now', 'yfym'); ?></a>.</li>
			<li><?php _e('Noticed a bug or have an idea how to improve the quality of the plugin', 'yfym'); ?>? <a href="mailto:support@icopydoc.ru"><?php _e('Let me know', 'yfym'); ?></a>.</li>
		</ul>
		<p><?php _e('The author of the plugin Maxim Glazunov', 'yfym'); ?>.</p>
		<p><span style="color: red;"><?php _e('Accept orders for individual revision of the plugin', 'yfym'); ?></span>:<br /><a href="mailto:support@icopydoc.ru"><?php _e('Leave a request', 'yfym'); ?></a>.</p>
	  </div>
	</div>		
	<?php do_action('yfym_between_container_1', $numFeed); ?>	
	 <div class="postbox">
	  <h2 class="hndle"><?php _e('Send data about the work of the plugin', 'yfym'); ?></h2>
	  <div class="inside">
		<p><?php _e('Sending statistics you help make the plugin even better', 'yfym'); ?>! <?php _e('The following data will be transferred', 'yfym'); ?>:</p>
		<ul class="yfym_ul">
			<li><?php _e('URL YML-feed', 'yfym'); ?>;</li>
			<li><?php _e('File generation status', 'yfym'); ?>;</li>
			<li><?php _e('Is the multisite mode enabled', 'yfym'); ?>?</li>
		</ul>
		<p><?php _e('The plugin helped you download the products to the Yandex Market', 'yfym'); ?>?</p>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
		 <input type="hidden" name="yfym_num_feed_for_save" value="<?php echo $numFeed; ?>">
		 <p>
			<input type="radio" name="yfym_its_ok" value="yes"><?php _e('Yes', 'yfym'); ?><br />
			<input type="radio" name="yfym_its_ok" value="no"><?php _e('No', 'yfym'); ?>
		 </p>
		 <p><?php _e("If you don't mind to be contacted in case of problems, please enter your email address", "yfym"); ?>. <span class="yfym_bold"><?php _e('And if you want a response, be sure to include your email address', 'yfym'); ?></span>.</p>
		 <p><input type="email" name="yfym_email"></p>
		 <p><?php _e("Your message", "yfym"); ?>:</p>
		 <p><textarea rows="6" cols="32" name="yfym_message" placeholder="<?php _e('Enter your text to send me a message (You can write me in Russian or English). I check my email several times a day', 'yfym'); ?>"></textarea></p>
		 <?php wp_nonce_field('yfym_nonce_action_send_stat', 'yfym_nonce_field_send_stat'); ?><input class="button-primary" type="submit" name="yfym_submit_send_stat" value="<?php _e('Send data', 'yfym'); ?>" />
		</form>
	  </div>
	 </div>
	<?php do_action('yfym_append_container_1', $numFeed); ?>
  </div></div>

  <div id="postbox-container-2" class="postbox-container"><div class="meta-box-sortables">
  	<?php do_action('yfym_prepend_container_2', $numFeed); ?>
	<div class="postbox">
	 <h2 class="hndle"><?php _e('Feed', 'yfym'); ?> <?php echo $numFeed; ?>: <?php if ($numFeed !== '1') {echo $numFeed;} ?>feed-yml-<?php echo $cur_blog_id; ?>.xml <?php $assignment = yfym_optionGET('yfym_feed_assignment', $numFeed, 'set_arr'); if ($assignment === '') {} else {echo '('.$assignment.')';} ?> <?php if (empty($yfym_file_url)) : ?><?php _e('not created yet', 'yfym'); ?><?php else : ?><?php if ($status_sborki !== -1) : ?><?php _e('updating', 'yfym'); ?><?php else : ?><?php _e('created', 'yfym'); ?><?php endif; ?><?php endif; ?></h2>	
	 <div class="inside">
		<?php if (empty($yfym_file_url)) : ?> 
			<?php if ($status_sborki !== -1) : ?>
				<p><?php _e('We are working on automatic file creation. YML will be developed soon', 'yfym'); ?>.</p>
			<?php else : ?>		
				<p><?php _e('In order to do that, select another menu entry (which differs from "off") in the box called "Automatic file creation". You can also change values in other boxes if necessary, then press "Save"', 'yfym'); ?>.</p>
				<p><?php _e('After 1-7 minutes (depending on the number of products), the feed will be generated and a link will appear instead of this message', 'yfym'); ?>.</p>
			<?php endif; ?>
		<?php else : ?>
			<?php if ($status_sborki !== -1) : ?>
				<p><?php _e('We are working on automatic file creation. YML will be developed soon', 'yfym'); ?>.</p>
			<?php else : ?>
				<p><span class="yfym_bold"><?php _e('Your YML feed here', 'yfym'); ?>:</span><br/><a target="_blank" href="<?php echo $yfym_file_url; ?>"><?php echo $yfym_file_url; ?></a>
				<br/><?php _e('File size', 'yfym'); ?>: <?php clearstatcache();
				if ($numFeed === '1') {$prefFeed = '';} else {$prefFeed = $numFeed;}
				$upload_dir = (object)wp_get_upload_dir();
				if (is_multisite()) {
					$filename = $upload_dir->basedir."/".$prefFeed."feed-yml-".get_current_blog_id().".xml";
				} else {
					$filename = $upload_dir->basedir."/".$prefFeed."feed-yml-0.xml";				
				}
				if (is_file($filename)) {echo yfym_formatSize(filesize($filename));} else {echo '0 KB';} ?>
				<br/><?php _e('Generated', 'yfym'); ?>: <?php echo $yfym_date_sborki; ?></p>
			<?php endif; ?>		
		<?php endif; ?>
		<p><?php _e('Please note that Yandex Market checks YML no more than 3 times a day! This means that the changes on the Yandex Market are not instantaneous', 'yfym'); ?>!</p>
	  </div>
	</div>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
	 <?php do_action('yfym_prepend_form_container_2', $numFeed); ?>
	 <input type="hidden" name="yfym_num_feed_for_save" value="<?php echo $numFeed; ?>">
	 <div class="postbox">
	  <h2 class="hndle"><?php _e('Main parameters', 'yfym'); ?></h2>
	   <div class="inside">
		<table class="form-table"><tbody>
		 <tr>
			<th scope="row"><label for="yfym_run_cron"><?php _e('Automatic file creation', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_run_cron" id="yfym_run_cron">
					<option value="off" <?php selected($yfym_status_cron, 'off' ); ?>><?php _e('Off', 'yfym'); ?></option>
					<?php $yfym_enable_five_min = yfym_optionGET('yfym_enable_five_min'); if ($yfym_enable_five_min === 'on') : ?>
					<option value="five_min" <?php selected($yfym_status_cron, 'five_min');?> ><?php _e('Every five minutes', 'yfym'); ?></option>
					<?php endif; ?>
					<option value="hourly" <?php selected($yfym_status_cron, 'hourly');?> ><?php _e('Hourly', 'yfym'); ?></option>
					<option value="six_hours" <?php selected($yfym_status_cron, 'six_hours'); ?> ><?php _e('Every six hours', 'yfym'); ?></option>	
					<option value="twicedaily" <?php selected($yfym_status_cron, 'twicedaily');?> ><?php _e('Twice a day', 'yfym'); ?></option>
					<option value="daily" <?php selected($yfym_status_cron, 'daily');?> ><?php _e('Daily', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('The refresh interval on your feed', 'yfym'); ?></span>
			</td>
		 </tr>			
		 <tr>
			<th scope="row"><label for="yfym_ufup"><?php _e('Update feed when updating products', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="yfym_ufup" id="yfym_ufup" <?php checked($yfym_ufup, 'on' ); ?>/>
			</td>
		 </tr>
		 <?php do_action('yfym_after_ufup_option', $numFeed); /* С версии 3.0.0 */ ?>
		 <tr>
			<th scope="row"><label for="yfym_feed_assignment"><?php _e('Feed assignment', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="text" maxlength="20" name="yfym_feed_assignment" id="yfym_feed_assignment" value="<?php echo $yfym_feed_assignment; ?>" placeholder="<?php _e('For Yandex Market', 'yfym');?>" /><br />
				<span class="description"><?php _e('Not used in feed. Inner note for your convenience', 'yfym'); ?>.</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_file_extension"><?php _e('Feed file extension', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_file_extension" id="yfym_file_extension">
					<option value="xml" <?php selected($yfym_file_extension, 'xml'); ?>>XML (<?php _e('recommend', 'yfym'); ?>)</option>
					<option value="yml" <?php selected($yfym_file_extension, 'yml'); ?>>YML</option>
					<?php do_action('yfym_after_file_extension_option', $numFeed); ?>
				</select><br />
				<span class="description"><?php _e('Default', 'yfym'); ?>: XML</span>
			</td>
		 </tr>		 
		 <tr>
		 	<th scope="row"><label for="yfym_yml_rules"><?php _e('To follow the rules', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_yml_rules" id="yfym_yml_rules">
				<option value="yandex_market" <?php selected($yfym_yml_rules, 'yandex_market'); ?>><?php _e('Yandex Market', 'yfym'); ?> ADV (<?php _e('Simplified type', 'yfym'); ?>)</option>
				<option value="dbs" <?php selected($yfym_yml_rules, 'dbs'); ?>><?php _e('Yandex Market', 'yfym'); ?> DBS (<?php _e('Simplified type', 'yfym'); ?>)</option>
				<option value="single_catalog" <?php selected($yfym_yml_rules, 'single_catalog'); ?>>FBY, FBY+, FBS (<?php _e('in a single catalog', 'yfym'); ?>) (beta)</option>
				<option value="beru" <?php selected($yfym_yml_rules, 'beru'); ?>><?php _e('Former Beru', 'yfym'); ?></option>
				<option value="all_elements" <?php selected($yfym_yml_rules, 'all_elements'); ?>><?php _e('No rules', 'yfym'); ?> (<?php _e('Not recommended', 'yfym'); ?>)</option>
				<option value="ozon" <?php selected($yfym_yml_rules, 'ozon'); ?>>OZON</option>
				<?php do_action('yfym_append_select_yfym_yml_rules', $yfym_yml_rules, $numFeed); ?>
				</select><br />
				<?php do_action('yfym_after_select_yfym_yml_rules', $yfym_yml_rules, $numFeed); ?>
				<span class="description"><?php _e('Exclude products that do not meet the requirements', 'yfym'); ?> <i>(<?php _e('missing required elements/data', 'yfym'); ?>)</i>. <?php _e('The plugin will try to automatically remove products from the YML-feed for which the required fields for the feed are not filled', 'yfym'); ?>. <?php _e('Also, this item affects the structure of the file', 'yfym'); ?>.</span>
			</td>
		 </tr>		 
		 <tr>
			<th scope="row"><label for="yfym_whot_export"><?php _e('Whot export', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_whot_export" id="yfym_whot_export">
					<option value="all" <?php selected($yfym_whot_export, 'all'); ?>><?php _e('Simple & Variable products', 'yfym'); ?></option>
					<option value="simple" <?php selected($yfym_whot_export, 'simple'); ?>><?php _e('Only simple products', 'yfym'); ?></option>
					<?php do_action('yfym_after_whot_export_option', $numFeed); ?>
				</select><br />
				<span class="description"><?php _e('Whot export', 'yfym'); ?></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_desc"><?php _e('Description of the product', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_desc" id="yfym_desc">
				<option value="excerpt" <?php selected($yfym_desc, 'excerpt'); ?>><?php _e('Only Excerpt description', 'yfym'); ?></option>
				<option value="full" <?php selected($yfym_desc, 'full'); ?>><?php _e('Only Full description', 'yfym'); ?></option>
				<option value="excerptfull" <?php selected($yfym_desc, 'excerptfull'); ?>><?php _e('Excerpt or Full description', 'yfym'); ?></option>
				<option value="fullexcerpt" <?php selected($yfym_desc, 'fullexcerpt'); ?>><?php _e('Full or Excerpt description', 'yfym'); ?></option>
				<option value="excerptplusfull" <?php selected($yfym_desc, 'excerptplusfull'); ?>><?php _e('Excerpt plus Full description', 'yfym'); ?></option>
				<option value="fullplusexcerpt" <?php selected($yfym_desc, 'fullplusexcerpt'); ?>><?php _e('Full plus Excerpt description', 'yfym'); ?></option>
				<?php do_action('yfym_append_select_yfym_desc', $yfym_desc, $numFeed); /* с версии 3.2.1 */ ?>
				</select><br />
				<?php do_action('yfym_after_select_yfym_desc', $yfym_desc, $numFeed); /* с версии 3.2.1 */ ?>
				<span class="description"><?php _e('The source of the description', 'yfym'); ?>
				</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_the_content"><?php _e('Use the filter', 'yfym'); ?> the_content</label></th>
			<td class="overalldesc">
				<select name="yfym_the_content" id="yfym_the_content">
				<option value="disabled" <?php selected($yfym_the_content, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
				<option value="enabled" <?php selected($yfym_the_content, 'enabled'); ?>><?php _e('Enabled', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Default', 'yfym'); ?>: <?php _e('Enabled', 'yfym'); ?></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_var_desc_priority"><?php _e('The varition description takes precedence over others', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="yfym_var_desc_priority" id="yfym_var_desc_priority" <?php checked($yfym_var_desc_priority, 'on'); ?>/>
			</td>
		 </tr>
		 <tr class="yfym_tr">
			<th scope="row"><label for="yfym_shop_name"><?php _e('Shop name', 'yfym'); ?></label></th>
			<td class="overalldesc">
			 <input maxlength="20" type="text" name="yfym_shop_name" id="yfym_shop_name" value="<?php echo $yfym_shop_name; ?>" /><br />
			 <span class="description"><?php _e('Required element', 'yfym'); ?> <strong>name</strong>. <?php _e('The short name of the store should not exceed 20 characters', 'yfym'); ?>.</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_company_name"><?php _e('Company name', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="text" name="yfym_company_name" id="yfym_company_name" value="<?php echo $yfym_company_name; ?>" /><br />
				<span class="description"><?php _e('Required element', 'yfym'); ?> <strong>company</strong>. <?php _e('Full name of the company that owns the store', 'yfym'); ?>.</span>
			</td>
		 </tr>		 
		 <tr>
			<th scope="row"><label for="yfym_currencies"><?php _e('Element "currencies"', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_currencies" id="yfym_currencies">
					<option value="enabled" <?php selected($yfym_currencies, 'enabled'); ?>><?php _e('Enabled', 'yfym'); ?></option>	
					<option value="disabled" <?php selected($yfym_currencies, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
				</select>
			</td>
		 </tr>		 
		 <tr>
			<th scope="row"><label for="yfym_main_product"><?php _e('What kind of products do you sell', 'yfym'); ?>?</label></th>
			<td class="overalldesc">
					<select name="yfym_main_product" id="yfym_main_product">
					<option value="electronics" <?php selected($yfym_main_product, 'electronics'); ?>><?php _e('Electronics', 'yfym'); ?></option>
					<option value="computer" <?php selected($yfym_main_product, 'computer'); ?>><?php _e('Computer techologies', 'yfym'); ?></option>
					<option value="clothes_and_shoes" <?php selected($yfym_main_product, 'clothes_and_shoes'); ?>><?php _e('Clothes and shoes', 'yfym'); ?></option>
					<option value="auto_parts" <?php selected($yfym_main_product, 'auto_parts'); ?>><?php _e('Auto parts', 'yfym'); ?></option>
					<option value="products_for_children" <?php selected($yfym_main_product, 'products_for_children'); ?>><?php _e('Products for children', 'yfym'); ?></option>
					<option value="sporting_goods" <?php selected($yfym_main_product, 'sporting_goods'); ?>><?php _e('Sporting goods', 'yfym'); ?></option>
					<option value="goods_for_pets" <?php selected($yfym_main_product, 'goods_for_pets'); ?>><?php _e('Goods for pets', 'yfym'); ?></option>
					<option value="sexshop" <?php selected($yfym_main_product, 'sexshop'); ?>><?php _e('Sex shop (Adult products)', 'yfym'); ?></option>
					<option value="books" <?php selected($yfym_main_product, 'books'); ?>><?php _e('Books', 'yfym'); ?></option>
					<option value="health" <?php selected($yfym_main_product, 'health'); ?>><?php _e('Health products', 'yfym'); ?></option>	
					<option value="food" <?php selected($yfym_main_product, 'food'); ?>><?php _e('Food', 'yfym'); ?></option>
					<option value="construction_materials" <?php selected($yfym_main_product, 'construction_materials'); ?>><?php _e('Construction Materials', 'yfym'); ?></option>
					<option value="other" <?php selected($yfym_main_product, 'other'); ?>><?php _e('Other', 'yfym'); ?></option>	
				</select><br />
				<span class="description"><?php _e('Specify the main category', 'yfym'); ?></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_adult"><?php _e('Adult Market', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_adult" id="yfym_adult">
				<option value="no" <?php selected($yfym_adult, 'no'); ?>><?php _e('No', 'yfym'); ?></option>
				<option value="yes" <?php selected($yfym_adult, 'yes'); ?>><?php _e('Yes', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>adult</strong></span>
			</td>
		 </tr>
		 <?php if (class_exists('WOOCS')) : 		 
			global $WOOCS; $currencies_arr = $WOOCS->get_currencies(); 
		 	if (is_array($currencies_arr)) : $array_keys = array_keys($currencies_arr); ?>
			<tr>
				<th scope="row"><label for="yfym_wooc_currencies"><?php _e('Feed currency', 'yfym'); ?></label></th>
				<td class="overalldesc">
					<select name="yfym_wooc_currencies" id="yfym_wooc_currencies">
					 <?php for ($i = 0; $i < count($array_keys); $i++) : ?>
						<option value="<?php echo $currencies_arr[$array_keys[$i]]['name']; ?>" <?php selected($yfym_wooc_currencies, $currencies_arr[$array_keys[$i]]['name']); ?>><?php echo $currencies_arr[$array_keys[$i]]['name']; ?></option>					
					 <?php endfor; ?>
					</select><br />
					<span class="description"><?php _e('You have plugin installed', 'yfym'); ?> <strong class="yfym_bold">WooCommerce Currency Switcher by PluginUs.NET. Woo Multi Currency and Woo Multi Pay</strong><br />
					<?php _e('Indicate in what currency the prices should be', 'yfym'); ?>.<br /><strong class="yfym_bold"><?php _e('Please note', 'yfym'); ?>:</strong> <?php _e('Yandex Market only supports the following currencies', 'yfym'); ?>: RUR, RUB, UAH, BYN, KZT, USD, EUR. <?php _e('Choosing a different currency can lead to errors', 'yfym'); ?>
					</span>
				</td>
			</tr>
			<?php endif; ?><?php endif; ?>
		 <tr class="yfym_tr">
			<th scope="row"><label for="yfym_step_export"><?php _e('Step of export', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_step_export" id="yfym_step_export">
				<option value="80" <?php selected($yfym_step_export, '80'); ?>>80</option>
				<option value="200" <?php selected($yfym_step_export, '200'); ?>>200</option>
				<option value="300" <?php selected($yfym_step_export, '300'); ?>>300</option>
				<option value="450" <?php selected($yfym_step_export, '450'); ?>>450</option>
				<option value="500" <?php selected($yfym_step_export, '500'); ?>>500</option>
				<option value="800" <?php selected($yfym_step_export, '800'); ?>>800</option>
				<option value="1000" <?php selected($yfym_step_export, '1000'); ?>>1000</option>
				<?php do_action('yfym_step_export_option', $numFeed); ?>
				</select><br />
				<span class="description"><?php _e('The value affects the speed of file creation', 'yfym'); ?><?php _e('If you have any problems with the generation of the file - try to reduce the value in this field', 'yfym'); ?>. <?php _e('More than 500 can only be installed on powerful servers', 'yfym'); ?>.</span>
			</td>
		 </tr>
		 <tr class="yfym_tr">
			<th scope="row"><label for="yfym_no_group_id_arr"><?php _e('Categories of variable products for which group_id is not allowed', 'yfym'); ?></label></th>
			<td class="overalldesc">
			 <select id="yfym_no_group_id_arr" style="width: 100%;" name="yfym_no_group_id_arr[]" size="8" multiple>
				<?php foreach (get_terms('product_cat', array('hide_empty'=>0, 'parent'=>0)) as $term) {
						echo yfym_cat_tree($term->taxonomy, $term->term_id, $yfym_no_group_id_arr); } ?>
			 </select><br />
			 <span class="description"><?php _e('According to Yandex Market rules in this field you need to mark ALL categories of products not related to "Clothes, Shoes and Accessories", "Furniture", "Cosmetics, perfumes and care", "Baby products", "Accessories for portable electronics". Ie categories for which it is forbidden to use the attribute group_id', 'yfym'); ?>.</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_add_in_name_arr"><?php _e('Add attributes to the variable products name', 'yfym'); ?></label><br />(<?php _e('You can only add attributes that are used for variations and that cannot be grouped using', 'yfym'); ?> group_id)</th>
			<td class="overalldesc">
			 <select id="yfym_add_in_name_arr" style="width: 100%;" name="yfym_add_in_name_arr[]" size="8" multiple>
				<?php foreach (yfym_get_attributes() as $attribute) : ?>
					<option value="<?php echo $attribute['id']; ?>"<?php if (!empty($add_in_name_arr)) { foreach ($add_in_name_arr as $value) {selected($value, $attribute['id']);}} ?>><?php echo $attribute['name']; ?></option>
				<?php endforeach; ?>
			 </select><br />
			 <span class="description"><?php _e('It works only for variable products that are not in the category "Clothes, Shoes and Accessories", "Furniture", "Cosmetics, perfumes and care", "Baby products", "Accessories for portable electronics"', 'yfym'); ?></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_separator_type"><?php _e('Separator options', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_separator_type" id="yfym_separator_type">
					<option value="type1" <?php selected($yfym_separator_type, 'type1'); ?>><?php _e('Type', 'yfym'); ?>_1 (В1:З1, В2:З2, ... Вn:Зn)</option>
					<option value="type2" <?php selected($yfym_separator_type, 'type2')?> ><?php _e('Type', 'yfym'); ?>_2 (В1-З1, В2-З2, ... Вn:Зn)</option>
					<option value="type3" <?php selected($yfym_separator_type, 'type3'); ?> ><?php _e('Type', 'yfym'); ?>_3 В1:З1, В2:З2, ... Вn:Зn</option>
					<option value="type4" <?php selected($yfym_separator_type, 'type4'); ?> ><?php _e('Type', 'yfym'); ?>_4 З1 З2 ... Зn</option>
					<?php do_action('yfym_after_option_separator_type', $numFeed); ?>
				</select><br />
				<span class="description"><?php _e('Separator options', 'yfym'); ?></span>
			</td>
		 </tr>
		 <tr class="yfym_tr">
			<th scope="row"><label for="yfym_behavior_onbackorder"><?php _e('For pre-order products, establish availability equal to', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_behavior_onbackorder" id="yfym_behavior_onbackorder">
					<option value="false" <?php selected($yfym_behavior_onbackorder, 'false'); ?>>False</option>
					<option value="true" <?php selected($yfym_behavior_onbackorder, 'true')?> >True</option>
				</select><br />
				<span class="description"><?php _e('For pre-order products, establish availability equal to', 'yfym'); ?> false/true</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_behavior_stip_symbol"><?php _e('In attributes', 'yfym'); ?> vendorCode <?php _e('and', 'yfym'); ?> shop-sku <?php _e('ampersand', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_behavior_stip_symbol" id="yfym_behavior_stip_symbol">
					<option value="default" <?php selected($yfym_behavior_stip_symbol, 'default'); ?>><?php _e('Default', 'yfym'); ?></option>
					<option value="del" <?php selected($yfym_behavior_stip_symbol, 'del'); ?>><?php _e('Delete', 'yfym'); ?></option>
					<option value="slash" <?php selected($yfym_behavior_stip_symbol, 'slash'); ?>><?php _e('Replace with', 'yfym'); ?> /</option>
					<option value="amp" <?php selected($yfym_behavior_stip_symbol, 'amp'); ?>><?php _e('Replace with', 'yfym'); ?> amp;</option>
				</select><br />
				<span class="description"><?php _e('Default', 'yfym'); ?> "<?php _e('Delete', 'yfym'); ?>"</span>
			</td>
		 </tr>
		 <tr class="yfym_tr">
			<th scope="row"><label for="yfym_skip_missing_products"><?php _e('Skip missing products', 'yfym'); ?> (<?php _e('except for products for which a pre-order is permitted', 'yfym'); ?>.)</label></th>
			<td class="overalldesc">
				<input type="checkbox" name="yfym_skip_missing_products" id="yfym_skip_missing_products" <?php checked($yfym_skip_missing_products, 'on' ); ?>/>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_skip_backorders_products"><?php _e('Skip backorders products', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="yfym_skip_backorders_products" id="yfym_skip_backorders_products" <?php checked($yfym_skip_backorders_products, 'on' ); ?>/>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_no_default_png_products"><?php _e('Remove default.png from YML', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="yfym_no_default_png_products" id="yfym_no_default_png_products" <?php checked($yfym_no_default_png_products, 'on' ); ?>/>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_skip_products_without_pic"><?php _e('Skip products without pictures', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="yfym_skip_products_without_pic" id="yfym_skip_products_without_pic" <?php checked($yfym_skip_products_without_pic, 'on' ); ?>/>
			</td>
		 </tr>
		 <?php do_action('yfym_after_step_export', $numFeed); ?>		 		 
		</tbody></table>
	   </div>
	 </div>	

	 <div class="postbox">
	  <h2 class="hndle"><?php _e('Turbo Pages', 'yfym'); ?>, CDEK, Единый каталог, OZON, EBay</h2>
	  <div class="inside">
	  	<p><i><?php _e('The elements located in this block are important when creating a YML feed for some models, but they are not important or even prohibited for others', 'yfym'); ?>.</i></p>
	 	<table class="form-table"><tbody>
	 	 <tr>
			<th scope="row"><label for="yfym_clear_get"><?php _e('Clear URL from GET-paramrs', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_clear_get" id="yfym_clear_get">
				<option value="no" <?php selected($yfym_clear_get, 'no'); ?>><?php _e('No', 'yfym'); ?></option>
				<option value="yes" <?php selected($yfym_clear_get, 'yes'); ?>><?php _e('Yes', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('This option may be useful when setting up Turbo pages', 'yfym'); ?><br />
				<a target="_blank" href="https://icopydoc.ru/vklyuchaem-turbo-stranitsy-dlya-magazina-woocommerce-instruktsiya/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=yandex-turbo-instruction"><?php _e('Tips for configuring Turbo pages', 'yfym'); ?></a></span>
			</td>
		 </tr>
		 <tr class="yfym_tr">
		 	<th scope="row"><label for="yfym_amount"><?php _e('Quantity of products', 'yfym'); ?></label><br /><i>(<?php _e('Forbidden in Yandex Market', 'yfym'); ?>. <?php _e('Use only in CDEK feed', 'yfym'); ?>)</i></th>
			<td class="overalldesc">
				<select name="yfym_amount" id="yfym_amount">	 
					<option value="disabled" <?php selected($yfym_amount, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
					<option value="enabled" <?php selected($yfym_amount, 'enabled'); ?>><?php _e('Enabled', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Element', 'yfym'); ?> <strong>amount</strong>. <?php _e('Do not use it if you create YML-feed for Yandex Market', 'yfym'); ?>.<br /><?php _e('To make it work you must enable "Manage stock" and indicate "Stock quantity"', 'yfym'); ?>.<br /><a target="_blank" href="//docs.cdek.market/prodavcam/instrukcii/import-tovarov-yml.html"><?php _e('Read more on', 'yfym'); ?> CDEK</a></span>
			</td>
		 </tr>
		 <tr class="yfym_tr">
		 	<th scope="row"><label for="yfym_shop_sku">Shop sku</label><br /><i>(<?php _e('Forbidden in Yandex Market', 'yfym'); ?> <?php _e('Model', 'yfym'); ?> ADV, DBS, FBY, FBY+, FBS. <?php _e('Use only for', 'yfym'); ?>: <?php _e('Beru', 'yfym'); ?>)</i></th>
			<td class="overalldesc">
				<select name="yfym_shop_sku" id="yfym_shop_sku">	 
					<option value="disabled" <?php selected($yfym_shop_sku, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
					<option value="products_id" <?php selected($yfym_shop_sku, 'products_id'); ?>><?php _e('Add from products ID', 'yfym'); ?> (<?php _e('recommended', 'yfym'); ?>)</option>
					<option value="sku" <?php selected($yfym_shop_sku, 'sku'); ?>><?php _e('Substitute from SKU', 'yfym'); ?></option>
					<?php foreach (yfym_get_attributes() as $attribute) : ?>
					<option value="<?php echo $attribute['id']; ?>" <?php selected($yfym_shop_sku, $attribute['id'] ); ?>><?php echo $attribute['name']; ?></option>
					<?php endforeach; ?>
				</select><br />
				<span class="description"><?php _e('Element', 'yfym'); ?> <strong>shop-sku</strong>. <?php _e('Required if the feeds for', 'yfym'); ?>: BERU.</span> <?php _e('Do not use it if you create YML-feed for Yandex Market', 'yfym'); ?> ADV, DBS, FBY, FBY+, FBS.<br /><a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-elements.html"><?php _e('Read more on Yandex', 'yfym'); ?></a>
			</td>
		 </tr>
		 <tr>
		 	<th scope="row"><label for="yfym_count"><?php _e('Quantity of products', 'yfym'); ?></label><br /><i>(<?php _e('Forbidden in Yandex Market', 'yfym'); ?> <?php _e('Models', 'yfym'); ?>: ADV, FBY, FBY+, FBS. <?php _e('Use only for', 'yfym'); ?>: DBS, <?php _e('Beru', 'yfym'); ?>)</i></th>
			<td class="overalldesc">
				<select name="yfym_count" id="yfym_count">	 
					<option value="disabled" <?php selected($yfym_count, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
					<option value="enabled" <?php selected($yfym_count, 'enabled'); ?>><?php _e('Enabled', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Element', 'yfym'); ?> <strong>count</strong>. <?php _e('Required if the feed is for BERU', 'yfym'); ?> (Витрина + Доставка) <?php _e('and', 'yfym'); ?> DBS.</span> <?php _e('Do not use it if you create YML-feed for Yandex Market', 'yfym'); ?> ADV, FBY, FBY+, FBS.<br /><?php _e('To make it work you must enable "Manage stock" and indicate "Stock quantity"', 'yfym'); ?>.<br /><a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-elements.html"><?php _e('Read more on Yandex', 'yfym'); ?></a>
			</td>
		 </tr>
		 <tr>
		 	<th scope="row"><label for="yfym_auto_disabled"><?php _e('Automatically remove products from sale', 'yfym'); ?></label><br /><i>(<?php _e('Forbidden in Yandex Market', 'yfym'); ?>. <?php _e('Use only in Beru feed', 'yfym'); ?>)</i></th>
			<td class="overalldesc">
				<select name="yfym_auto_disabled" id="yfym_auto_disabled">	 
					<option value="disabled" <?php selected($yfym_auto_disabled, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
					<option value="yes" <?php selected($yfym_auto_disabled, 'yes'); ?>><?php _e('Yes', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Element', 'yfym'); ?> <strong>disabled</strong>. <?php _e('Only for BERU', 'yfym'); ?>.</span> <?php _e('Do not use it if you create YML-feed for Yandex Market', 'yfym'); ?>. <a target="_blank" href="//yandex.ru/support/marketplace/catalog/disable-product.html"><?php _e('Read more on Yandex', 'yfym'); ?></a>.<br /><?php _e('When using this option, it is recommended to uncheck checkboxes', 'yfym'); ?> "<?php _e('Skip missing products', 'yfym'); ?>" <?php _e('and', 'yfym'); ?> "<?php _e('Skip backorders products', 'yfym'); ?>".
			</td>
		 </tr>
		 <tr>
		 	<th scope="row"><label for="yfym_market_sku_status"><?php _e('Add market-sku to feed', 'yfym'); ?></label><br /><i>(<?php _e('Forbidden in Yandex Market', 'yfym'); ?> <?php _e('Models', 'yfym'); ?>: ADV, DBS. <?php _e('Use only for', 'yfym'); ?>: FBY, FBY+, FBS, <?php _e('Beru', 'yfym'); ?>)</i></th>
			<td class="overalldesc">
				<select name="yfym_market_sku_status" id="yfym_market_sku_status">	 
					<option value="disabled" <?php selected($yfym_market_sku_status, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
					<option value="enabled" <?php selected($yfym_market_sku_status, 'enabled'); ?>><?php _e('Enabled', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Element', 'yfym'); ?> <strong>market-sku</strong>. <?php _e('Optional when creating a catalog. A must for price recommendations', 'yfym'); ?>. <?php _e('Do not use it if you create YML-feed for Yandex Market', 'yfym'); ?> ADV, DBS.<br /><a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html"><?php _e('Read more on Yandex', 'yfym'); ?></a></span>
			</td>
		 </tr>		 
		 <tr>
		 	<th scope="row"><label for="yfym_manufacturer"><?php _e('Manufacturer company', 'yfym'); ?></label><br /><i>(<?php _e('Forbidden in Yandex Market', 'yfym'); ?> <?php _e('Models', 'yfym'); ?>: ADV, DBS. <?php _e('Use only for', 'yfym'); ?>: FBY, FBY+, FBS, <?php _e('Beru', 'yfym'); ?>)</i></th>
			<td class="overalldesc">
				<select name="yfym_manufacturer" id="yfym_manufacturer">	 
					<option value="disabled" <?php selected($yfym_manufacturer, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
					<?php foreach (yfym_get_attributes() as $attribute) : ?>
					<option value="<?php echo $attribute['id']; ?>" <?php selected($yfym_manufacturer, $attribute['id'] ); ?>><?php echo $attribute['name']; ?></option>
					<?php endforeach; ?>
				</select><br />
				<span class="description"><?php _e('Element', 'yfym'); ?> <strong>manufacturer</strong>. <?php _e('Required if the feeds for', 'yfym'); ?>: FBY, FBY+, FBS, <?php _e('Beru', 'yfym'); ?><br /><a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-elements.html"><?php _e('Read more on Yandex', 'yfym'); ?></a></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_vendor"><?php _e('Vendor', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_vendor" id="yfym_vendor">
				<option value="disabled" <?php selected($vendor, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
				<?php if (is_plugin_active('perfect-woocommerce-brands/perfect-woocommerce-brands.php') || is_plugin_active('perfect-woocommerce-brands/main.php') || class_exists('Perfect_Woocommerce_Brands')) : ?>
				<option value="sfpwb" <?php selected($vendor, 'sfpwb'); ?>><?php _e('Substitute from', 'yfym'); ?> Perfect Woocommerce Brands</option>
				<?php endif; ?>
				<?php if (is_plugin_active('premmerce-woocommerce-brands/premmerce-brands.php')) : ?>
				<option value="premmercebrandsplugin" <?php selected($vendor, 'premmercebrandsplugin'); ?>><?php _e('Substitute from', 'yfym'); ?> Premmerce Brands for WooCommerce</option>
				<?php endif; ?>	
				<?php if (class_exists('woo_brands')) : ?>
				<option value="woo_brands" <?php selected($vendor, 'woo_brands'); ?>><?php _e('Substitute from', 'yfym'); ?> Woocomerce Brands Pro</option>
				<?php endif; ?>			
				<?php foreach (yfym_get_attributes() as $attribute) : ?>
				<option value="<?php echo $attribute['id']; ?>" <?php selected($vendor, $attribute['id']); ?>><?php echo $attribute['name']; ?></option>
				<?php endforeach; ?>
				</select><br />
				<span class="description"><?php _e('Element', 'yfym'); ?> <strong>vendor</strong>. <?php _e('Required if the feeds for', 'yfym'); ?>: FBY, FBY+, FBS, <?php _e('Beru', 'yfym'); ?>. <?php _e('Optional element for', 'yfym'); ?>: ADV, DBS.</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_country_of_origin"><?php _e('Country of origin', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_country_of_origin" id="yfym_country_of_origin">
				<option value="off" <?php selected($country_of_origin, 'off'); ?>><?php _e('Disabled', 'yfym'); ?></option>
				<?php foreach (yfym_get_attributes() as $attribute) : ?>	
				<option value="<?php echo $attribute['id']; ?>" <?php selected($country_of_origin, $attribute['id'] ); ?>><?php echo $attribute['name']; ?></option>
				<?php endforeach; ?>
				</select><br />
				<span class="description"><?php _e('Element', 'yfym'); ?> <strong>country_of_origin</strong>. <?php _e('This element indicates the country where the product was manufactured', 'yfym'); ?>. <?php _e('Required if the feeds for', 'yfym'); ?>: FBY, FBY+, FBS, <?php _e('Beru', 'yfym'); ?>. <?php _e('Optional element for', 'yfym'); ?>: ADV, DBS. <br /><a href="//partner.market.yandex.ru/pages/help/Countries.pdf" target="_blank"><?php _e('A list of possible values', 'yfym'); ?></a>.</span>
			</td>
		 </tr>
		 <tr class="yfym_tr">
			<th scope="row"><label for="yfym_source_id"><?php _e('Source ID of the product', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_source_id" id="yfym_source_id">
				<option value="disabled" <?php selected($yfym_source_id, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
				<option value="sku" <?php selected($yfym_source_id, 'sku'); ?>><?php _e('Substitute from SKU', 'yfym'); ?></option>
				<option value="post_meta" <?php selected($yfym_source_id, 'post_meta'); ?>><?php _e('Substitute from post meta', 'yfym'); ?></option>
				<?php if (class_exists('WooCommerce_Germanized')) : ?>
				<option value="germanized" <?php selected($yfym_source_id, 'germanized'); ?>><?php _e('Substitute from', 'yfym'); ?> WooCommerce Germanized</option>
				<?php endif; ?>				
				<?php foreach (yfym_get_attributes() as $attribute) : ?>	
				<option value="<?php echo $attribute['id']; ?>" <?php selected($yfym_source_id, $attribute['id'] ); ?>><?php echo $attribute['name']; ?></option><?php endforeach; ?>
				</select><br />
				<span class="description"><?php _e('Product ID source for OZON', 'yfym'); ?></span><br /><a href="https://seller-edu.ozon.ru/docs/work-with-goods/fidi.html" target="_blank"><?php _e('Read more', 'yfym'); ?></a>
				<span class="description"><?php _e('If selected', 'yfym'); ?> <span class="yfym_bold">"<?php _e('Substitute from post meta', 'yfym'); ?>"</span> <?php _e('do not forget to fill out this field', 'yfym'); ?>:</span><br />
				<input placeholder="<?php _e('Name post_meta', 'yfym'); ?>" type="text" name="yfym_source_id_post_meta" id="yfym_source_id_post_meta" value="<?php echo $yfym_source_id_post_meta; ?>" />
			</td>
		 </tr>
		 <tr class="yfym_tr">
			<th scope="row"><label for="yfym_ebay_stock"><?php _e('Add information about stock to feed for EBay', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" id="yfym_ebay_stock" name="yfym_ebay_stock" <?php checked($yfym_ebay_stock, 'on' ); ?>/>				
			</td>
		 </tr>
		 <?php do_action('yfym_append_turbo_set', $numFeed); /* с версии 3.3.8 */?>	 
		</tbody></table>
	  </div>
	 </div>

	 <?php do_action('yfym_before_pad', $numFeed); ?>
	 <div class="postbox">
	   <h2 class="hndle"><?php _e('Price and delivery', 'yfym'); ?></h2>
	   <div class="inside">	    
		<table class="form-table"><tbody>
		 <tr>
			<th scope="row"><label for="yfym_pickup"><?php _e('Pickup', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_pickup" id="yfym_pickup">
				<option value="true" <?php selected($yfym_pickup, 'true'); ?>><?php _e('True', 'yfym'); ?></option>
				<option value="false" <?php selected($yfym_pickup, 'false'); ?>><?php _e('False', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>pickup</strong>. <?php _e('Option to get order from pickup point', 'yfym'); ?>.</span>
			</td>
		 </tr>
		 <?php do_action('yfym_after_pickup', $numFeed); /* с версии 2.0.8 */ ?>
		 <tr>
			<th scope="row"><label for="yfym_price_from"><?php _e('Price from', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_price_from" id="yfym_price_from">
					<option value="yes" <?php selected( $yfym_price_from, 'yes' ); ?>><?php _e( 'Yes', 'yfym'); ?></option>
					<option value="no" <?php selected( $yfym_price_from, 'no' ); ?>><?php _e( 'No', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Apply the setting Price from', 'yfym'); ?> <strong>from="true"</strong> <?php _e('attribute of', 'yfym'); ?> <strong>price</strong><br /><strong><?php _e('Example', 'yfym'); ?>:</strong><br /><code>&lt;price from=&quot;true&quot;&gt;2000&lt;/price&gt;</code></span>
			</td>
		 </tr>	
		 <tr>
			<th scope="row"><label for="yfym_oldprice"><?php _e('Old price', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_oldprice" id="yfym_oldprice">
					<option value="yes" <?php selected($yfym_oldprice, 'yes' ); ?>><?php _e('Yes', 'yfym'); ?></option>
					<option value="no" <?php selected($yfym_oldprice, 'no' ); ?>><?php _e('No', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>oldprice</strong>. <?php _e('In oldprice indicates the old price of the goods, which must necessarily be higher than the new price (price)', 'yfym'); ?>.</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_delivery"><?php _e('Delivery', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_delivery" id="yfym_delivery">
					<option value="true" <?php selected( $yfym_delivery, 'true' ); ?>><?php _e( 'True', 'yfym'); ?></option>
					<option value="false" <?php selected( $yfym_delivery, 'false' ); ?>><?php _e( 'False', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>delivery</strong>. <?php _e('The delivery item must be set to false if the item is prohibited to sell remotely (jewelry, medicines)', 'yfym'); ?>.<br />
				<a target="_blank" href="//yandex.ru/support/partnermarket/delivery.html"><?php _e('Read more on Yandex', 'yfym'); ?></a>
				</span>
			</td>
		 </tr> 
		 <tr>
			<th scope="row"><label for="yfym_vat"><?php _e('VAT rate', 'yfym'); ?></label><br />
				<a target="_blank" href="https://icopydoc.ru/vklyuchaem-turbo-stranitsy-dlya-magazina-woocommerce-instruktsiya/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=yandex-turbo-instruction-vat"><?php _e('Tips for configuring Turbo pages', 'yfym'); ?></a></th>
			<td class="overalldesc">
				<select name="yfym_vat" id="yfym_vat">
					<option value="disabled" <?php selected($yfym_vat, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
					<optgroup label="<?php _e('Enable. No default value', 'yfym'); ?>">
					<option value="enable" <?php selected($yfym_vat, 'enable'); ?>><?php _e('Enable', 'yfym'); ?></option>
					</optgroup>
					<optgroup label="<?php _e('Enable. Default value', 'yfym'); ?>">
					<option value="NO_VAT" <?php selected($yfym_vat, 'NO_VAT'); ?>><?php _e('No VAT', 'yfym'); ?></option>
					<option value="VAT_0" <?php selected($yfym_vat, 'VAT_0'); ?>>0%</option>
					<option value="VAT_10" <?php selected($yfym_vat, 'VAT_10'); ?>>10%</option>
					<option value="VAT_10_110" <?php selected($yfym_vat, 'VAT_10_110'); ?>>10/110</option>	
					<option value="VAT_18" <?php selected($yfym_vat, 'VAT_18'); ?>>18%</option>
					<option value="VAT_18_118" <?php selected($yfym_vat, 'VAT_18_118'); ?>>18/118</option>
					<option value="VAT_20" <?php selected($yfym_vat, 'VAT_20'); ?>>20%</option>
					<option value="VAT_20_120" <?php selected($yfym_vat, 'VAT_20_120'); ?>>20/120</option>
					</optgroup>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>vat</strong>. <br /><?php _e('This element is used when creating an YML feed for Yandex.Delivery', 'yfym'); ?>. <?php _e('Do not use it if you create YML-feed for Yandex Market', 'yfym'); ?>.<br />
				<a target="_blank" href="//yandex.ru/support/delivery/settings/vat.html"><?php _e('Read more on Yandex', 'yfym'); ?></a>
				</span>
			</td>
		 </tr> 		 
		 <tr class="yfym_tr">
			<th scope="row"><label for="yfym_delivery_options"><?php _e('Use delivery-options', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="yfym_delivery_options" id="yfym_delivery_options" <?php checked($yfym_delivery_options, 'on' ); ?>/><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>delivery-options</strong> <a target="_blank" href="//yandex.ru/support/partnermarket/elements/delivery-options.html#structure"><?php _e('Read more on Yandex', 'yfym'); ?></a></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_delivery_cost"><?php _e('Delivery cost', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input min="0" type="number" name="yfym_delivery_cost" id="yfym_delivery_cost" value="<?php echo $yfym_delivery_cost; ?>" /><br />
				<span class="description"><?php _e('Required element', 'yfym'); ?> <strong>cost</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_delivery_days"><?php _e('Delivery days', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="text" name="yfym_delivery_days" id="yfym_delivery_days" value="<?php echo $yfym_delivery_days; ?>" /><br />
				<span class="description"><?php _e('Required element', 'yfym'); ?> <strong>days</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_order_before"><?php _e('The time', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="text" name="yfym_order_before" id="yfym_order_before" value="<?php echo $yfym_order_before; ?>" /><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>order-before</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong>.<br /><?php _e('The time in which you need to place an order to get it at this time', 'yfym'); ?></span>
			</td>
		 </tr>
		 <tr class="yfym_tr">
			<th scope="row"><label for="yfym_delivery_options2"><?php _e('Add a second delivery methods', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="yfym_delivery_options2" id="yfym_delivery_options2" <?php checked($yfym_delivery_options2, 'on' ); ?>/><br />
				<span class="description"><?php _e('Add a second delivery methods to', 'yfym'); ?> <strong>delivery-options</strong> <a target="_blank" href="//yandex.ru/support/partnermarket/elements/delivery-options.html#structure"><?php _e('Read more on Yandex', 'yfym'); ?></a></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_delivery_cost2"><?php _e('Delivery cost', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input min="0" type="number" name="yfym_delivery_cost2" id="yfym_delivery_cost2" value="<?php echo $yfym_delivery_cost2; ?>" /><br />
				<span class="description"><?php _e('Required element', 'yfym'); ?> <strong>cost</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_delivery_days2"><?php _e('Delivery days', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="text" name="yfym_delivery_days2" id="yfym_delivery_days2" value="<?php echo $yfym_delivery_days2; ?>" /><br />
				<span class="description"><?php _e('Required element', 'yfym'); ?> <strong>days</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_order_before2"><?php _e('The time', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="text" name="yfym_order_before2" id="yfym_order_before2" value="<?php echo $yfym_order_before2; ?>" /><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>order-before</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong>.<br /><?php _e('The time in which you need to place an order to get it at this time', 'yfym'); ?></span>
			</td>
		 </tr>		 
		</tbody></table>
	   </div>
	 </div>	
	 <?php do_action('yfym_optional_element', $numFeed); ?>
	 <div class="postbox">
	   <h2 class="hndle"><?php _e('Other elements', 'yfym'); ?></h2>		 
	   <div class="inside">		
		<table class="form-table"><tbody>
		 <tr>
			<th scope="row"><label for="yfym_enable_auto_discounts"><?php _e('Use Auto discounts', 'yfym'); ?></label></th>
			<td class="overalldesc">
			 <input type="checkbox" name="yfym_enable_auto_discounts" id="yfym_enable_auto_discounts" <?php checked($yfym_enable_auto_discounts, 'on' ); ?>/><br /><span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>enable_auto_discounts</strong></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_barcode"><?php _e('Barcode', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_barcode" id="yfym_barcode">
				<option value="disabled" <?php selected($yfym_barcode, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
				<option value="sku" <?php selected($yfym_barcode, 'sku'); ?>><?php _e('Substitute from SKU', 'yfym'); ?></option>
				<option value="post_meta" <?php selected($yfym_barcode, 'post_meta'); ?>><?php _e('Substitute from post meta', 'yfym'); ?></option>
				<?php if (class_exists('WooCommerce_Germanized')) : ?>
				<option value="germanized" <?php selected($yfym_barcode, 'germanized'); ?>><?php _e('Substitute from', 'yfym'); ?> WooCommerce Germanized</option>
				<?php endif; ?>
				<?php foreach (yfym_get_attributes() as $attribute) : ?>
				<option value="<?php echo $attribute['id']; ?>" <?php selected($yfym_barcode, $attribute['id']); ?>><?php echo $attribute['name']; ?></option><?php endforeach; ?>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>barcode</strong></span><br />
				<span class="description"><?php _e('If selected', 'yfym'); ?> <span class="yfym_bold">"<?php _e('Substitute from post meta', 'yfym'); ?>"</span> <?php _e('do not forget to fill out this field', 'yfym'); ?>:</span><br />
				<input placeholder="<?php _e('Name post_meta', 'yfym'); ?>" type="text" name="yfym_barcode_post_meta" id="yfym_barcode_post_meta" value="<?php echo $yfym_barcode_post_meta; ?>" />
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_vendorcode"><?php _e('Vendor Code', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_vendorcode" id="yfym_vendorcode">
				<option value="disabled" <?php selected($yfym_vendorcode, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
				<option value="sku" <?php selected($yfym_vendorcode, 'sku'); ?>><?php _e('Substitute from SKU', 'yfym'); ?></option>
				<?php foreach (yfym_get_attributes() as $attribute) : ?>
				<option value="<?php echo $attribute['id']; ?>" <?php selected($yfym_vendorcode, $attribute['id']); ?>><?php echo $attribute['name']; ?></option><?php endforeach; ?>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>vendorCode</strong></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_expiry"><?php _e('Shelf life / service life', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_expiry" id="yfym_expiry">
				<option value="off" <?php selected($expiry, 'off'); ?>><?php _e('Disabled', 'yfym'); ?></option>
				<?php foreach (yfym_get_attributes() as $attribute) : ?>
				<option value="<?php echo $attribute['id']; ?>" <?php selected( $expiry, $attribute['id'] ); ?>><?php echo $attribute['name']; ?></option>	<?php endforeach; ?>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>expiry</strong>.<br /><?php _e('Shelf life / service life. expiry date / service life', 'yfym'); ?>.</span><br /><a target="_blank" href="//yandex.ru/support/partnermarket/offers.html"><?php _e('Read more on Yandex', 'yfym'); ?></a>
			</td>
		 </tr> 

		 <tr>
			<th scope="row"><label for="yfym_downloadable"><?php _e('Mark downloadable products', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_downloadable" id="yfym_downloadable">
				<option value="off" <?php selected($downloadable, 'off'); ?>><?php _e('Disabled', 'yfym'); ?></option>
				<option value="on" <?php selected($downloadable, 'on'); ?>><?php _e('On', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>downloadable</strong></span>
			</td>
		 </tr>

		 <tr>
			 <th scope="row"><label for="yfym_age"><?php _e('Age', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_age" id="yfym_age">
				<option value="off" <?php selected($age, 'off'); ?>><?php _e('Disabled', 'yfym'); ?></option>
				<?php foreach (yfym_get_attributes() as $attribute) : ?>
				<option value="<?php echo $attribute['id']; ?>" <?php selected($age, $attribute['id'] ); ?>><?php echo $attribute['name']; ?></option>	<?php endforeach; ?>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>age</strong></span>
			</td>
		 </tr>
		 
		 <tr>
			<th scope="row"><label for="yfym_model"><?php _e('Model', 'yfym'); ?></label></th>
			<td class="overalldesc">
			 <select name="yfym_model" id="yfym_model">
				<option value="off" <?php selected($model, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
				<option value="sku" <?php selected($model, 'sku'); ?>><?php _e('Substitute from SKU', 'yfym'); ?></option>
				<?php foreach (yfym_get_attributes() as $attribute) : ?>
				<option value="<?php echo $attribute['id']; ?>" <?php selected($model, $attribute['id']); ?>><?php echo $attribute['name']; ?></option>
				<?php endforeach; ?>
			 </select><br />
			 <span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>model</strong><br />(<?php _e('Considered obsolete. Do not use the Yandex Market', 'yfym'); ?>.)</span>
			</td>
		 </tr>	
		<tr>
			<th scope="row"><label for="yfym_manufacturer_warranty"><?php _e('Manufacturer warrant', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_manufacturer_warranty" id="yfym_manufacturer_warranty">	 
					<option value="off" <?php selected($manufacturer_warranty, 'off'); ?>><?php _e('Disabled', 'yfym'); ?></option>
					<option value="alltrue" <?php selected($manufacturer_warranty, 'alltrue'); ?>><?php _e('Add to all', 'yfym'); ?> true</option>
					<option value="allfalse" <?php selected($manufacturer_warranty, 'allfalse'); ?>><?php _e('Add to all', 'yfym'); ?> false</option>
					<?php foreach (yfym_get_attributes() as $attribute) : ?>
					<option value="<?php echo $attribute['id']; ?>" <?php selected( $manufacturer_warranty, $attribute['id'] ); ?>><?php echo $attribute['name']; ?></option>
					<?php endforeach; ?>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>manufacturer_warranty</strong>. <?php _e("This element is used for products that have an official manufacturer's warranty", 'yfym'); ?>.</span><ul><li>false — <?php _e('Product does not have an official warranty', 'yfym'); ?></li><li>true — <?php _e('Product has an official warranty', 'yfym'); ?></li></ul>
			</td>
		</tr>
		<?php do_action('yfym_after_manufacturer_warranty', $numFeed); ?>
		 <tr>
			<th scope="row"><label for="yfym_sales_notes"><?php _e('Sales notes', 'yfym'); ?></label><br /><span style="color: red;"><?php _e('Attention!', 'yfym'); ?></span> <a target="_blank" href="https://icopydoc.ru/sales-notes/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=sales-notes-instruction"><?php _e('Read more about how this works', 'yfym'); ?></a></th>
			<td class="overalldesc">
				<select name="yfym_sales_notes_cat" id="yfym_sales_notes">
				<option value="off" <?php selected($sales_notes_cat, 'off'); ?>><?php _e('Disable use of Sales notes', 'yfym'); ?></option>
				<?php foreach (yfym_get_attributes() as $attribute) : ?>
				<option value="<?php echo $attribute['id']; ?>" <?php selected( $sales_notes_cat, $attribute['id'] ); ?>><?php echo $attribute['name']; ?></option>
				<?php endforeach; ?>
				</select>
				<p><?php _e('If the attribute from the select is absent from the product, then substitute', 'yfym'); ?>:</p><p>
				<textarea maxlength="50" style="width: 100%;" name="yfym_sales_notes" placeholder="<?php _e('Sales notes', 'yfym'); ?>" class="form-required"><?php echo $sales_notes; ?></textarea></p>
				<span class="description">
				<?php _e('Optional element', 'yfym'); ?> <strong>Sales notes</strong></span><br />
				<span class="description" style="color: red;"><?php _e('Attention!', 'yfym'); ?></span> <span class="description"><?php _e('The text may be up to 50 characters in length. Also in the item is forbidden to specify the terms of delivery and price reduction (discount on merchandise)', 'yfym'); ?>.<br /> 
				<a target="_blank" href="//yandex.ru/support/partnermarket/elements/sales_notes.html"><?php _e('Read more on Yandex', 'yfym'); ?></a></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_store"><?php _e('Store', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<select name="yfym_store" id="yfym_store">
				<option value="true" <?php selected($yfym_store, 'true'); ?>><?php _e('True', 'yfym'); ?></option>
				<option value="false" <?php selected($yfym_store, 'false'); ?>><?php _e('False', 'yfym'); ?></option>
				</select><br />
				<span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>store</strong></span>
				<ul>
					<li><?php _e('true', 'yfym'); ?> — <?php _e('The product can be purchased in retail stores', 'yfym'); ?></li>
					<li><?php _e('false', 'yfym'); ?> — <?php _e('the product cannot be purchased in retail stores', 'yfym'); ?></li>
				</ul><span class="description">
				<a target="_blank" href="//yandex.ru/support/partnermarket/delivery.html"><?php _e('Read more on Yandex', 'yfym'); ?></a>
				</span>
			</td>
		 </tr>		 
		 <tr>
			<th scope="row"><label for="yfym_params_arr"><?php _e('Include these attributes in the values Param', 'yfym'); ?></label>
			</th>
			<td class="overalldesc">
			 <select id="yfym_params_arr" style="width: 100%;" name="yfym_params_arr[]" size="8" multiple>
				<?php foreach (yfym_get_attributes() as $attribute) : ?>
					<option value="<?php echo $attribute['id']; ?>"<?php if (!empty($params_arr)) {foreach ($params_arr as $value) {selected($value, $attribute['id']);}} ?>><?php echo $attribute['name']; ?></option>
				<?php endforeach; ?>
			 </select><br />
			 <span class="description"><?php _e('Optional element', 'yfym'); ?> <strong>param</strong></span><br />
			 <span class="description" style="color: blue;"><?php _e('Hint', 'yfym'); ?>:</span> <span class="description"><?php _e('To select multiple values, hold down the (ctrl) button on Windows or (cmd) on a Mac. To deselect, press and hold (ctrl) or (cmd), click on the marked items', 'yfym'); ?></span>
			</td>
		 </tr>
		</tbody></table> 
	   </div>
	 </div>
	 <?php do_action('yfym_before_button_primary_submit', $numFeed); ?>	 
	 <div class="postbox">
	  <div class="inside">
		<table class="form-table"><tbody>
		 <tr>
			<th scope="row"><label for="button-primary"></label></th>
			<td class="overalldesc"><?php wp_nonce_field('yfym_nonce_action','yfym_nonce_field'); ?><input id="button-primary" class="button-primary" type="submit" name="yfym_submit_action" value="<?php _e( 'Save', 'yfym'); ?>" /><br />
			<span class="description"><?php _e('Click to save the settings', 'yfym'); ?></span></td>
		 </tr>
		</tbody></table>
	  </div>
	 </div>

	 <?php do_action('yfym_append_form_container_2', $numFeed); ?>
	</form>
	<?php do_action('yfym_append_container_2', $numFeed); ?>
  </div></div>
 </div><!-- /post-body --><br class="clear"></div><!-- /poststuff -->
 <?php do_action('yfym_after_poststuff', $numFeed); ?>

 <div id="icp_slides" class="clear">
  <div class="icp_wrap">
	<input type="radio" name="icp_slides" id="icp_point1">
	<input type="radio" name="icp_slides" id="icp_point2">
	<input type="radio" name="icp_slides" id="icp_point3" checked>
	<input type="radio" name="icp_slides" id="icp_point4">
	<input type="radio" name="icp_slides" id="icp_point5">
	<input type="radio" name="icp_slides" id="icp_point6">
	<input type="radio" name="icp_slides" id="icp_point7">
	<div class="icp_slider">
		<div class="icp_slides icp_img1"><a href="//wordpress.org/plugins/yml-for-yandex-market/" target="_blank"></a></div>
		<div class="icp_slides icp_img2"><a href="//wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"></a></div>
		<div class="icp_slides icp_img3"><a href="//wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"></a></div>
		<div class="icp_slides icp_img4"><a href="//wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"></a></div>
		<div class="icp_slides icp_img5"><a href="//wordpress.org/plugins/xml-for-avito/" target="_blank"></a></div>
		<div class="icp_slides icp_img6"><a href="//wordpress.org/plugins/xml-for-o-yandex/" target="_blank"></a></div>
		<div class="icp_slides icp_img7"><a href="//wordpress.org/plugins/import-from-yml/" target="_blank"></a></div>
	</div>
	<div class="icp_control">
		<label for="icp_point1"></label>
		<label for="icp_point2"></label>
		<label for="icp_point3"></label>
		<label for="icp_point4"></label>
		<label for="icp_point5"></label>
		<label for="icp_point6"></label>
		<label for="icp_point7"></label>
	</div>
  </div> 
 </div>
 <?php do_action('yfym_after_icp_slides', $numFeed); ?>

 <div class="metabox-holder">
  <div class="postbox">
  	<h2 class="hndle"><?php _e('My plugins that may interest you', 'yfym'); ?></h2>
	<div class="inside">
		<p><span class="yfym_bold">XML for Google Merchant Center</span> - <?php _e('Сreates a XML-feed to upload to Google Merchant Center', 'yfym'); ?>. <a href="https://wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p> 
		<p><span class="yfym_bold">YML for Yandex Market</span> - <?php _e('Сreates a YML-feed for importing your products to Yandex Market', 'yfym'); ?>. <a href="https://wordpress.org/plugins/yml-for-yandex-market/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
		<p><span class="yfym_bold">Import from YML</span> - <?php _e('Imports products from YML to your shop', 'yfym'); ?>. <a href="https://wordpress.org/plugins/import-from-yml/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
		<p><span class="yfym_bold">XML for Hotline</span> - <?php _e('Сreates a XML-feed for importing your products to Hotline', 'yfym'); ?>. <a href="https://wordpress.org/plugins/xml-for-hotline/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
		<p><span class="yfym_bold">Gift upon purchase for WooCommerce</span> - <?php _e('This plugin will add a marketing tool that will allow you to give gifts to the buyer upon purchase', 'yfym'); ?>. <a href="https://wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
		<p><span class="yfym_bold">Import products to ok.ru</span> - <?php _e('With this plugin, you can import products to your group on ok.ru', 'yfym'); ?>. <a href="https://wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
		<p><span class="yfym_bold">XML for Avito</span> - <?php _e('Сreates a XML-feed for importing your products to', 'yfym'); ?> Avito. <a href="https://wordpress.org/plugins/xml-for-avito/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
		<p><span class="yfym_bold">XML for O.Yandex (Яндекс Объявления)</span> - <?php _e('Сreates a XML-feed for importing your products to', 'yfym'); ?> Яндекс.Объявления. <a href="https://wordpress.org/plugins/xml-for-o-yandex/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
	</div>
  </div>
 </div>
 <?php do_action('yfym_append_wrap', $numFeed); ?>
</div><!-- /wrap -->
<?php
} /* end функция настроек yfym_export_page */ ?>