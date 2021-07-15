<?php defined('ABSPATH') OR exit;
/*
Plugin Name: Yml for Yandex Market
Description: Подключите свой магазин к Яндекс Маркету и выгружайте товары, получая новых клиентов!
Tags: yml, yandex, market, export, woocommerce
Author: Maxim Glazunov
Author URI: https://icopydoc.ru
License: GPLv2
Version: 3.5.5
Text Domain: yml-for-yandex-market
Domain Path: /languages/
WC requires at least: 3.0.0
WC tested up to: 5.4.1
*/
/*	Copyright YEAR PLUGIN_AUTHOR_NAME (email : djdiplomat@yandex.ru)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
require_once plugin_dir_path(__FILE__).'/functions.php'; // Подключаем файл функций
require_once plugin_dir_path(__FILE__).'/offer.php';
require_once plugin_dir_path(__FILE__).'/models/adv.php';
require_once plugin_dir_path(__FILE__).'/models/all_elements.php';
require_once plugin_dir_path(__FILE__).'/models/dbs.php';
// require_once plugin_dir_path(__FILE__).'/models/main_part.php';
require_once plugin_dir_path(__FILE__).'/models/old.php';
require_once plugin_dir_path(__FILE__).'/models/single_catalog.php';
require_once plugin_dir_path(__FILE__).'/models/ozon.php';
// require_once plugin_dir_path(__FILE__).'/models/main_part.php';
register_activation_hook(__FILE__, array('YmlforYandexMarket', 'on_activation'));
register_deactivation_hook(__FILE__, array('YmlforYandexMarket', 'on_deactivation'));
register_uninstall_hook(__FILE__, array('YmlforYandexMarket', 'on_uninstall'));
add_action('plugins_loaded', array('YmlforYandexMarket', 'init'));
add_action('plugins_loaded', 'yfym_load_plugin_textdomain'); // load translation
function yfym_load_plugin_textdomain() {
 load_plugin_textdomain('yfym', false, dirname(plugin_basename(__FILE__)).'/languages/');
}
class YmlforYandexMarket {
 protected static $instance;
 public static function init() {
	is_null(self::$instance) AND self::$instance = new self;
	return self::$instance;
 }

 public function __construct() {
	// yfym_DIR contains /home/p135/www/site.ru/wp-content/plugins/myplagin/
	define('yfym_DIR', plugin_dir_path(__FILE__)); 
	// yfym_URL contains http://site.ru/wp-content/plugins/myplagin/
	define('yfym_URL', plugin_dir_url(__FILE__));
	// yfym_UPLOAD_DIR contains /home/p256/www/site.ru/wp-content/uploads
	$upload_dir = (object)wp_get_upload_dir();
	define('yfym_UPLOAD_DIR', $upload_dir->basedir);
	// yfym_UPLOAD_DIR contains /home/p256/www/site.ru/wp-content/uploads/yfym
	$name_dir = $upload_dir->basedir."/yfym"; 
	define('yfym_NAME_DIR', $name_dir);
	$yfym_keeplogs = yfym_optionGET('yfym_keeplogs');
	define('yfym_KEEPLOGS', $yfym_keeplogs);
	define('yfym_VER', '3.5.5');
	$yfym_version = yfym_optionGET('yfym_version');
  	if ($yfym_version !== yfym_VER) {yfym_set_new_options();} // автообновим настройки, если нужно	
	if (!defined('yfym_ALLNUMFEED')) {
		define('yfym_ALLNUMFEED', '5');
	}

	add_action('admin_menu', array($this, 'add_admin_menu'));
	add_filter('upload_mimes', array($this, 'yfym_add_mime_types'), 11); // чутка позже остальных

	add_filter('cron_schedules', array($this, 'cron_add_seventy_sec'));
	add_filter('cron_schedules', array($this, 'cron_add_five_min'));	
	add_filter('cron_schedules', array($this, 'cron_add_six_hours'));

	add_action('yfym_cron_sborki', array($this, 'yfym_do_this_seventy_sec'), 10, 1);
	add_action('yfym_cron_period', array($this, 'yfym_do_this_event'), 10, 1);
		
	// индивидуальные опции доставки товара
	add_action('save_post', array($this, 'yfym_save_post_product_function'), 50, 3);
	// пришлось юзать save_post вместо save_post_product ибо wc блочит обновы
	// https://wpruse.ru/woocommerce/custom-fields-in-products/
	// https://wpruse.ru/woocommerce/custom-fields-in-variations/
	add_filter('woocommerce_product_data_tabs', array($this, 'yfym_added_wc_tabs'), 10, 1);
	add_action('admin_footer', array($this, 'yfym_art_added_tabs_icon'), 10, 1);
	add_action('woocommerce_product_data_panels', array($this, 'yfym_art_added_tabs_panel'), 10, 1);
	add_action('woocommerce_process_product_meta',  array($this, 'yfym_art_woo_custom_fields_save'), 10, 1);
	add_action('woocommerce_product_options_general_product_data', array($this, 'yfym_woocommerce_product_options_general_product_data'), 10, 1);
	
	add_action('admin_notices', array($this, 'yfym_admin_notices_function'));

	/* Регаем стили только для страницы настроек плагина */
	add_action('admin_init', function() {
		wp_register_style('yfym-admin-css', plugins_url('css/yfym_style.css', __FILE__));
	}, 9999);

	add_filter('plugin_action_links', array($this, 'yfym_plugin_action_links'), 10, 2 );
 }

 public static function yfym_plugin_action_links($actions, $plugin_file) {
	if (false === strpos($plugin_file, basename(__FILE__))) {
		// проверка, что у нас текущий плагин
		return $actions;
	}
	$settings_link = '<a style="color: green; font-weight: 700;" href="/wp-admin/admin.php?page=yfymextensions">'. __('More features', 'yfym').'</a>';
	array_unshift($actions, $settings_link); 	
	$settings_link = '<a href="/wp-admin/admin.php?page=yfymexport">'. __('Settings', 'yfym').'</a>';
	array_unshift($actions, $settings_link); 
	return $actions; 
 }

 public static function yfym_admin_css_func() {
	/* Ставим css-файл в очередь на вывод */
	wp_enqueue_style('yfym-admin-css');
 } 

 public static function yfym_admin_head_css_func() {
	/* печатаем css в шапке админки */
	print '<style>/* Yml for Yandex Market */
		.metabox-holder .postbox-container .empty-container {height: auto !important;}
		.icp_img1 {background-image: url('. yfym_URL .'img/sl1.jpg);}
		.icp_img2 {background-image: url('. yfym_URL .'img/sl2.jpg);}
		.icp_img3 {background-image: url('. yfym_URL .'img/sl3.jpg);}
		.icp_img4 {background-image: url('. yfym_URL .'img/sl4.jpg);}
		.icp_img5 {background-image: url('. yfym_URL .'img/sl5.jpg);}
		.icp_img6 {background-image: url('. yfym_URL .'img/sl6.jpg);}
		.icp_img7 {background-image: url('. yfym_URL .'img/sl7.jpg);}
	</style>';
 }  
 
 // Срабатывает при активации плагина (вызывается единожды)
 public static function on_activation() {
	$upload_dir = (object)wp_get_upload_dir();
	$name_dir = $upload_dir->basedir.'/yfym';
	if (!is_dir($name_dir)) {
	 if (!mkdir($name_dir)) {
		error_log('ERROR: Ошибка создания папки '.$name_dir.'; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
		//return false;
	 }
	}
	$numFeed = '1'; // (string)
	if (!defined('yfym_ALLNUMFEED')) {define('yfym_ALLNUMFEED', '5');}
	$allNumFeed = (int)yfym_ALLNUMFEED;
	for ($i = 1; $i<$allNumFeed+1; $i++) {
		$name_dir = $upload_dir->basedir.'/yfym/feed'.$numFeed;
		if (!is_dir($name_dir)) {
		 if (!mkdir($name_dir)) {
			error_log('ERROR: Ошибка создания папки '.$name_dir.'; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
			//return false;
		 }
		}
		yfym_optionADD('yfym_status_cron', 'off', $numFeed);
		yfym_optionADD('yfym_step_export', '500', $numFeed);
		yfym_optionADD('yfym_status_sborki', '-1', $numFeed); // статус сборки файла
		yfym_optionADD('yfym_date_sborki', 'unknown', $numFeed); // дата последней сборки
		yfym_optionADD('yfym_type_sborki', 'yml', $numFeed); // тип собираемого файла yml или xls
		yfym_optionADD('yfym_file_url', '', $numFeed); // урл до файла
		yfym_optionADD('yfym_file_file', '', $numFeed); // путь до файла
		yfym_optionADD('yfym_file_ids_in_yml', '', $numFeed);
		yfym_optionADD('yfym_ufup', '0', $numFeed);
		yfym_optionADD('yfym_magazin_type', 'woocommerce', $numFeed); // тип плагина магазина 
		yfym_optionADD('yfym_vendor', 'disabled', $numFeed); 
		yfym_optionADD('yfym_whot_export', 'all', $numFeed); // что выгружать (все или там где галка)
		yfym_optionADD('yfym_yml_rules', 'yandex_market', $numFeed);
		yfym_optionADD('yfym_skip_missing_products', '0', $numFeed);
		yfym_optionADD('yfym_date_save_set', 'unknown', $numFeed); // дата сохранения настроек		
		yfym_optionADD('yfym_separator_type', 'type1', $numFeed); 
		yfym_optionADD('yfym_behavior_onbackorder', 'false', $numFeed); 
		yfym_optionADD('yfym_behavior_stip_symbol', 'default', $numFeed); 
		yfym_optionADD('yfym_feed_assignment', '', $numFeed);
		yfym_optionADD('yfym_file_extension', 'xml', $numFeed, 'no');

		yfym_optionADD('yfym_shop_sku', 'disabled', $numFeed);
		yfym_optionADD('yfym_count', 'disabled', $numFeed);
		yfym_optionADD('yfym_auto_disabled', 'disabled', $numFeed);
		yfym_optionADD('yfym_amount', 'disabled', $numFeed);
		yfym_optionADD('yfym_manufacturer', 'disabled', $numFeed);	

		$blog_title = get_bloginfo('name');
		$blog_title = substr($blog_title, 0, 20);
		yfym_optionADD('yfym_shop_name', $blog_title, $numFeed);
		yfym_optionADD('yfym_company_name', $blog_title, $numFeed);
		yfym_optionADD('yfym_main_product', 'other', $numFeed);		
		yfym_optionADD('yfym_adult', 'no', $numFeed);
		yfym_optionADD('yfym_wooc_currencies', '', $numFeed);
		yfym_optionADD('yfym_desc', 'fullexcerpt', $numFeed);
		yfym_optionADD('yfym_the_content', 'enabled', $numFeed);
		yfym_optionADD('yfym_var_desc_priority', 'on', $numFeed);
		yfym_optionADD('yfym_clear_get', 'no', $numFeed);
		yfym_optionADD('yfym_price_from', 'no', $numFeed); // разрешить "цена от"
		yfym_optionADD('yfym_oldprice', 'no', $numFeed);
		yfym_optionADD('yfym_vat', 'disabled', $numFeed);
		yfym_optionADD('yfym_params_arr', serialize(array()), $numFeed);
		yfym_optionADD('yfym_add_in_name_arr', serialize(array()), $numFeed);
		yfym_optionADD('yfym_no_group_id_arr', serialize(array()), $numFeed);
/* ? */	yfym_optionADD('yfym_product_tag_arr', '', $numFeed); // id меток таксономии product_tag
		yfym_optionADD('yfym_store', 'false', $numFeed);
		yfym_optionADD('yfym_delivery', 'false', $numFeed);
		yfym_optionADD('yfym_delivery_options', '0', $numFeed);
		yfym_optionADD('yfym_delivery_cost', '0', $numFeed);
		yfym_optionADD('yfym_delivery_days', '32', $numFeed);
		yfym_optionADD('yfym_order_before', '', $numFeed);
		yfym_optionADD('yfym_delivery_options2', '0', $numFeed);
		yfym_optionADD('yfym_delivery_cost2', '0', $numFeed);
		yfym_optionADD('yfym_delivery_days2', '32', $numFeed);
		yfym_optionADD('yfym_order_before2', '', $numFeed);		
		yfym_optionADD('yfym_sales_notes_cat', 'off', $numFeed);
		yfym_optionADD('yfym_sales_notes', '', $numFeed);
		yfym_optionADD('yfym_model', 'disabled', $numFeed); // атрибут model магазина
		yfym_optionADD('yfym_pickup', 'true', $numFeed);
		yfym_optionADD('yfym_barcode', 'disabled', $numFeed);
		yfym_optionADD('yfym_barcode_post_meta', '', $numFeed);
		yfym_optionADD('yfym_vendorcode', 'disabled', $numFeed);
		yfym_optionADD('yfym_enable_auto_discount', '', $numFeed);
		yfym_optionADD('yfym_expiry', 'off', $numFeed);
		yfym_optionADD('yfym_downloadable', 'off', $numFeed);
		yfym_optionADD('yfym_age', 'off', $numFeed);	
		yfym_optionADD('yfym_country_of_origin', 'off', $numFeed);
		yfym_optionADD('yfym_manufacturer_warranty', 'off', $numFeed);
		yfym_optionADD('yfym_errors', '', $numFeed);
		yfym_optionADD('yfym_enable_auto_discounts', '', $numFeed);
		yfym_optionADD('yfym_skip_backorders_products', '0', $numFeed);
		yfym_optionADD('yfym_no_default_png_products', '0', $numFeed);	
		yfym_optionADD('yfym_skip_products_without_pic', '0', $numFeed);
		$numFeed++;		
	}
	if (is_multisite()) {
		add_blog_option(get_current_blog_id(), 'yfym_version', '3.5.5');
		add_blog_option(get_current_blog_id(), 'yfym_keeplogs', '0');
		add_blog_option(get_current_blog_id(), 'yfym_disable_notices', '0');
		add_blog_option(get_current_blog_id(), 'yfym_enable_five_min', '0');
	} else {
		add_option('yfym_version', '3.5.5');
		add_option('yfym_keeplogs', '0');
		add_option('yfym_disable_notices', '0');
		add_option('yfym_enable_five_min', '0');		
	}	
 }
 
 // Срабатывает при отключении плагина (вызывается единожды)
 public static function on_deactivation() {
	$numFeed = '1'; // (string)
	if (!defined('yfym_ALLNUMFEED')) {define('yfym_ALLNUMFEED', '5');}
	$allNumFeed = (int)yfym_ALLNUMFEED;
	for ($i = 1; $i<$allNumFeed+1; $i++) {	 
		wp_clear_scheduled_hook('yfym_cron_period', array($numFeed));
		wp_clear_scheduled_hook('yfym_cron_sborki', array($numFeed));
		$numFeed++;
	}
	deactivate_plugins('yml-for-yandex-market-aliexpress-export/yml-for-yandex-market-aliexpress-export.php');
	deactivate_plugins('yml-for-yandex-market-book-export/yml-for-yandex-market-book-export.php');	
	deactivate_plugins('yml-for-yandex-market-pro/yml-for-yandex-market-pro.php');
	deactivate_plugins('yml-for-yandex-market-prom-export/yml-for-yandex-market-prom-export.php');
	deactivate_plugins('yml-for-yandex-market-promos-export/yml-for-yandex-market-promos-export.php');	
	deactivate_plugins('yml-for-yandex-market-rozetka-export/yml-for-yandex-market-rozetka-export.php');
 } 
 
 // Срабатывает при удалении плагина (вызывается единожды)
 public static function on_uninstall() {
	if (is_multisite()) {		
		delete_blog_option(get_current_blog_id(), 'yfym_version');
		delete_blog_option(get_current_blog_id(), 'yfym_keeplogs');
		delete_blog_option(get_current_blog_id(), 'yfym_disable_notices');
		delete_blog_option(get_current_blog_id(), 'yfym_enable_five_min');			
	} else {
		delete_option('yfym_version');
		delete_option('yfym_keeplogs');
		delete_option('yfym_disable_notices');
		delete_option('yfym_enable_five_min');
	}
	$numFeed = '1'; // (string)
	$allNumFeed = (int)yfym_ALLNUMFEED;
	for ($i = 1; $i<$allNumFeed+1; $i++) {		
		yfym_optionDEL('yfym_shop_sku', $numFeed);
		yfym_optionDEL('yfym_count', $numFeed);
		yfym_optionADD('yfym_auto_disabled', $numFeed);
		yfym_optionDEL('yfym_amount', $numFeed);
		yfym_optionDEL('yfym_manufacturer', $numFeed);

		yfym_optionDEL('yfym_shop_name', $numFeed);
		yfym_optionDEL('yfym_company_name', $numFeed);
		yfym_optionDEL('yfym_main_product', $numFeed);			
		yfym_optionDEL('yfym_version', $numFeed);
		yfym_optionDEL('yfym_status_cron', $numFeed);
		yfym_optionDEL('yfym_whot_export', $numFeed);
		yfym_optionDEL('yfym_yml_rules', $numFeed);
		yfym_optionDEL('yfym_skip_missing_products', $numFeed);
		yfym_optionDEL('yfym_date_save_set', $numFeed);
		yfym_optionDEL('yfym_separator_type', $numFeed);
		yfym_optionDEL('yfym_behavior_onbackorder', $numFeed);
		yfym_optionDEL('yfym_behavior_stip_symbol', $numFeed); 
		yfym_optionDEL('yfym_feed_assignment', $numFeed);
		yfym_optionDEL('yfym_file_extension', $numFeed);
		yfym_optionDEL('yfym_status_sborki', $numFeed);
		yfym_optionDEL('yfym_date_sborki', $numFeed);
		yfym_optionDEL('yfym_type_sborki', $numFeed);
		yfym_optionDEL('yfym_vendor', $numFeed);
		yfym_optionDEL('yfym_model', $numFeed);
		yfym_optionDEL('yfym_params_arr', $numFeed);
		yfym_optionDEL('yfym_add_in_name_arr', $numFeed);
		yfym_optionDEL('yfym_no_group_id_arr', $numFeed);
/*?*/	yfym_optionDEL('yfym_product_tag_arr', $numFeed);
		yfym_optionDEL('yfym_file_url', $numFeed);
		yfym_optionDEL('yfym_file_file', $numFeed);
		yfym_optionDEL('yfym_ufup', $numFeed);
		yfym_optionDEL('yfym_magazin_type', $numFeed);
		yfym_optionDEL('yfym_pickup', $numFeed);
		yfym_optionDEL('yfym_store', $numFeed);
		yfym_optionDEL('yfym_delivery', $numFeed);
		yfym_optionDEL('yfym_delivery_options', $numFeed);		
		yfym_optionDEL('yfym_delivery_cost', $numFeed);
		yfym_optionDEL('yfym_delivery_days', $numFeed);
		yfym_optionDEL('yfym_order_before', $numFeed);	
		yfym_optionDEL('yfym_delivery_options2', $numFeed);
		yfym_optionDEL('yfym_delivery_cost2', $numFeed);
		yfym_optionDEL('yfym_delivery_days2', $numFeed);
		yfym_optionDEL('yfym_order_before2', $numFeed);		
		yfym_optionDEL('yfym_sales_notes_cat', $numFeed);
		yfym_optionDEL('yfym_sales_notes', $numFeed);
		yfym_optionDEL('yfym_price_from', $numFeed);	
		yfym_optionDEL('yfym_desc', $numFeed);
		yfym_optionDEL('yfym_the_content', $numFeed);
		yfym_optionDEL('yfym_var_desc_priority', $numFeed);
		yfym_optionDEL('yfym_clear_get', $numFeed);
		yfym_optionDEL('yfym_barcode', $numFeed);
		yfym_optionDEL('yfym_barcode_post_meta', $numFeed);
		yfym_optionDEL('yfym_vendorcode', $numFeed);
		yfym_optionDEL('yfym_enable_auto_discount', $numFeed);
		yfym_optionDEL('yfym_expiry', $numFeed);
		yfym_optionDEL('yfym_downloadable', $numFeed);
		yfym_optionDEL('yfym_age', $numFeed);
		yfym_optionDEL('yfym_country_of_origin', $numFeed);
		yfym_optionDEL('yfym_manufacturer_warranty', $numFeed);
		yfym_optionDEL('yfym_adult', $numFeed);
		yfym_optionDEL('yfym_wooc_currencies', $numFeed);
		yfym_optionDEL('yfym_oldprice', $numFeed);
		yfym_optionDEL('yfym_vat', $numFeed);
		yfym_optionDEL('yfym_step_export', $numFeed);
		yfym_optionDEL('yfym_errors', $numFeed);
		yfym_optionDEL('yfym_enable_auto_discounts', $numFeed);
		yfym_optionDEL('yfym_skip_backorders_products', $numFeed);
		yfym_optionDEL('yfym_no_default_png_products', $numFeed);
		yfym_optionDEL('yfym_skip_products_without_pic', $numFeed);
		$numFeed++;
	}
 }

 // Добавляем пункты меню
 public function add_admin_menu() {
	$page_suffix = add_menu_page(null , __('Export Yandex Market', 'yfym'), 'manage_options', 'yfymexport', 'yfym_export_page', 'dashicons-redo', 51);
	require_once yfym_DIR.'/export.php'; // Подключаем файл настроек
	// создаём хук, чтобы стили выводились только на странице настроек
	add_action('admin_print_styles-'. $page_suffix, array($this, 'yfym_admin_css_func'));
 	add_action('admin_print_styles-'. $page_suffix, array($this, 'yfym_admin_head_css_func'));

/*	if (!class_exists('WP_List_Table')) {
		require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
	}
	require_once plugin_dir_path(__FILE__).'/includes/class_wp_list_table.php';
*/
	add_submenu_page('yfymexport', __('Debug', 'yfym'), __('Debug page', 'yfym'), 'manage_options', 'yfymdebug', 'yfym_debug_page');
	require_once yfym_DIR.'/debug.php';
	$page_subsuffix = add_submenu_page('yfymexport', __('Add Extensions', 'yfym'), __('Extensions', 'yfym'), 'manage_options', 'yfymextensions', 'yfym_extensions_page');
	require_once yfym_DIR.'/extensions.php';
	add_action('admin_print_styles-'. $page_subsuffix, array($this, 'yfym_admin_css_func'));
 } 
 
 // Разрешим загрузку xml и csv файлов
 public function yfym_add_mime_types($mimes) {
	$mimes ['csv'] = 'text/csv';
	$mimes ['xml'] = 'text/xml';
	$mimes ['yml'] = 'text/yml';
	return $mimes;
 } 

 /* добавляем интервалы крон в 70 секунд и 6 часов */
 public function cron_add_seventy_sec($schedules) {
	$schedules['seventy_sec'] = array(
		'interval' => 70,
		'display' => '70 sec'
	);
	return $schedules;
 }
 public function cron_add_five_min($schedules) {
	$schedules['five_min'] = array(
		'interval' => 300,
		'display' => '5 min'
	);
	return $schedules;
 } 
 public function cron_add_six_hours($schedules) {
	$schedules['six_hours'] = array(
		'interval' => 21600,
		'display' => '6 hours'
	);
	return $schedules;
 }
 /* end добавляем интервалы крон в 70 секунд и 6 часов */ 
 
 // Сохраняем данные блока, когда пост сохраняется
 function yfym_save_post_product_function ($post_id, $post, $update) {
	yfym_error_log('Стартовала функция yfym_save_post_product_function! Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
	
	if ($post->post_type !== 'product') {return;} // если это не товар вукомерц
	if (wp_is_post_revision($post_id)) {return;} // если это ревизия
	// проверяем nonce нашей страницы, потому что save_post может быть вызван с другого места.
	// если это автосохранение ничего не делаем
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {return;}
	// проверяем права юзера
	if (!current_user_can('edit_post', $post_id)) {return;}
	// Все ОК. Теперь, нужно найти и сохранить данные
	// Очищаем значение поля input.
	yfym_error_log('Работает функция yfym_save_post_product_function! Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);

	// Убедимся что поле установлено.
	if (isset($_POST['yfym_cost'])) {	
		$yfym_individual_delivery = sanitize_text_field($_POST['yfym_individual_delivery']);
		$yfym_cost = sanitize_text_field($_POST['yfym_cost']);
		$yfym_days = sanitize_text_field($_POST['yfym_days']);		
		$yfym_order_before = sanitize_text_field($_POST['yfym_order_before']);
		$yfym_individual_pickup = sanitize_text_field($_POST['yfym_individual_pickup']);
		$yfym_pickup_cost = sanitize_text_field($_POST['yfym_pickup_cost']);
		$yfym_pickup_days = sanitize_text_field($_POST['yfym_pickup_days']);
		$yfym_pickup_order_before = sanitize_text_field($_POST['yfym_pickup_order_before']);
		$yfym_bid = sanitize_text_field($_POST['yfym_bid']);
		$yfym_individual_vat = sanitize_text_field($_POST['yfym_individual_vat']);
		$yfym_condition = sanitize_text_field($_POST['yfym_condition']);
		$yfym_reason = sanitize_text_field($_POST['yfym_reason']);
		$market_sku = sanitize_text_field($_POST['_yfym_market_sku']);
		$tn_ved_code = sanitize_text_field($_POST['_yfym_tn_ved_code']);
		$yfym_credit_template = sanitize_text_field($_POST['yfym_credit_template']);
		$yfym_supplier = sanitize_text_field($_POST['_yfym_supplier']);
		$yfym_min_quantity = sanitize_text_field($_POST['_yfym_min_quantity']);
		$yfym_step_quantity = sanitize_text_field($_POST['_yfym_step_quantity']);
		$yfym_premium_price = sanitize_text_field($_POST['_yfym_premium_price']);

		$yfym_recommend_stock_data_arr = array();
		$yfym_recommend_stock_data_arr['availability'] = sanitize_text_field($_POST['_yfym_availability']);
		$yfym_recommend_stock_data_arr['transport_unit'] = sanitize_text_field($_POST['_yfym_transport_unit']);
		$yfym_recommend_stock_data_arr['min_delivery_pieces'] = sanitize_text_field($_POST['_yfym_min_delivery_pieces']);
		$yfym_recommend_stock_data_arr['quantum'] = sanitize_text_field($_POST['_yfym_quantum']);
		$yfym_recommend_stock_data_arr['leadtime'] = sanitize_text_field($_POST['_yfym_leadtime']);
		$yfym_recommend_stock_data_arr['box_count'] = sanitize_text_field($_POST['_yfym_box_count']);
		if (isset($_POST['_delivery_weekday_arr']) && !empty($_POST['_delivery_weekday_arr'] )) {
			$yfym_recommend_stock_data_arr['delivery_weekday_arr'] = $_POST['_delivery_weekday_arr'];
		} else {
			$yfym_recommend_stock_data_arr['delivery_weekday_arr'] = array();
		}	
			
		// Обновляем данные в базе данных
		update_post_meta($post_id, 'yfym_individual_delivery', $yfym_individual_delivery);
		update_post_meta($post_id, 'yfym_cost', $yfym_cost);
		update_post_meta($post_id, 'yfym_days', $yfym_days);
		update_post_meta($post_id, 'yfym_order_before', $yfym_order_before);
		update_post_meta($post_id, 'yfym_individual_pickup', $yfym_individual_pickup);
		update_post_meta($post_id, 'yfym_pickup_cost', $yfym_pickup_cost);
		update_post_meta($post_id, 'yfym_pickup_days', $yfym_pickup_days);
		update_post_meta($post_id, 'yfym_pickup_order_before', $yfym_pickup_order_before);
		update_post_meta($post_id, 'yfym_bid', $yfym_bid);
		update_post_meta($post_id, 'yfym_individual_vat', $yfym_individual_vat);
		update_post_meta($post_id, 'yfym_condition', $yfym_condition);
		update_post_meta($post_id, 'yfym_reason', $yfym_reason);
		update_post_meta($post_id, '_yfym_market_sku', $market_sku);
		update_post_meta($post_id, '_yfym_tn_ved_code', $tn_ved_code);
		update_post_meta($post_id, 'yfym_credit_template', $yfym_credit_template);	
		update_post_meta($post_id, '_yfym_supplier', $yfym_supplier);
		update_post_meta($post_id, '_yfym_recommend_stock_data_arr', $yfym_recommend_stock_data_arr);		
		update_post_meta($post_id, '_yfym_min_quantity', $yfym_min_quantity);
		update_post_meta($post_id, '_yfym_step_quantity', $yfym_step_quantity);
		update_post_meta($post_id, '_yfym_premium_price', $yfym_premium_price);
	}
	
	$numFeed = '1'; // (string) создадим строковую переменную
	// нужно ли запускать обновление фида при перезаписи файла
	$allNumFeed = (int)yfym_ALLNUMFEED;
	for ($i = 1; $i<$allNumFeed+1; $i++) {

		yfym_error_log('FEED № '.$numFeed.'; Шаг $i = '.$i.' цикла по формированию кэша файлов; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);

		$result_yml_unit = yfym_unit($post_id, $numFeed); // формируем фид товара
		if (is_array($result_yml_unit)) {
			$result_yml = $result_yml_unit[0];
			$ids_in_yml = $result_yml_unit[1];
		} else {
			$result_yml = $result_yml_unit;
			$ids_in_yml = '';
		}
		yfym_wf($result_yml, $post_id, $numFeed, $ids_in_yml); // записываем кэш-файл

		$yfym_ufup = yfym_optionGET('yfym_ufup', $numFeed);
		if ($yfym_ufup !== 'on') {$numFeed++; continue; /*return;*/}
		$status_sborki = (int)yfym_optionGET('yfym_status_sborki', $numFeed);
		if ($status_sborki > -1) {$numFeed++; continue; /*return;*/} // если идет сборка фида - пропуск
		
		$yfym_date_save_set = yfym_optionGET('yfym_date_save_set', $numFeed);
		$yfym_date_sborki = yfym_optionGET('yfym_date_sborki', $numFeed);

		if ($numFeed === '1') {$prefFeed = '';} else {$prefFeed = $numFeed;}
		if (is_multisite()) {
			/*
			*	wp_get_upload_dir();
			*   'path'    => '/home/site.ru/public_html/wp-content/uploads/2016/04',
			*	'url'     => 'http://site.ru/wp-content/uploads/2016/04',
			*	'subdir'  => '/2016/04',
			*	'basedir' => '/home/site.ru/public_html/wp-content/uploads',
			*	'baseurl' => 'http://site.ru/wp-content/uploads',
			*	'error'   => false,
			*/
			$upload_dir = (object)wp_get_upload_dir();
			$filenamefeed = $upload_dir->basedir."/".$prefFeed."feed-yml-".get_current_blog_id().".xml";
		} else {
			$upload_dir = (object)wp_get_upload_dir();
			$filenamefeed = $upload_dir->basedir."/".$prefFeed."feed-yml-0.xml";
		}
		if (!file_exists($filenamefeed)) {
			yfym_error_log('FEED № '.$numFeed.'; WARNING: Файла filenamefeed = '.$filenamefeed.' не существует! Пропускаем быструю сборку; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
			$numFeed++; continue; /*return;*/ 
		} // файла с фидом нет
		
		clearstatcache(); // очищаем кэш дат файлов
		$last_upd_file = filemtime($filenamefeed);
		yfym_error_log('FEED № '.$numFeed.'; $yfym_date_save_set='.$yfym_date_save_set.';$filenamefeed='.$filenamefeed, 0);
		yfym_error_log('FEED № '.$numFeed.'; Начинаем сравнивать даты! Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);	
		if ($yfym_date_save_set > $last_upd_file) {
			// настройки фида сохранялись позже, чем создан фид		
			// нужно полностью пересобрать фид
			yfym_error_log('FEED № '.$numFeed.'; NOTICE: Настройки фида сохранялись позже, чем создан фид; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
			$yfym_status_cron = yfym_optionGET('yfym_status_cron', $numFeed);
			$recurrence = $yfym_status_cron;
			wp_clear_scheduled_hook('yfym_cron_period', array($numFeed));
			wp_schedule_event(time(), $recurrence, 'yfym_cron_period', array($numFeed));
			yfym_error_log('FEED № '.$numFeed.'; yfym_cron_period внесен в список заданий! Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
		} else { // нужно лишь обновить цены	
			yfym_error_log('FEED № '.$numFeed.'; NOTICE: Настройки фида сохранялись раньше, чем создан фид. Нужно лишь обновить цены; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
			yfym_clear_file_ids_in_yml($numFeed); /* С версии 3.1.0 */
			yfym_onlygluing($numFeed);
		}
		$numFeed++;
	}
	return;
 }
  
 public static function yfym_added_wc_tabs($tabs) {
	$tabs['yfym_special_panel'] = array(
		'label' => __('YML for Yandex Market', 'yfym'), // название вкладки
		'target' => 'yfym_added_wc_tabs', // идентификатор вкладки
		'class' => array('hide_if_grouped'), // классы управления видимостью вкладки в зависимости от типа товара
		'priority' => 70, // приоритет вывода
	);
	return $tabs;
 }

 public static function yfym_art_added_tabs_icon() { 
	// https://rawgit.com/woothemes/woocommerce-icons/master/demo.html 
	?>
	<style>
		#woocommerce-coupon-data ul.wc-tabs li.yfym_special_panel_options a::before,
		#woocommerce-product-data ul.wc-tabs li.yfym_special_panel_options a::before,
		.woocommerce ul.wc-tabs li.yfym_special_panel_options a::before {
			content: "\f172";
		}
	</style>
	<?php
 } 
 public static function yfym_art_added_tabs_panel() {
	global $post; ?>
	<div id="yfym_added_wc_tabs" class="panel woocommerce_options_panel">
		<?php do_action('yfym_prepend_options_panel', $post); ?>
		<div class="options_group">
			<h2><strong><?php _e('Individual product settings for YML-feed', 'yfym'); ?></strong></h2>
			<h2><?php _e('Here you can set up individual options terms for this product', 'yfym'); ?>. <a target="_blank" href="//yandex.ru/support/partnermarket/elements/delivery-options.html#structure"><?php _e('Read more on Yandex', 'yfym'); ?></a></h2>
			<?php do_action('yfym_prepend_options_group_1', $post); ?>
			<?php			
			woocommerce_wp_select(array(
				'id' => 'yfym_individual_delivery',
				'label' => __('Delivery', 'yfym'),
				'options' => array(
					'off' => __('Disabled', 'yfym'),
					'false' => 'False',
					'true' => 'True',
				),
				'description' => __('Optional element', 'yfym').' <strong>delivery</strong>',
			));
			 
			// цифровое поле
			woocommerce_wp_text_input(array(
				'id' => 'yfym_cost',
				'label' => __('Delivery cost', 'yfym'),
			//	'placeholder' => '1',
				'description' => __('Required element', 'yfym').' <strong>cost</strong> '. __('of attribute', 'yfym').' <strong>delivery-option</strong>',
				'type' => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0',
				),
			)); 
			
			// текстовое поле
			woocommerce_wp_text_input(array(
				'id' => 'yfym_days',
				'label' => __('Delivery days', 'yfym'),
				'description' => __('Required element', 'yfym').' <strong>days</strong> '. __('of attribute', 'yfym').' <strong>delivery-option</strong>',
				'type' => 'text',
			));
			
			// текстовое поле
			woocommerce_wp_text_input(array(
				'id' => 'yfym_order_before',
				'label' => __('The time', 'yfym'),
				'description' => __('Optional element', 'yfym').' <strong>order-before</strong> '. __('of attribute', 'yfym').' <strong>delivery-option</strong>. '. __('The time in which you need to place an order to get it at this time', 'yfym'),
			//	'desc_tip' => 'true',
				'type' => 'text',
			));	

			?>
			<?php do_action('yfym_append_options_group_1', $post); ?>
		</div>
		<?php do_action('yfym_append_options_between_group_1_2', $post); ?>
		<div class="options_group">
			<h2><?php _e('Here you can configure the pickup conditions for this product', 'yfym'); ?></h2>
			<?php do_action('yfym_prepend_options_group_2', $post); ?>
		<?php
			woocommerce_wp_select(array(
				'id' => 'yfym_individual_pickup',
				'label' => __('Pickup', 'yfym'),
				'options' => array(
					'off' => __('Disabled', 'yfym'),
					'false' => 'False',
					'true' => 'True',
				),
				'description' => __('Optional element', 'yfym').' <strong>pickup</strong>',
			));
			 
			// цифровое поле
			woocommerce_wp_text_input(array(
				'id' => 'yfym_pickup_cost',
				'label' => __('Pickup cost', 'yfym'),
				'description' => __('Required element', 'yfym').' <strong>cost</strong> '. __('of attribute', 'yfym').' <strong>pickup-options</strong>',
				'type' => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0',
				),
			)); 
			
			// текстовое поле
			woocommerce_wp_text_input(array(
				'id' => 'yfym_pickup_days',
				'label' => __('Pickup days', 'yfym'),
				'description' => __('Required element', 'yfym').' <strong>days</strong> '. __('of attribute', 'yfym').' <strong>pickup-options</strong>',
				'type' => 'text',
			));
			
			// текстовое поле
			woocommerce_wp_text_input(array(
				'id' => 'yfym_pickup_order_before',
				'label' => __('The time', 'yfym'),
				'description' => __('Optional element', 'yfym').' <strong>order-before</strong> '. __('of attribute', 'yfym').' <strong>pickup-options</strong>. '. __('The time in which you need to place an order to get it at this time', 'yfym'),
				'type' => 'text',
			));	

			?>
			<?php do_action('yfym_append_options_group_2', $post); ?>
		</div>
		<?php do_action('yfym_append_options_between_group_2_3', $post); ?>
		<div class="options_group">
			<h2><?php _e('Bid values', 'yfym'); ?> & <?php _e('Сondition', 'yfym'); ?></h2>
			<?php do_action('yfym_prepend_options_group_3', $post); ?>
		<?php	
			woocommerce_wp_text_input(array(
				'id' => 'yfym_bid',
				'label' => __('Bid values', 'yfym'),
				'description' => __('Optional element', 'yfym').' <strong>bid</strong>. '. __('Bid values in your price list. Specify the bid amount in Yandex cents: for example, the value 80 corresponds to the bid of 0.8 Yandex units. The values must be positive integers', 'yfym').' <a target="_blank" href="//yandex.ru/support/partnermarket/elements/bid-cbid.html">'. __('Read more on Yandex', 'yfym').'</a>',
				'type' => 'text',
			));			
		
			woocommerce_wp_select(array(
				'id' => 'yfym_individual_vat',
				'label' => __('VAT rate', 'yfym'),
				'options' => array(
					'global' => __('Use global settings', 'yfym'),
					'NO_VAT' => __('No VAT', 'yfym'),
					'VAT_0' => '0%',
					'VAT_10' => '10%',
					'VAT_10_110' => '10/110',
					'VAT_18' => '18%',
					'VAT_18_118' => '18/118',
					'VAT_20' => '20%',
					'VAT_20_120' => '20/120',						
				),
				'description' => __('Optional element', 'yfym').' <strong>vat</strong> '. __('This element is used when creating an YML feed for Yandex.Delivery', 'yfym'). ' <a target="_blank" href="//yandex.ru/support/delivery/settings/vat.html">'. __('Read more on Yandex', 'yfym').'</a>',
			));

			woocommerce_wp_select(array(
				'id' => 'yfym_condition',
				'label' => __('Сondition', 'yfym'),
				'options' => array(
					'off' => __('None', 'yfym'),
					'likenew' => __('Like New', 'yfym'),
					'used' => __('Used', 'yfym'),	
				),
				'description' => __('Optional element', 'yfym').' <strong>condition</strong>',
			));	
			
			woocommerce_wp_text_input(array(
				'id' => 'yfym_reason',
				'label' => __('Reason', 'yfym'),
				'placeholder' => '',
				'description' => __('Required element', 'yfym').' <strong>reason</strong> '. __('of attribute', 'yfym').' <strong>condition</strong>',
				'type' => 'text',
			));				
		?>
		<?php do_action('yfym_append_options_group_3', $post); ?>
		</div>
		<div class="options_group">	
			<h2>Маркетплейс Яндекс.Маркета</h2>
			<p><?php _e('This data is used only when creating a feed for', 'yfym'); ?> Маркетплейс Яндекс.Маркета</p>
			<?php do_action('yfym_prepend_options_group_other', $post); ?>
			<?php
				woocommerce_wp_text_input(array(
					'id' => '_yfym_market_sku',
					'label' => __('Product ID on Yandex', 'yfym'),
					'placeholder' => '',
					'description' => __('Optional element', 'yfym').' <strong>market-sku</strong> ('. __('Forbidden in Yandex Market', 'yfym').') '. __('Product ID on Yandex. You can get it after downloading the file in your personal account', 'yfym').'. <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">'. __('Read more on Yandex', 'yfym').'</a>',
					'type' => 'text',
				));	
				woocommerce_wp_text_input(array(
					'id' => '_yfym_tn_ved_code',
					'label' => __('Code ТН ВЭД', 'yfym'),
					'placeholder' => '',
					'description' => __('Optional element', 'yfym').' <strong>tn-ved-code</strong> ('. __('Forbidden in Yandex Market', 'yfym').') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">'. __('Read more on Yandex', 'yfym').'</a>',
					'type' => 'text',
				));				

				if (get_post_meta($post->ID, '_yfym_recommend_stock_data_arr', true) == '') {
					$yfym_recommend_stock_data_arr = array();
				} else {
					$yfym_recommend_stock_data_arr = get_post_meta($post->ID, '_yfym_recommend_stock_data_arr', true);
				}		
				$availability = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'availability', 'disabled');	
				$transport_unit = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'transport_unit');
				$min_delivery_pieces = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'min_delivery_pieces');
				$quantum = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'quantum');
				$leadtime = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'leadtime');
				$box_count = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'box_count');
				$delivery_weekday_arr = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'delivery_weekday_arr', array()); 

				woocommerce_wp_select(array(
					'id' => '_yfym_availability',
					'label' => __('Supply plans', 'yfym'),
					'value' => $availability,
					'options' => array(
						'disabled' => __('Disabled', 'yfym'),
						'ACTIVE' => __('Supplies will', 'yfym'),
						'INACTIVE' => __('There will be no supplies', 'yfym'),
						'DELISTED' => __('Product in the archive', 'yfym'),	
					),
					'description' => __('Optional element', 'yfym').' <strong>availability</strong> ('. __('Forbidden in Yandex Market', 'yfym').') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">'. __('Read more on Yandex', 'yfym').'</a>',
				));	
				woocommerce_wp_text_input(array(
					'id' => '_yfym_transport_unit',
					'label' => __('The number of products in the package (multiplicity of the box)', 'yfym'),
					'value' => $transport_unit,
					'type' => 'number',
					'placeholder' => '',
					'description' => __('Optional element', 'yfym').' <strong>transport-unit</strong> ('. __('Forbidden in Yandex Market', 'yfym').') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">'. __('Read more on Yandex', 'yfym').'</a>',
					'type' => 'text',
				));
				woocommerce_wp_text_input(array(
					'id' => '_yfym_min_delivery_pieces',
					'label' => __('Minimum delivery pieces', 'yfym'),
					'value' => $min_delivery_pieces,
					'type' => 'number',
					'placeholder' => '',
					'description' => __('Optional element', 'yfym').' <strong>min-delivery-pieces</strong> ('. __('Forbidden in Yandex Market', 'yfym').') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">'. __('Read more on Yandex', 'yfym').'</a>',
					'type' => 'text',
				));
				woocommerce_wp_text_input(array(
					'id' => '_yfym_quantum',
					'label' => __('Additional batch (quantum of delivery)', 'yfym'),
					'value' => $quantum,
					'type' => 'number',
					'placeholder' => '',
					'description' => __('Optional element', 'yfym').' <strong>quantum</strong> ('. __('Forbidden in Yandex Market', 'yfym').') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">'. __('Read more on Yandex', 'yfym').'</a>',
					'type' => 'text',
				));
				woocommerce_wp_text_input(array(
					'id' => '_yfym_leadtime',
					'label' => __('Lead time', 'yfym'),
					'value' => $leadtime,
					'placeholder' => '',
					'description' => __('Optional element', 'yfym').' <strong>leadtime</strong> ('. __('Forbidden in Yandex Market', 'yfym').') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">'. __('Read more on Yandex', 'yfym').'</a>',
					'type' => 'text',
				));
				woocommerce_wp_text_input(array(
					'id' => '_yfym_box_count',
					'label' => __('Box count', 'yfym'),
					'value' => $box_count,
					'type' => 'number',
					'placeholder' => '',
					'description' => __('Optional element', 'yfym').' <strong>box-count</strong> ('. __('Forbidden in Yandex Market', 'yfym').') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">'. __('Read more on Yandex', 'yfym').'</a>',
					'type' => 'text',
				));
				yfym_woocommerce_wp_select_multiple(array(
					'id' => '_delivery_weekday_arr',
				//	'wrapper_class' => 'show_if_simple', 
					'label' => __('Days of the week when you are ready to deliver the goods to the warehouse of the marketplace', 'yfym'),
					'value' => $delivery_weekday_arr,
					'options' => array(
						'MONDAY' => __('Monday', 'yfym'),
						'TUESDAY' => __('Tuesday', 'yfym'),
						'WEDNESDAY' => __('Wednesday', 'yfym'),
						'THURSDAYy' => __('Thursday', 'yfym'),
						'FRIDAY' => __('Friday', 'yfym'),
						'SATURDAY' => __('Saturday', 'yfym'),
						'SUNDAY' => __('Sunday', 'yfym'),
					)
				));
			?>	
			<?php do_action('yfym_append_options_group_4', $post); ?>
		</div>		
		<div class="options_group">	
			<h2><?php _e('Other', 'yfym'); ?></h2>
			<?php do_action('yfym_prepend_options_group_other', $post); ?>
			<?php
				woocommerce_wp_text_input(array(
					'id' => 'yfym_credit_template',
					'label' => __('Credit program identifier', 'yfym'),
					'placeholder' => '',
					'description' => __('Optional element', 'yfym').' <strong>credit-template</strong> <a target="_blank" href="//yandex.ru/support/partnermarket/efficiency/credit.html">'. __('Read more on Yandex', 'yfym').'</a>',
					'type' => 'text',
				));	
				woocommerce_wp_text_input(array(
					'id' => '_yfym_supplier',
					'label' => 'ОГРН/ОГРНИП '. __('of a third-party seller', 'yfym'),
					'description' => __('Optional element', 'yfym').' <strong>supplier</strong>. <a target="_blank" href="//yandex.ru/support/partnermarket/registration/marketplace.html">'. __('Read more on Yandex', 'yfym').'</a>',
					'type' => 'text',
				));
				woocommerce_wp_text_input(array(
					'id' => '_yfym_min_quantity',
					'label' => __('Minimum number of products per order', 'yfym'),
					'description' => __('For these categories only', 'yfym').': "Автошины", "Грузовые шины", "Мотошины", "Диски" <strong>min-quantity</strong>. <a target="_blank" href="//yandex.ru/support/partnermarket/elements/min-quantity.html">'. __('Read more on Yandex', 'yfym').'</a>',
					'type' => 'text',
				));
				woocommerce_wp_text_input(array(
					'id' => '_yfym_step_quantity',
					'label' => 'step-quantity',
					'description' => __('For these categories only', 'yfym').': "Автошины", "Грузовые шины", "Мотошины", "Диски" <strong>step-quantity</strong>',
					'type' => 'text',
				));
				?>	
			<?php do_action('yfym_append_options_group_5', $post); ?>
		</div>					
		<?php do_action('yfym_append_options_panel', $post); ?>
	</div>
	<?php
 } 

 public static function yfym_art_woo_custom_fields_save($post_id) {
	// Сохранение текстового поля
	//if (isset($_POST['_yfym_condition'])) {update_post_meta($post_id, '_yfym_condition', esc_attr($_POST['_yfym_condition']));}
 } 

 public static function yfym_woocommerce_product_options_general_product_data() {
	global $product, $post;
	echo '<div class="options_group">'; // Группировка полей 
	woocommerce_wp_text_input( array(
		'id'			=> '_yfym_premium_price',
		'label'			=> 'premium_price',
		'placeholder'	=> '0',
		'description'	=> __('Price for Ozon Premium customers. Used only in the OZONE feed', 'yfym'),
		'type'			=> 'number',
		'custom_attributes' => array(
			'step'	=> '0.01',
			'min'	=> '0',
		),
	));
	echo '</div>';
 }

 /* функции крона */
 public function yfym_do_this_seventy_sec($numFeed = '1') {
	yfym_error_log('FEED № '.$numFeed.'; Крон yfym_do_this_seventy_sec запущен; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);	 
	// yfym_optionGET('yfym_status_sborki', $numFeed);	
	$this->yfym_construct_yml($numFeed); // делаем что-либо каждые 70 сек
 }
 public function yfym_do_this_event($numFeed = '1') {
	yfym_error_log('FEED № '.$numFeed.'; Крон yfym_do_this_event включен. Делаем что-то каждый час; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
	$step_export = (int)yfym_optionGET('yfym_step_export', $numFeed);
	if ($step_export === 0) {$step_export = 500;}		
	yfym_optionUPD('yfym_status_sborki', $step_export, $numFeed);

	wp_clear_scheduled_hook('yfym_cron_sborki', array($numFeed));

	// Возвращает nul/false. null когда планирование завершено. false в случае неудачи.
	$res = wp_schedule_event(time(), 'seventy_sec', 'yfym_cron_sborki', array($numFeed));
	if ($res === false) {
		yfym_error_log('FEED № '.$numFeed.'; ERROR: Не удалось запланировань CRON seventy_sec; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
	} else {
		yfym_error_log('FEED № '.$numFeed.'; CRON seventy_sec успешно запланирован; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
	}
 }
 /* end функции крона */
 
 // Вывод различных notices
 public function yfym_admin_notices_function() {
	$numFeed = '1'; // (string) создадим строковую переменную
	// нужно ли запускать обновление фида при перезаписи файла
	$allNumFeed = (int)yfym_ALLNUMFEED;

	$yfym_disable_notices = yfym_optionGET('yfym_disable_notices');
	if ($yfym_disable_notices !== 'on') {
		for ($i = 1; $i<$allNumFeed+1; $i++) {
			$status_sborki = yfym_optionGET('yfym_status_sborki', $numFeed);
			if ($status_sborki == false) {
				$numFeed++; continue;
			} else {
				$status_sborki = (int)$status_sborki;
			}		
			if ($status_sborki !== -1) {	
				$count_posts = wp_count_posts('product');
				$vsegotovarov = $count_posts->publish;
				$step_export = (int)yfym_optionGET('yfym_step_export', $numFeed);
				if ($step_export === 0) {$step_export = 500;}
				$vobrabotke = $status_sborki-$step_export;
				if ($vsegotovarov > $vobrabotke) {
					$vyvod = 'FEED № '.$numFeed.' '. __('Progress', 'yfym').': '.$vobrabotke.' '. __('from', 'yfym').' '.$vsegotovarov.' '. __('products', 'yfym') .'.<br />'.__('If the progress indicators have not changed within 20 minutes, try reducing the "Step of export" in the plugin settings', 'yfym');
				} else {
					$vyvod = 'FEED № '.$numFeed.' '. __('Prior to the completion of less than 70 seconds', 'yfym');
				}	
				print '<div class="updated notice notice-success is-dismissible"><p>'. __('We are working on automatic file creation. YML will be developed soon', 'yfym').'. '.$vyvod.'.</p></div>';
			}
			$numFeed++;
		}
	}
	if (class_exists('YmlforYandexMarketBookExport')) {
		if (defined('yfymbe_VER')) {	
			if (version_compare(yfymbe_VER, '2.5.0', '<')) {
				print '<div class="notice error is-dismissible"><p>'. __('You are using the', 'yfym'). ' <strong>Yml for Yandex Market Book Export</strong> ' . __('plugin of obsolete version', 'yfym'). ' '. yfymbe_VER .'. '. __('This version does not know how to work with multiple YML-feeds. I highly recommend upgrading the', 'yfym'). ' <strong>Yml for Yandex Market Book Export</strong> ' .  __('version to', 'yfym'). ' 2.5.0 '.  __('or higher', 'yfym'). '.</p></div>';
			}
		}	
	}

	if (class_exists('YmlforYandexMarketPro')) {
		if (defined('yfymp_VER')) {	
			if (version_compare(yfymp_VER, '4.0.0', '<')) {
				print '<div class="notice error is-dismissible"><p>'. __('You are using the', 'yfym'). ' <strong>Yml for Yandex Market Pro</strong> ' . __('plugin of obsolete version', 'yfym'). ' '. yfymp_VER .'. '. __('This version does not know how to work with multiple YML-feeds. I highly recommend upgrading the', 'yfym'). ' <strong>Yml for Yandex Market Pro</strong> ' .  __('version to', 'yfym'). ' 4.0.0 '.  __('or higher', 'yfym'). '.</p></div>';
			}
		}	
	}

	if (class_exists('YmlforYandexMarketPromos')) {
		if (defined('yfympe_VER')) {	
			if (version_compare(yfympe_VER, '4.0.0', '<')) {
				print '<div class="notice error is-dismissible"><p>'. __('You are using the', 'yfym'). ' <strong>Yml for Yandex Market Promos Export</strong> ' . __('plugin of obsolete version', 'yfym'). ' '. yfympe_VER .'. '. __('This version does not know how to work with multiple YML-feeds. I highly recommend upgrading the', 'yfym'). ' <strong>Yml for Yandex Market Promos Export</strong> ' .  __('version to', 'yfym'). ' 4.0.0 '.  __('or higher', 'yfym'). '.</p></div>';
			}
		}	
	}	
	
	if (class_exists('YmlforYandexMarketProm')) {
		if (defined('yfympr_VER')) {	
			if (version_compare(yfympr_VER, '2.0.0', '<')) {
				print '<div class="notice error is-dismissible"><p>'. __('You are using the', 'yfym'). ' <strong>Yml for Yandex Market Prom Export</strong> ' . __('plugin of obsolete version', 'yfym'). ' '. yfympr_VER .'. '. __('This version does not know how to work with multiple YML-feeds. I highly recommend upgrading the', 'yfym'). ' <strong>Yml for Yandex Market Prom Export</strong> ' .  __('version to', 'yfym'). ' 2.0.0 '.  __('or higher', 'yfym'). '.</p></div>';
			}
		}	
	}	
		

	if (yfym_optionGET('yfym_magazin_type', $numFeed) === 'woocommerce') { 
		if (!class_exists('WooCommerce')) {
			print '<div class="notice error is-dismissible"><p>'. __('WooCommerce is not active', 'yfym'). '!</p></div>';
		}
	}

	if (is_plugin_active('w3-total-cache/w3-total-cache.php')) {
		// global $pagenow; https://wpincode.com/kak-dobavit-sobstvennye-uvedomleniya-v-adminke-wordpress/
		if (isset($_GET['page'])) {
			if ($_GET['page'] === 'yfymdebug' || $_GET['page'] === 'yfymexport') {
			print '<div class="notice notice-warning"><p><span class="yfym_bold">W3 Total Cache</span> '. __('plugin is active', 'yfym'). '. '. __('If no YML feed is being generated, please', 'yfym'). ' <a href="https://icopydoc.ru/w3tc-page-cache-meshaet-sozdaniyu-fida-reshenie/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=notice&utm_term=w3-total-cache" target="_blank">'. __('read this guide', 'yfym') .'</a>.</p></div>';
			}
		}
	}
	  
	if (isset($_REQUEST['yfym_submit_action'])) {
		$run_text = '';
		if (sanitize_text_field($_POST['yfym_run_cron']) !== 'off') {
			$run_text = '. '. __('Creating the feed is running. You can continue working with the website', 'yfym');
		}
		print '<div class="updated notice notice-success is-dismissible"><p>'. __('Updated', 'yfym'). $run_text .'.</p></div>';
	}

	if (isset($_REQUEST['yfym_submit_debug_page'])) {
		print '<div class="updated notice notice-success is-dismissible"><p>'. __('Updated', 'yfym'). '.</p></div>';
	}

	if (isset($_REQUEST['yfym_submit_clear_logs'])) {
		$upload_dir = (object)wp_get_upload_dir();
		$name_dir = $upload_dir->basedir."/yfym";
		$filename = $name_dir.'/yfym.log';
		$res = unlink($filename);
		if ($res == true) {
			print '<div class="notice notice-success is-dismissible"><p>' .__('Logs were cleared', 'yfym'). '.</p></div>';					
		} else {
			print '<div class="notice notice-warning is-dismissible"><p>' .__('Error accessing log file. The log file may have been deleted previously', 'yfym'). '.</p></div>';		
		}
	}

	/* сброс настроек */
	if (isset($_REQUEST['yfym_submit_reset'])) {
		if (!empty($_POST) && check_admin_referer('yfym_nonce_action_reset', 'yfym_nonce_field_reset')) {
			$this->on_uninstall();
			$this->on_activation();	
			print '<div class="updated notice notice-success is-dismissible"><p>'. __('The settings have been reset', 'yfym'). '.</p></div>';
		}
	} /* end сброс настроек */

	/* отправка отчёта */
	if (isset($_REQUEST['yfym_submit_send_stat'])) {
		if (!empty($_POST) && check_admin_referer('yfym_nonce_action_send_stat', 'yfym_nonce_field_send_stat')) { 	
		if (is_multisite()) { 
			$yfym_is_multisite = 'включен';	
			$yfym_keeplogs = get_blog_option(get_current_blog_id(), 'yfym_keeplogs');
		} else {
			$yfym_is_multisite = 'отключен'; 
			$yfym_keeplogs = get_option('yfym_keeplogs');
		}
		$numFeed = '1'; // (string)
		$mail_content = "Версия плагина: ". yfym_VER . PHP_EOL;
		$mail_content .= "Версия WP: ".get_bloginfo('version'). PHP_EOL;	 
		$woo_version = yfym_get_woo_version_number();
		$mail_content .= "Версия WC: ".$woo_version. PHP_EOL;
		$mail_content .= "Версия PHP: ".phpversion(). PHP_EOL;   
		$mail_content .= "Режим мультисайта: ".$yfym_is_multisite. PHP_EOL;
		$mail_content .= "Вести логи: ".$yfym_keeplogs. PHP_EOL;
		$mail_content .= "Расположение логов: ". yfym_UPLOAD_DIR .'/yfym.log'. PHP_EOL;	
		$possible_problems_arr = yfym_possible_problems_list();
		if ($possible_problems_arr[1] > 0) {
			$possible_problems_arr[3] = str_replace('<br/>', PHP_EOL, $possible_problems_arr[3]);
			$mail_content .= "Самодиагностика: ". PHP_EOL .$possible_problems_arr[3];
		} else {
			$mail_content .= "Самодиагностика: Функции самодиагностики не выявили потенциальных проблем". PHP_EOL;
		}
		if (!class_exists('YmlforYandexMarketAliexpress')) {
			$mail_content .= "Aliexpress Export: не активна". PHP_EOL;
		} else {
			$order_id = yfym_optionGET('yfymae_order_id');
			$order_email = yfym_optionGET('yfymae_order_email');
			$mail_content .= "Aliexpress Export: активна (v ".yfymae_VER." (#".$order_id." / ".$order_email."))". PHP_EOL;
		}
		if (!class_exists('YmlforYandexMarketBookExport')) {
			$mail_content .= "Book Export: не активна". PHP_EOL;
		} else {
			if (!defined('yfymbe_VER')) {define('yfymbe_VER', 'н/д');} 
			$order_id = yfym_optionGET('yfymbe_order_id');
			$order_email = yfym_optionGET('yfymbe_order_email');
			$mail_content .= "Book Export: активна (v ".yfymbe_VER." (#".$order_id." / ".$order_email."))". PHP_EOL;
		}
		if (!class_exists('YmlforYandexMarketPro')) {
			$mail_content .= "Pro: не активна". PHP_EOL;
		} else {
			if (!defined('yfymp_VER')) {define('yfymp_VER', 'н/д');} 
			$order_id = yfym_optionGET('yfymp_order_id');
			$order_email = yfym_optionGET('yfymp_order_email');			
			$mail_content .= "Pro: активна (v ".yfymp_VER." (#".$order_id." / ".$order_email."))". PHP_EOL;
		}
		if (!class_exists('YmlforYandexMarketProm')) {
			$mail_content .= "Prom Export: не активна". PHP_EOL;
		} else {
			$order_id = yfym_optionGET('yfympr_order_id');
			$order_email = yfym_optionGET('yfympr_order_email');
			$mail_content .= "Prom Export: активна (v ".yfympr_VER." (#".$order_id." / ".$order_email."))". PHP_EOL;
		}		
		if (!class_exists('YmlforYandexMarketPromosExport')) {
			$mail_content .= "Promos Export: не активна". PHP_EOL;
		} else {
			if (!defined('yfympe_VER')) {define('yfympe_VER', 'н/д');} 
			$order_id = yfym_optionGET('yfympe_order_id');
			$order_email = yfym_optionGET('yfympe_order_email');
			$mail_content .= "Promos Export: активна (v ".yfympe_VER." (#".$order_id." / ".$order_email."))". PHP_EOL;
		}
		if (!class_exists('YmlforYandexMarketRozetka')) {
			$mail_content .= "Prom Export: не активна". PHP_EOL;
		} else {
			$order_id = yfym_optionGET('yfymre_order_id');
			$order_email = yfym_optionGET('yfymre_order_email');
			$mail_content .= "Rozetka Export: активна (v ".yfymre_VER." (#".$order_id." / ".$order_email."))". PHP_EOL;
		}
		if (isset($_REQUEST['yfym_its_ok'])) {
			$mail_content .= PHP_EOL ."Помог ли плагин: ".sanitize_text_field($_REQUEST['yfym_its_ok']);
		}
		if (isset($_POST['yfym_email'])) {
			$mail_content .= PHP_EOL ."Почта: ".sanitize_email($_POST['yfym_email']);
		}
		if (isset($_POST['yfym_message'])) {
			$mail_content .= PHP_EOL ."Сообщение: ".sanitize_text_field($_POST['yfym_message']);
		}
		$argsp = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1 );
		$products = new WP_Query($argsp);
		$vsegotovarov = $products->found_posts;
		$mail_content .= PHP_EOL ."Число товаров на выгрузку: ". $vsegotovarov;
		$allNumFeed = (int)yfym_ALLNUMFEED;
		for ($i = 1; $i<$allNumFeed+1; $i++) {
			$status_sborki = (int)yfym_optionGET('yfym_status_sborki', $numFeed);
			$yfym_file_url = urldecode(yfym_optionGET('yfym_file_url', $numFeed));
			$yfym_file_file = urldecode(yfym_optionGET('yfym_file_file', $numFeed));
			$yfym_whot_export = yfym_optionGET('yfym_whot_export', $numFeed);
			$yfym_yml_rules = yfym_optionGET('yfym_yml_rules', $numFeed);
			$yfym_skip_missing_products = yfym_optionGET('yfym_skip_missing_products', $numFeed);	
			$yfym_skip_backorders_products = yfym_optionGET('yfym_skip_backorders_products', $numFeed);
			$yfym_status_cron = yfym_optionGET('yfym_status_cron', $numFeed);
			$yfym_ufup = yfym_optionGET('yfym_ufup', $numFeed);	
			$yfym_date_sborki = yfym_optionGET('yfym_date_sborki', $numFeed);
			$yfym_main_product = yfym_optionGET('yfym_main_product', $numFeed);
			$yfym_errors = yfym_optionGET('yfym_errors', $numFeed);

			$mail_content .= PHP_EOL."ФИД №: ".$i. PHP_EOL . PHP_EOL;
			$mail_content .= "status_sborki: ".$status_sborki. PHP_EOL;
			$mail_content .= "УРЛ: ".get_site_url(). PHP_EOL;
			$mail_content .= "УРЛ YML-фида: ".$yfym_file_url . PHP_EOL;
			$mail_content .= "Временный файл: ".$yfym_file_file. PHP_EOL;
			$mail_content .= "Что экспортировать: ".$yfym_whot_export. PHP_EOL;
			$mail_content .= "Придерживаться правил: ".$yfym_yml_rules. PHP_EOL;
			$mail_content .= "Исключать товары которых нет в наличии (кроме предзаказа): ".$yfym_skip_missing_products. PHP_EOL;
			$mail_content .= "Исключать из фида товары для предзаказа: ".$yfym_skip_backorders_products. PHP_EOL;
			$mail_content .= "Автоматическое создание файла: ".$yfym_status_cron. PHP_EOL;
			$mail_content .= "Обновить фид при обновлении карточки товара: ".$yfym_ufup. PHP_EOL;
			$mail_content .= "Дата последней сборки XML: ".$yfym_date_sborki. PHP_EOL;
			$mail_content .= "Что продаёт: ".$yfym_main_product. PHP_EOL;
			$mail_content .= "Ошибки: ".$yfym_errors. PHP_EOL;
			$numFeed++;
		}
		wp_mail('support@icopydoc.ru', 'Отчёт YML for WP', $mail_content);
		print '<div class="updated notice notice-success is-dismissible"><p>'. __('The data has been sent. Thank you', 'yfym'). '.</p></div>';
		}
	} /* end отправка отчёта */   
 }
 
 // сборка
 public static function yfym_construct_yml($numFeed = '1') {
	yfym_error_log('FEED № '.$numFeed.'; Стартовала yfym_construct_yml. Файл: yml-for-yandex-market.php; Строка: '.__LINE__ , 0);

 	$result_yml = '';
	$status_sborki = (int)yfym_optionGET('yfym_status_sborki', $numFeed);
  
	// файл уже собран. На всякий случай отключим крон сборки
	if ($status_sborki == -1 ) {wp_clear_scheduled_hook('yfym_cron_sborki', array($numFeed)); return;}
		  
	$yfym_date_save_set = yfym_optionGET('yfym_date_save_set', $numFeed);
	if ($yfym_date_save_set == '') {
		$unixtime = current_time('timestamp', 1); // 1335808087 - временная зона GMT(Unix формат)
		yfym_optionUPD('yfym_date_save_set', $unixtime, $numFeed);
	}
	$yfym_date_sborki = yfym_optionGET('yfym_date_sborki', $numFeed);
	  
	if ($numFeed === '1') {$prefFeed = '';} else {$prefFeed = $numFeed;}	  
	if (is_multisite()) {
		/*
		* wp_get_upload_dir();
		* 'path'    => '/home/site.ru/public_html/wp-content/uploads/2016/04',
		* 'url'     => 'http://site.ru/wp-content/uploads/2016/04',
		* 'subdir'  => '/2016/04',
		* 'basedir' => '/home/site.ru/public_html/wp-content/uploads',
		* 'baseurl' => 'http://site.ru/wp-content/uploads',
		* 'error'   => false,
		*/
		$upload_dir = (object)wp_get_upload_dir();
		$filenamefeed = $upload_dir->basedir."/".$prefFeed."feed-yml-".get_current_blog_id().".xml";		
	} else {
		$upload_dir = (object)wp_get_upload_dir();
		$filenamefeed = $upload_dir->basedir."/".$prefFeed."feed-yml-0.xml";
	}
	if (file_exists($filenamefeed)) {		
		yfym_error_log('FEED № '.$numFeed.'; Файл с фидом '.$filenamefeed.' есть. Файл: yml-for-yandex-market.php; Строка: '.__LINE__ , 0);
		// return; // файла с фидом нет
		clearstatcache(); // очищаем кэш дат файлов
		$last_upd_file = filemtime($filenamefeed);
		yfym_error_log('FEED № '.$numFeed.'; $yfym_date_save_set='.$yfym_date_save_set.'; $filenamefeed='.$filenamefeed, 0);
		yfym_error_log('FEED № '.$numFeed.'; Начинаем сравнивать даты! Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);	
		if ($yfym_date_save_set < $last_upd_file) {
			yfym_error_log('FEED № '.$numFeed.'; NOTICE: Нужно лишь обновить цены во всём фиде! Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
			yfym_clear_file_ids_in_yml($numFeed); /* С версии 3.1.0 */
			yfym_onlygluing($numFeed);
			return;
		}	
	}
	// далее исходим из того, что файла с фидом нет, либо нужна полная сборка
	  
	$step_export = (int)yfym_optionGET('yfym_step_export', $numFeed);
	if ($step_export == 0) {$step_export = 500;}
	  
	if ($status_sborki == $step_export) { // начинаем сборку файла
		do_action('yfym_before_construct', 'full'); // сборка стартовала
		$result_yml = yfym_feed_header($numFeed);
		/* создаем файл или перезаписываем старый удалив содержимое */
		$result = yfym_write_file($result_yml, 'w+', $numFeed);
		if ($result !== true) {
			yfym_error_log('FEED № '.$numFeed.'; yfym_write_file вернула ошибку! $result ='.$result.'; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
			return; 
		} else {
			yfym_error_log('FEED № '.$numFeed.'; yfym_write_file отработала успешно; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
		}
		yfym_clear_file_ids_in_yml($numFeed); /* С версии 3.1.0 */	
	} 
	if ($status_sborki > 1) {
		$result_yml	= '';
		$offset = $status_sborki-$step_export;
		$whot_export = yfym_optionGET('yfym_whot_export', $numFeed);
		if ($whot_export === 'vygruzhat') {
			$args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => $step_export,
				'offset' => $offset,
				'relation' => 'AND',
				'meta_query' => array(
					array(
						'key' => 'vygruzhat',
						'value' => 'on'
					)
				)
			);			
		} else { // if ($whot_export == 'all' || $whot_export == 'simple')
			$args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => $step_export, // сколько выводить товаров
				'offset' => $offset,
				'relation' => 'AND'
			);
		}
/*
		$yfym_stopusepro = yfym_optionGET('yfym_stopusepro', $numFeed);
		if ($yfym_stopusepro === 'on') {}
*/
		$args = apply_filters('yfym_query_arg_filter', $args, $numFeed);
		$featured_query = new WP_Query($args);
		$prod_id_arr = array(); 
		if ($featured_query->have_posts()) { 		
		 	for ($i = 0; $i < count($featured_query->posts); $i++) {
				// $prod_id_arr[] .= $featured_query->posts[$i]->ID;
				$prod_id_arr[$i]['ID'] = $featured_query->posts[$i]->ID;
				$prod_id_arr[$i]['post_modified_gmt'] =$featured_query->posts[$i]->post_modified_gmt;
		 	}
			wp_reset_query(); /* Remember to reset */
			unset($featured_query); // чутка освободим память
			yfym_gluing($prod_id_arr, $numFeed);
			$status_sborki = $status_sborki + $step_export;
			yfym_error_log('FEED № '.$numFeed.'; status_sborki увеличен на '.$step_export.' и равен '.$status_sborki.'; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);	  
			yfym_optionUPD('yfym_status_sborki', $status_sborki, $numFeed);		   
		} else {
			// если постов нет, пишем концовку файла
			yfym_error_log('FEED № '.$numFeed.'; Постов больше нет, пишем концовку файла; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);	
			$result_yml .= "</offers>". PHP_EOL; 
			$result_yml = apply_filters('yfym_after_offers_filter', $result_yml, $numFeed);
			$result_yml .= "</shop>". PHP_EOL ."</yml_catalog>";
			/* создаем файл или перезаписываем старый удалив содержимое */
			$result = yfym_write_file($result_yml, 'a', $numFeed);
			yfym_error_log('FEED № '.$numFeed.'; Файл фида готов. Осталось только переименовать временный файл в основной; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
			yfym_rename_file($numFeed);
			// выставляем статус сборки в "готово"
			$status_sborki = -1;
			if ($result === true) {
				yfym_optionUPD('yfym_status_sborki', $status_sborki, $numFeed);
				// останавливаем крон сборки
				wp_clear_scheduled_hook('yfym_cron_sborki', array($numFeed));
				do_action('yfym_after_construct', 'full'); // сборка закончена
				yfym_error_log('FEED № '.$numFeed.'; SUCCESS: Сборка успешно завершена; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
			} else {
				yfym_error_log('FEED № '.$numFeed.'; ERROR: На завершающем этапе yfym_write_file вернула ошибку! Я не смог записать концовку файла... $result ='.$result.'; Файл: yml-for-yandex-market.php; Строка: '.__LINE__, 0);
				do_action('yfym_after_construct', 'false'); // сборка закончена
				return;
			}
		} // end if ($featured_query->have_posts())
	  } // end if ($status_sborki > 1)
   } // end public static function yfym_construct_yml
} /* end class YmlforYandexMarket */
?>