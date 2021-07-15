<?php if (!defined('ABSPATH')) {exit;}
include_once ABSPATH . 'wp-admin/includes/plugin.php'; // без этого не будет работать вне адмники is_plugin_active
function yfym_single_catalog($postId, $product, $data, $numFeed) { // https://yandex.ru/support/marketplace/catalog/yml-simple.html
 // https://yandex.ru/support/partnermarket-catalog/catalog-smb/yml-simple.html
 yfym_error_log('FEED № '.$numFeed.'; Стартовала yfym_single_catalog. $postId = '.$postId.'; Файл: single_catalog.php; Строка: '.__LINE__, 0);	
 $result_yml = ''; $ids_in_yml = ''; $skip_flag = false;
 $yfym_desc = yfym_optionGET('yfym_desc', $numFeed);

// добавить period-of-validity-days, service-life-days

 $currencyId_yml = $data['result_id_yml'];
 $result_yml_store = $data['result_yml_store'];
 $result_yml_delivery = $data['result_yml_delivery'];
 $pickup = $data['pickup'];
 $result_yml_pickup = $data['result_yml_pickup'];
 $result_yml_name = $data['result_yml_name'];
 $result_yml_desc = $data['result_yml_desc'];
 $catpostid = $data['catpostid'];
 $result_yml_cat = $data['result_yml_cat'];
 $CurCategoryId = $data['cur_category_id'];
 $result_yml_vat = $data['result_yml_vat'];
 $special_data_for_flag = $data['special_data_for_flag'];
 $params_arr = $data['params_arr'];
 
 $result_market_sku_yml = ''; $result_tn_ved_code_yml = '';
 $result_availability_yml = '';
 $result_transport_unit_yml = '';
 $result_min_delivery_pieces_yml = '';
 $result_quantum_yml = '';
 $result_leadtime_yml = '';
 $result_box_count_yml = '';
 $yfym_delivery_weekday_yml = '';

 $yfym_market_sku_status = yfym_optionGET('yfym_market_sku_status', $numFeed);
 if ((get_post_meta($postId, '_yfym_market_sku', true) !== '') && ($yfym_market_sku_status === 'enabled')) {
	$yfym_market_sku = get_post_meta($postId, '_yfym_market_sku', true);
	$result_market_sku_yml .= '<market-sku>'.$yfym_market_sku.'</market-sku>'.PHP_EOL;
 }
 if (get_post_meta($postId, '_yfym_tn_ved_code', true) !== '') {
	$yfym_tn_ved_code = get_post_meta($postId, '_yfym_tn_ved_code', true);
	$result_tn_ved_code_yml .= '<tn-ved-codes>'.PHP_EOL;
	$result_tn_ved_code_yml .= '<tn-ved-code>'.$yfym_tn_ved_code.'</tn-ved-code>'.PHP_EOL;
	$result_tn_ved_code_yml .= '</tn-ved-codes>'.PHP_EOL;
 }	
	
 if (get_post_meta($postId, '_yfym_recommend_stock_data_arr', true) !== '') {
	$yfym_recommend_stock_data_arr = get_post_meta($postId, '_yfym_recommend_stock_data_arr', true);

	$availability = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'availability', 'disabled');	
	if ($availability === '' || $availability === 'disabled') {} else {
		$result_availability_yml .= '<availability>'.$availability.'</availability>'.PHP_EOL;
	}

	$transport_unit = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'transport_unit');
	if ($transport_unit !== '') {
		$result_transport_unit_yml .= '<transport-unit>'.$transport_unit.'</transport-unit>'.PHP_EOL;
	}

	$min_delivery_pieces = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'min_delivery_pieces');
	if ($min_delivery_pieces !== '') {
		$result_min_delivery_pieces_yml .= '<min-delivery-pieces>'.$min_delivery_pieces.'</min-delivery-pieces>'.PHP_EOL;
	}

	$quantum = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'quantum');
	if ($quantum !== '') {
		$result_quantum_yml .= '<quantum>'.$quantum.'</quantum>'.PHP_EOL;
	}

	$leadtime = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'leadtime');
	if ($leadtime !== '') {
		$result_leadtime_yml .= '<leadtime>'.$leadtime.'</leadtime>'.PHP_EOL;
	}

	$box_count = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'box_count');
	$box_count = apply_filters('yfym_box_count_filter', $box_count, $product->get_id(), $product, $numFeed);
	if ($box_count !== '') {
		$result_box_count_yml .= '<box-count>'.$box_count.'</box-count>'.PHP_EOL;
	}

	$delivery_weekday_arr = yfym_data_from_arr($yfym_recommend_stock_data_arr, 'delivery_weekday_arr', array());
	if (!empty($delivery_weekday_arr)) {
		$yfym_delivery_weekday_yml .= '<delivery-weekdays>'.PHP_EOL;
		for ($d = 0; $d < count($delivery_weekday_arr); $d++) {
			$yfym_delivery_weekday_yml .= '<delivery-weekday>'.$delivery_weekday_arr[$d].'</delivery-weekday>'.PHP_EOL;
		}
		$yfym_delivery_weekday_yml .= '</delivery-weekdays>'.PHP_EOL;
	}
 }
 
 $append_offer_tag = '';
		  
 /* Вариации */
 // если вариация - нам нет смысла выгружать общее предложение
 if ($product->is_type('variable')) {
	yfym_error_log('FEED № '.$numFeed.'; У нас вариативный товар. Файл: single_catalog.php; Строка: '.__LINE__, 0);	
	$yfym_var_desc_priority = yfym_optionGET('yfym_var_desc_priority', $numFeed);
	$variations = array();
	if ($product->is_type('variable')) {
		$variations = $product->get_available_variations();
		$variation_count = count($variations);
	} 
	for ($i = 0; $i<$variation_count; $i++) {
	
		$offer_id = (($product->is_type('variable')) ? $variations[$i]['variation_id'] : $product->get_id());
		$offer = new WC_Product_Variation($offer_id); // получим вариацию
		$result_yml_name = apply_filters('yfym_variable_change_name', $result_yml_name, $product->get_id(), $product, $offer, $numFeed);
		/* с версии 2.0.7 в фильтр добавлен параметр $product */

		/*
		* $offer->get_price() - актуальная цена (равна sale_price или regular_price если sale_price пуст)
		* $offer->get_regular_price() - обычная цена
		* $offer->get_sale_price() - цена скидки
		*/
			 
		$price_yml = $offer->get_price(); // цена вариации
		$price_yml = apply_filters('yfym_variable_price_filter', $price_yml, $product, $offer, $offer_id, $numFeed); /* с версии 3.0.0 */ 
		// если цены нет - пропускаем вариацию 			 
		if ($price_yml == 0 || empty($price_yml)) {yfym_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к нет цены; Файл: single_catalog.php; Строка: '.__LINE__, 0); continue;}
		
		if (class_exists('YmlforYandexMarketPro')) {
			if ((yfym_optionGET('yfymp_compare_value', $numFeed) !== false) && (yfym_optionGET('yfymp_compare_value', $numFeed) !== '')) {
			 $yfymp_compare_value = yfym_optionGET('yfymp_compare_value', $numFeed);
			 $yfymp_compare = yfym_optionGET('yfymp_compare', $numFeed);			 
			 if ($yfymp_compare == '>=') {
				if ($price_yml < $yfymp_compare_value) {continue;}
			 } else {
				if ($price_yml >= $yfymp_compare_value) {continue;}
			 }
			}
		}
		// пропуск вариаций, которых нет в наличии
		$yfym_skip_missing_products = yfym_optionGET('yfym_skip_missing_products', $numFeed);
		if ($yfym_skip_missing_products === 'on') {
			if ($offer->is_in_stock() == false) {yfym_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к ее нет в наличии; Файл: single_catalog.php; Строка: '.__LINE__, 0); continue;}
		}
			 
		// пропускаем вариации на предзаказ
		$skip_backorders_products = yfym_optionGET('yfym_skip_backorders_products', $numFeed);
		if ($skip_backorders_products === 'on') {
		 if ($offer->get_manage_stock() == true) { // включено управление запасом
			if (($offer->get_stock_quantity() < 1) && ($offer->get_backorders() !== 'no')) {yfym_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к запрещен предзаказ и включено управление запасом; Файл: single_catalog.php; Строка: '.__LINE__, 0); continue;}
		 } else {
			if ($offer->get_stock_status() === 'onbackorder') { // предзаказ на уровнее вариации включен
				yfym_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к запрещен предзаказ на уровне вариации и отключено управление запасом; Файл: single_catalog.php; Строка: '.__LINE__, 0); continue;
			}
		 }
		}
		
		$thumb_yml = get_the_post_thumbnail_url($offer->get_id(), 'full');
		if (empty($thumb_yml)) {			
			// убираем default.png из фида
			$no_default_png_products = yfym_optionGET('yfym_no_default_png_products', $numFeed);
			if (($no_default_png_products === 'on') && (!has_post_thumbnail($postId))) {$picture_yml = '';} else {
				$thumb_id = get_post_thumbnail_id($postId);
				$thumb_url = wp_get_attachment_image_src($thumb_id,'full', true);	
				$thumb_yml = $thumb_url[0]; /* урл оригинал миниатюры товара */
				$picture_yml = '<picture>'.deleteGET($thumb_yml).'</picture>'.PHP_EOL;
			}
		} else {
			$picture_yml = '<picture>'.deleteGET($thumb_yml).'</picture>'.PHP_EOL;
		}
		$picture_yml = apply_filters('yfym_pic_variable_offer_filter', $picture_yml, $product, $numFeed, $offer); /* c версии 3.1.2 добавлен $offer */
			
		// пропускаем вариации без картинок
		$yfym_skip_products_without_pic = yfym_optionGET('yfym_skip_products_without_pic', $numFeed); 
		if (($yfym_skip_products_without_pic === 'on') && ($picture_yml == '')) {	  
			yfym_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к нет картинки даже в галерее; Файл: single_catalog.php; Строка: '.__LINE__, 0); continue; /*continue;*/  
		}

		$skip_flag = apply_filters('yfym_skip_flag_variable', $skip_flag, $postId, $product, $offer, $numFeed); /* c версии 3.2.6 */
		if ($skip_flag === true) {yfym_error_log('FEED № '.$numFeed.'; Вариативный товар с postId = '.$postId.', offer_id = '.$offer_id.' пропущен по флагу; Файл: single_catalog.php; Строка: '.__LINE__, 0); return $result_yml;}
		if ($skip_flag === 'continue') {yfym_error_log('FEED № '.$numFeed.'; Вариация товара с с postId = '.$postId.', offer_id = '.$offer_id.' пропущена по флагу; Файл: single_catalog.php; Строка: '.__LINE__, 0); $skip_flag = false; continue;}   
		
		do_action('yfym_before_variable_offer');

		if ($offer->get_manage_stock() == true) { // включено управление запасом
			if ($offer->get_stock_quantity() > 0) {
				$available = 'true';
			} else {
				if ($offer->get_backorders() === 'no') { // предзаказ запрещен
					$available = 'false';
				} else {
					$yfym_behavior_onbackorder = yfym_optionGET('yfym_behavior_onbackorder', $numFeed);
					if ($yfym_behavior_onbackorder === 'false') {
						$available = 'false';
					} else {
						$available = 'true';
					}
				}
			}
		} else { // отключено управление запасом
			if ($offer->get_stock_status() === 'instock') {
				$available = 'true';
			} else if ($offer->get_stock_status() === 'outofstock') { 
				$available = 'false';
			} else {
				$yfym_behavior_onbackorder = yfym_optionGET('yfym_behavior_onbackorder', $numFeed);
				if ($yfym_behavior_onbackorder === 'false') {
					$available = 'false';
				} else {
					$available = 'true';
				}
			}
 		}
		$available = apply_filters('yfym_available_variable_filter', $available, $product, $offer, $postId, $numFeed); /* С версии 3.5.3 */

		// массив категорий для которых запрещен group_id
		$no_group_id_arr = unserialize(yfym_optionGET('yfym_no_group_id_arr', $numFeed));
		if (empty($no_group_id_arr)) {
			// массив пуст. все категории выгружаем с group_id
			$gi = 'group_id="'.$product->get_id().'"';
			$result_yml_name_itog = $result_yml_name;
		} else {
			// массив с group_id заполнен
			$CurCategoryId = (string)$CurCategoryId;
			// если id текущей категории совпал со списком категорий без group_id			  
			if (in_array($CurCategoryId, $no_group_id_arr)) {
			 $gi = '';
				
			 $add_in_name_arr = unserialize(yfym_optionGET('yfym_add_in_name_arr', $numFeed));
			 $attributes = $product->get_attributes(); // получили все атрибуты товара
			 $param_at_name = '';		

			 $separator_type = yfym_optionGET('yfym_separator_type', $numFeed);			 
			 switch ($separator_type) {
				case "type1":
					$so = '('; $sz = ')';
					$sd = ':'; $sr = ',';
				break;
				case "type2":	
					$so = '('; $sz = ')';
					$sd = '-'; $sr = ',';
				break; 
				case "type3":
					$so = ''; $sz = '';
					$sd = ':'; $sr = ',';
				break;
				case "type4":
					$so = ''; $sz = '';
					$sd = ''; $sr = '';
				break;				
				default: 
					$so = ''; $sz = '';
					$sd = ':'; $sr = ',';
			 }		 
			 
			 foreach ($attributes as $param) {					
				if ($param->get_variation() == false) {
					// это обычный атрибут
					continue;
					$param_val = $product->get_attribute(wc_attribute_taxonomy_name_by_id($param->get_id()));
				} else { 
					// это атрибут вариации
					$param_val = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($param->get_id()));
				}
				// если этот параметр не нужно выгружать - пропускаем
				$variation_id_string = (string)$param->get_id(); // важно, т.к. в настройках id как строки
				if (!in_array($variation_id_string, $add_in_name_arr, true)) {continue;}
				$param_name = wc_attribute_label(wc_attribute_taxonomy_name_by_id($param->get_id()));
				// если пустое имя атрибута или значение - пропускаем
				if (empty($param_name) || (empty($param_val) && $param_val !== '0')) {continue;}
				if ($separator_type === 'type4') {
					$param_at_name .= $sd.ucfirst(yfym_replace_decode($param_val)).$sr.' ';
				} else {
					$param_at_name .= $param_name.$sd.ucfirst(yfym_replace_decode($param_val)).$sr.' ';
				}
			 }
				$param_at_name = trim($param_at_name);
				if ($param_at_name == '') {
					yfym_error_log('FEED № '.$numFeed.'; Пропускаем товар т.к. нет атрибутов вариаций для уникальзации заголовков. Файл: single_catalog.php; Строка: '.__LINE__, 0);	
					return ''; // $result_yml;	/*continue;*/
				}

				// подрежем последнюю запятую/разделитель
				$lenght_sr = strlen($sr);
				if ($lenght_sr > 0) {$param_at_name = substr($param_at_name, 0, -$lenght_sr);}

				$result_yml_name_itog = $result_yml_name.' '.$so.$param_at_name.$sz;
				$result_yml_name_itog = apply_filters('yfym_name_no_groupid_filter', $result_yml_name_itog, $result_yml_name, $product, $so, $sz, $sd, $sr, $numFeed);
			} else {
				// совпадений нет. подставляем group_id
				$gi = 'group_id="'.$product->get_id().'"';
				$result_yml_name_itog = $result_yml_name;
			}
		}

		
		// страна производитель
		$result_yml_country_of_origin = '';
		$country_of_origin = yfym_optionGET('yfym_country_of_origin', $numFeed);
		if (!empty($country_of_origin) && $country_of_origin !== 'off') {
			$country_of_origin = (int)$country_of_origin;
			$country_of_origin_yml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($country_of_origin));
			if (!empty($country_of_origin_yml)) {	
				$result_yml_country_of_origin = "<country_of_origin>".ucfirst(yfym_replace_decode($country_of_origin_yml))."</country_of_origin>".PHP_EOL;		
			} else {
				$country_of_origin_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($country_of_origin));
				if (!empty($country_of_origin_yml)) {	
					$result_yml_country_of_origin = "<country_of_origin>".ucfirst(yfym_replace_decode($country_of_origin_yml))."</country_of_origin>".PHP_EOL;		
				}
			}
		}
		if ($result_yml_country_of_origin === '') {
			yfym_error_log('FEED № '.$numFeed.'; Пропускаем вариацию offer_id = '.$offer_id.' товара $postId = '.$postId.' т.к. нет country_of_origin. Файл: single_catalog.php; Строка: '.__LINE__, 0);	
			continue;		
		} 

		$result_yml_manufacturer = '';
		$yfym_manufacturer = yfym_optionGET('yfym_manufacturer', $numFeed);
		if (!empty($yfym_manufacturer) && $yfym_manufacturer !== 'disabled') {
			$yfym_manufacturer = (int)$yfym_manufacturer;
			$yfym_manufacturer_yml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_manufacturer));
			if (!empty($yfym_manufacturer_yml)) {	
				$result_yml_manufacturer = "<manufacturer>".ucfirst(yfym_replace_decode($yfym_manufacturer_yml))."</manufacturer>".PHP_EOL;		
			} else {
				$yfym_manufacturer_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_manufacturer));
				if (!empty($yfym_manufacturer_yml)) {	
					$result_yml_manufacturer = "<manufacturer>".ucfirst(yfym_replace_decode($yfym_manufacturer_yml))."</manufacturer>".PHP_EOL;		
				}
			}
		}
		if ($result_yml_manufacturer === '') {
			yfym_error_log('FEED № '.$numFeed.'; Пропускаем вариацию offer_id = '.$offer_id.' товара $postId = '.$postId.' т.к. нет manufacturer. Файл: single_catalog.php; Строка: '.__LINE__, 0);	
			continue;			
		} 

		$result_yml_vendor = '';
		$vendor = yfym_optionGET('yfym_vendor', $numFeed);
		if ((is_plugin_active('perfect-woocommerce-brands/perfect-woocommerce-brands.php') || is_plugin_active('perfect-woocommerce-brands/main.php') || class_exists('Perfect_Woocommerce_Brands')) && $vendor === 'sfpwb') {
			$barnd_terms = get_the_terms($product->get_id(), 'pwb-brand');
			if ($barnd_terms !== false) {
			 foreach($barnd_terms as $barnd_term) {
				$result_yml_vendor = '<vendor>'. $barnd_term->name .'</vendor>'.PHP_EOL;
				break;
			 }
			}
		} else if ((is_plugin_active('premmerce-woocommerce-brands/premmerce-brands.php')) && ($vendor === 'premmercebrandsplugin')) {
			$barnd_terms = get_the_terms($product->get_id(), 'product_brand');
			if ($barnd_terms !== false) {
			 foreach($barnd_terms as $barnd_term) {
				$result_yml_vendor = '<vendor>'. $barnd_term->name .'</vendor>'.PHP_EOL;
				break;
			 }
			}			
		} else if (class_exists('woo_brands') && $vendor === 'woo_brands') {
			$barnd_terms = get_the_terms($product->get_id(), 'product_brand');
			if ($barnd_terms !== false) {
			 foreach($barnd_terms as $barnd_term) {
				$result_yml_vendor = '<vendor>'. $barnd_term->name .'</vendor>'.PHP_EOL;
				break;
			 }
			}			
		} else {
		 if ($vendor !== 'disabled') {
			$vendor = (int)$vendor;
			$vendor_yml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($vendor));
			if (!empty($vendor_yml)) {
				$result_yml_vendor = '<vendor>'.ucfirst(yfym_replace_decode($vendor_yml)).'</vendor>'.PHP_EOL;
			} else {
				$vendor_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($vendor));
				if (!empty($vendor_yml)) {
					$result_yml_vendor = '<vendor>'.ucfirst(yfym_replace_decode($vendor_yml)).'</vendor>'.PHP_EOL;
				}
			}
		 }
		}
		if ($result_yml_vendor === '') {
			yfym_error_log('FEED № '.$numFeed.'; Пропускаем вариацию offer_id = '.$offer_id.' товара $postId = '.$postId.' т.к. нет vendor. Файл: single_catalog.php; Строка: '.__LINE__, 0);	
			continue;
		}

		$result_yml_shop_sku = '';
		$yfym_shop_sku = yfym_optionGET('yfym_shop_sku', $numFeed);
		switch ($yfym_shop_sku) { /* disabled, sku, products_id, или id */
			case "disabled":	
			   // выгружать штрихкод нет нужды
			break; 
			case "sku":
				// выгружать из артикула
				$sku_yml = $offer->get_sku(); // артикул
				if (!empty($sku_yml)) {
					$sku_yml = yfym_replace_symbol($sku_yml, $numFeed);
					$result_yml_shop_sku = "<shop-sku>".$sku_yml."</shop-sku>".PHP_EOL;
				} else {
					// своего артикула у вариации нет. Пробуем подставить общий sku
					$sku_yml = $product->get_sku();
					if (!empty($sku_yml)) {
						$sku_yml = yfym_replace_symbol($sku_yml, $numFeed);
						$result_yml_shop_sku = "<shop-sku>".$sku_yml."</shop-sku>".PHP_EOL;
					}
				}
			break;
			case "products_id":
				// выгружать из артикула
				$result_yml_shop_sku = "<shop-sku>".$offer_id."</shop-sku>".PHP_EOL;
			break;
			default:
				$yfym_shop_sku = (int)$yfym_shop_sku;
				$yfym_shop_sku_yml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_shop_sku));
				if (!empty($yfym_shop_sku_yml)) {
					$yfym_shop_sku_yml = yfym_replace_symbol($yfym_shop_sku_yml, $numFeed);
					$result_yml_shop_sku = '<shop-sku>'.ucfirst(yfym_replace_decode($yfym_shop_sku_yml)).'</shop-sku>'.PHP_EOL;
				} else {
					$yfym_shop_sku_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_shop_sku));
					if (!empty($yfym_shop_sku_yml)) {
						$yfym_shop_sku_yml = yfym_replace_symbol($yfym_shop_sku_yml, $numFeed);
						$result_yml_shop_sku = '<shop-sku>'.ucfirst(yfym_replace_decode($yfym_shop_sku_yml)).'</shop-sku>'.PHP_EOL;
					}
				}
		}

		$offer_type = '';
		$offer_type = apply_filters('yfym_variable_offer_type_filter', $offer_type, $catpostid, $postId, $offer_id, $product, $offer, $numFeed);  /* с версии 3.3.3 */	   

		// $result_yml .= '<offer '.$gi.' id="'.$product->get_id().'var'.$offer_id.'" available="'.$available.'">'.PHP_EOL;
		/* с версии 2.1.2 */
		$append_offer_tag = apply_filters('yfym_append_offer_tag_filter', $append_offer_tag, $product, $numFeed);		
		$result_yml .= '<offer '.$offer_type.$gi.' id="'.$offer_id.'" available="'.$available.'" '.$append_offer_tag.'>'.PHP_EOL;		
		do_action('yfym_prepend_variable_offer');

		// $param_at_name = '';
		// Param в вариациях
		if (!empty($params_arr)) {
			$attributes = $product->get_attributes(); // получили все атрибуты товара		 
			foreach ($attributes as $param) {					
			 if ($param->get_variation() == false) {
				// это обычный атрибут
				$param_val = $product->get_attribute(wc_attribute_taxonomy_name_by_id($param->get_id())); 
			 } else { 
				$param_val = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($param->get_id()));
			 }				
			 // если этот параметр не нужно выгружать - пропускаем
			 $variation_id_string = (string)$param->get_id(); // важно, т.к. в настройках id как строки
			 if (!in_array($variation_id_string, $params_arr, true)) {continue;}
			 $param_name = wc_attribute_label(wc_attribute_taxonomy_name_by_id($param->get_id()));
			 // если пустое имя атрибута или значение - пропускаем
			 if (empty($param_name) || empty($param_val)) {continue;}
			 $result_yml .= '<param name="'.htmlspecialchars($param_name).'">'.ucfirst(yfym_replace_decode($param_val)).'</param>'.PHP_EOL;
			 // $param_at_name .= ucfirst(urldecode($param_val)).' ';
			}	
		}

		$result_yml_name_itog = apply_filters('yfym_before_insert_name_filter', $result_yml_name_itog, $numFeed); /* с версии 3.3.18 */
		$result_yml .= "<name>".htmlspecialchars($result_yml_name_itog, ENT_NOQUOTES)."</name>".PHP_EOL;
		$yfym_enable_auto_discounts = yfym_optionGET('yfym_enable_auto_discounts', $numFeed);
		if ($yfym_enable_auto_discounts === 'on') {
			$result_yml .= "<enable_auto_discounts>yes</enable_auto_discounts>".PHP_EOL;
		}
		// $result_yml .= "<name>".$result_yml_name." (".$param_at_name.")</name>".PHP_EOL;

		// Описание.
		if ($yfym_var_desc_priority === 'on' || empty($description_yml)) {
			switch ($yfym_desc) { 
				case "excerptplusfull": 
					$description_yml = $product->get_short_description().'<br/>'.$offer->get_description(); 
				break;					
				case "fullplusexcerpt": 
					$description_yml = $offer->get_description().'<br/>'.$product->get_short_description();
				break;	
				default: $description_yml = $offer->get_description();
			}		
		}

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
			$description_yml = apply_filters('yfym_description_filter_variable', $description_yml, $postId, $product, $offer, $numFeed); /* с версии 3.2.6 */
			$description_yml = trim($description_yml);
			if ($description_yml !== '') {
				$result_yml .= '<description><![CDATA['.$description_yml.']]></description>'.PHP_EOL;
			}
			$description_yml = ''; // обнулим значение описания вариации, чтобы след вариация получила своё
		} else {
			// если у вариации нет своего описания - пробуем подставить общее
			if (!empty($result_yml_desc)) {$result_yml .= $result_yml_desc;}
		}
		 
		$result_yml .= $picture_yml;	
	 
		$result_url = htmlspecialchars(get_permalink($offer->get_id()));
		$yfym_clear_get = yfym_optionGET('yfym_clear_get', $numFeed);
		if ($yfym_clear_get === 'yes') {$result_url = deleteGET($result_url, 'url');}
		$result_url = apply_filters('yfym_url_filter', $result_url, $product, $CurCategoryId, $numFeed); /* с версии 2.0.12 в фильтр добавлен параметр $CurCategoryId */
		$result_url = apply_filters('yfym_variable_url_filter', $result_url, $product, $offer, $CurCategoryId, $numFeed); /* с версии 3.3.14 */		
		 
		$result_yml .= "<url>".$result_url."</url>".PHP_EOL;
		 
		$price_yml = apply_filters('yfym_variable_price_yml_filter', $price_yml, $product, $offer, $numFeed); /* с версии 3.1.0 */
		$yfym_price_from = yfym_optionGET('yfym_price_from', $numFeed);
		if ($yfym_price_from === 'yes') {
			$result_yml .= "<price from='true'>".$price_yml."</price>".PHP_EOL;
		} else {
			$result_yml .= "<price>".$price_yml."</price>".PHP_EOL;
		}
		// старая цена
		$yfym_oldprice = yfym_optionGET('yfym_oldprice', $numFeed);
		if ($yfym_oldprice === 'yes') {
			$price_yml = (float)$price_yml;
			$sale_price = (float)$offer->get_sale_price();
			$sale_price = apply_filters('yfym_variable_sale_price_filter', $sale_price, $price_yml, $product, $offer, $numFeed); /* с версии 3.5.1 */
			if ($sale_price > 0) {
				if ($price_yml === $sale_price) {		
					$oldprice_yml = $offer->get_regular_price();
					$oldprice_name_tag = 'oldprice';
					$oldprice_name_tag = apply_filters('yfym_oldprice_name_tag_filter', $oldprice_name_tag, $numFeed); /* с версии 3.2.0 */ 			
					$result_yml .= "<".$oldprice_name_tag.">".$oldprice_yml."</".$oldprice_name_tag.">".PHP_EOL;
				}
			}
		}	 
		$result_yml .= '<currencyId>'.$currencyId_yml.'</currencyId>'.PHP_EOL;

		if ($offer->get_manage_stock() == true) { // включено управление запасом
			$stock_quantity = $offer->get_stock_quantity();
			$yfym_count = yfym_optionGET('yfym_count', $numFeed);
			if ($yfym_count === 'enabled' && $stock_quantity > -1) {
				$result_yml .= '<count>'.$stock_quantity.'</count>'.PHP_EOL;
			}
		} else {
			if ($product->get_manage_stock() == true) { // включено управление запасом
				$stock_quantity = $product->get_stock_quantity();
				$yfym_count = yfym_optionGET('yfym_count', $numFeed);
				if ($yfym_count === 'enabled' && $stock_quantity > -1) {
					$result_yml .= '<count>'.$stock_quantity.'</count>'.PHP_EOL;
				}
			} 
		}			

		// штрихкод			 
		$yfym_barcode = yfym_optionGET('yfym_barcode', $numFeed);
		switch ($yfym_barcode) { /* disabled, sku, или id */
			case "disabled":	
				// выгружать штрихкод нет нужды
			break; 
			case "sku":
				// выгружать из артикула
				$sku_yml = $offer->get_sku(); // артикул
				if (!empty($sku_yml)) {
					$result_yml .= "<barcode>".$sku_yml."</barcode>".PHP_EOL;
				} else {
					// своего артикула у вариации нет. Пробуем подставить общий sku
					$sku_yml = $product->get_sku();
					if (!empty($sku_yml)) {
						$result_yml .= "<barcode>".$sku_yml."</barcode>".PHP_EOL;
					}
				}
			break;
			case "post_meta":
				$barcode_post_meta_id = yfym_optionGET('yfym_barcode_post_meta', $numFeed);
				$barcode_post_meta_id = trim($barcode_post_meta_id);

				if (get_post_meta($postId, $barcode_post_meta_id, true) !== '') {					
					$barcode_yml = get_post_meta($postId, $barcode_post_meta_id, true);
					$result_yml .= "<barcode>".$barcode_yml."</barcode>".PHP_EOL;
				}
			break;
			case "germanized":
				if (class_exists('WooCommerce_Germanized')) {
					$var_id = $offer->get_id();
					if (get_post_meta($var_id, '_ts_gtin', true) !== '') {
						$yfym_barcode_yml = get_post_meta($var_id, '_ts_gtin', true);
						$result_yml .= "<barcode>".$yfym_barcode_yml."</barcode>".PHP_EOL;
					} else {
						if (get_post_meta($postId, '_ts_gtin', true) !== '') {
							$yfym_barcode_yml = get_post_meta($postId, '_ts_gtin', true);
							$result_yml .= "<barcode>".$yfym_barcode_yml."</barcode>".PHP_EOL;
						}
					}
				}
			break;
			default:
				$result_barcode_yml = '';
				$result_barcode_yml = apply_filters('yfym_switch_variable_barcode_filter', $result_barcode_yml, $yfym_barcode, $product, $offer, $numFeed);
				if ($result_barcode_yml === '') {
					$yfym_barcode = (int)$yfym_barcode;
					$yfym_barcode_yml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_barcode));	
					if (!empty($yfym_barcode_yml)) {				
						$result_yml .= '<barcode>'.yfym_replace_decode($yfym_barcode_yml).'</barcode>'.PHP_EOL;
					} else {
						$yfym_barcode_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_barcode));	
						if (!empty($yfym_barcode_yml)) {
							$result_yml .= '<barcode>'.yfym_replace_decode($yfym_barcode_yml).'</barcode>'.PHP_EOL;
						}					
					}
				} else {
					$result_yml .= $result_barcode_yml;
				}
		}
			 
		$weight_yml = $offer->get_weight(); // вес
		if (!empty($weight_yml)) {
			$weight_yml = round(wc_get_weight($weight_yml, 'kg'), 3);
			$result_yml .= "<weight>".$weight_yml."</weight>".PHP_EOL;
		}
			 
		/* $dimensions = $offer->get_dimensions();
		if (!empty($dimensions)) { */
		$dimensions = wc_format_dimensions($offer->get_dimensions(false));
        if ($offer->has_dimensions()) {
			$length_yml = $offer->get_length();
			if (!empty($length_yml)) {$length_yml = round(wc_get_dimension($length_yml, 'cm'), 3);}
			   
			$width_yml = $offer->get_width();
			if (!empty($length_yml)) {$width_yml = round(wc_get_dimension($width_yml, 'cm'), 3);}
			  
			$height_yml = $offer->get_height();
			if (!empty($length_yml)) {$height_yml = round(wc_get_dimension($height_yml, 'cm'), 3);}		  
			   
			if (($length_yml > 0) && ($width_yml > 0) && ($height_yml > 0)) {
				$result_yml .= '<dimensions>'.$length_yml.'/'.$width_yml.'/'.$height_yml.'</dimensions>'.PHP_EOL;
			}
		}

		$expiry = yfym_optionGET('yfym_expiry', $numFeed);
		if (!empty($expiry) && $expiry !== 'off') {
			$expiry = (int)$expiry;
			$expiry_yml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($expiry));
			if (!empty($expiry_yml)) {	
				$result_yml .= "<expiry>".ucfirst(yfym_replace_decode($expiry_yml))."</expiry>".PHP_EOL;		
			} else {
				$expiry_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($expiry));
				if (!empty($expiry_yml)) {	
					$result_yml .= "<expiry>".ucfirst(yfym_replace_decode($expiry_yml))."</expiry>".PHP_EOL;		
				}		
			}
		}
		$age = yfym_optionGET('yfym_age', $numFeed);
		if (!empty($age) && $age !== 'off') {
		 $age = (int)$age;
		 $age_yml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($age));
		 if (!empty($age_yml)) {	
			$result_yml .= "<age>".ucfirst(yfym_replace_decode($age_yml))."</age>".PHP_EOL;		
		 } else {
			$age_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($age));
			if (!empty($age_yml)) {	
				$result_yml .= "<age>".ucfirst(yfym_replace_decode($age_yml))."</age>".PHP_EOL;		
			}
		 }
		}
		$downloadable = yfym_optionGET('yfym_downloadable', $numFeed);
		if (!empty($downloadable) && $downloadable !== 'off') {
			if ($offer->is_downloadable('yes')) {
				$result_yml .= "<downloadable>true</downloadable>".PHP_EOL;	
			} else {
				$result_yml .= "<downloadable>false</downloadable>".PHP_EOL;							
			}
		}
			 
		$result_yml .= $result_yml_country_of_origin;
		$result_yml .= $result_yml_manufacturer;
		$result_yml .= $result_market_sku_yml; 
		$result_yml .= $result_tn_ved_code_yml;	
				
		$result_yml .= $result_availability_yml;
		$result_yml .= $result_transport_unit_yml;
		$result_yml .= $result_min_delivery_pieces_yml;
		$result_yml .= $result_quantum_yml;
		$result_yml .= $result_leadtime_yml;
		$result_yml .= $result_box_count_yml;
		$result_yml .= $yfym_delivery_weekday_yml;

		// гарантия
		$manufacturer_warranty = yfym_optionGET('yfym_manufacturer_warranty', $numFeed);
		if (!empty($manufacturer_warranty) && $manufacturer_warranty !== 'off') {			
			if ($manufacturer_warranty === 'alltrue') {
				$result_yml .= "<manufacturer_warranty>true</manufacturer_warranty>".PHP_EOL;
			} else if ($manufacturer_warranty === 'allfalse') {
				$result_yml .= "<manufacturer_warranty>false</manufacturer_warranty>".PHP_EOL;
			} else {
			 $manufacturer_warranty = (int)$manufacturer_warranty;
			 $manufacturer_warranty_yml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($manufacturer_warranty));
			 if (!empty($manufacturer_warranty_yml)) {	
				$result_yml .= "<manufacturer_warranty>".yfym_replace_decode($manufacturer_warranty_yml)."</manufacturer_warranty>".PHP_EOL;
			 } else {$manufacturer_warranty_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($manufacturer_warranty));
				if (!empty($manufacturer_warranty_yml)) {	
					$result_yml .= "<manufacturer_warranty>".yfym_replace_decode($manufacturer_warranty_yml)."</manufacturer_warranty>".PHP_EOL;
				}
			 }
			}			
		}

		$result_yml .= $result_yml_vendor;
		$result_yml .= $result_yml_shop_sku;
			 
		// вариция. если offer_type пуст, то можно выгружать vendorCode
		// if ($offer_type =='') { 
		$yfym_vendorcode = yfym_optionGET('yfym_vendorcode', $numFeed);
		switch ($yfym_vendorcode) { /* disabled, sku, или id */
		case "disabled":	
			// выгружать штрихкод нет нужды
		break; 
		case "sku":
			// выгружать из артикула
			$sku_yml = $offer->get_sku(); // артикул
			if (!empty($sku_yml)) {
				$sku_yml = yfym_replace_symbol($sku_yml, $numFeed);
				$result_yml .= "<vendorCode>".$sku_yml."</vendorCode>".PHP_EOL;
			} else {
				// своего артикула у вариации нет. Пробуем подставить общий sku
				$sku_yml = $product->get_sku();
				if (!empty($sku_yml)) {
					$sku_yml = yfym_replace_symbol($sku_yml, $numFeed);
					$result_yml .= "<vendorCode>".$sku_yml."</vendorCode>".PHP_EOL;
				}
			}
		break;
		default:
			$yfym_vendorcode = (int)$yfym_vendorcode;
			$yfym_vendorcode_yml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_vendorcode));
			$yfym_vendorcode_yml = apply_filters('yfym_variable_vendorcode_filter', $yfym_vendorcode_yml, $product, $offer, $numFeed); /* c версии 3.4.2 */
			if (!empty($yfym_vendorcode_yml)) {
				$yfym_vendorcode_yml = yfym_replace_symbol($yfym_vendorcode_yml, $numFeed);
				$result_yml .= '<vendorCode>'.yfym_replace_decode($yfym_vendorcode_yml).'</vendorCode>'.PHP_EOL;
			} else {
				$yfym_vendorcode_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_vendorcode));
				if (!empty($yfym_vendorcode_yml)) {
					$yfym_vendorcode_yml = yfym_replace_symbol($yfym_vendorcode_yml, $numFeed);
					$result_yml .= '<vendorCode>'.yfym_replace_decode($yfym_vendorcode_yml).'</vendorCode>'.PHP_EOL;
					}
				}
			}
		
		// }
		/* c версии 2.0.8 */
		$result_yml_pickup = apply_filters('yfym_pickup_filter', $result_yml_pickup, $catpostid, $product, $pickup, $numFeed);
			 
		$result_yml .= $result_yml_store;
		$result_yml .= $result_yml_pickup;
		$result_yml .= $result_yml_delivery;
		$result_yml .= $result_yml_cat; // Категории
		$result_yml .= $result_yml_vat;		 

		do_action('yfym_append_variable_offer');
		$result_yml = apply_filters('yfym_append_variable_offer_filter', $result_yml, $product, $offer, $numFeed);	 

		if ((get_post_meta($postId, 'yfym_cost', true) !== '') && (get_post_meta($postId, 'yfym_days', true) !== '')) {
			$yfym_cost = get_post_meta($postId, 'yfym_cost', true);
			$yfym_days = get_post_meta($postId, 'yfym_days', true);	
			if (get_post_meta($postId, 'yfym_order_before', true) !== '') {
				$yfym_order_before = get_post_meta($postId, 'yfym_order_before', true);
				$yfym_order_before_yml = ' order-before="'.$yfym_order_before.'"';
			} else {
				$yfym_order_before_yml = '';
			}	
			$result_yml .= '<delivery-options>'.PHP_EOL;
			$result_yml .= '<option cost="'.$yfym_cost.'" days="'.$yfym_days.'"'.$yfym_order_before_yml.'/>'.PHP_EOL;
			$result_yml .= '</delivery-options>	'.PHP_EOL;
		}
		
		if ((get_post_meta($postId, 'yfym_pickup_cost', true) !== '') && (get_post_meta($postId, 'yfym_pickup_days', true) !== '')) {
			$yfym_pickup_cost = get_post_meta($postId, 'yfym_pickup_cost', true);
			$yfym_pickup_days = get_post_meta($postId, 'yfym_pickup_days', true);	
			if (get_post_meta($postId, 'yfym_pickup_order_before', true) !== '') {
				$yfym_pickup_order_before = get_post_meta($postId, 'yfym_pickup_order_before', true);
				$yfym_pickup_order_before_yml = ' order-before="'.$yfym_pickup_order_before.'"';
			} else {
				$yfym_pickup_order_before_yml = '';
			}	
			$result_yml .= '<pickup-options>'.PHP_EOL;
			$result_yml .= '<option cost="'.$yfym_pickup_cost.'" days="'.$yfym_pickup_days.'"'.$yfym_pickup_order_before_yml.'/>'.PHP_EOL;
			$result_yml .= '</pickup-options>	'.PHP_EOL;
		}
		
		if ((get_post_meta($postId, 'yfym_condition', true) !== '') && (get_post_meta($postId, 'yfym_condition', true) !== 'off') && (get_post_meta($postId, 'yfym_reason', true) !== '')) {
			$yfym_condition = get_post_meta($postId, 'yfym_condition', true);
			$yfym_reason = get_post_meta($postId, 'yfym_reason', true);	
			$result_yml .= '<condition type="'.$yfym_condition.'">'.PHP_EOL;
				$result_yml .= '<reason>'.$yfym_reason.'</reason>'.PHP_EOL;
			$result_yml .= '</condition>'.PHP_EOL;	
		}	

		if ((get_post_meta($postId, 'yfym_credit_template', true) !== '') && (get_post_meta($postId, 'yfym_credit_template', true) !== '')) {
			$yfym_credit_template = get_post_meta($postId, 'yfym_credit_template', true);
			$result_yml .= '<credit-template id="'.$yfym_credit_template.'"/>'.PHP_EOL;
		}
		if ((get_post_meta($postId, '_yfym_supplier', true) !== '') && (get_post_meta($postId, '_yfym_supplier', true) !== '')) {
			$yfym_supplier = get_post_meta($postId, '_yfym_supplier', true);
			$result_yml .= '<supplier ogrn="'.$yfym_supplier.'"/>'.PHP_EOL;
		}	
		if ((get_post_meta($postId, '_yfym_min_quantity', true) !== '') && (get_post_meta($postId, '_yfym_min_quantity', true) !== '')) {
			$yfym_min_quantity = get_post_meta($postId, '_yfym_min_quantity', true);
			$result_yml .= '<min-quantity>'.$yfym_min_quantity.'</min-quantity>'.PHP_EOL;
		}
		if ((get_post_meta($postId, '_yfym_step_quantity', true) !== '') && (get_post_meta($postId, '_yfym_step_quantity', true) !== '')) {
			$yfym_step_quantity = get_post_meta($postId, '_yfym_step_quantity', true);
			$result_yml .= '<step-quantity>'.$yfym_step_quantity.'</step-quantity>'.PHP_EOL;
		}	
		$result_yml .= '</offer>'.PHP_EOL;

		do_action('yfym_after_variable_offer');

		$ids_in_yml .= $postId.';'.$offer_id.';'.$price_yml.';'.$CurCategoryId.PHP_EOL; /* с версии 3.1.0 */

		/* с версии 2.3.0 */
		$stop_flag = false;
		$stop_flag = apply_filters('yfym_after_variable_offer_stop_flag', $stop_flag, $i, $variation_count, $offer_id, $offer, $numFeed);
		if ($stop_flag == true) {break;}
	} // end for ($i = 0; $i<$variation_count; $i++) 
	yfym_error_log('FEED № '.$numFeed.'; Все вариации выгрузили. '.$ids_in_yml.' Файл: single_catalog.php; Строка: '.__LINE__, 0);	
	
	return array($result_yml, $ids_in_yml); // все вариации выгрузили	
 } // end if ($product->is_type('variable'))	 
 /* end Вариации */

 yfym_error_log('FEED № '.$numFeed.'; У нас обычный товар. Файл: single_catalog.php; Строка: '.__LINE__, 0);
 // если цена не указана - пропускаем товар
 $price_yml = $product->get_price();
 $price_yml = apply_filters('yfym_simple_price_filter', $price_yml, $product, $numFeed); /* с версии 3.0.0 */ 
 if ($price_yml == 0 || empty($price_yml)) {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к нет цены; Файл: single_catalog.php; Строка: '.__LINE__, 0); return $result_yml;}
 if (class_exists('YmlforYandexMarketPro')) {
	if ((yfym_optionGET('yfymp_compare_value', $numFeed) !== false) && (yfym_optionGET('yfymp_compare_value', $numFeed) !== '')) {
		$yfymp_compare_value = yfym_optionGET('yfymp_compare_value', $numFeed);
		$yfymp_compare = yfym_optionGET('yfymp_compare', $numFeed);			 
		if ($yfymp_compare == '>=') {
			if ($price_yml < $yfymp_compare_value) {return $result_yml;}
		} else {
			if ($price_yml >= $yfymp_compare_value) {return $result_yml;}
		}
	}
 }
 // пропуск товаров, которых нет в наличии
 $yfym_skip_missing_products = yfym_optionGET('yfym_skip_missing_products', $numFeed);
 yfym_error_log('FEED № '.$numFeed.'; $yfym_skip_missing_products = '.$yfym_skip_missing_products.'; gettype = '.gettype($yfym_skip_missing_products).'; Файл: single_catalog.php; Строка: '.__LINE__, 0);
 if ($yfym_skip_missing_products === 'on') {
	if ($product->is_in_stock() == false) {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к нет в наличии; Файл: single_catalog.php; Строка: '.__LINE__, 0); return $result_yml;}
 }		  

 // пропускаем товары на предзаказ
 $skip_backorders_products = yfym_optionGET('yfym_skip_backorders_products', $numFeed);
 if ($skip_backorders_products === 'on') {
	if ($product->get_manage_stock() == true) { // включено управление запасом  
		if (($product->get_stock_quantity() < 1) && ($product->get_backorders() !== 'no')) {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к запрещен предзаказ и включено управление запасом; Файл: single_catalog.php; Строка: '.__LINE__, 0); return $result_yml; /*continue;*/}
	} else {
		if ($product->get_stock_status() !== 'instock') {yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к запрещен предзаказ; Файл: single_catalog.php; Строка: '.__LINE__, 0); return $result_yml; /*continue;*/}
	}
 }  

 // убираем default.png из фида
 $no_default_png_products = yfym_optionGET('yfym_no_default_png_products', $numFeed);
 if (($no_default_png_products === 'on') && (!has_post_thumbnail($postId))) {$picture_yml = '';} else {
	$thumb_id = get_post_thumbnail_id($postId);
	$thumb_url = wp_get_attachment_image_src($thumb_id, 'full', true);	
	$thumb_yml = $thumb_url[0]; /* урл оригинал миниатюры товара */
	$picture_yml = '<picture>'.deleteGET($thumb_yml).'</picture>'.PHP_EOL;
 }
 $picture_yml = apply_filters('yfym_pic_simple_offer_filter', $picture_yml, $product, $numFeed);

 // пропускаем товары без картинок
 $yfym_skip_products_without_pic = yfym_optionGET('yfym_skip_products_without_pic', $numFeed); 
 if (($yfym_skip_products_without_pic === 'on') && ($picture_yml == '')) {	  
	yfym_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к нет картинки даже в галерее; Файл: single_catalog.php; Строка: '.__LINE__, 0); return $result_yml; /*continue;*/  
 }
	
 // страна производитель
 $result_yml_country_of_origin = '';
 $country_of_origin = yfym_optionGET('yfym_country_of_origin', $numFeed);
 if (!empty($country_of_origin) && $country_of_origin !== 'off') {
	$country_of_origin = (int)$country_of_origin;
	$country_of_origin_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($country_of_origin));
	if (!empty($country_of_origin_yml)) {	
		$result_yml_country_of_origin = "<country_of_origin>".ucfirst(yfym_replace_decode($country_of_origin_yml))."</country_of_origin>".PHP_EOL;		
	}				
 }
 if ($result_yml_country_of_origin === '') {
	yfym_error_log('FEED № '.$numFeed.'; Пропускаем товар с postId = '.$postId.' т.к. нет country_of_origin. Файл: single_catalog.php; Строка: '.__LINE__, 0);	
	return $result_yml;			
 } 

 $result_yml_manufacturer = '';
 $yfym_manufacturer = yfym_optionGET('yfym_manufacturer', $numFeed);
 if (!empty($yfym_manufacturer) && $yfym_manufacturer !== 'disabled') {
	$yfym_manufacturer = (int)$yfym_manufacturer;
	$yfym_manufacturer_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_manufacturer));
	if (!empty($yfym_manufacturer_yml)) {	
		$result_yml_manufacturer = "<manufacturer>".ucfirst(yfym_replace_decode($yfym_manufacturer_yml))."</manufacturer>".PHP_EOL;		
	} 
 }  
 if ($result_yml_manufacturer === '') {
	yfym_error_log('FEED № '.$numFeed.'; Пропускаем товар с postId = '.$postId.' т.к. нет manufacturer. Файл: single_catalog.php; Строка: '.__LINE__, 0);	
	return $result_yml;			
 } 

 $result_yml_vendor = '';
 $vendor = yfym_optionGET('yfym_vendor', $numFeed);
 if ((is_plugin_active('perfect-woocommerce-brands/perfect-woocommerce-brands.php') || is_plugin_active('perfect-woocommerce-brands/main.php') || class_exists('Perfect_Woocommerce_Brands')) && $vendor === 'sfpwb') {
	$barnd_terms = get_the_terms($product->get_id(), 'pwb-brand');
	if ($barnd_terms !== false) {
	 foreach($barnd_terms as $barnd_term) {
		$result_yml_vendor = '<vendor>'. $barnd_term->name .'</vendor>'.PHP_EOL;
		break;
	 }
	}
 } else if ((is_plugin_active('premmerce-woocommerce-brands/premmerce-brands.php')) && ($vendor === 'premmercebrandsplugin')) {
	$barnd_terms = get_the_terms($product->get_id(), 'product_brand');
	if ($barnd_terms !== false) {
	 foreach($barnd_terms as $barnd_term) {
		$result_yml_vendor = '<vendor>'. $barnd_term->name .'</vendor>'.PHP_EOL;
		break;
	 }
	}
 } else if (class_exists('woo_brands') && $vendor === 'woo_brands') {
	$barnd_terms = get_the_terms($product->get_id(), 'product_brand');
	if ($barnd_terms !== false) {
	 foreach($barnd_terms as $barnd_term) {
		$result_yml_vendor = '<vendor>'. $barnd_term->name .'</vendor>'.PHP_EOL;
		break;
	 }
	}
 } else { 
	if ($vendor !== 'disabled') {
		$vendor = (int)$vendor;
		$vendor_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($vendor));
		if (!empty($vendor_yml)) {
			$result_yml_vendor = '<vendor>'.$vendor_yml.'</vendor>'.PHP_EOL;
		}
	}	
 }
 if ($result_yml_vendor === '') {
	yfym_error_log('FEED № '.$numFeed.'; Пропускаем товар с postId = '.$postId.' т.к. нет vendor. Файл: single_catalog.php; Строка: '.__LINE__, 0);	
	return $result_yml;			
 }

 $result_yml_shop_sku = '';
 $yfym_shop_sku = yfym_optionGET('yfym_shop_sku', $numFeed);
 switch ($yfym_shop_sku) { /* disabled, sku, products_id, или id */
	case "disabled":	
	   // выгружать штрихкод нет нужды
	break; 
	case "sku":
		// выгружать из артикула
		$sku_yml = $product->get_sku();
		if (!empty($sku_yml)) {
			$sku_yml = yfym_replace_symbol($sku_yml, $numFeed);
			$result_yml_shop_sku = "<shop-sku>".$sku_yml."</shop-sku>".PHP_EOL;
		}
	break;
	case "products_id":
		// выгружать из id
		$result_yml_shop_sku = "<shop-sku>".$product->get_id()."</shop-sku>".PHP_EOL;
	break;
	default:
		if ($yfym_shop_sku !== 'disabled') {
			$yfym_shop_sku = (int)$yfym_shop_sku;
			$yfym_shop_sku_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_shop_sku));
			if (!empty($yfym_shop_sku_yml)) {	
				$yfym_shop_sku_yml = yfym_replace_symbol($yfym_shop_sku_yml, $numFeed);			
				$result_yml_shop_sku = '<shop-sku>'.$yfym_shop_sku_yml.'</shop-sku>'.PHP_EOL;
			}
		}
 }

 do_action('yfym_before_simple_offer');

 /* Обычный товар */
 if ($product->get_manage_stock() == true) { // включено управление запасом
	if ($product->get_stock_quantity() > 0) {
		$available = 'true';
	} else {
		if ($product->get_backorders() === 'no') { // предзаказ запрещен
			$available = 'false';
		} else {
			$yfym_behavior_onbackorder = yfym_optionGET('yfym_behavior_onbackorder', $numFeed);
			if ($yfym_behavior_onbackorder === 'false') {
				$available = 'false';
			} else {
				$available = 'true';
			}
		}
	}
 } else { // отключено управление запасом
	if ($product->get_stock_status() === 'instock') {
		$available = 'true';
	} else if ($product->get_stock_status() === 'outofstock') { 
		$available = 'false';
	} else {
		$yfym_behavior_onbackorder = yfym_optionGET('yfym_behavior_onbackorder', $numFeed);
		if ($yfym_behavior_onbackorder === 'false') {
			$available = 'false';
		} else {
			$available = 'true';
		}
	}
 }
 $available = apply_filters('yfym_available_filter', $available, $product, $postId, $numFeed); /* С версии 3.5.3 */
		  		  
 $offer_type = '';
 $offer_type = apply_filters('yfym_offer_type_filter', $offer_type, $catpostid, $postId, $product, $numFeed);  /* изменён с версии 3.3.3 */

 /* с версии 2.1.2 */ 
 $append_offer_tag = apply_filters('yfym_append_offer_tag_filter', $append_offer_tag, $product, $numFeed);
 $result_yml .= '<offer '.$offer_type.' id="'.$postId.'" available="'.$available.'" '.$append_offer_tag.'>'.PHP_EOL;
 do_action('yfym_prepend_simple_offer');

 $params_arr = unserialize(yfym_optionGET('yfym_params_arr', $numFeed));		  
 if (!empty($params_arr)) {		
	$attributes = $product->get_attributes();				
	foreach ($attributes as $param) {
		// проверка на вариативность атрибута не нужна
		$param_val = $product->get_attribute(wc_attribute_taxonomy_name_by_id($param->get_id()));		
		// если этот параметр не нужно выгружать - пропускаем
		$variation_id_string = (string)$param->get_id(); // важно, т.к. в настройках id как строки
		if (!in_array($variation_id_string, $params_arr, true)) {continue;}
		$param_name = wc_attribute_label(wc_attribute_taxonomy_name_by_id($param->get_id()));
		// если пустое имя атрибута или значение - пропускаем
		if (empty($param_name) || empty($param_val)) {continue;}
		$result_yml .= '<param name="'.htmlspecialchars($param_name).'">'.ucfirst(yfym_replace_decode($param_val)).'</param>'.PHP_EOL;
	}
 }

 $result_yml_name = apply_filters('yfym_before_insert_name_filter', $result_yml_name, $numFeed); /* с версии 3.3.18 */
 $result_yml .= "<name>".htmlspecialchars($result_yml_name, ENT_NOQUOTES)."</name>".PHP_EOL;
 $yfym_enable_auto_discounts = yfym_optionGET('yfym_enable_auto_discounts', $numFeed);
 if ($yfym_enable_auto_discounts === 'on') {
	$result_yml .= "<enable_auto_discounts>yes</enable_auto_discounts>".PHP_EOL;
 }			
 // описание
 $result_yml .= $result_yml_desc;
 
 $result_yml .= $picture_yml;  
		   
 $result_url = htmlspecialchars(get_permalink($product->get_id())); // урл товара
 $yfym_clear_get = yfym_optionGET('yfym_clear_get', $numFeed);
 if ($yfym_clear_get === 'yes') {$result_url = deleteGET($result_url, 'url');} 
 $result_url = apply_filters('yfym_url_filter', $result_url, $product, $CurCategoryId, $numFeed);
 $result_url = apply_filters('yfym_simple_url_filter', $result_url, $product, $CurCategoryId, $numFeed); /* с версии 3.3.14 */
 /* с версии 2.0.12 в фильтр добавлен параметр $CurCategoryId */
 
 $result_yml .= "<url>".$result_url."</url>".PHP_EOL;

 $price_yml = apply_filters('yfym_simple_price_yml_filter', $price_yml, $product, $numFeed); /* с версии 3.1.0 */ 
 $yfym_price_from = yfym_optionGET('yfym_price_from', $numFeed);
 if ($yfym_price_from === 'yes') {
	$result_yml .= "<price from='true'>".$price_yml."</price>".PHP_EOL;
 } else {
	$result_yml .= "<price>".$price_yml."</price>".PHP_EOL;
 }
 // старая цена
 $yfym_oldprice = yfym_optionGET('yfym_oldprice', $numFeed);
 if ($yfym_oldprice === 'yes') {
	$price_yml = (float)$price_yml;
	$sale_price = (float)$product->get_sale_price();
	$sale_price = apply_filters('yfym_simple_sale_price_filter', $sale_price, $price_yml, $product, $numFeed); /* с версии 3.5.1 */
	if ($sale_price > 0) {
		if ($price_yml === $sale_price) {		
			$oldprice_yml = $product->get_regular_price();
			$oldprice_name_tag = 'oldprice';
			$oldprice_name_tag = apply_filters('yfym_oldprice_name_tag_filter', $oldprice_name_tag, $numFeed); /* с версии 3.2.0 */ 			
			$result_yml .= "<".$oldprice_name_tag.">".$oldprice_yml."</".$oldprice_name_tag.">".PHP_EOL;
		}
	}
 }
		  
 $result_yml .= '<currencyId>'.$currencyId_yml.'</currencyId>'.PHP_EOL;

 if ($product->get_manage_stock() == true) { // включено управление запасом  
	$stock_quantity = $product->get_stock_quantity();
	$yfym_count = yfym_optionGET('yfym_count', $numFeed);
	if ($yfym_count === 'enabled' && $stock_quantity > -1) {
		$result_yml .= '<count>'.$stock_quantity.'</count>'.PHP_EOL;
	}
 }  

 // штрихкод
 $yfym_barcode = yfym_optionGET('yfym_barcode', $numFeed);
 switch ($yfym_barcode) { /* disabled, sku, или id */
	case "disabled":	
		// выгружать штрихкод нет нужды
	break; 
	case "sku":
		// выгружать из артикула
		$sku_yml = $product->get_sku();
		if (!empty($sku_yml)) {
			$result_yml .= "<barcode>".$sku_yml."</barcode>".PHP_EOL;
		}	
	break;
	case "post_meta":
		$barcode_post_meta_id = yfym_optionGET('yfym_barcode_post_meta', $numFeed);
		$barcode_post_meta_id = trim($barcode_post_meta_id);

		if (get_post_meta($postId, $barcode_post_meta_id, true) !== '') {					
			$barcode_yml = get_post_meta($postId, $barcode_post_meta_id, true);
			$result_yml .= "<barcode>".$barcode_yml."</barcode>".PHP_EOL;
		}
	break;
	case "germanized":
		if (class_exists('WooCommerce_Germanized')) {
			if (get_post_meta($postId, '_ts_gtin', true) !== '') {
				$yfym_barcode_yml = get_post_meta($postId, '_ts_gtin', true);
				$result_yml .= "<barcode>".$yfym_barcode_yml."</barcode>".PHP_EOL;
			}
		}
	break;
	default:
		$result_barcode_yml = '';
		$result_barcode_yml = apply_filters('yfym_switch_simple_barcode_filter', $result_barcode_yml, $yfym_barcode, $product, $numFeed);
		if ($result_barcode_yml === '') {
			$yfym_barcode = (int)$yfym_barcode;
			$yfym_barcode_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_barcode));
			if (!empty($yfym_barcode_yml)) {
				$result_yml .= '<barcode>'.yfym_replace_decode($yfym_barcode_yml).'</barcode>'.PHP_EOL;
			}
		} else {
			$result_yml .= $result_barcode_yml;
		}
 }


 $weight_yml = $product->get_weight(); // вес
 if (!empty($weight_yml)) {
	$weight_yml = round(wc_get_weight($weight_yml, 'kg'), 3);
	$result_yml .= "<weight>".$weight_yml."</weight>".PHP_EOL;
 }

 /*$dimensions = $product->get_dimensions();
 if (!empty($dimensions)) {*/
 $dimensions = wc_format_dimensions($product->get_dimensions(false));
 if ($product->has_dimensions()) {
	$length_yml = $product->get_length();
	if (!empty($length_yml)) {$length_yml = round(wc_get_dimension($length_yml, 'cm'), 3);}
	   
	$width_yml = $product->get_width();
	if (!empty($length_yml)) {$width_yml = round(wc_get_dimension($width_yml, 'cm'), 3);}
	   
	$height_yml = $product->get_height();
	if (!empty($length_yml)) {$height_yml = round(wc_get_dimension($height_yml, 'cm'), 3);}		  
		   
	if (($length_yml > 0) && ($width_yml > 0) && ($height_yml > 0)) {
		$result_yml .= '<dimensions>'.$length_yml.'/'.$width_yml.'/'.$height_yml.'</dimensions>'.PHP_EOL;
	}
 }

 $expiry = yfym_optionGET('yfym_expiry', $numFeed);
 if (!empty($expiry) && $expiry !== 'off') {
	$expiry = (int)$expiry;
	$expiry_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($expiry));
	if (!empty($expiry_yml)) {	
		$result_yml .= "<expiry>".ucfirst(yfym_replace_decode($expiry_yml))."</expiry>".PHP_EOL;		
	}
 }
 $age = yfym_optionGET('yfym_age', $numFeed);
 if (!empty($age) && $age !== 'off') {	
	$age = (int)$age;
	$age_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($age));
	if (!empty($age_yml)) {	
		$result_yml .= "<age>".ucfirst(yfym_replace_decode($age_yml))."</age>".PHP_EOL;		
	}
 }
 $downloadable = yfym_optionGET('yfym_downloadable', $numFeed);
 if (!empty($downloadable) && $downloadable !== 'off') {
	if ($product->is_downloadable('yes')) {
		$result_yml .= "<downloadable>true</downloadable>".PHP_EOL;	
	} else {
		$result_yml .= "<downloadable>false</downloadable>".PHP_EOL;							
	}
 }
		  
 $sales_notes_cat = yfym_optionGET('yfym_sales_notes_cat', $numFeed);
 if (!empty($sales_notes_cat) && $sales_notes_cat !== 'off') {
	$sales_notes_cat = (int)$sales_notes_cat;
	$sales_notes_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($sales_notes_cat));
	if (!empty($sales_notes_yml)) {	
		$result_yml .= "<sales_notes>".ucfirst(yfym_replace_decode($sales_notes_yml))."</sales_notes>".PHP_EOL;		
	} else {
		$sales_notes = yfym_optionGET('yfym_sales_notes', $numFeed);
		if (!empty($sales_notes)) {
			$result_yml .= "<sales_notes>$sales_notes</sales_notes>".PHP_EOL;
		}
	}
 }

 $result_yml .= $result_yml_country_of_origin;
 $result_yml .= $result_yml_manufacturer;
 $result_yml .= $result_market_sku_yml; 
 $result_yml .= $result_tn_ved_code_yml;

 $result_yml .= $result_availability_yml;
 $result_yml .= $result_transport_unit_yml;
 $result_yml .= $result_min_delivery_pieces_yml;
 $result_yml .= $result_quantum_yml;
 $result_yml .= $result_leadtime_yml;
 $result_yml .= $result_box_count_yml;
 $result_yml .= $yfym_delivery_weekday_yml;

 // гарантия
 $manufacturer_warranty = yfym_optionGET('yfym_manufacturer_warranty', $numFeed);
 if (!empty($manufacturer_warranty) && $manufacturer_warranty !== 'off') {	
	if ($manufacturer_warranty === 'alltrue') {
		$result_yml .= "<manufacturer_warranty>true</manufacturer_warranty>".PHP_EOL;
	} else if ($manufacturer_warranty === 'allfalse') {
		$result_yml .= "<manufacturer_warranty>false</manufacturer_warranty>".PHP_EOL;
	} else {
	 $manufacturer_warranty = (int)$manufacturer_warranty;		
	 $manufacturer_warranty_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($manufacturer_warranty));
	 if (!empty($manufacturer_warranty_yml)) {	
		$result_yml .= "<manufacturer_warranty>".yfym_replace_decode($manufacturer_warranty_yml)."</manufacturer_warranty>".PHP_EOL;
	 }
	}
 }

 $result_yml .= $result_yml_vendor;
 $result_yml .= $result_yml_shop_sku;

 // если offer_type пуст, то можно выгружать vendorCode
 if ($offer_type == '') {
	$yfym_vendorcode = yfym_optionGET('yfym_vendorcode', $numFeed);
	switch ($yfym_vendorcode) { /* disabled, sku, или id */
		case "disabled":	
			// выгружать штрихкод нет нужды
		break; 
		case "sku":
		// выгружать из артикула
		$sku_yml = $product->get_sku();
		if (!empty($sku_yml)) {
			$sku_yml = yfym_replace_symbol($sku_yml, $numFeed);
			$result_yml .= "<vendorCode>".$sku_yml."</vendorCode>".PHP_EOL;
		}			
		break;
		default:
			$yfym_vendorcode = (int)$yfym_vendorcode;
			$yfym_vendorcode_yml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($yfym_vendorcode));	
			$yfym_vendorcode_yml = apply_filters('yfym_simple_vendorcode_filter', $yfym_vendorcode_yml, $product, $numFeed); /* c версии 3.4.2 */			
			if (!empty($yfym_vendorcode_yml)) {
				$yfym_vendorcode_yml = yfym_replace_symbol($yfym_vendorcode_yml, $numFeed);
				$result_yml .= '<vendorCode>'.yfym_replace_decode($yfym_vendorcode_yml).'</vendorCode>'.PHP_EOL;
			}
	}
 }
		  
 // do_action('yfym_after_sku_simple_offer');
 /* c версии 2.0.8 */
 $result_yml_pickup = apply_filters('yfym_pickup_filter', $result_yml_pickup, $catpostid, $product, $pickup, $numFeed);
 		  
 $result_yml .= $result_yml_store;
 $result_yml .= $result_yml_pickup;
 $result_yml .= $result_yml_delivery;
 $result_yml .= $result_yml_cat; // Категории
 $result_yml .= $result_yml_vat;
		  
 do_action('yfym_append_simple_offer'); 
 $result_yml = apply_filters('yfym_append_simple_offer_filter', $result_yml, $product, $numFeed);

 if ((get_post_meta($postId, 'yfym_cost', true) !== '') && (get_post_meta($postId, 'yfym_days', true) !== '')) {
	$yfym_cost = get_post_meta($postId, 'yfym_cost', true);
	$yfym_days = get_post_meta($postId, 'yfym_days', true);	
	if (get_post_meta($postId, 'yfym_order_before', true) !== '') {
		$yfym_order_before = get_post_meta($postId, 'yfym_order_before', true);
		$yfym_order_before_yml = ' order-before="'.$yfym_order_before.'"';
	} else {
		$yfym_order_before_yml = '';
	}	
	$result_yml .= '<delivery-options>'.PHP_EOL;
	$result_yml .= '<option cost="'.$yfym_cost.'" days="'.$yfym_days.'"'.$yfym_order_before_yml.'/>'.PHP_EOL;
	$result_yml .= '</delivery-options>	'.PHP_EOL;
 }
 
 if ((get_post_meta($postId, 'yfym_pickup_cost', true) !== '') && (get_post_meta($postId, 'yfym_pickup_days', true) !== '')) {
	$yfym_pickup_cost = get_post_meta($postId, 'yfym_pickup_cost', true);
	$yfym_pickup_days = get_post_meta($postId, 'yfym_pickup_days', true);	
	if (get_post_meta($postId, 'yfym_pickup_order_before', true) !== '') {
		$yfym_pickup_order_before = get_post_meta($postId, 'yfym_pickup_order_before', true);
		$yfym_pickup_order_before_yml = ' order-before="'.$yfym_pickup_order_before.'"';
	} else {
		$yfym_pickup_order_before_yml = '';
	}	
	$result_yml .= '<pickup-options>'.PHP_EOL;
	$result_yml .= '<option cost="'.$yfym_pickup_cost.'" days="'.$yfym_pickup_days.'"'.$yfym_pickup_order_before_yml.'/>'.PHP_EOL;
	$result_yml .= '</pickup-options>	'.PHP_EOL;
 }
 
 if ((get_post_meta($postId, 'yfym_condition', true) !== '') && (get_post_meta($postId, 'yfym_condition', true) !== 'off') && (get_post_meta($postId, 'yfym_reason', true) !== '')) {
	$yfym_condition = get_post_meta($postId, 'yfym_condition', true);
	$yfym_reason = get_post_meta($postId, 'yfym_reason', true);	
	$result_yml .= '<condition type="'.$yfym_condition.'">'.PHP_EOL;
		$result_yml .= '<reason>'.$yfym_reason.'</reason>'.PHP_EOL;
	$result_yml .= '</condition>'.PHP_EOL;	
 } 
 
 if ((get_post_meta($postId, 'yfym_credit_template', true) !== '') && (get_post_meta($postId, 'yfym_credit_template', true) !== '')) {
	$yfym_credit_template = get_post_meta($postId, 'yfym_credit_template', true);
	$result_yml .= '<credit-template id="'.$yfym_credit_template.'"/>'.PHP_EOL;
 } 
 if ((get_post_meta($postId, '_yfym_supplier', true) !== '') && (get_post_meta($postId, '_yfym_supplier', true) !== '')) {
	$yfym_supplier = get_post_meta($postId, '_yfym_supplier', true);
	$result_yml .= '<supplier ogrn="'.$yfym_supplier.'"/>'.PHP_EOL;
 } 
 if ((get_post_meta($postId, '_yfym_min_quantity', true) !== '') && (get_post_meta($postId, '_yfym_min_quantity', true) !== '')) {
	$yfym_min_quantity = get_post_meta($postId, '_yfym_min_quantity', true);
	$result_yml .= '<min-quantity>'.$yfym_min_quantity.'</min-quantity>'.PHP_EOL;
 }
 if ((get_post_meta($postId, '_yfym_step_quantity', true) !== '') && (get_post_meta($postId, '_yfym_step_quantity', true) !== '')) {
	$yfym_step_quantity = get_post_meta($postId, '_yfym_step_quantity', true);
	$result_yml .= '<step-quantity>'.$yfym_step_quantity.'</step-quantity>'.PHP_EOL;
 }
 $result_yml .= '</offer>'.PHP_EOL;
		  
 do_action('yfym_after_simple_offer');

 $ids_in_yml .= $postId.';'.$postId.';'.$price_yml.';'.$CurCategoryId.PHP_EOL;
 
 return array($result_yml, $ids_in_yml);
} // end function yfym_unit($postId) {
?>