<?php if (!defined('ABSPATH')) {exit;}
/*
* С версии 1.0.0
* Добавлен параметр $n
* Записывает или обновляет файл фида.
* Возвращает всегда true
*/
function xfavi_write_file($result_xml, $cc, $numFeed = '1') {
 /* $cc = 'w+' или 'a'; */	 
 xfavi_error_log('FEED № '.$numFeed.'; Стартовала xfavi_write_file c параметром cc = '.$cc.'; Файл: functions.php; Строка: '.__LINE__, 0);
 $filename = urldecode(xfavi_optionGET('xfavi_file_file', $numFeed));
 if ($numFeed === '1') {$prefFeed = '';} else {$prefFeed = $numFeed;}

 if ($filename == '') {	
	$upload_dir = (object)wp_get_upload_dir(); // $upload_dir->basedir
	$filename = $upload_dir->basedir."/xml-for-avito/".$prefFeed."feed-avito-0-tmp.xml"; // $upload_dir->path
 }
		
 // if ((validate_file($filename) === 0)&&(file_exists($filename))) {
 if (file_exists($filename)) {
	// файл есть
	if (!$handle = fopen($filename, $cc)) {
		xfavi_error_log('FEED № '.$numFeed.'; Не могу открыть файл '.$filename.'; Файл: functions.php; Строка: '.__LINE__, 0);
		xfavi_errors_log('FEED № '.$numFeed.'; Не могу открыть файл '.$filename.'; Файл: functions.php; Строка: '.__LINE__, 0);
	}
	if (fwrite($handle, $result_xml) === FALSE) {
		xfavi_error_log('FEED № '.$numFeed.'; Не могу произвести запись в файл '.$handle.'; Файл: functions.php; Строка: '.__LINE__, 0);
		xfavi_errors_log('FEED № '.$numFeed.'; Не могу произвести запись в файл '.$handle.'; Файл: functions.php; Строка: '.__LINE__, 0);
	} else {
		xfavi_error_log('FEED № '.$numFeed.'; Ура! Записали; Файл: Файл: functions.php; Строка: '.__LINE__, 0);
		xfavi_error_log($filename, 0);
		return true;
	}
	fclose($handle);
 } else {
	xfavi_error_log('FEED № '.$numFeed.'; Файла $filename = '.$filename.' еще нет. Файл: functions.php; Строка: '.__LINE__, 0);
	// файла еще нет
	// попытаемся создать файл
	if (is_multisite()) {
		$upload = wp_upload_bits($prefFeed.'feed-avito-'.get_current_blog_id().'-tmp.xml', null, $result_xml ); // загружаем shop2_295221-xml в папку загрузок
	} else {
		$upload = wp_upload_bits($prefFeed.'feed-avito-0-tmp.xml', null, $result_xml ); // загружаем shop2_295221-xml в папку загрузок
	}
	/*
	*	для работы с csv или xml требуется в плагине разрешить загрузку таких файлов
	*	$upload['file'] => '/var/www/wordpress/wp-content/uploads/2010/03/feed-xml.xml', // путь
	*	$upload['url'] => 'http://site.ru/wp-content/uploads/2010/03/feed-xml.xml', // урл
	*	$upload['error'] => false, // сюда записывается сообщение об ошибке в случае ошибки
	*/
	// проверим получилась ли запись
	if ($upload['error']) {
		xfavi_error_log('FEED № '.$numFeed.'; Запись вызвала ошибку: '. $upload['error'].'; Файл: functions.php; Строка: '.__LINE__, 0);
		$err = 'FEED № '.$numFeed.'; Запись вызвала ошибку: '. $upload['error'].'; Файл: functions.php; Строка: '.__LINE__ ;
		xfavi_errors_log($err);
	} else {
		xfavi_optionUPD('xfavi_file_file', urlencode($upload['file']), $numFeed);
		xfavi_error_log('FEED № '.$numFeed.'; Запись удалась! Путь файла: '. $upload['file'] .'; УРЛ файла: '. $upload['url'], 0);
		return true;
	}		
 }
}
/*
* С версии 1.0.0
* Перименовывает временный файл фида в основной.
* Возвращает false/true
*/
function xfavi_rename_file($numFeed = '1') {
 xfavi_error_log('FEED № '.$numFeed.'; Cтартовала xfavi_rename_file; Файл: functions.php; Строка: '.__LINE__, 0);	
 if ($numFeed === '1') {$prefFeed = '';} else {$prefFeed = $numFeed;}	
 /* Перименовывает временный файл в основной. Возвращает true/false */
 if (is_multisite()) {
	$upload_dir = (object)wp_get_upload_dir();
	$filenamenew = $upload_dir->basedir."/xml-for-avito/".$prefFeed."feed-avito-".get_current_blog_id().".xml";
	$filenamenewurl = $upload_dir->baseurl."/xml-for-avito/".$prefFeed."feed-avito-".get_current_blog_id().".xml";		
	// $filenamenew = BLOGUPLOADDIR."feed-avito-".get_current_blog_id().".xml";
	// надо придумать как поулчить урл загрузок конкретного блога
 } else {
	$upload_dir = (object)wp_get_upload_dir();
	/*
	*   'path'    => '/home/site.ru/public_html/wp-content/uploads/2016/04',
	*	'url'     => 'http://site.ru/wp-content/uploads/2016/04',
	*	'subdir'  => '/2016/04',
	*	'basedir' => '/home/site.ru/public_html/wp-content/uploads',
	*	'baseurl' => 'http://site.ru/wp-content/uploads',
	*	'error'   => false,
	*/
	$filenamenew = $upload_dir->basedir."/xml-for-avito/".$prefFeed."feed-avito-0.xml";
	$filenamenewurl = $upload_dir->baseurl."/xml-for-avito/".$prefFeed."feed-avito-0.xml";
 }
 $filenameold = urldecode(xfavi_optionGET('xfavi_file_file', $numFeed));

 xfavi_error_log('FEED № '.$numFeed.'; $filenameold = '.$filenameold.'; Файл: functions.php; Строка: '.__LINE__, 0);
 xfavi_error_log('FEED № '.$numFeed.'; $filenamenew = '.$filenamenew.'; Файл: functions.php; Строка: '.__LINE__, 0);

 if (rename($filenameold, $filenamenew) === FALSE) {
	xfavi_error_log('FEED № '.$numFeed.'; Не могу переименовать файл из '.$filenameold.' в '.$filenamenew.'! Файл: functions.php; Строка: '.__LINE__, 0);
	return false;
 } else {
	xfavi_optionUPD('xfavi_file_url', urlencode($filenamenewurl), $numFeed);
	xfavi_error_log('FEED № '.$numFeed.'; Файл переименован! Файл: functions.php; Строка: '.__LINE__, 0);
	return true;
 }
}
/*
* С версии 1.0.0
* Возвращает URL без get-параметров или возвращаем только get-параметры
*/	
function xfavi_deleteGET($url, $whot = 'url') {
 $url = str_replace("&amp;", "&", $url); // Заменяем сущности на амперсанд, если требуется
 list($url_part, $get_part) = array_pad(explode("?", $url), 2, ""); // Разбиваем URL на 2 части: до знака ? и после
 if ($whot == 'url') {
	return $url_part; // Возвращаем URL без get-параметров (до знака вопроса)
 } else if ($whot == 'get') {
	return $get_part; // Возвращаем get-параметры (без знака вопроса)
 } else {
	return false;
 }
}
/*
* С версии 1.0.0
* Записывает текст ошибки, чтобы потом можно было отправить в отчет
*/
function xfavi_errors_log($message) {
 if (is_multisite()) {
	update_blog_option(get_current_blog_id(), 'xfavi_errors', $message);
 } else {
	update_option('xfavi_errors', $message);
 }
}
/*
* С версии 1.0.0
* Возвращает версию Woocommerce (string) или (null)
*/ 
function xfavi_get_woo_version_number() {
 // If get_plugins() isn't available, require it
 if (!function_exists('get_plugins')) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php');
 }
 // Create the plugins folder and file variables
 $plugin_folder = get_plugins('/' . 'woocommerce');
 $plugin_file = 'woocommerce.php';
	
 // If the plugin version number is set, return it 
 if (isset( $plugin_folder[$plugin_file]['Version'] ) ) {
	return $plugin_folder[$plugin_file]['Version'];
 } else {	
	return NULL;
 }
}
/*
* С версии 1.0.0
* Возвращает дерево таксономий, обернутое в <option></option>
*/
function xfavi_cat_tree($TermName='', $termID, $value_arr, $separator='', $parent_shown=true) {
 /* 
 * $value_arr - массив id отмеченных ранее select-ов
 */
 $result = '';
 $args = 'hierarchical=1&taxonomy='.$TermName.'&hide_empty=0&orderby=id&parent=';
 if ($parent_shown) {
	$term = get_term($termID , $TermName); 
	$selected = '';
	if (!empty($value_arr)) {
	 foreach ($value_arr as $value) {		
	  if ($value == $term->term_id) {
		$selected = 'selected'; break;
	  }
	 }
	}
	// $result = $separator.$term->name.'('.$term->term_id.')<br/>';
	$result = '<option title="'.$term->name.'; ID: '.$term->term_id.'; '. __('товаров', 'xfavi'). ': '.$term->count.'" class="hover" value="'.$term->term_id.'" '.$selected .'>'.$separator.$term->name.'</option>';
	$parent_shown = false;
 }
 $separator .= '-';  
 $terms = get_terms($TermName, $args . $termID);
 if (count($terms) > 0) {
	foreach ($terms as $term) {
	 $selected = '';
	 if (!empty($value_arr)) {
	  foreach ($value_arr as $value) {
	   if ($value == $term->term_id) {
		$selected = 'selected'; break;
	   }
	  }
	 }
	 $result .= '<option title="'.$term->name.'; ID: '.$term->term_id.'; '. __('товаров', 'xfavi'). ': '.$term->count.'" class="hover" value="'.$term->term_id.'" '.$selected .'>'.$separator.$term->name.'</option>';
	 // $result .=  $separator.$term->name.'('.$term->term_id.')<br/>';
	 $result .= xfavi_cat_tree($TermName, $term->term_id, $value_arr, $separator, $parent_shown);
	}
 }
 return $result; 
}
/*
* @since 1.0.0
*
* @param string $optName (require)
* @param string $value (require)
* @param string $n (not require)
* @param string $autoload (not require) (@since v1.3.7)
*
* @return true/false
* Возвращает то, что может быть результатом add_blog_option, add_option
*/
function xfavi_optionADD($optName, $value='', $n='', $autoload = 'yes') {
	if ($optName == '') {return false;}
	if ($n === '1') {$n = '';}
		$optName = $optName.$n;
	if (is_multisite()) { 
		return add_blog_option(get_current_blog_id(), $optName, $value);
	} else {
		return add_option($optName, $value, '', $autoload);
	}
}
/*
* @since 1.0.0
*
* @param string $optName (require)
* @param string $value (require)
* @param string $n (not require)
* @param string $autoload (not require) (@since v1.3.7)
*
* @return true/false
* Возвращает то, что может быть результатом update_blog_option, update_option
*/
function xfavi_optionUPD($optName, $value='', $n='', $autoload = 'yes') {
	if ($optName == '') {return false;}
	if ($n === '1') {$n = '';}
	$optName = $optName.$n;
	if (is_multisite()) { 
	   return update_blog_option(get_current_blog_id(), $optName, $value);
	} else {
	   return update_option($optName, $value, $autoload);
	}
   }
