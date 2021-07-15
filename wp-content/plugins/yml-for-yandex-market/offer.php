<?php if (!defined('ABSPATH')) {exit;}
include_once ABSPATH . 'wp-admin/includes/plugin.php'; // без этого не будет работать вне адмники is_plugin_active
function yfym_feed_header($numFeed = '1') {
 yfym_error_log('FEED № '.$numFeed.'; Стартовала yfym_feed_header; Файл: offer.php; Строка: '.__LINE__, 0);	

 $result_yml = '';
 $unixtime = current_time('Y-m-d H:i'); // время в unix формате 
 yfym_optionUPD('yfym_date_sborki', $unixtime, $numFeed);		
 $shop_name = stripslashes(yfym_optionGET('yfym_shop_name', $numFeed));
 $company_name = stripslashes(yfym_optionGET('yfym_company_name', $numFeed));
 $result_yml .= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
 $result_yml .= '<yml_catalog date="'.$unixtime.'">'.PHP_EOL;
 $result_yml .= "<shop>". PHP_EOL ."<name>".esc_html($shop_name)."</name>".PHP_EOL;
 $result_yml .= "<company>".esc_html($company_name)."</company>".PHP_EOL;
 $res_home_url = home_url('/');
 $res_home_url = apply_filters('yfym_home_url', $res_home_url, $numFeed);
 $result_yml .= "<url>".$res_home_url."</url>".PHP_EOL;
 $result_yml .= "<platform>WordPress - Yml for Yandex Market</platform>".PHP_EOL;
 $result_yml .= "<version>".get_bloginfo('version')."</version>".PHP_EOL;

 if (class_exists('WOOCS')) { 
	$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $numFeed);
	if ($yfym_wooc_currencies !== '') {
		global $WOOCS;
		$WOOCS->set_currency($yfym_wooc_currencies);
	}
 }

 /* общие параметры */
 $res = get_woocommerce_currency(); // получаем валюта магазина
 $rateCB = '';
 switch ($res) { /* RUR, USD, EUR, UAH, KZT, BYN */
	case "RUB": $currencyId_yml = "RUR"; break;
	case "USD": $currencyId_yml = "USD"; $rateCB = "CB"; break;
	case "EUR": $currencyId_yml = "EUR"; $rateCB = "CB"; break;
	case "UAH": $currencyId_yml = "UAH"; break;
	case "KZT": $currencyId_yml = "KZT"; break;
	case "BYN": $currencyId_yml = "BYN"; break;	
	case "BYR": $currencyId_yml = "BYN"; break;
	case "ABC": $currencyId_yml = "BYN"; break;	
	default: $currencyId_yml = "RUR"; 
 }
 $rateCB = apply_filters('yfym_rateCB', $rateCB, $numFeed); /* с версии 2.3.1 */
 $currencyId_yml = apply_filters('yfym_currency_id', $currencyId_yml, $numFeed); /* с версии 3.3.15 */
 if ($rateCB == '') {
	$result_yml .= '<currencies>'. PHP_EOL .'<currency id="'.$currencyId_yml.'" rate="1"/>'. PHP_EOL .'</currencies>'.PHP_EOL;
 } else {
	$result_yml .= '<currencies>'. PHP_EOL .'<currency id="RUR" rate="1"/>'. PHP_EOL .'<currency id="'.$currencyId_yml.'" rate="'.$rateCB.'"/>'. PHP_EOL .'</currencies>'.PHP_EOL;		
 }
 // $terms = get_terms("product_cat");
 if (get_bloginfo('version') < '4.5') {
	$args_terms_arr = array(
		'hide_empty' => 0, 
		'orderby' => 'name'
	);
	$args_terms_arr = apply_filters('yfym_args_terms_arr_filter', $args_terms_arr, $numFeed); /* с версии 3.1.6. */	
	$terms = get_terms('product_cat', $args_terms_arr);
 } else {
	$args_terms_arr = array(
		'hide_empty'  => 0,  
		'orderby' => 'name',
		'taxonomy'    => 'product_cat'
	);
	$args_terms_arr = apply_filters('yfym_args_terms_arr_filter', $args_terms_arr, $numFeed); /* с версии 3.1.6. */	
	$terms = get_terms($args_terms_arr);		
 }
 $count = count($terms);
 $result_yml .= '<categories>'.PHP_EOL;
 if ($count > 0) {		
	$result_categories_yml = '';
	foreach ($terms as $term) {
		$result_categories_yml .= '<category id="'.$term->term_id.'"';
		if ($term->parent !== 0) {
			$result_categories_yml .= ' parentId="'.$term->parent.'"';
		}
		$add_attr = '';
		$add_attr = apply_filters('yfym_add_category_attr_filter', $add_attr, $terms, $term, $numFeed); /* c версии 3.4.2 */
		$result_categories_yml .= $add_attr.'>'.$term->name.'</category>'.PHP_EOL;
	}
	$result_categories_yml = apply_filters('yfym_result_categories_yml_filter', $result_categories_yml, $terms, $numFeed); /* c версии 3.2.0 */	
	$result_yml .= $result_categories_yml;
	unset($result_categories_yml);
 }
 $result_yml = apply_filters('yfym_append_categories_filter', $result_yml, $numFeed);
 $result_yml .= '</categories>'.PHP_EOL; 
		 
 $yfym_delivery_options = yfym_optionGET('yfym_delivery_options', $numFeed);
 if ($yfym_delivery_options === 'on') {
	$delivery_cost = yfym_optionGET('yfym_delivery_cost', $numFeed);
	$delivery_days = yfym_optionGET('yfym_delivery_days', $numFeed);
	$order_before = yfym_optionGET('yfym_order_before', $numFeed);
	if ($order_before == '') {$order_before_yml = '';} else {$order_before_yml = ' order-before="'.$order_before.'"';} 
	$result_yml .= '<delivery-options>'.PHP_EOL;
	$result_yml .= '<option cost="'.$delivery_cost.'" days="'.$delivery_days.'"'.$order_before_yml.'/>'.PHP_EOL;
	$yfym_delivery_options2 = yfym_optionGET('yfym_delivery_options2', $numFeed);
	if ($yfym_delivery_options2 === 'on') {
		$delivery_cost2 = yfym_optionGET('yfym_delivery_cost2', $numFeed);
		$delivery_days2 = yfym_optionGET('yfym_delivery_days2', $numFeed);
		$order_before2 = yfym_optionGET('yfym_order_before2', $numFeed);
		if ($order_before2 == '') {$order_before_yml2 = '';} else {$order_before_yml2 = ' order-before="'.$order_before2.'"';} 
		$result_yml .= '<option cost="'.$delivery_cost2.'" days="'.$delivery_days2.'"'.$order_before_yml2.'/>'.PHP_EOL;
	}
	$result_yml .= '</delivery-options>	'.PHP_EOL;
 }	
			
 // магазин 18+
 $adult = yfym_optionGET('yfym_adult', $numFeed);
 if ($adult === 'yes') {$result_yml .= '<adult>true</adult>'.PHP_EOL;}		
 /* end общие параметры */		
 do_action('yfym_before_offers');
		
 /* индивидуальные параметры товара */
 $result_yml .= '<offers>'.PHP_EOL;	
 return $result_yml;
}
function yfym_unit($postId, $numFeed='1') {	
 yfym_error_log('FEED № '.$numFeed.'; Стартовала yfym_unit. $postId = '.$postId.'; Файл: offer.php; Строка: '.__LINE__, 0);	
 $result_yml = ''; $ids_in_yml = ''; $skip_flag = false;

 if (class_exists('WOOCS')) { 
	$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $numFeed);
	if ($yfym_wooc_currencies !== '') {
		global $WOOCS;
		$WOOCS->set_currency($yfym_wooc_currencies);
	}
 }

 $product = wc_get_product($postId);
 if ($product == null) {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к get_post вернула null; Файл: offer.php; Строка: '.__LINE__, 0); return $result_yml;}

 if ($product->is_type('grouped')) {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к сгруппированный; Файл: offer.php; Строка: '.__LINE__, 0); return $result_yml;}
 
 // что выгружать
 if ($product->is_type('variable')) {
	$yfym_whot_export = yfym_optionGET('yfym_whot_export', $numFeed);
	if ($yfym_whot_export === 'simple') {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к вариативный; Файл: offer.php; Строка: '.__LINE__, 0); return $result_yml;}
 }

 $special_data_for_flag = '';
 $special_data_for_flag = apply_filters('yfym_special_data_for_flag_filter', $special_data_for_flag, $product, $numFeed);
 
 if (get_post_meta($postId, 'yfymp_removefromyml', true) === 'on')  {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен принудительно; Файл: offer.php; Строка: '.__LINE__, 0); return $result_yml;}
 $skip_flag = apply_filters('yfym_skip_flag', $skip_flag, $postId, $product, $numFeed); /* c версии 3.2.6 */
 if ($skip_flag === true) {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен по флагу; Файл: offer.php; Строка: '.__LINE__, 0); return $result_yml;}

 /* общие данные для вариативных и обычных товаров */
 $res = get_woocommerce_currency(); // получаем валюта магазина
 switch ($res) { /* RUR, USD, UAH, KZT, BYN */
	case "RUB":	$currencyId_yml = "RUR"; break;
	case "USD":	$currencyId_yml = "USD"; break;
	case "EUR":	$currencyId_yml = "EUR"; break;
	case "UAH":	$currencyId_yml = "UAH"; break;
	case "KZT":	$currencyId_yml = "KZT"; break;
	case "BYN":	$currencyId_yml = "BYN"; break;
	case "BYR": $currencyId_yml = "BYN"; break;
	case "ABC": $currencyId_yml = "BYN"; break;
	default: $currencyId_yml = "RUR";
 }
 $currencyId_yml = apply_filters('yfym_currency_id', $currencyId_yml, $numFeed); /* с версии 3.3.15 */
		  
 // Возможность купить товар в розничном магазине. // true или false
 $store = yfym_optionGET('yfym_store', $numFeed);
 if ($store === false || $store == '') {
	yfym_error_log('FEED № '.$numFeed.'; WARNING: Товар с postId = '.$postId.' вернул пустой $result_yml_store; Файл: old.php; Строка: '.__LINE__, 0);
	$result_yml_store = '';
 } else {
	$result_yml_store = "<store>".$store."</store>".PHP_EOL;
 }

 if (get_post_meta($postId, 'yfym_individual_delivery', true) !== '') {	
	$delivery = get_post_meta($postId, 'yfym_individual_delivery', true);
	if ($delivery === 'off') {$delivery = yfym_optionGET('yfym_delivery', $numFeed);}
 } else {
 	$delivery = yfym_optionGET('yfym_delivery', $numFeed);
 }
 if ($delivery === false || $delivery == '') {
	yfym_error_log('FEED № '.$numFeed.'; WARNING: Товар с postId = '.$postId.' вернул пустой $delivery; Файл: old.php; Строка: '.__LINE__, 0);
	$result_yml_delivery = '';
 } else {
 	$result_yml_delivery = "<delivery>".$delivery."</delivery>".PHP_EOL; /* !советуют false */
 }
 /*	
 *	== delivery ==
 *	Элемент, отражающий возможность доставки соответствующего товара.
 *	«false» — товар не может быть доставлен («самовывоз»).
 *	«true» — доставка товара осуществлятся в регионы, указанные 
 *	во вкладке «Магазин» в разделе «Товары и цены». 
 *	Стоимость доставки описывается в теге <local_delivery_cost>.
 */	 

 if (get_post_meta($postId, 'yfym_individual_pickup', true) !== '') {	
	$pickup = get_post_meta($postId, 'yfym_individual_pickup', true);
	if ($pickup === 'off') {$pickup = yfym_optionGET('yfym_pickup', $numFeed);}
 } else {
 	$pickup = yfym_optionGET('yfym_pickup', $numFeed);
 }
 if ($pickup === false || $pickup == '') {
	yfym_error_log('FEED № '.$numFeed.'; WARNING: Товар с postId = '.$postId.' вернул пустой $pickup; Файл: old.php; Строка: '.__LINE__, 0);
	$result_yml_pickup = '';
 } else {
 	$result_yml_pickup = "<pickup>".$pickup."</pickup>".PHP_EOL;
 }

 $result_yml_name = htmlspecialchars($product->get_title(), ENT_NOQUOTES); // htmlspecialchars($cur_post->post_title); // название товара
 $result_yml_name = apply_filters('yfym_change_name', $result_yml_name, $product->get_id(), $product, $numFeed);
 /* с версии 2.0.7 в фильтр добавлен параметр $product */
		  
 // описание
 $yfym_desc = yfym_optionGET('yfym_desc', $numFeed);
 $yfym_the_content = yfym_optionGET('yfym_the_content', $numFeed);

 switch ($yfym_desc) { 
	case "full": $description_yml = $product->get_description(); break;
	case "excerpt": $description_yml = $product->get_short_description(); break;
	case "fullexcerpt": 
		$description_yml = $product->get_description(); 
		if (empty($description_yml)) {
			$description_yml = $product->get_short_description();
		}
	break;
	case "excerptfull": 
		$description_yml = $product->get_short_description();		 
		if (empty($description_yml)) {
			$description_yml = $product->get_description();
		} 
	break;
	case "fullplusexcerpt": 
		$description_yml = $product->get_description().'<br/>'.$product->get_short_description();
	break;
	case "excerptplusfull": 
		$description_yml = $product->get_short_description().'<br/>'.$product->get_description(); 
	break;	
	default: $description_yml = $product->get_description(); 
		if (class_exists('YmlforYandexMarketPro')) {
			if ($yfym_desc === 'post_meta') {
				$description_yml = '';
				$description_yml = apply_filters('yfym_description_filter', $description_yml, $postId, $product, $numFeed);
				if (!empty($description_yml)) {trim($description_yml);}
			}
		}
 }	
 $result_yml_desc = '';
 $description_yml = apply_filters('yfym_description_yml_filter', $description_yml, $postId, $product, $numFeed); /* с версии 3.3.0 */
 if (!empty($description_yml)) {
	$enable_tags = '<p>,<h3>,<ul>,<li>,<br/>,<br>';
	/* с версии 3.1.3 */
	$enable_tags = apply_filters('yfym_enable_tags_filter', $enable_tags, $numFeed);
	if ($yfym_the_content === 'enabled') {
		$description_yml = html_entity_decode(apply_filters('the_content', $description_yml)); /* с версии 3.3.6 */
	}
	$description_yml = strip_tags($description_yml, $enable_tags);
	$description_yml = str_replace('<br>', '<br/>', $description_yml);
	$description_yml = strip_shortcodes($description_yml);
	$description_yml = apply_filters('yfym_description_filter', $description_yml, $postId, $product, $numFeed);
	$description_yml = apply_filters('yfym_description_filter_simple', $description_yml, $postId, $product, $numFeed); /* с версии 3.3.6 */
	/* с версии 2.0.10 в фильтр добавлен параметр $product */
	$description_yml = trim($description_yml);
	if ($description_yml !== '') {
		$result_yml_desc = '<description><![CDATA['.$description_yml.']]></description>'.PHP_EOL;
	} 
 }
		  
 $params_arr = unserialize(yfym_optionGET('yfym_params_arr', $numFeed));
		  
 // echo "Категории ".$product->get_categories();
 $result_yml_cat = '';
 $catpostid = '';
 $CurCategoryId = ''; 
 if (class_exists('WPSEO_Primary_Term')) {		  
	$catWPSEO = new WPSEO_Primary_Term('product_cat', $postId);
	$catidWPSEO = $catWPSEO->get_primary_term();	
	if ($catidWPSEO !== false) { 
	 $CurCategoryId = $catidWPSEO;
	 $result_yml_cat .= '<categoryId>'.$catidWPSEO.'</categoryId>'.PHP_EOL;
	 $catpostid = $catidWPSEO;
	} else {
	 $termini = get_the_terms($postId, 'product_cat');	
	 if ($termini !== false) {
	  foreach ($termini as $termin) {
		$catpostid = $termin->term_id;
		$result_yml_cat .= '<categoryId>'.$termin->term_id.'</categoryId>'.PHP_EOL;
		$CurCategoryId = $termin->term_id; // запоминаем id категории для товара
		break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
	  }
	 } else { // если база битая. фиксим id категорий
	  yfym_error_log('FEED № '.$numFeed.'; Warning: Для товара $postId = '.$postId.' get_the_terms = false. Возможно база битая. Пробуем задействовать wp_get_post_terms; Файл: old.php; Строка: '.__LINE__, 0);$product_cats = wp_get_post_terms($postId, 'product_cat', array("fields" => "ids" ));	  
	  // Раскомментировать строку ниже для автопочинки категорий в БД (место 1 из 2)
	  // wp_set_object_terms($postId, $product_cats, 'product_cat');
	  if (is_array($product_cats) && count($product_cats)) {
		$catpostid = $product_cats[0];
		$result_yml_cat .= '<categoryId>'.$catpostid.'</categoryId>'.PHP_EOL;
		$CurCategoryId = $product_cats[0]; // запоминаем id категории для товара
		yfym_error_log('FEED № '.$numFeed.'; Warning: Для товара $postId = '.$postId.' база наверняка битая. wp_get_post_terms вернула массив. $catpostid = '.$catpostid.'; Файл: old.php; Строка: '.__LINE__, 0);
	  }
	 }
	}
 } else {	
	$termini = get_the_terms($postId, 'product_cat');
	if ($termini !== false) {
	 foreach ($termini as $termin) {
		$catpostid = $termin->term_id;
		$result_yml_cat .= '<categoryId>'.$termin->term_id.'</categoryId>'.PHP_EOL;
		$CurCategoryId = $termin->term_id; // запоминаем id категории для товара
		break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
	 }
	} else { // если база битая. фиксим id категорий
	 yfym_error_log('FEED № '.$numFeed.'; Warning: Для товара $postId = '.$postId.' get_the_terms = false. Возможно база битая. Пробуем задействовать wp_get_post_terms; Файл: old.php; Строка: '.__LINE__, 0);
	 $product_cats = wp_get_post_terms($postId, 'product_cat', array("fields" => "ids" ));
	 // Раскомментировать строку ниже для автопочинки категорий в БД (место 2 из 2)	 
	 // wp_set_object_terms($postId, $product_cats, 'product_cat');	 
	 if (is_array($product_cats) && count($product_cats)) {
		$catpostid = $product_cats[0];
		$result_yml_cat .= '<categoryId>'.$catpostid.'</categoryId>'.PHP_EOL;
		$CurCategoryId = $product_cats[0]; // запоминаем id категории для товара
		yfym_error_log('FEED № '.$numFeed.'; Warning: Для товара $postId = '.$postId.' база наверняка битая. wp_get_post_terms вернула массив. $catpostid = '.$catpostid.'; Файл: old.php; Строка: '.__LINE__, 0);		
	 }
	}
 }
 $result_yml_cat = apply_filters('yfym_after_cat_filter', $result_yml_cat, $postId, $numFeed);
 if ($result_yml_cat == '') {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к нет категорий; Файл: old.php; Строка: '.__LINE__, 0); return $result_yml;}
 /* $termin->ID - понятное дело, ID элемента
 * $termin->slug - ярлык элемента
 * $termin->term_group - значение term group
 * $termin->term_taxonomy_id - ID самой таксономии
 * $termin->taxonomy - название таксономии
 * $termin->description - описание элемента
 * $termin->parent - ID родительского элемента
 * $termin->count - количество содержащихся в нем постов
 */	

 $vat = yfym_optionGET('yfym_vat', $numFeed);
 if ($vat === 'disabled') {$result_yml_vat = '';} else {
	if (get_post_meta($postId, 'yfym_individual_vat', true) !== '') {$individual_vat = get_post_meta($postId, 'yfym_individual_vat', true);} else {$individual_vat = 'global';}
	if ($individual_vat === 'global') {
		if ($vat === 'enable') {
			$result_yml_vat = '';
		} else {
			$result_yml_vat = "<vat>".$vat."</vat>".PHP_EOL;
		}
	} else {
		$result_yml_vat = "<vat>".$individual_vat."</vat>".PHP_EOL;
	}
 }
 /* end общие данные для вариативных и обычных товаров */
 $data = array(
	'result_id_yml' => $currencyId_yml,
	'result_yml_store' => $result_yml_store, 
	'result_yml_delivery' => $result_yml_delivery,
	'pickup' => $pickup,
	'result_yml_pickup' => $result_yml_pickup,
	'result_yml_name' => $result_yml_name,
	'result_yml_desc' => $result_yml_desc,
	'catpostid' => $catpostid,
	'result_yml_cat' => $result_yml_cat,
	'cur_category_id' => $CurCategoryId,
	'result_yml_vat' => $result_yml_vat,
	'special_data_for_flag' => $special_data_for_flag,
	'params_arr' => $params_arr
 );

 $res_yml = null;
 $yfym_yml_rules = yfym_optionGET('yfym_yml_rules', $numFeed);
 switch ($yfym_yml_rules) {
	case "yandex_market": $res_yml = yfym_adv($postId, $product, $data, $numFeed); break;
	case "single_catalog": $res_yml = yfym_single_catalog($postId, $product, $data, $numFeed); break;	
	case "dbs": $res_yml = yfym_dbs($postId, $product, $data, $numFeed); break;
	case "beru": $res_yml = yfym_old($postId, $numFeed); break;
	case "all_elements": $res_yml = yfym_all_elements($postId, $numFeed); break;
	case "ozon": $res_yml = yfym_ozon($postId, $product, $data, $numFeed); break;
	default: 
		$res_yml = apply_filters('yfym_res_yml', $res_yml, $yfym_yml_rules, $postId, $product, $data, $numFeed);
 }

 if ($res_yml === null || $res_yml === '') {
	yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к $res_yml = null или ""; Файл: offer.php; Строка: '.__LINE__, 0); return $result_yml;
 }
 $result_yml = $res_yml[0];
 $ids_in_yml = $res_yml[1];
 
 return array($result_yml, $ids_in_yml);
} // end function yfym_unit($postId) {
?>