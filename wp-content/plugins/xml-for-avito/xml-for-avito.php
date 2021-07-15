<?php defined('ABSPATH') OR exit;
/*
Plugin Name: XML for Avito
Description: Подключите свой магазин к Avito чтобы увеличить продажи!
Tags: xml, avito, market, export, woocommerce
Author: Maxim Glazunov
Author URI: https://icopydoc.ru
License: GPLv2
Version: 1.3.11
Text Domain: xml-for-avito
Domain Path: /languages/
WC requires at least: 3.0.0
WC tested up to: 5.2.2
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
require_once plugin_dir_path(__FILE__).'/ad/business.php';
require_once plugin_dir_path(__FILE__).'/ad/dom.php';
require_once plugin_dir_path(__FILE__).'/ad/hobby.php';
require_once plugin_dir_path(__FILE__).'/ad/lichnye_veshi.php';
require_once plugin_dir_path(__FILE__).'/ad/main_part.php';
require_once plugin_dir_path(__FILE__).'/ad/tehnika.php';
require_once plugin_dir_path(__FILE__).'/ad/zapchasti.php';
require_once plugin_dir_path(__FILE__).'/ad/zhivotnye.php';
register_activation_hook(__FILE__, array('XmlforAvito', 'on_activation'));
register_deactivation_hook(__FILE__, array('XmlforAvito', 'on_deactivation'));
register_uninstall_hook(__FILE__, array('XmlforAvito', 'on_uninstall'));
add_action('plugins_loaded', array('XmlforAvito', 'init'));
add_action('plugins_loaded', 'xfavi_load_plugin_textdomain'); // load translation
function xfavi_load_plugin_textdomain() {
 load_plugin_textdomain('xfavi', false, dirname(plugin_basename(__FILE__)).'/languages/');
}
class XmlforAvito {
 protected static $instance;
 public static function init() {
	is_null(self::$instance) AND self::$instance = new self;
	return self::$instance;
 }

 public function __construct() {
	// xfavi_DIR contains /home/p135/www/site.ru/wp-content/plugins/myplagin/
	define('xfavi_DIR', plugin_dir_path(__FILE__)); 
	// xfavi_URL contains http://site.ru/wp-content/plugins/myplagin/
	define('xfavi_URL', plugin_dir_url(__FILE__));
	// xfavi_UPLOAD_DIR contains /home/p256/www/site.ru/wp-content/uploads
	$upload_dir = (object)wp_get_upload_dir();
	define('xfavi_UPLOAD_DIR', $upload_dir->basedir);
	// xfavi_UPLOAD_DIR contains /home/p256/www/site.ru/wp-content/uploads/xml-for-avito
	$name_dir = $upload_dir->basedir."/xml-for-avito"; 
	define('xfavi_NAME_DIR', $name_dir);
	$xfavi_keeplogs = xfavi_optionGET('xfavi_keeplogs');
	define('xfavi_KEEPLOGS', $xfavi_keeplogs);
	define('xfavi_VER', '1.3.11');
	$xfavi_version = xfavi_optionGET('xfavi_version');
  	if ($xfavi_version !== xfavi_VER) {xfavi_set_new_options();} // автообновим настройки, если нужно	
	if (!defined('xfavi_ALLNUMFEED')) {
		define('xfavi_ALLNUMFEED', '3');
	}

	add_action('admin_menu', array($this, 'add_admin_menu'));
	add_filter('upload_mimes', array($this, 'xfavi_add_mime_types'));

	add_filter('cron_schedules', array($this, 'cron_add_seventy_sec'));
	add_filter('cron_schedules', array($this, 'cron_add_five_min'));	
	add_filter('cron_schedules', array($this, 'cron_add_six_hours'));

	add_action('xfavi_cron_sborki', array($this, 'xfavi_do_this_seventy_sec'), 10, 1);
	add_action('xfavi_cron_period', array($this, 'xfavi_do_this_event'), 10, 1);
		
	// индивидуальные опции доставки товара
	// add_action('add_meta_boxes', array($this, 'xfavi_add_custom_box'));
	add_action('save_post', array($this, 'xfavi_save_post_product_function'), 50, 3);
	// пришлось юзать save_post вместо save_post_product ибо wc блочит обновы

	// https://wpruse.ru/woocommerce/custom-fields-in-products/
	// https://wpruse.ru/woocommerce/custom-fields-in-variations/
	add_filter('woocommerce_product_data_tabs', array($this, 'xfavi_added_wc_tabs'), 10, 1);
	add_action('admin_footer', array($this, 'xfavi_art_added_tabs_icon'), 10, 1);
	add_action('woocommerce_product_data_panels', array($this, 'xfavi_art_added_tabs_panel'), 10, 1);
	add_action('woocommerce_process_product_meta',  array($this, 'xfavi_art_woo_custom_fields_save'), 10, 1);
	
	/* Мета-поля для категорий товаров */
	add_action("product_cat_edit_form_fields", array($this, 'xfavi_add_meta_product_cat'), 10, 1);
	add_action('edited_product_cat', array($this, 'xfavi_save_meta_product_cat'), 10, 1); 
	add_action('create_product_cat', array($this, 'xfavi_save_meta_product_cat'), 10, 1);	
	
	add_action('admin_notices', array($this, 'xfavi_admin_notices_function'));
	add_action('admin_enqueue_scripts', array(&$this, 'xfavi_reg_script')); // правильно регаем скрипты в админку

	/* Регаем стили только для страницы настроек плагина */
	add_action('admin_init', function() {
		wp_register_style('xfavi-admin-css', plugins_url('css/xfavi_style.css', __FILE__));
	}, 9999);
 }

 public function xfavi_reg_script() {
	// правильно регаем скрипты в админку через промежуточную функцию
	wp_enqueue_script('xfavi_find_products', plugin_dir_url(__FILE__) . 'js/jquery.chained.min.js', array('jquery'));
 }

 public static function xfavi_admin_css_func() {
	/* Ставим css-файл в очередь на вывод */
	wp_enqueue_style('xfavi-admin-css');
 } 

 public static function xfavi_admin_head_css_func() {
	/* печатаем css в шапке админки */
	print '<style>/* xml for Yandex Market */
		.metabox-holder .postbox-container .empty-container {height: auto !important;}
		.icp_img1 {background-image: url('. xfavi_URL .'/img/sl1.jpg);}
		.icp_img2 {background-image: url('. xfavi_URL .'/img/sl2.jpg);}
		.icp_img3 {background-image: url('. xfavi_URL .'/img/sl3.jpg);}
		.icp_img4 {background-image: url('. xfavi_URL .'/img/sl4.jpg);}
		.icp_img5 {background-image: url('. xfavi_URL .'/img/sl6.jpg);}
		.icp_img6 {background-image: url('. xfavi_URL .'/img/sl7.jpg);}
		.icp_img7 {background-image: url('. xfavi_URL .'/img/sl7.jpg);}
	</style>';
 }  
 
 // Срабатывает при активации плагина (вызывается единожды)
 public static function on_activation() {
	$upload_dir = (object)wp_get_upload_dir();
	$name_dir = $upload_dir->basedir.'/xml-for-avito';
	if (!is_dir($name_dir)) {
	 if (!mkdir($name_dir)) {
		error_log('ERROR: Ошибка создания папки '.$name_dir.'; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
		//return false;
	 }
	}
	$numFeed = '1'; // (string)
	if (!defined('xfavi_ALLNUMFEED')) {define('xfavi_ALLNUMFEED', '3');}
	$allNumFeed = (int)xfavi_ALLNUMFEED;
	for ($i = 1; $i<$allNumFeed+1; $i++) {
		$name_dir = $upload_dir->basedir.'/xml-for-avito/feed'.$numFeed;
		if (!is_dir($name_dir)) {
		 if (!mkdir($name_dir)) {
			error_log('ERROR: Ошибка создания папки '.$name_dir.'; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
			//return false;
		 }
		}
		xfavi_optionADD('xfavi_status_cron', 'off', $numFeed);
		xfavi_optionADD('xfavi_step_export', '500', $numFeed);
		xfavi_optionADD('xfavi_status_sborki', '-1', $numFeed); // статус сборки файла
		xfavi_optionADD('xfavi_date_sborki', 'unknown', $numFeed); // дата последней сборки
		xfavi_optionADD('xfavi_type_sborki', 'xml', $numFeed); // тип собираемого файла xml или xls
		xfavi_optionADD('xfavi_file_url', '', $numFeed); // урл до файла
		xfavi_optionADD('xfavi_file_file', '', $numFeed); // путь до файла
		xfavi_optionADD('xfavi_file_ids_in_xml', '', $numFeed);		
		xfavi_optionADD('xfavi_magazin_type', 'woocommerce', $numFeed); // тип плагина магазина
		xfavi_optionADD('xfavi_date_save_set', 'unknown', $numFeed); // дата сохранения настроек
		xfavi_optionADD('xfavi_errors', '', $numFeed);
		
		xfavi_optionADD('xfavi_run_cron', 'off', $numFeed);
		xfavi_optionADD('xfavi_ufup', '0', $numFeed);
		xfavi_optionADD('xfavi_feed_assignment', '', $numFeed);
		xfavi_optionADD('xfavi_whot_export', 'all', $numFeed); // что выгружать (все или там где галка)
		xfavi_optionADD('xfavi_desc', 'fullexcerpt', $numFeed); 
		xfavi_optionADD('xfavi_the_content', 'enabled', $numFeed);
		xfavi_optionADD('xfavi_behavior_strip_symbol', 'default', $numFeed);
		xfavi_optionADD('xfavi_var_desc_priority', 'on', $numFeed);

		xfavi_optionADD('xfavi_allowEmail', 'Да', $numFeed);
		$blog_title = get_bloginfo('name');
		$xfavi_managerName = substr($blog_title, 0, 20);
		xfavi_optionADD('xfavi_managerName', $xfavi_managerName, $numFeed);
		xfavi_optionADD('xfavi_contactPhone', '', $numFeed);
		xfavi_optionADD('xfavi_address', '', $numFeed);
		
		xfavi_optionADD('xfavi_main_product', 'other', $numFeed); // какие товары продаёте
		xfavi_optionADD('xfavi_no_default_png_products', '0', $numFeed);
		xfavi_optionADD('xfavi_skip_products_without_pic', '0', $numFeed);
		xfavi_optionADD('xfavi_skip_missing_products', '0', $numFeed);
		xfavi_optionADD('xfavi_skip_backorders_products', '0', $numFeed);
		xfavi_optionADD('xfavi_size', '', $numFeed);
		xfavi_optionADD('xfavi_condition', 'new', $numFeed);
		$numFeed++;
	}
	if (is_multisite()) {
		add_blog_option(get_current_blog_id(), 'xfavi_version', '1.3.11');
		add_blog_option(get_current_blog_id(), 'xfavi_keeplogs', '0');
		add_blog_option(get_current_blog_id(), 'xfavi_disable_notices', '0');
		add_blog_option(get_current_blog_id(), 'xfavi_enable_five_min', '0');
	} else {
		add_option('xfavi_version', '1.3.11', '', 'no');
		add_option('xfavi_keeplogs', '0');
		add_option('xfavi_disable_notices', '0', '', 'no');
		add_option('xfavi_enable_five_min', '0', '', 'no');		
	}	
 }
 
 // Срабатывает при отключении плагина (вызывается единожды)
 public static function on_deactivation() {
	$numFeed = '1'; // (string)
	if (!defined('xfavi_ALLNUMFEED')) {define('xfavi_ALLNUMFEED', '3');}
	$allNumFeed = (int)xfavi_ALLNUMFEED;
	for ($i = 1; $i<$allNumFeed+1; $i++) {	 
		wp_clear_scheduled_hook('xfavi_cron_period', array($numFeed));
		wp_clear_scheduled_hook('xfavi_cron_sborki', array($numFeed));
		$numFeed++;
	}
	deactivate_plugins('xml-for-avito-pro/xml-for-avito-pro.php');
 } 
 
 // Срабатывает при удалении плагина (вызывается единожды)
 public static function on_uninstall() {
	if (is_multisite()) {		
		delete_blog_option(get_current_blog_id(), 'xfavi_version');
		delete_blog_option(get_current_blog_id(), 'xfavi_keeplogs');
		delete_blog_option(get_current_blog_id(), 'xfavi_disable_notices');
		delete_blog_option(get_current_blog_id(), 'xfavi_enable_five_min');			
	} else {
		delete_option('xfavi_version');
		delete_option('xfavi_keeplogs');
		delete_option('xfavi_disable_notices');
		delete_option('xfavi_enable_five_min');
	}
	$numFeed = '1'; // (string)
	$allNumFeed = (int)xfavi_ALLNUMFEED;
	for ($i = 1; $i<$allNumFeed+1; $i++) {
		xfavi_optionDEL('xfavi_status_cron',$numFeed);
		xfavi_optionDEL('xfavi_step_export', $numFeed);
		xfavi_optionDEL('xfavi_status_sborki', $numFeed);
		xfavi_optionDEL('xfavi_date_sborki', $numFeed);
		xfavi_optionDEL('xfavi_type_sborki', $numFeed);
		xfavi_optionDEL('xfavi_file_url', $numFeed);
		xfavi_optionDEL('xfavi_file_file', $numFeed);
		xfavi_optionDEL('xfavi_file_ids_in_xml', $numFeed);
		xfavi_optionDEL('xfavi_magazin_type', $numFeed);
		xfavi_optionDEL('xfavi_date_save_set', $numFeed);
		xfavi_optionDEL('xfavi_errors', $numFeed);
	
		xfavi_optionDEL('xfavi_run_cron', $numFeed);
		xfavi_optionDEL('xfavi_ufup', $numFeed);
		xfavi_optionDEL('xfavi_feed_assignment', $numFeed);
		xfavi_optionDEL('xfavi_whot_export', $numFeed); 
		xfavi_optionDEL('xfavi_desc', $numFeed);
		xfavi_optionDEL('xfavi_the_content', $numFeed);
		xfavi_optionDEL('xfavi_behavior_strip_symbol', $numFeed);
		xfavi_optionDEL('xfavi_var_desc_priority', $numFeed);
		xfavi_optionDEL('xfavi_allowEmail', $numFeed);
		xfavi_optionDEL('xfavi_managerName', $numFeed);
		xfavi_optionDEL('xfavi_contactPhone', $numFeed);
		xfavi_optionDEL('xfavi_address', $numFeed);

		xfavi_optionDEL('xfavi_main_product', $numFeed);
		xfavi_optionDEL('xfavi_no_default_png_products', $numFeed);
		xfavi_optionDEL('xfavi_skip_products_without_pic', $numFeed);
		xfavi_optionDEL('xfavi_skip_missing_products', $numFeed);
		xfavi_optionDEL('xfavi_skip_backorders_products', $numFeed);
		xfavi_optionDEL('xfavi_size', $numFeed);
		xfavi_optionDEL('xfavi_condition', $numFeed);		
		$numFeed++;
	}
 }

 // Добавляем пункты меню
 public function add_admin_menu() {
	$page_suffix = add_menu_page(null , __('Экспорт на Avito', 'xfavi'), 'manage_options', 'xfaviexport', 'xfavi_export_page', 'dashicons-redo', 51);
	require_once xfavi_DIR.'/export.php'; // Подключаем файл настроек
	// создаём хук, чтобы стили выводились только на странице настроек
	add_action('admin_print_styles-'. $page_suffix, array($this, 'xfavi_admin_css_func'));
 	add_action('admin_print_styles-'. $page_suffix, array($this, 'xfavi_admin_head_css_func'));

	add_submenu_page('xfaviexport', __('Отладка', 'xfavi'), __('Страница отладки', 'xfavi'), 'manage_options', 'xfavidebug', 'xfavi_debug_page');
	require_once xfavi_DIR.'/debug.php';
	$page_subsuffix = add_submenu_page('xfaviexport', __('Добавить расширение', 'xfavi'), __('Расширения', 'xfavi'), 'manage_options', 'xfaviextensions', 'xfavi_extensions_page');
	require_once xfavi_DIR.'/extensions.php';
	add_action('admin_print_styles-'. $page_subsuffix, array($this, 'xfavi_admin_css_func'));
 } 
 
 // Разрешим загрузку xml и csv файлов
 public function xfavi_add_mime_types($mimes) {
	$mimes ['csv'] = 'text/csv';
	$mimes ['xml'] = 'text/xml';
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
		'interval' => 360,
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
 function xfavi_save_post_product_function ($post_id, $post, $update) {
	xfavi_error_log('Стартовала функция xfavi_save_post_product_function! Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
	
	if ($post->post_type !== 'product') {return;} // если это не товар вукомерц
	if (wp_is_post_revision($post_id)) {return;} // если это ревизия
	// проверяем nonce нашей страницы, потому что save_post может быть вызван с другого места.
	// если это автосохранение ничего не делаем
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {return;}
	// проверяем права юзера
	if (!current_user_can('edit_post', $post_id)) {return;}
	// Все ОК. Теперь, нужно найти и сохранить данные
	// Очищаем значение поля input.
	xfavi_error_log('Работает функция xfavi_save_post_product_function! Файл: xml-for-avito.php; Строка: '.__LINE__, 0);

	// Убедимся что поле установлено.
	if (isset($_POST['_xfavi_condition'])) {
		$xfavi_condition = sanitize_text_field($_POST['_xfavi_condition']);
		$xfavi_adType = sanitize_text_field($_POST['_xfavi_adType']);
		$xfavi_goods_type = sanitize_text_field($_POST['_xfavi_goods_type']);
		$xfavi_goods_subtype = sanitize_text_field($_POST['_xfavi_goods_subtype']);
		$xfavi_apparel = sanitize_text_field($_POST['_xfavi_apparel']);
			
		// Обновляем данные в базе данных
		update_post_meta($post_id, '_xfavi_condition', $xfavi_condition);
		update_post_meta($post_id, '_xfavi_adType', $xfavi_adType);
		update_post_meta($post_id, '_xfavi_goods_type', $xfavi_goods_type);
		update_post_meta($post_id, '_xfavi_goods_subtype', $xfavi_goods_subtype);
		update_post_meta($post_id, '_xfavi_apparel', $xfavi_apparel);				
	}
	
	$numFeed = '1'; // (string) создадим строковую переменную
	// нужно ли запускать обновление фида при перезаписи файла
	$allNumFeed = (int)xfavi_ALLNUMFEED;
	for ($i = 1; $i<$allNumFeed+1; $i++) {

		xfavi_error_log('FEED № '.$numFeed.'; Шаг $i = '.$i.' цикла по формированию кэша файлов; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);

		$result_xml_unit = xfavi_unit($post_id, $numFeed); // формируем фид товара
		if (is_array($result_xml_unit)) {
			$result_xml = $result_xml_unit[0];
			$ids_in_xml = $result_xml_unit[1];
		} else {
			$result_xml = $result_xml_unit;
			$ids_in_xml = '';
		}
		xfavi_wf($result_xml, $post_id, $numFeed, $ids_in_xml); // записываем кэш-файл

		$xfavi_ufup = xfavi_optionGET('xfavi_ufup', $numFeed);
		if ($xfavi_ufup !== 'on') {$numFeed++; continue; /*return;*/}
		$status_sborki = (int)xfavi_optionGET('xfavi_status_sborki', $numFeed);
		if ($status_sborki > -1) {$numFeed++; continue; /*return;*/} // если идет сборка фида - пропуск
		
		$xfavi_date_save_set = xfavi_optionGET('xfavi_date_save_set', $numFeed);
		$xfavi_date_sborki = xfavi_optionGET('xfavi_date_sborki', $numFeed);

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
			$filenamefeed = $upload_dir->basedir."/xml-for-avito/".$prefFeed."feed-avito-".get_current_blog_id().".xml";
		} else {
			$upload_dir = (object)wp_get_upload_dir();
			$filenamefeed = $upload_dir->basedir."/xml-for-avito/".$prefFeed."feed-avito-0.xml";
		}
		if (!file_exists($filenamefeed)) {
			xfavi_error_log('FEED № '.$numFeed.'; WARNING: Файла filenamefeed = '.$filenamefeed.' не существует! Пропускаем быструю сборку; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
			$numFeed++; continue; /*return;*/ 
		} // файла с фидом нет
		
		clearstatcache(); // очищаем кэш дат файлов
		$last_upd_file = filemtime($filenamefeed);
		xfavi_error_log('FEED № '.$numFeed.'; $xfavi_date_save_set='.$xfavi_date_save_set.';$filenamefeed='.$filenamefeed, 0);
		xfavi_error_log('FEED № '.$numFeed.'; Начинаем сравнивать даты! Файл: xml-for-avito.php; Строка: '.__LINE__, 0);	
		if ($xfavi_date_save_set > $last_upd_file) {
			// настройки фида сохранялись позже, чем создан фид		
			// нужно полностью пересобрать фид
			xfavi_error_log('FEED № '.$numFeed.'; NOTICE: Настройки фида сохранялись позже, чем создан фид; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
			$xfavi_status_cron = xfavi_optionGET('xfavi_status_cron', $numFeed);
			$recurrence = $xfavi_status_cron;
			wp_clear_scheduled_hook('xfavi_cron_period', array($numFeed));
			wp_schedule_event(time(), $recurrence, 'xfavi_cron_period', array($numFeed));
			xfavi_error_log('FEED № '.$numFeed.'; xfavi_cron_period внесен в список заданий! Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
		} else { // нужно лишь обновить цены	
			xfavi_error_log('FEED № '.$numFeed.'; NOTICE: Настройки фида сохранялись раньше, чем создан фид. Нужно лишь обновить цены; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
			xfavi_clear_file_ids_in_xml($numFeed); /* С версии 3.1.0 */
			xfavi_onlygluing($numFeed);
		}
		$numFeed++;
	}
	return;
 }
  
 /* функции крона */
 public function xfavi_do_this_seventy_sec($numFeed = '1') {
	xfavi_error_log('FEED № '.$numFeed.'; Крон xfavi_do_this_seventy_sec запущен; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);	 
	// xfavi_optionGET('xfavi_status_sborki', $numFeed);	
	$this->xfavi_construct_xml($numFeed); // делаем что-либо каждые 70 сек
 }
 public function xfavi_do_this_event($numFeed = '1') {
	xfavi_error_log('FEED № '.$numFeed.'; Крон xfavi_do_this_event включен. Делаем что-то каждый час; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
	$step_export = (int)xfavi_optionGET('xfavi_step_export', $numFeed);
	if ($step_export === 0) {$step_export = 500;}		
	xfavi_optionUPD('xfavi_status_sborki', $step_export, $numFeed);

	wp_clear_scheduled_hook('xfavi_cron_sborki', array($numFeed));

	// Возвращает nul/false. null когда планирование завершено. false в случае неудачи.
	$res = wp_schedule_event(time(), 'seventy_sec', 'xfavi_cron_sborki', array($numFeed));
	if ($res === false) {
		xfavi_error_log('FEED № '.$numFeed.'; ERROR: Не удалось запланировань CRON seventy_sec; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
	} else {
		xfavi_error_log('FEED № '.$numFeed.'; CRON seventy_sec успешно запланирован; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
	}
 }
 /* end функции крона */

 public static function xfavi_added_wc_tabs($tabs) {
	$tabs['xfavi_special_panel'] = array(
		'label' => __('Avito', 'xfavi'), // название вкладки
		'target' => 'xfavi_added_wc_tabs', // идентификатор вкладки
		'class' => array('hide_if_grouped'), // классы управления видимостью вкладки в зависимости от типа товара
		'priority' => 70, // приоритет вывода
	);
	return $tabs;
 }

 public static function xfavi_art_added_tabs_icon() { 
	// https://rawgit.com/woothemes/woocommerce-icons/master/demo.html 
	?>
	<style>
		#woocommerce-coupon-data ul.wc-tabs li.xfavi_special_panel_options a::before,
		#woocommerce-product-data ul.wc-tabs li.xfavi_special_panel_options a::before,
		.woocommerce ul.wc-tabs li.xfavi_special_panel_options a::before {
			font-family: WooCommerce;
			content: "\e014";
		}
	</style>
	<?php
 }

 public static function xfavi_art_added_tabs_panel() {
	global $post; ?>
	<div id="xfavi_added_wc_tabs" class="panel woocommerce_options_panel">
		<?php do_action('xfavi_before_options_group', $post); ?>
		<div class="options_group">
			<h2><strong><?php _e('Индивидуальные настройки товара для XML фида для Avito', 'xfavi'); ?></strong></h2>
			<?php do_action('xfavi_prepend_options_group', $post); ?>
			<?php			
			woocommerce_wp_select(array(
				'id' => '_xfavi_condition',
				'label' => __('Состояние товара', 'xfavi'),
				'placeholder' => '1',
				'description' => __('Обязательный элемент', 'xfavi'). ' <strong>Condition</strong>',				
				'options' => array(
					'new' => __('Новый', 'xfavi'),
					'bu' => __('Б/у', 'xfavi'),
				),
			));		 
			woocommerce_wp_select(array(
				'id' => '_xfavi_adType',
				'label' => __('AdType', 'xfavi'),
				'placeholder' => '1',
				'description' => __('Обязателен для форматов "Для дома и дачи" и "Личные вещи"', 'xfavi'),					
				'options' => array(
					'default' => __('По умолчанию', 'xfavi'),
					'disabled' => __('Отключено', 'xfavi'),
					'Товар приобретен на продажу' => __('Товар приобретен на продажу', 'xfavi'),
					'Товар от производителя' => __('Товар от производителя', 'xfavi'),
				),
			)); ?>
			<p class="form-field _xfavi_goods_type">
				<label for="xfavi_goods_type"><?php _e('Тип товара', 'xfavi'); ?></label><select name="_xfavi_goods_type" id="_xfavi_goods_type" class="select short">
			 	<?php $xfavi_goods_type = esc_attr(get_post_meta($post->ID, '_xfavi_goods_type', 1)); ?>
			 	<option value="default" <?php selected($xfavi_goods_type, 'default'); ?>><?php _e('По умолчанию', 'xfavi'); ?></option>
				<option value="disabled" <?php selected($xfavi_goods_type, 'disabled'); ?>><?php _e('Отключено', 'xfavi'); ?></option>
				<?php echo xfavi_option_construct_product($post);
				
				/*xfavi_category_goods_type(2, $xfavi_goods_type);*/ ?>	
				</select><span class="description"><?php _e('Элемент', 'xfavi'); ?> <strong>GoodsType</strong> / <strong>Breed</strong> / <strong>TypeId</strong> / <strong>VehicleType</strong>. <a href="//autoload.avito.ru/format/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a></span>
			</p>
			<p class="form-field _xfavi_goods_subtype">
				<label for="xfavi_goods_subtype"><?php _e('Тип товара', 'xfavi'); ?></label><select name="_xfavi_goods_subtype" id="_xfavi_goods_subtype" class="select short">
			 	<?php $xfavi_goods_subtype = esc_attr(get_post_meta($post->ID, '_xfavi_goods_subtype', 1)); ?>
			 	<option value="default" <?php selected($xfavi_goods_subtype, 'default'); ?>><?php _e('По умолчанию', 'xfavi'); ?></option>
				<option value="Изоляция" <?php selected($xfavi_goods_subtype, 'Изоляция'); ?>><?php _e('Изоляция', 'xfavi'); ?></option>
				<option value="Кровля и водосток" <?php selected($xfavi_goods_subtype, 'Кровля и водосток'); ?>><?php _e('Кровля и водосток', 'xfavi'); ?></option>
				<option value="Лаки и краски" <?php selected($xfavi_goods_subtype, 'Лаки и краски'); ?>><?php _e('Лаки и краски', 'xfavi'); ?></option>
				<option value="Листовые материалы" <?php selected($xfavi_goods_subtype, 'Листовые материалы'); ?>><?php _e('Листовые материалы', 'xfavi'); ?></option>
				<option value="Металлопрокат" <?php selected($xfavi_goods_subtype, 'Металлопрокат'); ?>><?php _e('Металлопрокат', 'xfavi'); ?></option>
				<option value="Общестроительные материалы" <?php selected($xfavi_goods_subtype, 'Общестроительные материалы'); ?>><?php _e('Общестроительные материалы', 'xfavi'); ?></option>
				<option value="Отделка" <?php selected($xfavi_goods_subtype, 'Отделка'); ?>><?php _e('Отделка', 'xfavi'); ?></option>
				<option value="Пиломатериалы" <?php selected($xfavi_goods_subtype, 'Пиломатериалы'); ?>><?php _e('Пиломатериалы', 'xfavi'); ?></option>
				<option value="Строительные смеси" <?php selected($xfavi_goods_subtype, 'Строительные смеси'); ?>><?php _e('Строительные смеси', 'xfavi'); ?></option>
				<option value="Строительство стен" <?php selected($xfavi_goods_subtype, 'Строительство стен'); ?>><?php _e('Строительство стен', 'xfavi'); ?></option>
				<option value="Электрика" <?php selected($xfavi_goods_subtype, 'Электрика'); ?>><?php _e('Электрика', 'xfavi'); ?></option>
				<option value="Другое" <?php selected($xfavi_goods_subtype, 'Другое'); ?>><?php _e('Другое', 'xfavi'); ?></option>		 	
				</select><span class="description"><?php _e('Элемент', 'xfavi'); ?> <strong>GoodsSubType</strong>. <a href="//autoload.avito.ru/format/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a></span>
			</p>
			<p class="form-field _xfavi_apparel">
				<label for="_xfavi_apparel">Apparel</label><select name="_xfavi_apparel" id="_xfavi_apparel" class="select short" >
				<?php $xfavi_apparel = esc_attr(get_post_meta($post->ID, '_xfavi_apparel', 1)); ?>
				<option value="default" <?php selected($xfavi_apparel, 'default'); ?>><?php _e('По умолчанию', 'xfavi'); ?></option>
			 	<option value="disabled" <?php selected($xfavi_apparel, 'disabled'); ?>><?php _e('Отключено', 'xfavi'); ?></option>
				<?php echo xfavi_category_goods_type(3, $xfavi_apparel); ?>			 	
				</select><span class="description"><?php _e('Обязательный элемент для Одежды, обуви, аксессуаров', 'xfavi'); ?> <strong>Apparel</strong>. <a href="//autoload.avito.ru/format/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a></span>				
			</p>
			<?php do_action('xfavi_append_options_group', $post); ?>
		</div>
		<?php do_action('xfavi_after_options_group', $post); ?>
	</div>
	<?php
 } 

 public static function xfavi_art_woo_custom_fields_save($post_id) {
	// Сохранение текстового поля
	if (isset($_POST['_xfavi_condition'])) {update_post_meta($post_id, '_xfavi_condition', sanitize_text_field($_POST['_xfavi_condition']));}
	if (isset($_POST['_xfavi_custom'])) {update_post_meta($post_id, '_xfavi_custom', sanitize_text_field($_POST['_xfavi_custom']));}
 }  
 
 public static function xfavi_add_meta_product_cat($term) { ?>
	<tr class="form-field term-parent-wrap">
		<th scope="row" valign="top"><label><?php _e('Обрабатывать согласно правилам Авито', 'xfavi'); ?></label></th>
	 	<td>	   
			<select name="xfavi_cat_meta[xfavi_avito_standart]" id="xfavi_avito_standart">
			<?php $xfavi_avito_standart = esc_attr(get_term_meta($term->term_id, 'xfavi_avito_standart', 1)); ?>				
				<option value="dom" <?php selected($xfavi_avito_standart, 'dom'); ?>><?php _e('Для дома и дачи', 'xfavi'); ?></option>
				<option value="tehnika" <?php selected($xfavi_avito_standart, 'tehnika'); ?>><?php _e('Бытовая электроника', 'xfavi'); ?></option>
				<option value="business" <?php selected($xfavi_avito_standart, 'business'); ?>><?php _e('Для бизнеса', 'xfavi'); ?></option>
				<option value="lichnye_veshi" <?php selected($xfavi_avito_standart, 'lichnye_veshi'); ?>><?php _e('Личные вещи', 'xfavi'); ?></option>
				<option value="zhivotnye" <?php selected($xfavi_avito_standart, 'zhivotnye'); ?>><?php _e('Животные', 'xfavi'); ?></option>
				<option value="zapchasti" <?php selected($xfavi_avito_standart, 'zapchasti'); ?>><?php _e('Запчасти и аксессуары', 'xfavi'); ?> (<?php _e('кроме', 'xfavi'); ?> "<?php _e('Шины, диски и колёса', 'xfavi'); ?>")</option>
				<option value="hobby" <?php selected($xfavi_avito_standart, 'hobby'); ?>><?php _e('Хобби и отдых', 'xfavi'); ?></option>
				</select><br /><label>AdType:</label><br />
			<select name="xfavi_cat_meta[xfavi_adType]" id="xfavi_adType">
			<?php $xfavi_adType = esc_attr(get_term_meta($term->term_id, 'xfavi_adType', 1)); ?>
				<option data-chained="zapchasti" value="Товар приобретен на продажу" <?php selected($xfavi_adType, 'Товар приобретен на продажу'); ?>><?php _e('Товар приобретен на продажу', 'xfavi'); ?></option>
				<option data-chained="zapchasti" value="Товар от производителя" <?php selected($xfavi_adType, 'Товар от производителя'); ?>><?php _e('Товар от производителя', 'xfavi'); ?></option>
				<option data-chained="hobby" value="Товар приобретен на продажу" <?php selected($xfavi_adType, 'Товар приобретен на продажу'); ?>><?php _e('Товар приобретен на продажу', 'xfavi'); ?></option>
				<option data-chained="hobby" value="Товар от производителя" <?php selected($xfavi_adType, 'Товар от производителя'); ?>><?php _e('Товар от производителя', 'xfavi'); ?></option>
				<option data-chained="tehnika" value="disabled" <?php selected($xfavi_adType, 'disabled'); ?>><?php _e('Отключено', 'xfavi'); ?></option>
				<option data-chained="zhivotnye" value="disabled" <?php selected($xfavi_adType, 'disabled'); ?>><?php _e('Отключено', 'xfavi'); ?></option>
				<option data-chained="business" value="disabled" <?php selected($xfavi_adType, 'disabled'); ?>><?php _e('Отключено', 'xfavi'); ?></option>
				<option data-chained="lichnye_veshi" value="Товар приобретен на продажу" <?php selected($xfavi_adType, 'Товар приобретен на продажу'); ?>><?php _e('Товар приобретен на продажу', 'xfavi'); ?></option>
				<option data-chained="lichnye_veshi" value="Товар от производителя" <?php selected($xfavi_adType, 'Товар от производителя'); ?>><?php _e('Товар от производителя', 'xfavi'); ?></option>
				<option data-chained="dom" value="Товар приобретен на продажу" <?php selected($xfavi_adType, 'Товар приобретен на продажу'); ?>><?php _e('Товар приобретен на продажу', 'xfavi'); ?></option>
				<option data-chained="dom" value="Товар от производителя" <?php selected($xfavi_adType, 'Товар от производителя'); ?>><?php _e('Товар от производителя', 'xfavi'); ?></option>
		   </select><br />
		   <p class="description"><?php _e('Укажите по каким правилам будут обрабатываться товары из данной категории', 'xfavi'); ?>. <a href="//autoload.avito.ru/format/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a></p>
		</td>		
	</tr>
	<?php $result_arr = xfavi_option_construct($term); ?>
	<tr class="form-field term-parent-wrap">
		<th scope="row" valign="top"><label><?php _e('Авито', 'xfavi'); ?> Category</label></th>
		<td>
	   		<select name="xfavi_cat_meta[xfavi_avito_product_category]" id="xfavi_avito_product_category">
				<?php echo $result_arr[0]; ?>
			</select><br />
			<p class="description"><?php _e('Укажите какой категори на Авито соответствует данная категория', 'xfavi'); ?>. <?php _e('Обязательный элемент', 'xfavi'); ?> <strong>Category</strong>. <a href="//autoload.avito.ru/format/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a></p>
		</td>
	</tr>
	<tr class="form-field term-parent-wrap">
		<th scope="row" valign="top"><label><?php _e('Авито', 'xfavi'); ?>:<br />- GoodsType<br />- Breed*<br />- TypeId**<br />- VehicleType***</label></th>
		<td>
	   		<select name="xfavi_cat_meta[xfavi_default_goods_type]" id="xfavi_default_goods_type">
				<option value="disabled"><?php _e('Отключено', 'xfavi'); ?></option>
	   			<?php echo $result_arr[1]; ?>
	   		</select><br />
			<p class="description"><?php _e('Элемент', 'xfavi'); ?> <strong>GoodsType</strong> / <strong>Breed</strong> / <strong>TypeId</strong> / <strong>VehicleType</strong>. <a href="//autoload.avito.ru/format/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a><br />
			*Breed - <?php _e('Если Авито Category "Собаки" или "Кошки"', 'xfavi'); ?><br />
			**TypeId - <?php _e('Если Авито Category "Запчасти и аксессуары"', 'xfavi'); ?><br />
			***VehicleType  - <?php _e('Если Авито Category "Велосипеды"', 'xfavi'); ?>
			</p>
		</td>		
	</tr>
	<tr class="form-field term-parent-wrap">
		<th scope="row" valign="top"><label><?php _e('Авито', 'xfavi'); ?> GoodsSubType</label></label></th>
		<td>
	   		<select name="xfavi_cat_meta[xfavi_default_goods_subtype]" id="xfavi_default_goods_subtype">
				<option value="disabled"><?php _e('Отключено', 'xfavi'); ?></option>
	   			<?php echo $result_arr[2]; ?>
	   		</select><br />
			   <p class="description"><?php _e('Элемент', 'xfavi'); ?> <strong>GoodsSubType</strong>. <a href="//autoload.avito.ru/format/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a></p>
		</td>		
	</tr>
	<tr class="form-field term-parent-wrap">
		<th scope="row" valign="top"><label><?php _e('Apparel', 'xfavi'); ?></label></th>
		<td>
			<select name="xfavi_cat_meta[xfavi_default_apparel]" id="xfavi_default_apparel">
			<?php $xfavi_default_apparel = esc_attr(get_term_meta($term->term_id, 'xfavi_default_apparel', 1)); ?>
				<option value="disabled" <?php selected($xfavi_default_apparel, 'disabled'); ?>><?php _e('Отключено', 'xfavi'); ?></option>
				<?php echo xfavi_category_goods_type(3, $xfavi_default_apparel); ?>			 	
			</select><br />					 
			<p class="description"><?php _e('Обязательный элемент для Одежды, обуви, аксессуаров', 'xfavi'); ?> <strong>Apparel</strong>. <a href="//autoload.avito.ru/format/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a></p>
		</td>		
	</tr>	
	<script type="text/javascript">jQuery(document).ready(function() {
		/* https://github.com/tuupola/jquery_chained or $("#series").chainedTo("#mark"); */
		jQuery("#xfavi_adType").chained("#xfavi_avito_standart");
		jQuery("#xfavi_avito_product_category").chained("#xfavi_avito_standart"); 
		jQuery("#xfavi_default_goods_type").chained("#xfavi_avito_product_category");
		jQuery("#xfavi_default_goods_subtype").chained("#xfavi_default_goods_type"); 		
	});</script>  	
   <?php 
 } 
 /* Сохранение данных в БД */
 function xfavi_save_meta_product_cat($term_id){
	if (!isset($_POST['xfavi_cat_meta'])) {return;}
	$xfavi_cat_meta = array_map('sanitize_text_field', $_POST['xfavi_cat_meta']);
	foreach($xfavi_cat_meta as $key => $value) {
		if (empty($value)) {
			delete_term_meta($term_id, $key);
			continue;
		}
		update_term_meta($term_id, $key, $value);
	}
	return $term_id;
 } 
 
 // Вывод различных notices
 public function xfavi_admin_notices_function() {
	$numFeed = '1'; // (string) создадим строковую переменную
	// нужно ли запускать обновление фида при перезаписи файла
	$allNumFeed = (int)xfavi_ALLNUMFEED;

	$xfavi_disable_notices = xfavi_optionGET('xfavi_disable_notices');
if ($xfavi_disable_notices !== 'on') {
	for ($i = 1; $i<$allNumFeed+1; $i++) {
		$status_sborki = xfavi_optionGET('xfavi_status_sborki', $numFeed);
		if ($status_sborki == false) {
			$numFeed++; continue;
		} else {
			$status_sborki = (int)$status_sborki;
		}		
		if ($status_sborki !== -1) {	
			$count_posts = wp_count_posts('product');
			$vsegotovarov = $count_posts->publish;
			$step_export = (int)xfavi_optionGET('xfavi_step_export', $numFeed);
			if ($step_export === 0) {$step_export = 500;}
			$vobrabotke = $status_sborki-$step_export;
			if ($vsegotovarov > $vobrabotke) {
				$vyvod = 'FEED № '.$numFeed.' '. __('Прогресс', 'xfavi').': '.$vobrabotke.' '. __('из', 'xfavi').' '.$vsegotovarov.' '. __('товаров', 'xfavi') .'.<br />'.__('Если индикаторы прогресса не изменились в течение 20 минут, попробуйте уменьшить "Шаг экспорта" в настройках плагина', 'xfavi');
			} else {
				$vyvod = 'FEED № '.$numFeed.' '. __('До завершения менее 70 секунд', 'xfavi');
			}	
			print '<div class="updated notice notice-success is-dismissible"><p>'. __('Идет автоматическое создание файла. XML-фид в скором времени будет создан', 'xfavi').'. '.$vyvod.'.</p></div>';
		}
		$numFeed++;
	}
}

	if (xfavi_optionGET('xfavi_magazin_type', $numFeed) === 'woocommerce') { 
		if (!class_exists('WooCommerce')) {
			print '<div class="notice error is-dismissible"><p>'. __('Для работы требуется плагин WooCommerce', 'xfavi'). '!</p></div>';
		}
	}
	  
	if (isset($_REQUEST['xfavi_submit_action'])) {
		$run_text = '';
		if (sanitize_text_field($_POST['xfavi_run_cron']) !== 'off') {
			$run_text = '. '. __('Создание XML-фида запущено. Вы можете продолжить работу с сайтом', 'xfavi');
		}
		print '<div class="updated notice notice-success is-dismissible"><p>'. __('Обновлено', 'xfavi'). $run_text .'.</p></div>';
	}

	if (isset($_REQUEST['xfavi_submit_debug_page'])) {
		print '<div class="updated notice notice-success is-dismissible"><p>'. __('Обновлено', 'xfavi'). '.</p></div>';
	}

	if (isset($_REQUEST['xfavi_submit_clear_logs'])) {
		$upload_dir = (object)wp_get_upload_dir();
		$name_dir = $upload_dir->basedir."/xml-for-avito";
		$filename = $name_dir.'/xml-for-avito.log';
		$res = unlink($filename);
		if ($res == true) {
			print '<div class="notice notice-success is-dismissible"><p>' .__('Логи были очищены', 'xfavi'). '.</p></div>';					
		} else {
			print '<div class="notice notice-warning is-dismissible"><p>' .__('Ошибка доступа к log-файлу. Возможно log-файл был удален ранее', 'xfavi'). '.</p></div>';		
		}
	}

	/* сброс настроек */
	if (isset($_REQUEST['xfavi_submit_reset'])) {
		if (!empty($_POST) && check_admin_referer('xfavi_nonce_action_reset', 'xfavi_nonce_field_reset')) {
			$this->on_uninstall();
			$this->on_activation();	
			do_action('xfavi_submit_reset');
			print '<div class="updated notice notice-success is-dismissible"><p>'. __('Настройки были сброшены', 'xfavi'). '.</p></div>';			
		}
	} /* end сброс настроек */

	/* отправка отчёта */
	if (isset($_REQUEST['xfavi_submit_send_stat'])) {
		if (!empty($_POST) && check_admin_referer('xfavi_nonce_action_send_stat', 'xfavi_nonce_field_send_stat')) { 	
			if (is_multisite()) { 
				$xfavi_is_multisite = 'включен';	
				$xfavi_keeplogs = get_blog_option(get_current_blog_id(), 'xfavi_keeplogs');
			} else {
				$xfavi_is_multisite = 'отключен'; 
				$xfavi_keeplogs = get_option('xfavi_keeplogs');
			}
			$numFeed = '1'; // (string)
			$mail_content = "Версия плагина: ". xfavi_VER . PHP_EOL;
			$mail_content .= "Версия WP: ".get_bloginfo('version'). PHP_EOL;	 
			$woo_version = xfavi_get_woo_version_number();
			$mail_content .= "Версия WC: ".$woo_version. PHP_EOL;	
			$mail_content .= "Версия PHP: ".phpversion(). PHP_EOL;  
			$mail_content .= "Режим мультисайта: ".$xfavi_is_multisite. PHP_EOL;
			$mail_content .= "Вести логи: ".$xfavi_keeplogs. PHP_EOL;
			$mail_content .= "Расположение логов: ". xfavi_UPLOAD_DIR .'/xfavi.log'. PHP_EOL;
			$possible_problems_arr = xfavi_possible_problems_list();
			if ($possible_problems_arr[1] > 0) {
				$possible_problems_arr[3] = str_replace('<br/>', PHP_EOL, $possible_problems_arr[3]);
				$mail_content .= "Самодиагностика: ". PHP_EOL .$possible_problems_arr[3];
			} else {
				$mail_content .= "Самодиагностика: Функции самодиагностики не выявили потенциальных проблем". PHP_EOL;
			}  
			if (!class_exists('XmlforAvitoPro')) {$mail_content .= "Pro: не активна". PHP_EOL;} else {if (!defined('xfavip_VER')) {define('xfavip_VER', 'н/д');} $mail_content .= "Pro: активна (v ".xfavip_VER.")". PHP_EOL;}
			if (isset($_REQUEST['xfavi_its_ok'])) {
				$mail_content .= PHP_EOL ."Помог ли плагин: ".sanitize_text_field($_REQUEST['xfavi_its_ok']);
			}
			if (isset($_POST['xfavi_email'])) {
				$mail_content .= PHP_EOL ."Почта: ".sanitize_text_field($_POST['xfavi_email']);
			}
			if (isset($_POST['xfavi_message'])) {
				$mail_content .= PHP_EOL ."Сообщение: ".sanitize_text_field($_POST['xfavi_message']);
			}
			$argsp = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1 );
			$products = new WP_Query($argsp);
			$vsegotovarov = $products->found_posts;
			$mail_content .= PHP_EOL ."Число товаров на выгрузку: ". $vsegotovarov;
			$allNumFeed = (int)xfavi_ALLNUMFEED;
			for ($i = 1; $i<$allNumFeed+1; $i++) {
				$status_sborki = (int)xfavi_optionGET('xfavi_status_sborki', $numFeed);
				$xfavi_file_url = urldecode(xfavi_optionGET('xfavi_file_url', $numFeed));
				$xfavi_file_file = urldecode(xfavi_optionGET('xfavi_file_file', $numFeed));
				$xfavi_whot_export = xfavi_optionGET('xfavi_whot_export', $numFeed);
				$xfavi_status_cron = xfavi_optionGET('xfavi_status_cron', $numFeed);
				$xfavi_ufup = xfavi_optionGET('xfavi_ufup', $numFeed);	
				$xfavi_date_sborki = xfavi_optionGET('xfavi_date_sborki', $numFeed);
				$xfavi_main_product = xfavi_optionGET('xfavi_main_product', $numFeed);
				$xfavi_errors = xfavi_optionGET('xfavi_errors', $numFeed);
	
				$mail_content .= PHP_EOL."ФИД №: ".$i. PHP_EOL . PHP_EOL;
				$mail_content .= "status_sborki: ".$status_sborki. PHP_EOL;
				$mail_content .= "УРЛ: ".get_site_url(). PHP_EOL;
				$mail_content .= "УРЛ XML-фида: ".$xfavi_file_url . PHP_EOL;
				$mail_content .= "Временный файл: ".$xfavi_file_file. PHP_EOL;
				$mail_content .= "Что экспортировать: ".$xfavi_whot_export. PHP_EOL;
				$mail_content .= "Автоматическое создание файла: ".$xfavi_status_cron. PHP_EOL;
				$mail_content .= "Обновить фид при обновлении карточки товара: ".$xfavi_ufup. PHP_EOL;
				$mail_content .= "Дата последней сборки XML: ".$xfavi_date_sborki. PHP_EOL;
				$mail_content .= "Что продаёт: ".$xfavi_main_product. PHP_EOL;
				$mail_content .= "Ошибки: ".$xfavi_errors. PHP_EOL;
				$numFeed++;
			}
			wp_mail('support@icopydoc.ru', 'Отчёт XML for Avito', $mail_content);
			print '<div class="updated notice notice-success is-dismissible"><p>'. __('Данные были отправлены. Спасибо', 'xfavi'). '.</p></div>';						
		} 
	} /* end отправка отчёта */ 	
 }	
 
 // сборка
 public static function xfavi_construct_xml($numFeed = '1') {
	xfavi_error_log('FEED № '.$numFeed.'; Стартовала xfavi_construct_xml. Файл: xml-for-avito.php; Строка: '.__LINE__ , 0);

 	$result_xml = '';
	$status_sborki = (int)xfavi_optionGET('xfavi_status_sborki', $numFeed);
  
	// файл уже собран. На всякий случай отключим крон сборки
	if ($status_sborki == -1 ) {wp_clear_scheduled_hook('xfavi_cron_sborki', array($numFeed)); return;}
		  
	$xfavi_date_save_set = xfavi_optionGET('xfavi_date_save_set', $numFeed);
	if ($xfavi_date_save_set == '') {
		$unixtime = current_time('timestamp', 1); // 1335808087 - временная зона GMT(Unix формат)
		xfavi_optionUPD('xfavi_date_save_set', $unixtime, $numFeed);
	}
	$xfavi_date_sborki = xfavi_optionGET('xfavi_date_sborki', $numFeed);
	  
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
		$filenamefeed = $upload_dir->basedir."/xml-for-avito/".$prefFeed."feed-avito-".get_current_blog_id().".xml";		
	} else {
		$upload_dir = (object)wp_get_upload_dir();
		$filenamefeed = $upload_dir->basedir."/xml-for-avito/".$prefFeed."feed-avito-0.xml";
	}
	if (file_exists($filenamefeed)) {		
		xfavi_error_log('FEED № '.$numFeed.'; Файл с фидом '.$filenamefeed.' есть. Файл: xml-for-avito.php; Строка: '.__LINE__ , 0);
		// return; // файла с фидом нет
		clearstatcache(); // очищаем кэш дат файлов
		$last_upd_file = filemtime($filenamefeed);
		xfavi_error_log('FEED № '.$numFeed.'; $xfavi_date_save_set='.$xfavi_date_save_set.'; $filenamefeed='.$filenamefeed, 0);
		xfavi_error_log('FEED № '.$numFeed.'; Начинаем сравнивать даты! Файл: xml-for-avito.php; Строка: '.__LINE__, 0);	
		if ($xfavi_date_save_set < $last_upd_file) {
			xfavi_error_log('FEED № '.$numFeed.'; NOTICE: Нужно лишь обновить цены во всём фиде! Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
			xfavi_clear_file_ids_in_xml($numFeed); /* С версии 3.1.0 */
			xfavi_onlygluing($numFeed);
			return;
		}	
	}
	// далее исходим из того, что файла с фидом нет, либо нужна полная сборка
	  
	$step_export = (int)xfavi_optionGET('xfavi_step_export', $numFeed);
	if ($step_export == 0) {$step_export = 500;}
	  
	if ($status_sborki == $step_export) { // начинаем сборку файла
		do_action('xfavi_before_construct', 'full'); // сборка стартовала
		$result_xml = xfavi_feed_header($numFeed);
		/* создаем файл или перезаписываем старый удалив содержимое */
		$result = xfavi_write_file($result_xml, 'w+', $numFeed);
		if ($result !== true) {
			xfavi_error_log('FEED № '.$numFeed.'; xfavi_write_file вернула ошибку! $result ='.$result.'; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
			return; 
		} else {
			xfavi_error_log('FEED № '.$numFeed.'; xfavi_write_file отработала успешно; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
		}
		xfavi_clear_file_ids_in_xml($numFeed); 
	} 
	if ($status_sborki > 1) {
		$result_xml	= '';
		$offset = $status_sborki-$step_export;
		$whot_export = xfavi_optionGET('xfavi_whot_export', $numFeed);
		if ($whot_export === 'xfavi_vygruzhat') {
			$args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => $step_export,
				'offset' => $offset,
				'relation' => 'AND',
				'meta_query' => array(
					array(
						'key' => '_xfavi_vygruzhat',
						'value' => 'yes'
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

		$args = apply_filters('xfavi_query_arg_filter', $args, $numFeed);
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
			xfavi_gluing($prod_id_arr, $numFeed);
			$status_sborki = $status_sborki + $step_export;
			xfavi_error_log('FEED № '.$numFeed.'; status_sborki увеличен на '.$step_export.' и равен '.$status_sborki.'; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);	  
			xfavi_optionUPD('xfavi_status_sborki', $status_sborki, $numFeed);		   
		} else {
			// если постов нет, пишем концовку файла
			xfavi_error_log('FEED № '.$numFeed.'; Постов больше нет, пишем концовку файла; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
			$result_xml = apply_filters('xfavi_after_offers_filter', $result_xml, $numFeed);
			$result_xml .= "</Ads>";
			/* создаем файл или перезаписываем старый удалив содержимое */
			$result = xfavi_write_file($result_xml, 'a', $numFeed);
			xfavi_error_log('FEED № '.$numFeed.'; Файл фида готов. Осталось только переименовать временный файл в основной; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
			xfavi_rename_file($numFeed);
			// выставляем статус сборки в "готово"
			$status_sborki = -1;
			if ($result === true) {
				xfavi_optionUPD('xfavi_status_sborki', $status_sborki, $numFeed);
				// останавливаем крон сборки
				wp_clear_scheduled_hook('xfavi_cron_sborki', array($numFeed));
				do_action('xfavi_after_construct', 'full'); // сборка закончена
				xfavi_error_log('FEED № '.$numFeed.'; SUCCESS: Сборка успешно завершена; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
			} else {
				xfavi_error_log('FEED № '.$numFeed.'; ERROR: На завершающем этапе xfavi_write_file вернула ошибку! Я не смог записать концовку файла... $result ='.$result.'; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
				do_action('xfavi_after_construct', 'false'); // сборка закончена
				return;
			}
		} // end if ($featured_query->have_posts())
	  } // end if ($status_sborki > 1)
   } // end public static function xfavi_construct_xml
} /* end class XmlforAvito */
?>