/*
* @since 1.0.0
*
* @param string $optName (require)
* @param string $n (not require)
*
* @return Значение опции или false
* Возвращает то, что может быть результатом get_blog_option, get_option
*/
function xfavi_optionGET($optName, $n='') {
   if ($optName == '') {return false;}
   if ($n === '1') {$n='';}
   $optName = $optName.$n;
   if (is_multisite()) { 
	  return get_blog_option(get_current_blog_id(), $optName);
   } else {
	  return get_option($optName);
   }
}
/*
* @since 1.0.0
*
* @param string $optName (require)
* @param string $n (not require)
*
* @return true/false
* Возвращает то, что может быть результатом delete_blog_option, delete_option
*/
function xfavi_optionDEL($optName, $n='') {
   if ($optName == '') {return false;}
   if ($n === '1') {$n='';}   
   $optName = $optName.$n;
   if (is_multisite()) { 
	  return delete_blog_option(get_current_blog_id(), $optName);
   } else {
	  return delete_option($optName);
   }
} 
/*
* @since 1.0.0
* 
* Создает tmp файл-кэш товара
*/
function xfavi_wf($result_xml, $postId, $numFeed = '1', $ids_in_xml = '') {
	$upload_dir = (object)wp_get_upload_dir();
	$name_dir = $upload_dir->basedir.'/xml-for-avito/feed'.$numFeed;
	if (!is_dir($name_dir)) {
		error_log('WARNING: Папкт $name_dir ='.$name_dir.' нет; Файл: functions.php; Строка: '.__LINE__, 0);
		if (!mkdir($name_dir)) {
			error_log('ERROR: Создать папку $name_dir ='.$name_dir.' не вышло; Файл: functions.php; Строка: '.__LINE__, 0);
		}
	}
	if (is_dir($name_dir)) {
		$filename = $name_dir.'/'.$postId.'.tmp';
		$fp = fopen($filename, "w");
		fwrite($fp, $result_xml); // записываем в файл текст
		fclose($fp); // закрываем
	
		$filename = $name_dir.'/'.$postId.'-in.tmp';
		$fp = fopen($filename, "w");
		fwrite($fp, $ids_in_xml);
		fclose($fp);		
	} else {
		error_log('ERROR: Нет папки xfavi! $name_dir ='.$name_dir.'; Файл: functions.php; Строка: '.__LINE__, 0);
	}
}
/*
* @since 1.0.0
* Функция склейки/сборки
*/
function xfavi_gluing($id_arr, $numFeed = '1') {
 /*	
 * $id_arr[$i]['ID'] - ID товара
 * $id_arr[$i]['post_modified_gmt'] - Время обновления карточки товара
 * global $wpdb;
 * $res = $wpdb->get_results("SELECT ID, post_modified_gmt FROM $wpdb->posts WHERE post_type = 'product' AND post_status = 'publish'");	
 */	
 xfavi_error_log('FEED № '.$numFeed.'; Стартовала xfavi_gluing; Файл: functions.php; Строка: '.__LINE__, 0);
 if ($numFeed === '1') {$prefFeed = '';} else {$prefFeed = $numFeed;} 

 $upload_dir = (object)wp_get_upload_dir();
 $name_dir = $upload_dir->basedir.'/xml-for-avito';
 if (!is_dir($name_dir)) {
  if (!mkdir($name_dir)) {
	 error_log('ERROR: Ошибка создания папки '.$name_dir.'; Файл: xml-for-avito.php; Строка: '.__LINE__, 0);
	 //return false;
  }
 }

 $upload_dir = (object)wp_get_upload_dir();
 $name_dir = $upload_dir->basedir.'/xml-for-avito/feed'.$numFeed;
 if (!is_dir($name_dir)) {
	if (!mkdir($name_dir)) {
		error_log('FEED № '.$numFeed.'; Нет папки xfavi! И создать не вышло! $name_dir ='.$name_dir.'; Файл: functions.php; Строка: '.__LINE__, 0);
	} else {
		error_log('FEED № '.$numFeed.'; Создали папку xfavi! Файл: functions.php; Строка: '.__LINE__, 0);
	}
 }
 
 $xfavi_file_file = urldecode(xfavi_optionGET('xfavi_file_file', $numFeed));
 $xfavi_file_ids_in_xml = urldecode(xfavi_optionGET('xfavi_file_ids_in_xml', $numFeed));

 $xfavi_date_save_set = xfavi_optionGET('xfavi_date_save_set', $numFeed);
 clearstatcache(); // очищаем кэш дат файлов
 // $prod_id
 foreach ($id_arr as $product) {
	$filename = $name_dir.'/'.$product['ID'].'.tmp';
	$filenameIn = $name_dir.'/'.$product['ID'].'-in.tmp';
	xfavi_error_log('FEED № '.$numFeed.'; RAM '.round(memory_get_usage()/1024, 1).' Кб. ID товара/файл = '.$product['ID'].'.tmp; Файл: functions.php; Строка: '.__LINE__, 0);
	if (is_file($filename) && is_file($filenameIn)) { // if (file_exists($filename)) {
		$last_upd_file = filemtime($filename); // 1318189167			
		if (($last_upd_file < strtotime($product['post_modified_gmt'])) || ($xfavi_date_save_set > $last_upd_file)) {
			// Файл кэша обновлен раньше чем время модификации товара
			// или файл обновлен раньше чем время обновления настроек фида
			xfavi_error_log('FEED № '.$numFeed.'; NOTICE: Файл кэша '.$filename.' обновлен РАНЬШЕ чем время модификации товара или время сохранения настроек фида! Файл: functions.php; Строка: '.__LINE__, 0);	
			$result_xml_unit = xfavi_unit($product['ID'], $numFeed);
			if (is_array($result_xml_unit)) {
				$result_xml = $result_xml_unit[0];
				$ids_in_xml = $result_xml_unit[1];
			} else {
				$result_xml = $result_xml_unit;
				$ids_in_xml = '';
			}	
			xfavi_wf($result_xml, $product['ID'], $numFeed, $ids_in_xml);
			file_put_contents($xfavi_file_file, $result_xml, FILE_APPEND);			
			file_put_contents($xfavi_file_ids_in_xml, $ids_in_xml, FILE_APPEND);
		} else {
			// Файл кэша обновлен позже чем время модификации товара
			// или файл обновлен позже чем время обновления настроек фида
			xfavi_error_log('FEED № '.$numFeed.'; NOTICE: Файл кэша '.$filename.' обновлен ПОЗЖЕ чем время модификации товара или время сохранения настроек фида; Файл: functions.php; Строка: '.__LINE__, 0);
			xfavi_error_log('FEED № '.$numFeed.'; Пристыковываем файл кэша без изменений; Файл: functions.php; Строка: '.__LINE__, 0);
			$result_xml = file_get_contents($filename);
			file_put_contents($xfavi_file_file, $result_xml, FILE_APPEND);
			$ids_in_xml = file_get_contents($filenameIn);
			file_put_contents($xfavi_file_ids_in_xml, $ids_in_xml, FILE_APPEND);
		}
	} else { // Файла нет
		xfavi_error_log('FEED № '.$numFeed.'; NOTICE: Файла кэша товара '.$filename.' ещё нет! Создаем... Файл: functions.php; Строка: '.__LINE__, 0);		
		$result_xml_unit = xfavi_unit($product['ID'], $numFeed);
		if (is_array($result_xml_unit)) {
			$result_xml = $result_xml_unit[0];
			$ids_in_xml = $result_xml_unit[1];
		} else {
			$result_xml = $result_xml_unit;
			$ids_in_xml = '';
		}
		xfavi_wf($result_xml, $product['ID'], $numFeed, $ids_in_xml);
		xfavi_error_log('FEED № '.$numFeed.'; Создали! Файл: functions.php; Строка: '.__LINE__, 0);
		file_put_contents($xfavi_file_file, $result_xml, FILE_APPEND);
		file_put_contents($xfavi_file_ids_in_xml, $ids_in_xml, FILE_APPEND);
	}
 }
} // end function xfavi_gluing()
/*
* @since 1.0.0
* Функция склейки
*/
function xfavi_onlygluing($numFeed = '1') {
 xfavi_error_log('FEED № '.$numFeed.'; NOTICE: Стартовала xfavi_onlygluing; Файл: functions.php; Строка: '.__LINE__, 0); 	
 do_action('xfavi_before_construct', 'cache');
 $result_xml = xfavi_feed_header($numFeed);
 /* создаем файл или перезаписываем старый удалив содержимое */
 $result = xfavi_write_file($result_xml, 'w+', $numFeed);
 if ($result !== true) {
	xfavi_error_log('FEED № '.$numFeed.'; xfavi_write_file вернула ошибку! $result ='.$result.'; Файл: functions.php; Строка: '.__LINE__, 0);
 } 
 
 xfavi_optionUPD('xfavi_status_sborki', '-1', $numFeed); 
 $whot_export = xfavi_optionGET('xfavi_whot_export', $numFeed);

 $result_xml = '';
 $step_export = -1;
 $prod_id_arr = array(); 
 
 if ($whot_export === 'xfavi_vygruzhat') {
	$args = array(
		'post_type' => 'product',
		'post_status' => 'publish',
		'posts_per_page' => $step_export, // сколько выводить товаров
		// 'offset' => $offset,
		'relation' => 'AND',
		'fields'  => 'ids',
		'meta_query' => array(
			array(
				'key' => '_xfavi_vygruzhat',
				'value' => 'yes'
			)
		)
	);	
 } else { //  if ($whot_export == 'all' || $whot_export == 'simple')
	$args = array(
		'post_type' => 'product',
		'post_status' => 'publish',
		'posts_per_page' => $step_export, // сколько выводить товаров
		// 'offset' => $offset,
		'relation' => 'AND',
		'fields'  => 'ids'
	);
 }

 $args = apply_filters('xfavi_query_arg_filter', $args, $numFeed);
 xfavi_error_log('FEED № '.$numFeed.'; NOTICE: xfavi_onlygluing до запуска WP_Query RAM '.round(memory_get_usage()/1024, 1) . ' Кб; Файл: functions.php; Строка: '.__LINE__, 0); 
 $featured_query = new WP_Query($args);
 xfavi_error_log('FEED № '.$numFeed.'; NOTICE: xfavi_onlygluing после запуска WP_Query RAM '.round(memory_get_usage()/1024, 1) . ' Кб; Файл: functions.php; Строка: '.__LINE__, 0); 
 
 global $wpdb;
 if ($featured_query->have_posts()) { 
	for ($i = 0; $i < count($featured_query->posts); $i++) {
		/*	
		*	если не юзаем 'fields'  => 'ids'
		*	$prod_id_arr[$i]['ID'] = $featured_query->posts[$i]->ID;
		*	$prod_id_arr[$i]['post_modified_gmt'] = $featured_query->posts[$i]->post_modified_gmt;
		*/
		$curID = $featured_query->posts[$i];
		$prod_id_arr[$i]['ID'] = $curID;

		$res = $wpdb->get_results($wpdb->prepare("SELECT post_modified_gmt FROM $wpdb->posts WHERE id=%d", $curID), ARRAY_A);
		$prod_id_arr[$i]['post_modified_gmt'] = $res[0]['post_modified_gmt']; 	
		// get_post_modified_time('Y-m-j H:i:s', true, $featured_query->posts[$i]);
	}
	wp_reset_query(); /* Remember to reset */
	unset($featured_query); // чутка освободим память
 }
 if (!empty($prod_id_arr)) {
	xfavi_error_log('FEED № '.$numFeed.'; NOTICE: xfavi_onlygluing передала управление xfavi_gluing; Файл: functions.php; Строка: '.__LINE__, 0);
	xfavi_gluing($prod_id_arr, $numFeed);
 }
 
 // если постов нет, пишем концовку файла
 xfavi_error_log('FEED № '.$numFeed.'; Постов больше нет, пишем концовку файла; Файл: functions.php; Строка: '.__LINE__, 0); 
 $result_xml = apply_filters('xfavi_after_offers_filter', $result_xml, $numFeed);
 $result_xml .= "</Ads>";
 /* создаем файл или перезаписываем старый удалив содержимое */
 $result = xfavi_write_file($result_xml, 'a', $numFeed);
 xfavi_rename_file($numFeed);	 
 // выставляем статус сборки в "готово"
 $status_sborki = -1;
 if ($result == true) {
	xfavi_optionGET('xfavi_status_sborki', $status_sborki, $numFeed);	
	// останавливаем крон сборки
	wp_clear_scheduled_hook('xfavi_cron_sborki');
	do_action('xfavi_after_construct', 'cache');
 } else {
	xfavi_error_log('FEED № '.$numFeed.'; xfavi_write_file вернула ошибку! Я не смог записать концовку файла... $result ='.$result.'; Файл: functions.php; Строка: '.__LINE__, 0);
	do_action('xfavi_after_construct', 'false');
 }
} // end function xfavi_onlygluing()
/*
* С версии 1.0.0
* Записывает файл логов /wp-content/uploads/xml-for-avito/xml-for-avito.log
*/
function xfavi_error_log($text, $i) {
 // $xfavi_keeplogs = xfavi_optionGET('xfavi_keeplogs');	
 if (xfavi_KEEPLOGS !== 'on') {return;}
 $upload_dir = (object)wp_get_upload_dir();
 $name_dir = $upload_dir->basedir."/xml-for-avito";
 // подготовим массив для записи в файл логов
 if (is_array($text)) {$r = xfavi_array_to_log($text); unset($text); $text = $r;}
 if (is_dir($name_dir)) {
	$filename = $name_dir.'/xml-for-avito.log';
	file_put_contents($filename, '['.date('Y-m-d H:i:s').'] '.$text.PHP_EOL, FILE_APPEND);		
 } else {
	if (!mkdir($name_dir)) {
		error_log('Нет папки xfavi! И создать не вышло! $name_dir ='.$name_dir.'; Файл: functions.php; Строка: '.__LINE__, 0);
	} else {
		error_log('Создали папку xfavi!; Файл: functions.php; Строка: '.__LINE__, 0);
		$filename = $name_dir.'/xml-for-avito.log';
		file_put_contents($filename, '['.date('Y-m-d H:i:s').'] '.$text.PHP_EOL, FILE_APPEND);
	}
 } 
 return;
}
/*
* С версии 1.0.0
* Позволяте писать в логи массив /wp-content/uploads/xml-for-avito/xml-for-avito.log
*/
function xfavi_array_to_log($text, $i=0, $res = '') {
 $tab = ''; for ($x = 0; $x<$i; $x++) {$tab = '---'.$tab;}
 if (is_array($text)) { 
  $i++;
  foreach ($text as $key => $value) {
	if (is_array($value)) {	// массив
		$res .= PHP_EOL .$tab."[$key] => ";
		$res .= $tab.xfavi_array_to_log($value, $i);
	} else { // не массив
		$res .= PHP_EOL .$tab."[$key] => ". $value;
	}
  }
 } else {
	$res .= PHP_EOL .$tab.$text;
 }
 return $res;
}
/*
* С версии 1.0.0
* получить все атрибуты вукомерца 
*/
function xfavi_get_attributes() {
 $result = array();
 $attribute_taxonomies = wc_get_attribute_taxonomies();
 if (count($attribute_taxonomies) > 0) {
	$i = 0;
    foreach($attribute_taxonomies as $one_tax ) {
		/**
		* $one_tax->attribute_id => 6
		* $one_tax->attribute_name] => слаг (на инглише или русском)
		* $one_tax->attribute_label] => Еще один атрибут (это как раз название)
		* $one_tax->attribute_type] => select 
		* $one_tax->attribute_orderby] => menu_order
		* $one_tax->attribute_public] => 0			
		*/
		$result[$i]['id'] = $one_tax->attribute_id;
		$result[$i]['name'] = $one_tax->attribute_label;
		$i++;
    }
 }
 return $result;
}
 
