<?php if (!defined('ABSPATH')) {exit;}
function xfavi_dom($postId, $product, $data, $numFeed = '1') {	
 xfavi_error_log('FEED № '.$numFeed.'; Стартовала xfavi_dom. $postId = '.$postId.'; Файл: dom.php; Строка: '.__LINE__, 0);	
 $result_xml = '';

 // GoodsType
 if (get_post_meta($postId, '_xfavi_goods_type', true) === '' || get_post_meta($postId, '_xfavi_goods_type', true) === 'default') {
	// если в карточке товара запрет - проверяем значения по дефолту
	if (get_term_meta($data['catid'], 'xfavi_default_goods_type', true) == '') {
		xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к отсутствует GoodsType; Файл: dom.php; Строка: '.__LINE__, 0); return $result_xml;
	} else {		
		$xfavi_default_goods_type = get_term_meta($data['catid'], 'xfavi_default_goods_type', true);
		$xfavi_default_goods_type = str_replace('_', ' ', $xfavi_default_goods_type);		
		if ($xfavi_default_goods_type === 'disabled') {
			$result_goods_type = '';
		} else {
			$result_goods_type = $xfavi_default_goods_type;
		}
	}
 } else {
	$result_goods_type = get_post_meta($postId, '_xfavi_goods_type', true);
	$result_goods_type = str_replace('_', ' ', $result_goods_type);
	if ($result_goods_type === 'disabled') {
		xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к отсутствует GoodsType; Файл: dom.php; Строка: '.__LINE__, 0); return $result_xml;
	}
 }
 $result_xml_goods_type = '<GoodsType>'.$result_goods_type.'</GoodsType>'.PHP_EOL;
 // end GoodsType

 // AdType
 if (get_post_meta($postId, '_xfavi_adType', true) === 'disabled') {
	$result_adType = 'disabled';
 } else if (get_post_meta($postId, '_xfavi_adType', true) == '' || get_post_meta($postId, '_xfavi_adType', true) === 'default') {
	$result_adType = get_term_meta($data['catid'], 'xfavi_adType', true);
	$result_adType = str_replace('_', ' ', $result_adType);
 } else {
	$result_adType = get_post_meta($postId, '_xfavi_adType', true);
 }
 if ($result_adType == '' || $result_adType == 'disabled') {
	xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к отсутствует AdType; Файл: dom.php; Строка: '.__LINE__, 0); return $result_xml;
 } else {
	$result_xml_adType = '<AdType>'.$result_adType.'</AdType>'.PHP_EOL;
 }
 // end AdType

 // GoodsSubType
 if (get_post_meta($postId, '_xfavi_goods_subtype', true) === 'disabled') {
	$result_goods_subtype = 'disabled';
 } else if (get_post_meta($postId, '_xfavi_goods_subtype', true) == '' || get_post_meta($postId, '_xfavi_goods_subtype', true) === 'default') {
	$result_goods_subtype = get_term_meta($data['catid'], 'xfavi_default_goods_subtype', true);
	$result_goods_subtype = str_replace('_', ' ', $result_goods_subtype);
 } else {
	$result_goods_subtype = get_post_meta($postId, '_xfavi_goods_subtype', true);
 }
 if ($result_goods_subtype == '' || $result_goods_subtype == 'disabled') {
	$result_xml_goods_subtype = '';
 } else {
	$result_xml_adType .= '<GoodsSubType>'.$result_goods_subtype.'</GoodsSubType>'.PHP_EOL;
 }
 // end GoodsSubType 

 $data_xml = '';
 $data_xml .= $result_xml_goods_type;
// $data_xml .= $result_xml_apparel;
 $data_xml .= $result_xml_adType;

 $data_xml .= $data['result_xml_сontact_info'];
// $data_xml .= $data['result_xml_desc'];
 $data_xml .= $data['result_xml_avito_cat'];
 $data_xml .= $data['result_xml_condition'];

 $result_main_part = xfavi_main_part($postId, $product, $data, $data_xml, $numFeed);
 if (is_array($result_main_part)) {
	xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.'. Успешно приняты данные от xfavi_main_part; Файл: dom.php; Строка: '.__LINE__, 0);
	return $result_main_part;
 } else {
	xfavi_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к xfavi_main_part вернул string; Файл: dom.php; Строка: '.__LINE__, 0); 
	return $result_xml;
 }
} // end function dom($postId) {
?>