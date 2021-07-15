<?php if (!defined('ABSPATH')) {exit;}
include_once ABSPATH . 'wp-admin/includes/plugin.php'; // без этого не будет работать вне адмники is_plugin_active
/*
* $data_xml .= $result_goods_type;
* $data_xml .= $result_xml_apparel;
* $data_xml .= $result_xml_adType;
* $data_xml .= $data['result_xml_сontact_info'];
* $data_xml .= $data['result_xml_name'];
* $data_xml .= $data['result_xml_desc'];
* $data_xml .= $data['result_xml_avito_cat'];
* $data_xml .= $data['result_xml_condition'];
* $data_xml .= $data['description_xml'];
* $data_xml .= $data['catid'];
* $special_data_for_flag .= $data['special_data_for_flag'];
*/ 
function yfym_main_part($postId, $product, $data, $data_xml, $numFeed = '1') {	
 $description_xml = $data['result_xml_desc'];
 $catid = $data['catid'];
 $special_data_for_flag = $data['special_data_for_flag'];

 $result_xml = ''; $ids_in_xml = ''; $stop_flag = false; $skip_flag = false; 
 /* Вариации */
 // если вариация - нам нет смысла выгружать общее предложение
 if ($product->is_type('variable')) {
	yfym_error_log('FEED № '.$numFeed.'; У нас вариативный товар. Файл: main_part.php; Строка: '.__LINE__, 0);	
	$yfym_var_desc_priority = yfym_optionGET('yfym_var_desc_priority', $numFeed);
	$yfym_desc = yfym_optionGET('yfym_desc', $numFeed);
	$variations = array();
	if ($product->is_type('variable')) {
		$variations = $product->get_available_variations();
		$variation_count = count($variations);
	} 

	$n = 0; // число вариаций, которые попали в фид
	for ($i = 0; $i<$variation_count; $i++) {
	
		$offer_id = (($product->is_type('variable')) ? $variations[$i]['variation_id'] : $product->get_id());
		$offer = new WC_Product_Variation($offer_id); // получим вариацию
		/*
		* $offer->get_price() - актуальная цена (равна sale_price или regular_price если sale_price пуст)
		* $offer->get_regular_price() - обычная цена
		* $offer->get_sale_price() - цена скидки
		*/
		
		$price_xml = $offer->get_price(); // цена вариации
		$price_xml = apply_filters('yfym_variable_price_filter', $price_xml, $product, $offer, $offer_id, $numFeed);
		// если цены нет - пропускаем вариацию 			 
		if ($price_xml == 0 || empty($price_xml)) {yfym_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к нет цены; Файл: main_part.php; Строка: '.__LINE__, 0); continue;}
		
		if (class_exists('XmlforAvitoPro')) {
			if ((yfym_optionGET('yfymp_compare_value', $numFeed) !== false) && (yfym_optionGET('yfymp_compare_value', $numFeed) !== '')) {
			 $yfymp_compare_value = yfym_optionGET('yfymp_compare_value', $numFeed);
			 $yfymp_compare = yfym_optionGET('yfymp_compare', $numFeed);			 
			 if ($yfymp_compare == '>=') {
				if ($price_xml < $yfymp_compare_value) {continue;}
			 } else {
				if ($price_xml >= $yfymp_compare_value) {continue;}
			 }
			}
		}		

		$thumb_xml = get_the_post_thumbnail_url($offer->get_id(), 'full');
		if (empty($thumb_xml)) {			
			// убираем default.png из фида
			$no_default_png_products = yfym_optionGET('yfym_no_default_png_products', $numFeed);
			if (($no_default_png_products === 'on') && (!has_post_thumbnail($postId))) {$picture_xml = '';} else {
				$thumb_id = get_post_thumbnail_id($postId);
				$thumb_url = wp_get_attachment_image_src($thumb_id,'full', true);	
				$thumb_xml = $thumb_url[0]; /* урл оригинал миниатюры товара */
				$picture_xml = '<Image url="'.yfym_deleteGET($thumb_xml).'"/>'.PHP_EOL;
			}
		} else {
			$picture_xml = '<Image url="'.yfym_deleteGET($thumb_xml).'"/>'.PHP_EOL;
		}
		$picture_xml = apply_filters('yfym_pic_variable_offer_filter', $picture_xml, $product, $offer, $numFeed);
			
		// пропускаем вариации без картинок
		$yfym_skip_products_without_pic = yfym_optionGET('yfym_skip_products_without_pic', $numFeed); 
		if (($yfym_skip_products_without_pic === 'on') && ($picture_xml == '')) {	  
			yfym_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к нет картинки даже в галерее; Файл: main_part.php; Строка: '.__LINE__, 0); return $result_xml; /*continue;*/  
		}

		// пропуск вариаций, которых нет в наличии
		$yfym_skip_missing_products = yfym_optionGET('yfym_skip_missing_products', $numFeed);
		if ($yfym_skip_missing_products == 'on') {
			if ($offer->is_in_stock() == false) {yfym_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к ее нет в наличии; Файл: offer.php; Строка: '.__LINE__, 0); continue;}
		}
					 
		// пропускаем вариации на предзаказ
		$skip_backorders_products = yfym_optionGET('yfym_skip_backorders_products', $numFeed);
		if ($skip_backorders_products == 'on') {
		 if ($offer->get_manage_stock() == true) { // включено управление запасом			  
			if (($offer->get_stock_quantity() < 1) && ($offer->get_backorders() !== 'no')) {yfym_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к запрещен предзаказ и включено управление запасом; Файл: offer.php; Строка: '.__LINE__, 0); continue;}
		 }
		}

		// Описание.
		$result_xml_desc = '';	
		if ($yfym_var_desc_priority === 'on' || empty($description_xml)) {
			switch ($yfym_desc) { 
				case "excerptplusfull": 
					$description_xml = $product->get_short_description().'<br/>'.$offer->get_description(); 
				break;
				case "fullplusexcerpt": 
					$description_xml = $offer->get_description().'<br/>'.$product->get_short_description();
				break;	
				default: $description_xml = $offer->get_description();
			}		
		}		
		if (!empty($description_xml)) {
			$enable_tags = '<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<ul>,<li>,<ol>,<em>,<strong>,<br/>,<br>';
			$enable_tags = apply_filters('yfym_enable_tags_filter', $enable_tags, $numFeed);
			$yfym_the_content = yfym_optionGET('yfym_the_content', $numFeed); 
			if ($yfym_the_content === 'enabled') {
				$description_xml = html_entity_decode(apply_filters('the_content', $description_xml)); /* с версии 1.0.4 */
			}			
			$description_xml = strip_tags($description_xml, $enable_tags);			
			$description_xml = str_replace('<br>', '<br/>', $description_xml);
			$description_xml = strip_shortcodes($description_xml);			
			$description_xml = apply_filters('yfym_description_filter', $description_xml, $postId, $product, $numFeed);
			$description_xml = apply_filters('yfym_description_filter_variable', $description_xml, $postId, $product, $offer, $numFeed);
			$description_xml = trim($description_xml);
			if ($description_xml !== '') {
				$result_xml_desc = '<Description><![CDATA['.$description_xml.']]></Description>'.PHP_EOL;
			}
		} else {
			// если у вариации нет своего описания - пробуем подставить общее
			if (!empty($data['result_xml_desc'])) {$result_xml_desc = $data['result_xml_desc'];}
		}

		if ($result_xml_desc === '') {yfym_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.', offer_id = '.$offer_id.' пропущен т.к. нет описания товара; Файл: main_part.php; Строка: '.__LINE__, 0); continue;}	

		$stop_flag = apply_filters('yfym_before_variable_offer_stop_flag', $stop_flag, $i, $n, $variation_count, $offer_id, $offer, $special_data_for_flag, $numFeed);
		if ($stop_flag == true) {break;}		

		$skip_flag = apply_filters('yfym_skip_flag_variable', $skip_flag, $postId, $product, $offer, $special_data_for_flag, $numFeed);
		if ($skip_flag === true) {yfym_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.', offer_id = '.$offer_id.' пропущен по флагу; Файл: main_part.php; Строка: '.__LINE__, 0); continue;}
			 
		do_action('yfym_before_variable_offer', $numFeed);		

		$result_xml .= '<Ad>'.PHP_EOL;
		$result_xml .= '<Id>'.$offer->get_id().'</Id>'.PHP_EOL;	
 
		$result_xml_name = apply_filters('yfym_change_name_variable', $data['result_xml_name'], $postId, $product, $offer, $numFeed);
		$result_xml .= "<Title>".htmlspecialchars($result_xml_name, ENT_NOQUOTES)."</Title>".PHP_EOL;

		$result_xml .= $result_xml_desc;

		if ($picture_xml !== '') {
			$result_xml .= '<Images>'.PHP_EOL.$picture_xml.'</Images>'.PHP_EOL;	
		}
		
		$price_xml = $offer->get_price();
		$result_xml .= '<Price>'.$price_xml.'</Price>'.PHP_EOL;
		$result_xml .= $data_xml;

		$yfym_size = yfym_optionGET('yfym_size', $numFeed);
		if (!empty($yfym_size) && $yfym_size !== 'disabled') {
		 $yfym_size = (int)$yfym_size;
		 $yfym_size_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_size));
		 if (!empty($yfym_size_xml)) {	
			$result_xml .= "<Size>".ucfirst(urldecode($yfym_size_xml))."</Size>".PHP_EOL;		
		 } else {
			$yfym_size_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_size));
			if (!empty($yfym_size_xml)) {	
				$result_xml .= "<Size>".ucfirst(urldecode($yfym_size_xml))."</Size>".PHP_EOL;		
			}
		 }
		}	

		$result_xml = apply_filters('yfym_append_item_variable', $result_xml, $postId, $product, $offer, $numFeed);

		$result_xml .= '</Ad>'.PHP_EOL;
		$n++;

		do_action('yfym_after_variable_offer');

		$ids_in_xml .= $postId.';'.$offer_id.';'.$price_xml.';'.$catid.PHP_EOL;
	
		$stop_flag = apply_filters('yfym_after_variable_offer_stop_flag', $stop_flag, $i, $n, $variation_count, $offer_id, $offer, $special_data_for_flag, $numFeed); 
		if ($stop_flag == true) {break;}
	} // end for ($i = 0; $i<$variation_count; $i++) 
	yfym_error_log('FEED № '.$numFeed.'; Все вариации выгрузили. '.$ids_in_xml.' Файл: main_part.php; Строка: '.__LINE__, 0);	
	
	return array($result_xml, $ids_in_xml); // все вариации выгрузили	
 } // end if ($product->is_type('variable'))	 
 /* end Вариации */

 /* Обычный товар */

 // убираем default.png из фида
 $no_default_png_products = yfym_optionGET('yfym_no_default_png_products', $numFeed);
 if (($no_default_png_products === 'on') && (!has_post_thumbnail($postId))) {$picture_xml = '';} else {
	$thumb_id = get_post_thumbnail_id($postId);
	$thumb_url = wp_get_attachment_image_src($thumb_id, 'full', true);	
	$thumb_xml = $thumb_url[0]; /* урл оригинал миниатюры товара */
	$picture_xml = '<Image url="'.yfym_deleteGET($thumb_xml).'"/>'.PHP_EOL;
 }
 $picture_xml = apply_filters('yfym_pic_simple_offer_filter', $picture_xml, $product, $numFeed);

 // пропускаем товары без картинок
 $yfym_skip_products_without_pic = yfym_optionGET('yfym_skip_products_without_pic', $numFeed); 
 if (($yfym_skip_products_without_pic === 'on') && ($picture_xml == '')) {	  
	yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к нет картинки даже в галерее; Файл: main_part.php; Строка: '.__LINE__, 0); return $result_xml; /*continue;*/  
 }

 // пропуск товаров, которых нет в наличии
 $yfym_skip_missing_products = yfym_optionGET('yfym_skip_missing_products', $numFeed);
 if ($yfym_skip_missing_products == 'on') {
	if ($product->is_in_stock() == false) { yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к нет в наличии; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml;}
 }		  

 // пропускаем товары на предзаказ
 $skip_backorders_products = yfym_optionGET('yfym_skip_backorders_products', $numFeed);
 if ($skip_backorders_products == 'on') {
	if ($product->get_manage_stock() == true) { // включено управление запасом  
		if (($product->get_stock_quantity() < 1) && ($product->get_backorders() !== 'no')) {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к запрещен предзаказ и включено управление запасом; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml; /*continue;*/}
	} else {
		if ($product->get_stock_status() !== 'instock') { yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к запрещен предзаказ; Файл: offer.php; Строка: '.__LINE__, 0); return $result_xml; /*continue;*/}
	}
 }

 $price_xml = $product->get_price();
 $price_xml = apply_filters('yfym_simple_price_filter', $price_xml, $product, $numFeed);
 if ($price_xml == 0 || empty($price_xml)) {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к нет цены; Файл: main_part.php; Строка: '.__LINE__, 0); return $result_xml;}
 if (class_exists('XmlforAvitoPro')) {
	if ((yfym_optionGET('yfymp_compare_value', $numFeed) !== false) && (yfym_optionGET('yfymp_compare_value', $numFeed) !== '')) {
		$yfymp_compare_value = yfym_optionGET('yfymp_compare_value', $numFeed);
		$yfymp_compare = yfym_optionGET('yfymp_compare', $numFeed);			 
		if ($yfymp_compare == '>=') {
			if ($price_xml < $yfymp_compare_value) {return $result_xml;}
		} else {
			if ($price_xml >= $yfymp_compare_value) {return $result_xml;}
		}
	}
 } 

 if ($data['result_xml_desc'] == '') {yfym_error_log('FEED № '.$numFeed.'; Товара с postId = '.$postId.' пропущен т.к. нет описания товара; Файл: main_part.php; Строка: '.__LINE__, 0); return $result_xml;}

 $result_xml .= '<Ad>'.PHP_EOL;
 $result_xml .= '<Id>'.$postId.'</Id>'.PHP_EOL;

 $result_xml_name = apply_filters('yfym_change_name_simple', $data['result_xml_name'], $postId, $product, $numFeed);
 $result_xml .= "<Title>".htmlspecialchars($result_xml_name, ENT_NOQUOTES)."</Title>".PHP_EOL;
 $result_xml .= $data['result_xml_desc'];
 if ($picture_xml !== '') {
	$result_xml .= '<Images>'.PHP_EOL.$picture_xml.'</Images>'.PHP_EOL;	
 }

 $result_xml .= '<Price>'.$price_xml.'</Price>'.PHP_EOL;

 $result_xml .= $data_xml;

 $yfym_size = yfym_optionGET('yfym_size', $numFeed);
 if (!empty($yfym_size) && $yfym_size !== 'disabled') {	
	$yfym_size = (int)$yfym_size;
	$yfym_size_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_size));
	if (!empty($yfym_size_xml)) {	
		$result_xml .= "<Size>".ucfirst(urldecode($yfym_size_xml))."</Size>".PHP_EOL;		
	}
 } 

 $result_xml = apply_filters('yfym_append_item_simple', $result_xml, $postId, $product, $numFeed);

 $result_xml .= '</Ad>'.PHP_EOL;
		  
 do_action('yfym_after_simple_offer');

 $ids_in_xml .= $postId.';'.$postId.';'.$price_xml.';'.$catid.PHP_EOL;
 
 return array($result_xml, $ids_in_xml);
}
?>