/*
* @since 1.0.0
*
* @param string $numFeed (not require)
*
* @return nothing
* Создает пустой файл ids_in_xml.tmp или очищает уже имеющийся
*/
function xfavi_clear_file_ids_in_xml($numFeed = '1') {
	$xfavi_file_ids_in_xml = urldecode(xfavi_optionGET('xfavi_file_ids_in_xml', $numFeed));
	if (!is_file($xfavi_file_ids_in_xml)) {
		xfavi_error_log('FEED № '.$numFeed.'; WARNING: Файла c idшниками $xfavi_file_ids_in_xml = '.$xfavi_file_ids_in_xml.' нет! Создадим пустой; Файл: function.php; Строка: '.__LINE__, 0);

		$upload_dir = (object)wp_get_upload_dir();
		$name_dir = $upload_dir->basedir."/xml-for-avito";

		$xfavi_file_ids_in_xml = $name_dir .'/feed'.$numFeed.'/ids_in_xml.tmp';		
		$res = file_put_contents($xfavi_file_ids_in_xml, '');
		if ($res !== false) {
			xfavi_error_log('FEED № '.$numFeed.'; NOTICE: Файл c idшниками $xfavi_file_ids_in_xml = '.$xfavi_file_ids_in_xml.' успешно создан; Файл: function.php; Строка: '.__LINE__, 0);
			xfavi_optionUPD('xfavi_file_ids_in_xml', urlencode($xfavi_file_ids_in_xml), $numFeed);
		} else {
			xfavi_error_log('FEED № '.$numFeed.'; ERROR: Ошибка создания файла $xfavi_file_ids_in_xml = '.$xfavi_file_ids_in_xml.'; Файл: function.php; Строка: '.__LINE__, 0);
		}
	} else {
		xfavi_error_log('FEED № '.$numFeed.'; NOTICE: Обнуляем файл $xfavi_file_ids_in_xml = '.$xfavi_file_ids_in_xml.'; Файл: function.php; Строка: '.__LINE__, 0);
		file_put_contents($xfavi_file_ids_in_xml, '');
	}
}
/*
* @since 1.0.0
*
* @return nothing
* Обновляет настройки плагина
* Updates plugin settings
*/
function xfavi_set_new_options() {
	wp_clean_plugins_cache();
	wp_clean_update_cache();
	add_filter('pre_site_transient_update_plugins', '__return_null');
	wp_update_plugins();
	remove_filter('pre_site_transient_update_plugins', '__return_null');
		
	$numFeed = '1'; // (string)
	if (!defined('xfavi_ALLNUMFEED')) {define('xfavi_ALLNUMFEED', '3');}
	$allNumFeed = (int)xfavi_ALLNUMFEED;
	for ($i = 1; $i<$allNumFeed+1; $i++) {
		if (xfavi_optionGET('xfavi_feed_assignment', $numFeed) === false) {xfavi_optionUPD('xfavi_feed_assignment', '', $numFeed);}
		if (xfavi_optionGET('xfavi_code_post_meta', $numFeed) === false) {xfavi_optionUPD('xfavi_code_post_meta', '', $numFeed);}
		if (xfavi_optionGET('xfavi_guarantee', $numFeed) === false) {xfavi_optionUPD('xfavi_guarantee', 'disabled', $numFeed);}
		if (xfavi_optionGET('xfavi_guarantee_type', $numFeed) === false) {xfavi_optionUPD('xfavi_guarantee_type', 'manufacturer', $numFeed);}
		if (xfavi_optionGET('xfavi_guarantee_value', $numFeed) === false) {xfavi_optionUPD('xfavi_guarantee_value', '', $numFeed);}
		if (xfavi_optionGET('xfavi_guarantee_post_meta', $numFeed) === false) {xfavi_optionUPD('xfavi_guarantee_post_meta', '', $numFeed);}
		if (xfavi_optionGET('xfavi_the_content', $numFeed) === false) {xfavi_optionUPD('xfavi_the_content', 'enabled', $numFeed);}
		if (xfavi_optionGET('xfavi_var_desc_priority', $numFeed) === false) {xfavi_optionUPD('xfavi_var_desc_priority', '', $numFeed);}
		if (xfavi_optionGET('xfavi_behavior_strip_symbol', $numFeed) === false) {xfavi_optionUPD('xfavi_behavior_strip_symbol', 'default', $numFeed);}
		if (xfavi_optionGET('xfavi_skip_missing_products', $numFeed) === false) {xfavi_optionUPD('xfavi_skip_missing_products', '0', $numFeed);}
		if (xfavi_optionGET('xfavi_skip_backorders_products', $numFeed) === false) {xfavi_optionUPD('xfavi_skip_backorders_products', '0', $numFeed);}
		$numFeed++;
	}
	if (defined('xfavi_VER')) {
		if (is_multisite()) {
			update_blog_option(get_current_blog_id(), 'xfavi_version', xfavi_VER);
		} else {
			update_option('xfavi_version', xfavi_VER);
		}
	}
}
/*
* @since 1.1.0
*
* @return formatted string
*/
function xfavi_formatSize($bytes) {
	if ($bytes >= 1073741824) {
		   $bytes = number_format($bytes / 1073741824, 2) . ' GB';
	}
	elseif ($bytes >= 1048576) {
		   $bytes = number_format($bytes / 1048576, 2) . ' MB';
	}
	elseif ($bytes >= 1024) {
	   $bytes = number_format($bytes / 1024, 2) . ' KB';
	}
	elseif ($bytes > 1) {
		$bytes = $bytes . ' B';
	}
	elseif ($bytes == 1) {
	   $bytes = $bytes . ' B';
	}
	else {
	   $bytes = '0 B';
	}
	return $bytes;
}

