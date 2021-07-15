<?php if (!defined('ABSPATH')) {exit;}
include_once ABSPATH . 'wp-admin/includes/plugin.php'; // без этого не будет работать вне адмники is_plugin_active
function xfavi_feed_header($numFeed = '1') {
 xfavi_error_log('FEED № '.$numFeed.'; Стартовала xfavi_feed_header; Файл: offer.php; Строка: '.__LINE__, 0);	
 $result_xml = '';
 $result_xml .= '<Ads formatVersion="3" target="Avito.ru">'.PHP_EOL;
 $unixtime = current_time('Y-m-d H:i'); // время в unix формате 
 xfavi_optionUPD('xfavi_date_sborki', $unixtime, $numFeed);
 do_action('xfavi_before_items');
 return $result_xml;
}
/*
* @since 1.0.0
*
* @return array($result_xml, $ids_in_xml)
* @return empty string ''
*/
function xfavi_unit($postId, $numFeed = '1') {	
 xfavi_error_log('FEED № '.$numFeed.'; Стартовала xfavi_unit. $postId = '.$postId.'; Файл: offer.php; Строка: '.__LINE__, 0);	
 $result_xml = ''; $ids_in_xml = ''; $skip_flag = false;

 $product = wc_get_product($postId);
 if ($product == null) {xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к get_post вернула null; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml;}

 if ($product->is_type('grouped')) {xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к сгруппированный; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml;}
 
 // что выгружать
 if ($product->is_type('variable')) {
	$xfavi_whot_export = xfavi_optionGET('xfavi_whot_export', $numFeed);
	if ($xfavi_whot_export === 'simple') {xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к вариативный; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml;}
 }

 $special_data_for_flag = '';
 $special_data_for_flag = apply_filters('xfavi_special_data_for_flag_filter', $special_data_for_flag, $product, $numFeed);

 $skip_flag = apply_filters('xfavi_skip_flag', $skip_flag, $postId, $product, $special_data_for_flag, $numFeed); 
 if ($skip_flag === true) {xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен по флагу; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml;} 
 
 if (get_post_meta($postId, 'xfavip_removefromxml', true) === 'on') {xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен принудительно; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml;}

 /* общие данные для вариативных и обычных товаров */
 $result_xml_сontact_info = '';
 $xfavi_address = stripslashes(htmlspecialchars(xfavi_optionGET('xfavi_address', $numFeed)));
 if ($xfavi_address !== '') {
	$result_xml_сontact_info .= '<Address>'.$xfavi_address.'</Address>'.PHP_EOL;
 } else {
	xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к не указан адрес; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml;
 }
 $xfavi_allowEmail = xfavi_optionGET('xfavi_allowEmail', $numFeed); 
 $xfavi_managerName = xfavi_optionGET('xfavi_managerName', $numFeed);
 $xfavi_contactPhone = xfavi_optionGET('xfavi_contactPhone', $numFeed);
 if ($xfavi_allowEmail !=='') {$result_xml_сontact_info .= '<AllowEmail>'.$xfavi_allowEmail.'</AllowEmail>'.PHP_EOL;}
 if ($xfavi_managerName !=='') {$result_xml_сontact_info .= '<ManagerName>'.$xfavi_managerName.'</ManagerName>'.PHP_EOL;}
 if ($xfavi_contactPhone !=='') {$result_xml_сontact_info .= '<ContactPhone>'.$xfavi_contactPhone.'</ContactPhone>'.PHP_EOL;}

 $result_xml_сontact_info = apply_filters('xfavi_xml_сontact_info', $result_xml_сontact_info, $postId, $product, array($xfavi_address, $xfavi_allowEmail, $xfavi_managerName, $xfavi_contactPhone), $numFeed); /* с версии 1.3.1 */
 
 $result_xml_name = htmlspecialchars($product->get_title(), ENT_NOQUOTES); // название товара
 $result_xml_name = apply_filters('xfavi_change_name', $result_xml_name, $postId, $product, $numFeed);
		  
 // описание
 $xfavi_desc = xfavi_optionGET('xfavi_desc', $numFeed);
 $xfavi_the_content = xfavi_optionGET('xfavi_the_content', $numFeed); 
 $result_xml_desc = ''; 
 switch ($xfavi_desc) { 
	case "full": $description_xml = $product->get_description(); break;
	case "excerpt": $description_xml = $product->get_short_description(); break;
	case "fullexcerpt": 
		$description_xml = $product->get_description(); 
		if (empty($description_xml)) {
			$description_xml = $product->get_short_description();
		}
	break;
	case "excerptfull": 
		$description_xml = $product->get_short_description();		 
		if (empty($description_xml)) {
			$description_xml = $product->get_description();
		} 
	break;
	case "fullplusexcerpt": 
		$description_xml = $product->get_description().'<br/>'.$product->get_short_description();
	break;
	case "excerptplusfull": 
		$description_xml = $product->get_short_description().'<br/>'.$product->get_description(); 
	break;	
	default: $description_xml = $product->get_description();
 }	
 $result_xml_desc = '';
 if (!empty($description_xml)) {
	$enable_tags = '<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<ul>,<li>,<ol>,<em>,<strong>,<br/>,<br>';
	$enable_tags = apply_filters('xfavi_enable_tags_filter', $enable_tags, $numFeed);
	if ($xfavi_the_content === 'enabled') {
		$description_xml = html_entity_decode(apply_filters('the_content', $description_xml)); /* с версии 1.0.4 */
	}	
	$description_xml = strip_tags($description_xml, $enable_tags);
	$description_xml = str_replace('<br>', '<br/>', $description_xml);
	$description_xml = strip_shortcodes($description_xml);
	$description_xml = apply_filters('xfavi_description_filter', $description_xml, $postId, $product, $numFeed);
	$description_xml = apply_filters('xfavi_description_filter_simple', $description_xml, $postId, $product, $numFeed);

	$description_xml = trim($description_xml);
	if ($description_xml !== '') {
		$result_xml_desc = '<Description><![CDATA['.$description_xml.']]></Description>'.PHP_EOL;
	} 
 }

 // "Категории 
 $catid = '';  
 if (class_exists('WPSEO_Primary_Term')) {		  
	 $catWPSEO = new WPSEO_Primary_Term('product_cat', $postId);
	 $catidWPSEO = $catWPSEO->get_primary_term();	
	 if ($catidWPSEO !== false) { 
	  $catid = $catidWPSEO;
	 } else {
	  $termini = get_the_terms($postId, 'product_cat');	
	  if ($termini !== false) {
	   foreach ($termini as $termin) {
		 $catid = $termin->term_id;
		 break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
	   }
	  } else { // если база битая. фиксим id категорий
	   xfavi_error_log('FEED № '.$numFeed.'; WARNING: Для товара $postId = '.$postId.' get_the_terms = false. Возможно база битая. Пробуем задействовать wp_get_post_terms; Файл: offer.php; Строка: '.__LINE__, 0);
	   $product_cats = wp_get_post_terms($postId, 'product_cat', array("fields" => "ids"));	  
	   // Раскомментировать строку ниже для автопочинки категорий в БД (место 1 из 2)
	   // wp_set_object_terms($postId, $product_cats, 'product_cat');
	   if (is_array($product_cats) && count($product_cats)) {
		 $catid = $product_cats[0];
		 xfavi_error_log('FEED № '.$numFeed.'; WARNING: Для товара $postId = '.$postId.' база наверняка битая. wp_get_post_terms вернула массив. $catid = '.$catid.'; Файл: offer.php; Строка: '.__LINE__, 0);
	   }
	  }
	 }
  } else {	
	$termini = get_the_terms($postId, 'product_cat');	
	if ($termini !== false) {
	  foreach ($termini as $termin) {
		$catid = $termin->term_id;
		break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
	  }
	} else { // если база битая. фиксим id категорий
	  xfavi_error_log('FEED № '.$numFeed.'; WARNING: Для товара $postId = '.$postId.' get_the_terms = false. Возможно база битая. Пробуем задействовать wp_get_post_terms; Файл: offer.php; Строка: '.__LINE__, 0);
	  $product_cats = wp_get_post_terms($postId, 'product_cat', array("fields" => "ids"));	  
	  // Раскомментировать строку ниже для автопочинки категорий в БД (место 1 из 2)
	  // wp_set_object_terms($postId, $product_cats, 'product_cat');
	  if (is_array($product_cats) && count($product_cats)) {
		$catid = $product_cats[0];
		xfavi_error_log('FEED № '.$numFeed.'; WARNING: Для товара $postId = '.$postId.' база наверняка битая. wp_get_post_terms вернула массив. $catid = '.$catid.'; Файл: offer.php; Строка: '.__LINE__, 0);
	  }
	}
 }
 /* $termin->ID - понятное дело, ID элемента
 * $termin->slug - ярлык элемента
 * $termin->term_group - значение term group
 * $termin->term_taxonomy_id - ID самой таксономии
 * $termin->taxonomy - название таксономии
 * $termin->description - описание элемента
 * $termin->parent - ID родительского элемента
 * $termin->count - количество содержащихся в нем постов
 */	

 if ($catid == '') {xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к нет категорий; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml;}
  
 if (get_term_meta($catid, 'xfavi_avito_standart', true) !== '') {
	$xfavi_avito_standart = get_term_meta($catid, 'xfavi_avito_standart', true);
 } else {
	xfavi_error_log('FEED № '.$numFeed.'; WARNING: Для категории $catid = '.$catid.' задан стандарт по умолчанию; Файл: offer.php; Строка: '.__LINE__, 0);
	$xfavi_avito_standart = 'lichnye_veshi';
 }

 $result_xml_avito_cat = '';
 if (get_term_meta($catid, 'xfavi_avito_product_category', true) !== '') {
	$xfavi_avito_product_category = get_term_meta($catid, 'xfavi_avito_product_category', true);
	$xfavi_avito_product_category = str_replace('_', ' ', $xfavi_avito_product_category);
	$result_xml_avito_cat = '<Category>'.htmlspecialchars($xfavi_avito_product_category).'</Category>'.PHP_EOL;
 } else {
	xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к отсутствует Category; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml;
 }

 if (get_post_meta($postId, '_xfavi_condition', true) === '') {	
	$xfavi_condition = xfavi_optionGET('xfavi_condition', $numFeed);
 } else {	 
	$xfavi_condition = get_post_meta($postId, '_xfavi_condition', true);
 }
 if ($xfavi_condition === 'new') {
 	if (in_array($xfavi_avito_product_category, array('Товары для детей и игрушки', 'Детская одежда и обувь'))) {
		$result_xml_condition = '<Condition>Новый</Condition>'.PHP_EOL;
	} else {
		$result_xml_condition = '<Condition>Новое</Condition>'.PHP_EOL;
	}
 } else {
	$result_xml_condition = '<Condition>Б/у</Condition>'.PHP_EOL;
 }
 /* end общие данные для вариативных и обычных товаров */

 $data = array(
		'result_xml_сontact_info' => $result_xml_сontact_info,
		'result_xml_name' => $result_xml_name, 
		'result_xml_desc' => $result_xml_desc,
		'result_xml_avito_cat' => $result_xml_avito_cat,
		'result_xml_condition' => $result_xml_condition,
		'xfavi_avito_product_category' => $xfavi_avito_product_category,
		'description_xml' => $description_xml,
		'catid' => $catid,
		'special_data_for_flag' => $special_data_for_flag
 );
 
 $res_xml = null;
 switch ($xfavi_avito_standart) {
	case "lichnye_veshi": $res_xml = xfavi_lichnye_veshi($postId, $product, $data, $numFeed); break;
	case "dom": $res_xml = xfavi_dom($postId, $product, $data, $numFeed); break;
	case "tehnika": $res_xml = xfavi_tehnika($postId, $product, $data, $numFeed); break;
	case "zapchasti": $res_xml = xfavi_zapchasti($postId, $product, $data, $numFeed); break;
	case "business": $res_xml = xfavi_business($postId, $product, $data, $numFeed); break;	
	case "hobby": $res_xml = xfavi_hobby($postId, $product, $data, $numFeed); break;
	case "zhivotnye": $res_xml = xfavi_zhivotnye($postId, $product, $data, $numFeed); break;
	default: 
		$res_xml = apply_filters('xfavi_res_xml', $res_xml, $xfavi_avito_standart, $postId, $product, $data, $numFeed);
 }

 if ($res_xml === null || $res_xml === '') {
	xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к $res_xml = null или ""; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml;
 }
 $result_xml = $res_xml[0];
 $ids_in_xml = $res_xml[1];

 return array($result_xml, $ids_in_xml);
} // end function xfavi_unit($postId) {
?>