function xfavi_category_goods_type($items, $value) {
 $category_const_arr = array(); $result = '';
 $category_const_arr[0] = array('Одежда, обувь, аксессуары', array(
	'Женская одежда',
	'Мужская одежда',
	'Аксессуары'
 ));
 $category_const_arr[1] = array('Детская одежда и обувь', array(
	'Для девочек',
	'Для мальчиков'
 ));
 $category_const_arr[2] = array('Товары для детей и игрушки', array(
	'Автомобильные кресла',
	'Велосипеды и самокаты',
	'Детская мебель',
	'Детские коляски',
	'Игрушки',
	'Постельные принадлежности',
	'Товары для кормления',
	'Товары для купания',
	'Товары для школы'
 ));
 $category_const_arr[3] = array('Часы и украшения', array(
	'Бижутерия',
	'Часы',
	'Ювелирные изделия'
 ));
 $category_const_arr[4] = array('Красота и здоровье', array(
	'Косметика',
	'Парфюмерия',
	'Приборы и аксессуары',
	'Средства гигиены',
	'Средства для волос',
	'Медицинские изделия',
	'Биологически активные добавки'
 ));
 $category_const_arr[5] = array('Ремонт и строительство', array(
	'Двери',
	'Инструменты',
	'Камины и обогреватели',
	'Окна и балконы',
	'Потолки',
	'Садовая техника',
	'Сантехника и сауна',
	'Стройматериалы'
 ));
 $category_const_arr[6] = array('Мебель и интерьер', array(
	'Компьютерные столы и кресла',
	'Кровати, диваны и кресла',
	'Кухонные гарнитуры',
	'Освещение',
	'Подставки и тумбы',
	'Предметы интерьера, искусство',
	'Столы и стулья',
	'Текстиль и ковры',
	'Шкафы и комоды',
	'Другое'
));
$category_const_arr[7] = array('Бытовая техника', array(
	'Пылесосы',
	'Стиральные машины',
	'Утюги',
	'Швейные машины',
	'Бритвы и триммеры',
	'Машинки для стрижки',
	'Фены и приборы для укладки',
	'Эпиляторы',
	'Вытяжки',
	'Мелкая кухонная техника',
	'Микроволновые печи',
	'Плиты',
	'Посудомоечные машины',
	'Холодильники и морозильные камеры',
	'Вентиляторы',
	'Кондиционеры',
	'Обогреватели',
	'Очистители воздуха',
	'Термометры и метеостанции',
	'Другое'
 )); 
 $category_const_arr[8] = array('Посуда и товары для кухни', array(
	'Посуда',
	'Товары для кухни'
 ));
/* $category_const_arr[8] = array('Запчасти и аксессуары', array(
	'16-837', 'Запчасти / Для автомобилей / Двигатель / Патрубки вентиляции',
	'16-838', 'Запчасти / Для автомобилей / Двигатель / Поршни, шатуны, кольца',
	'16-839', 'Запчасти / Для автомобилей / Двигатель / Приводные ремни, натяжители',
	'16-840', 'Запчасти / Для автомобилей / Двигатель / Прокладки и ремкомплекты',
	'16-841', 'Запчасти / Для автомобилей / Двигатель / Ремни, цепи, элементы ГРМ',
	'16-842', 'Запчасти / Для автомобилей / Двигатель / Турбины, компрессоры',
	'16-843', 'Запчасти / Для автомобилей / Двигатель / Электродвигатели и компоненты',
	'11-621', 'Запчасти / Для автомобилей / Запчасти для ТО',
	'16-805', 'Запчасти / Для автомобилей / Кузов / Балки, лонжероны',
	'16-806', 'Запчасти / Для автомобилей / Кузов / Бамперы',
	'16-807', 'Запчасти / Для автомобилей / Кузов / Брызговики',
	'16-808', 'Запчасти / Для автомобилей / Кузов / Двери',
	'16-809', 'Запчасти / Для автомобилей / Кузов / Заглушки',
	'16-810', 'Запчасти / Для автомобилей / Кузов / Замки',
	'16-811', 'Запчасти / Для автомобилей / Кузов / Защита',
	'16-812', 'Запчасти / Для автомобилей / Кузов / Зеркала',
	'16-813', 'Запчасти / Для автомобилей / Кузов / Кабина',
	'16-814', 'Запчасти / Для автомобилей / Кузов / Капот',
	'16-815', 'Запчасти / Для автомобилей / Кузов / Крепления',
	'16-816', 'Запчасти / Для автомобилей / Кузов / Крылья',
	'16-817', 'Запчасти / Для автомобилей / Кузов / Крыша',
	'16-818', 'Запчасти / Для автомобилей / Кузов / Крышка, дверь багажника',
	'16-819', 'Запчасти / Для автомобилей / Кузов / Кузов по частям',
	'16-820', 'Запчасти / Для автомобилей / Кузов / Кузов целиком',
	'16-821', 'Запчасти / Для автомобилей / Кузов / Лючок бензобака',
	'16-822', 'Запчасти / Для автомобилей / Кузов / Молдинги, накладки',
	'16-823', 'Запчасти / Для автомобилей / Кузов / Пороги',
	'16-824', 'Запчасти / Для автомобилей / Кузов / Рама',
	'16-825', 'Запчасти / Для автомобилей / Кузов / Решетка радиатора',
	'16-826', 'Запчасти / Для автомобилей / Кузов / Стойка кузова',
	'11-623', 'Запчасти / Для автомобилей / Подвеска',
	'11-624', 'Запчасти / Для автомобилей / Рулевое управление',
	'11-625', 'Запчасти / Для автомобилей / Салон',
	'16-521', 'Запчасти / Для автомобилей / Система охлаждения',
	'11-626', 'Запчасти / Для автомобилей / Стекла',
	'11-627', 'Запчасти / Для автомобилей / Топливная и выхлопная системы',
	'11-628', 'Запчасти / Для автомобилей / Тормозная система',
	'11-629', 'Запчасти / Для автомобилей / Трансмиссия и привод',
	'11-630', 'Запчасти / Для автомобилей / Электрооборудование',
	'6-401', 'Запчасти / Для мототехники',
	'6-406', 'Запчасти / Для спецтехники',
	'6-411', 'Запчасти / Для водного транспорта',
	'4-943', 'Аксессуары',
	'21', 'GPS-навигаторы',
	'4-942', 'Автокосметика и автохимия',
	'20', 'Аудио- и видеотехника',
	'4-964', 'Багажники и фаркопы',
	'4-963', 'Инструменты',
	'4-965', 'Прицепы',
	'11-631', 'Противоугонные устройства / Автосигнализации',
	'11-632', 'Противоугонные устройства / Иммобилайзеры',
	'11-633', 'Противоугонные устройства / Механические блокираторы',
	'11-634', 'Противоугонные устройства / Спутниковые системы',
	'22', 'Тюнинг',
	'10-048', 'Шины, диски и колёса / Шины',
	'10-047', 'Шины, диски и колёса / Мотошины',
	'10-046', 'Шины, диски и колёса / Диски',
	'10-045', 'Шины, диски и колёса / Колёса',
	'10-044', 'Шины, диски и колёса / Колпаки',
	'6-416', 'Экипировка' 
 )); */


 $category_apparel_arr[0] = array('Женская, Мужская или Детская одежда', array(
	'Брюки',
	'Верхняя одежда',
	'Джинсы',
	'Пиджаки и костюмы',	
	'Обувь',
	'Другое'
 ));
 $category_apparel_arr[1] = array('Женская одежда', array(
	'Купальники',
	'Нижнее бельё',
	'Платья и юбки',
	'Рубашки и блузки',
	'Свадебные платья',
	'Топы и футболки',
	'Трикотаж'
 ));
 $category_apparel_arr[2] = array('Мужская одежда', array(
	'Рубашки',
	'Трикотаж и футболки',
 ));	
 $category_apparel_arr[3] = array('Для девочек или Мальчиков', array(
	'Комбинезоны и боди',
	'Пижамы',
	'Платья и юбки',
	'Трикотаж',
	'Шапки, варежки, шарфы',
 ));

 if ($items === 1) {
	for ($i = 0; $i < count($category_const_arr); $i++) {
		if ($category_const_arr[$i][0] === $value) {$selected = 'selected';} else {$selected = '';}
		$result .= '<option value="'.$category_const_arr[$i][0].'" '.$selected.'>'.$category_const_arr[$i][0].'</option>';
	}
 } else if ($items === 2) {
	for ($i = 0; $i < count($category_const_arr); $i++) {
		$result .= '<optgroup label="'.$category_const_arr[$i][0].'">';
			for ($n = 0; $n < count($category_const_arr[$i][1]); $n++) {
				if ($category_const_arr[$i][1][$n] === $value) {$selected = 'selected';} else {$selected = '';}
				$result .= '<option value="'.$category_const_arr[$i][1][$n].'" '.$selected.'>'.$category_const_arr[$i][1][$n].'</option>';
			}		
		$result .= '</optgroup>';
	}	 
} else if ($items === 3) {
	for ($i = 0; $i < count($category_apparel_arr); $i++) {
		$result .= '<optgroup label="'.$category_apparel_arr[$i][0].'">';
			for ($n = 0; $n < count($category_apparel_arr[$i][1]); $n++) {
				if ($category_apparel_arr[$i][1][$n] === $value) {$selected = 'selected';} else {$selected = '';}
				$result .= '<option value="'.$category_apparel_arr[$i][1][$n].'" '.$selected.'>'.$category_apparel_arr[$i][1][$n].'</option>';
			}		
		$result .= '</optgroup>';
	}	 
 }
 return $result;
}	
/*
* @since 1.1.1
*
* @return formatted string
*/
function xfavi_replace_symbol($string, $numFeed = '1') {
 $xfavi_behavior_stip_symbol = xfavi_optionGET('xfavi_behavior_strip_symbol', $numFeed);	
 switch ($xfavi_behavior_stip_symbol) {
	case "del":	
		$string = str_replace("&", '', $string);
	break;
		case "slash":
		$string = str_replace("&", '/', $string);
	break;
	case "amp":
		$string = htmlspecialchars($string);
	break;
	default:
		$string = htmlspecialchars($string);
 }
 return $string;
}
/*
* @since 1.3.0
*
* @return array()
*/
function xfavi_option_construct($term) {
 // https://www.php.net/manual/ru/class.simplexmlelement.php
 $result_arr = array();
 $xml_url = plugin_dir_path(__FILE__).'data/goodstype.xml';
 $xml_string = file_get_contents($xml_url);
 // $xml_object = simplexml_load_string($xml_string);
 $xml_object = new SimpleXMLElement($xml_string);

 $xfavi_avito_standart = esc_attr(get_term_meta($term->term_id, 'xfavi_avito_standart', 1));
 $xfavi_avito_product_category = esc_attr(get_term_meta($term->term_id, 'xfavi_avito_product_category', 1)); 
 $xfavi_default_goods_type = esc_attr(get_term_meta($term->term_id, 'xfavi_default_goods_type', 1));
 $xfavi_default_goods_subtype = esc_attr(get_term_meta($term->term_id, 'xfavi_default_goods_subtype', 1));

 $resultCategory = '';
 $resultGoodsType = '';
 $resultGoodsSubType = '';
 $flag = true;

 foreach ($xml_object->children() as $second_gen) {
	if ($xfavi_avito_product_category == str_replace(' ', '_', $second_gen['name'])) {$selected = 'selected';} else {$selected = '';}
	$resultCategory .= '<option value="'.str_replace(' ', '_', $second_gen['name']).'" data-chained="'.$second_gen['avito_standart'].'" '.$selected.'>'.$second_gen['name'].'</option>'.PHP_EOL;
	if (count($second_gen->children()) > 0) {
		foreach ($second_gen->children() as $third_gen) {
			if ($xfavi_default_goods_type == str_replace(' ', '_', $third_gen['name'])) {$selected = 'selected';} else {$selected = '';}
			$resultGoodsType .= '<option value="'.str_replace(' ', '_', $third_gen['name']).'" data-chained="'.str_replace(' ', '_', $second_gen['name']).'" '.$selected.'>'.$third_gen['name'].'</option>'.PHP_EOL;

			if (count($third_gen->children()) > 0) {
				foreach ($third_gen->children() as $fourth_gen) {
					if ($xfavi_default_goods_subtype == str_replace(' ', '_', $fourth_gen['name'])) {$selected = 'selected';} else {$selected = '';}
					$resultGoodsSubType .= '<option value="'.str_replace(' ', '_', $fourth_gen['name']).'" data-chained="'.str_replace(' ', '_', $third_gen['name']).'" '.$selected.'>'.$fourth_gen['name'].'</option>'.PHP_EOL;
				}
			} else {
				if ($xfavi_default_goods_subtype == 'disabled') {$selected = 'selected';} else {$selected = '';}
				$resultGoodsSubType .= '<option value="disabled" data-chained="'.str_replace(' ', '_', $third_gen['name']).'" '.$selected.'>Отключено</option>'.PHP_EOL;
			}
		} 
	} else {
		if ($xfavi_default_goods_type == 'disabled') {$selected = 'selected';} else {$selected = '';}
		$resultGoodsType .= '<option value="disabled" data-chained="'.str_replace(' ', '_', $second_gen['name']).'" '.$selected.'>Отключено</option>'.PHP_EOL;

		if ($flag === true) {
			if ($xfavi_default_goods_subtype == 'disabled') {$selected = 'selected';} else {$selected = '';}
			$resultGoodsSubType .= '<option value="disabled" data-chained="disabled" '.$selected.'>Отключено</option>'.PHP_EOL;
			$flag = false;
		}
	}
 }
 
 $result_arr = array($resultCategory, $resultGoodsType, $resultGoodsSubType);
 return $result_arr;
}
/*
* @since 1.3.0
*
* @return string
*/
function xfavi_option_construct_product($post) {
 // https://www.php.net/manual/ru/class.simplexmlelement.php
 $result = '';
 $xml_url = plugin_dir_path(__FILE__).'data/goodstype.xml';
 $xml_string = file_get_contents($xml_url);
 // $xml_object = simplexml_load_string($xml_string);
 $xml_object = new SimpleXMLElement($xml_string);
   
 $xfavi_goods_type = esc_attr(get_post_meta($post->ID, '_xfavi_goods_type', 1));

 foreach ($xml_object->children() as $second_gen) {	
	if (count($second_gen->children()) > 0) {
		$result .= '<optgroup label="'.$second_gen['name'].'">'.PHP_EOL;
		foreach ($second_gen->children() as $third_gen) {
			if ($xfavi_goods_type == str_replace(' ', '_', $third_gen['name'])) {$selected = 'selected';} else {$selected = '';}
			$result .= '<option value="'.str_replace(' ', '_', $third_gen['name']).'" '.$selected.'>'.$third_gen['name'].'</option>'.PHP_EOL;
		} 
		$result .= '</optgroup>'.PHP_EOL; 
	}
 }
 return $result;
}
/*
* @since 1.3.5
*
* @return array
*/
function xfavi_possible_problems_list() {
 $possibleProblems = ''; $possibleProblemsCount = 0; $conflictWithPlugins = 0; $conflictWithPluginsList = ''; 
 $check_global_attr_count = wc_get_attribute_taxonomies();
 if (count($check_global_attr_count) < 1) {
	$possibleProblemsCount++;
	$possibleProblems .= '<li>'. __('Ваш сайт не имеет глобальных атрибутов! Это может повлиять на качество YML-фида. Это также может вызвать трудности при настройке плагина', 'xfavi'). '. <a href="https://icopydoc.ru/globalnyj-i-lokalnyj-atributy-v-woocommerce/?utm_source=xml-for-avito&utm_medium=organic&utm_campaign=in-plugin-xml-for-avito&utm_content=debug-page&utm_term=possible-problems">'. __('Пожалуйста, прочитайте рекомендации', 'xfavi'). '</a>.</li>';
 }	
 if (is_plugin_active('snow-storm/snow-storm.php')) {
	$possibleProblemsCount++;
	$conflictWithPlugins++;
	$conflictWithPluginsList .= 'Snow Storm<br/>';
 }
 if (is_plugin_active('email-subscribers/email-subscribers.php')) {
	$possibleProblemsCount++;
	$conflictWithPlugins++;
	$conflictWithPluginsList .= 'Email Subscribers & Newsletters<br/>';
 }
 if (is_plugin_active('saphali-search-castom-filds/saphali-search-castom-filds.php')) {
	$possibleProblemsCount++;
	$conflictWithPlugins++;
	$conflictWithPluginsList .= 'Email Subscribers & Newsletters<br/>';
 }
 if (is_plugin_active('w3-total-cache/w3-total-cache.php')) {
	$possibleProblemsCount++;
	$conflictWithPlugins++;
	$conflictWithPluginsList .= 'W3 Total Cache<br/>';
 }
 if (is_plugin_active('docket-cache/docket-cache.php')) {
	$possibleProblemsCount++;
	$conflictWithPlugins++;
	$conflictWithPluginsList .= 'Docket Cache<br/>';
 }					
 if (class_exists('MPSUM_Updates_Manager')) {
	$possibleProblemsCount++;
	$conflictWithPlugins++;
	$conflictWithPluginsList .= 'Easy Updates Manager<br/>';
 }
 if (class_exists('OS_Disable_WordPress_Updates')) {
	$possibleProblemsCount++;
	$conflictWithPlugins++;
	$conflictWithPluginsList .= 'Disable All WordPress Updates<br/>';
 }
 if ($conflictWithPlugins > 0) {
	$possibleProblemsCount++;
	$possibleProblems .= '<li><p>'. __('Скорее всего, эти плагины негативно влияют на работу', 'xfavi'). ' XML for Avito:</p>'.$conflictWithPluginsList.'<p>'. __('Если вы разработчик одного из плагинов из списка выше, пожалуйста, свяжитесь со мной', 'xfavi').': <a href="mailto:support@icopydoc.ru">support@icopydoc.ru</a>.</p></li>';
 }
 return array($possibleProblems, $possibleProblemsCount, $conflictWithPlugins, $conflictWithPluginsList);
}
?>