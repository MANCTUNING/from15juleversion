<?php if (!defined('ABSPATH')) {exit;}
/*
* @since  1.0.0
*
* Обновлён с версии 3.0.0 
* Добавлен параметр $n
* Записывает или обновляет файл фида.
* Возвращает всегда true
*/
function yfym_write_file($result_yml, $cc, $numFeed = '1') {
 /* $cc = 'w+' или 'a'; */	 
 yfym_error_log('FEED № '.$numFeed.'; Стартовала yfym_write_file c параметром cc = '.$cc.'; Файл: functions.php; Строка: '.__LINE__, 0);
 $filename = urldecode(yfym_optionGET('yfym_file_file', $numFeed));
 if ($numFeed === '1') {$prefFeed = '';} else {$prefFeed = $numFeed;}

 if ($filename == '') {	
	$upload_dir = (object)wp_get_upload_dir(); // $upload_dir->basedir
	$filename = $upload_dir->basedir.$prefFeed."feed-yml-0-tmp.xml"; // $upload_dir->path
 }
		
 // if ((validate_file($filename) === 0)&&(file_exists($filename))) {
 if (file_exists($filename)) {
	// файл есть
	if (!$handle = fopen($filename, $cc)) {
		yfym_error_log('FEED № '.$numFeed.'; Не могу открыть файл '.$filename.'; Файл: functions.php; Строка: '.__LINE__, 0);
		yfym_errors_log('FEED № '.$numFeed.'; Не могу открыть файл '.$filename.'; Файл: functions.php; Строка: '.__LINE__, 0);
	}
	if (fwrite($handle, $result_yml) === FALSE) {
		yfym_error_log('FEED № '.$numFeed.'; Не могу произвести запись в файл '.$handle.'; Файл: functions.php; Строка: '.__LINE__, 0);
		yfym_errors_log('FEED № '.$numFeed.'; Не могу произвести запись в файл '.$handle.'; Файл: functions.php; Строка: '.__LINE__, 0);
	} else {
		yfym_error_log('FEED № '.$numFeed.'; Ура! Записали; Файл: Файл: functions.php; Строка: '.__LINE__, 0);
		yfym_error_log($filename, 0);
		return true;
	}
	fclose($handle);
 } else {
	yfym_error_log('FEED № '.$numFeed.'; Файла $filename = '.$filename.' еще нет. Файл: functions.php; Строка: '.__LINE__, 0);
	// файла еще нет
	// попытаемся создать файл
	if (is_multisite()) {
		$upload = wp_upload_bits($prefFeed.'feed-yml-'.get_current_blog_id().'-tmp.xml', null, $result_yml ); // загружаем shop2_295221-yml в папку загрузок
	} else {
		$upload = wp_upload_bits($prefFeed.'feed-yml-0-tmp.xml', null, $result_yml ); // загружаем shop2_295221-yml в папку загрузок
	}
	/*
	*	для работы с csv или xml требуется в плагине разрешить загрузку таких файлов
	*	$upload['file'] => '/var/www/wordpress/wp-content/uploads/2010/03/feed-yml.xml', // путь
	*	$upload['url'] => 'http://site.ru/wp-content/uploads/2010/03/feed-yml.xml', // урл
	*	$upload['error'] => false, // сюда записывается сообщение об ошибке в случае ошибки
	*/
	// проверим получилась ли запись
	if ($upload['error']) {
		yfym_error_log('FEED № '.$numFeed.'; Запись вызвала ошибку: '. $upload['error'].'; Файл: functions.php; Строка: '.__LINE__, 0);
		$err = 'FEED № '.$numFeed.'; Запись вызвала ошибку: '. $upload['error'].'; Файл: functions.php; Строка: '.__LINE__ ;
		yfym_errors_log($err);
	} else {
		yfym_optionUPD('yfym_file_file', urlencode($upload['file']), $numFeed);
		yfym_error_log('FEED № '.$numFeed.'; Запись удалась! Путь файла: '. $upload['file'] .'; УРЛ файла: '. $upload['url'], 0);
		return true;
	}		
 }
}
/*
* @since 1.2
*
* @return false/true
* Перименовывает временный файл фида в основной
*/
function yfym_rename_file($numFeed = '1') {
 yfym_error_log('FEED № '.$numFeed.'; Cтартовала yfym_rename_file; Файл: functions.php; Строка: '.__LINE__, 0);	
 if ($numFeed === '1') {$prefFeed = '';} else {$prefFeed = $numFeed;}
 $yfym_file_extension = yfym_optionGET('yfym_file_extension', $numFeed);
 if ($yfym_file_extension == '') {$yfym_file_extension = 'xml';}
 /* Перименовывает временный файл в основной. Возвращает true/false */
 if (is_multisite()) {
	$upload_dir = (object)wp_get_upload_dir();
	$filenamenew = $upload_dir->basedir."/".$prefFeed."feed-yml-".get_current_blog_id().".".$yfym_file_extension;
	$filenamenewurl = $upload_dir->baseurl."/".$prefFeed."feed-yml-".get_current_blog_id().".".$yfym_file_extension;		
	// $filenamenew = BLOGUPLOADDIR."feed-yml-".get_current_blog_id().".xml";
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
	$filenamenew = $upload_dir->basedir."/".$prefFeed."feed-yml-0.".$yfym_file_extension;
	$filenamenewurl = $upload_dir->baseurl."/".$prefFeed."feed-yml-0.".$yfym_file_extension;
 }
 $filenameold = urldecode(yfym_optionGET('yfym_file_file', $numFeed));

 yfym_error_log('FEED № '.$numFeed.'; $filenameold = '.$filenameold.'; Файл: functions.php; Строка: '.__LINE__, 0);
 yfym_error_log('FEED № '.$numFeed.'; $filenamenew = '.$filenamenew.'; Файл: functions.php; Строка: '.__LINE__, 0);

 if (rename($filenameold, $filenamenew) === FALSE) {
	yfym_error_log('FEED № '.$numFeed.'; Не могу переименовать файл из '.$filenameold.' в '.$filenamenew.'! Файл: functions.php; Строка: '.__LINE__, 0);
	return false;
 } else {
	yfym_optionUPD('yfym_file_url', urlencode($filenamenewurl), $numFeed);
	yfym_error_log('FEED № '.$numFeed.'; Файл переименован! Файл: functions.php; Строка: '.__LINE__, 0);
	return true;
 }
}
/*
* @since 1.2.5
* Возвращает URL без get-параметров или возвращаем только get-параметры
*/	
function deleteGET($url, $whot = 'url') {
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
* @since 1.3.3
* Записывает текст ошибки, чтобы потом можно было отправить в отчет
*/
function yfym_errors_log($message) {
 $message = '['.date('Y-m-d H:i:s').'] '. $message;
 if (is_multisite()) {
	update_blog_option(get_current_blog_id(), 'yfym_errors', $message);
 } else {
	update_option('yfym_errors', $message);
 }
}
/*
* @since 1.4.2
* Возвращает версию Woocommerce (string) или (null)
*/ 
function yfym_get_woo_version_number() {
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
* @since 1.4.6
* Возвращает дерево таксономий, обернутое в <option></option>
*/
function yfym_cat_tree($TermName='', $termID, $value_arr, $separator='', $parent_shown=true) {
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
	$result = '<option title="'.$term->name.'; ID: '.$term->term_id.'; '. __('products', 'yfym'). ': '.$term->count.'" class="hover" value="'.$term->term_id.'" '.$selected .'>'.$separator.$term->name.'</option>';		
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
	 $result .= '<option title="'.$term->name.'; ID: '.$term->term_id.'; '. __('products', 'yfym'). ': '.$term->count.'" class="hover" value="'.$term->term_id.'" '.$selected .'>'.$separator.$term->name.'</option>';
	 // $result .=  $separator.$term->name.'('.$term->term_id.')<br/>';
	 $result .= yfym_cat_tree($TermName, $term->term_id, $value_arr, $separator, $parent_shown);
	}
 }
 return $result; 
}
/*
* @since 3.0.0
*
* @param string $option_name (require)
* @param string $value (require)
* @param string $n (not require)
* @param string $autoload (not require) (yes/no) (@since 3.3.15)
* @param string $type (not require) (@since 3.5.5)
*
* @return true/false
* Возвращает то, что может быть результатом add_blog_option, add_option
*/
function yfym_optionADD($option_name, $value = '', $n = '', $autoload = 'yes', $type = 'option') {
	if ($option_name == '') {return false;}
	switch ($type) {
		case "set_arr":
			if ($n === '') {$n = '1';}
			$yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
			$yfym_settings_arr[$n][$option_name] = $value;
			if (is_multisite()) { 
				return update_blog_option(get_current_blog_id(), 'yfym_settings_arr', $yfym_settings_arr);
			} else {
				return update_option('yfym_settings_arr', $yfym_settings_arr, $autoload);
			}
		break;
		default:
			if ($n === '1') {$n = '';}
			$option_name = $option_name.$n;
			if (is_multisite()) { 
				return add_blog_option(get_current_blog_id(), $option_name, $value);
			} else {
				return add_option($option_name, $value, '', $autoload);
			}
	}
}
/*
* @since 3.0.0
*
* @param string $option_name (require)
* @param string $value (not require)
* @param string $n (not require)
* @param string $autoload (not require) (yes/no) (@since 3.3.15)
* @param string $type (not require) (@since 3.5.5)
*
* @return true/false
* Возвращает то, что может быть результатом update_blog_option, update_option
*/
function yfym_optionUPD($option_name, $value = '', $n = '', $autoload = 'yes', $type = '') {
	if ($option_name == '') {return false;}
	switch ($type) {
		case "set_arr": 
			if ($n === '') {$n = '1';}
			$yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
			$yfym_settings_arr[$n][$option_name] = $value;
			if (is_multisite()) { 
				return update_blog_option(get_current_blog_id(), 'yfym_settings_arr', $yfym_settings_arr);
			} else {
				return update_option('yfym_settings_arr', $yfym_settings_arr, $autoload);
			}
		break;
		default:
			if ($n === '1') {$n = '';}
			$option_name = $option_name.$n;
			if (is_multisite()) { 
				return update_blog_option(get_current_blog_id(), $option_name, $value);
			} else {
				return update_option($option_name, $value, $autoload);
			}
	}
}
/*
* @since 2.0.0
*
* @param string $option_name (require)
* @param string $n (not require) (@since 3.0.0)
* @param string $type (not require) (@since 3.5.5)
*
* @return Значение опции или false
* Возвращает то, что может быть результатом get_blog_option, get_option
*/
function yfym_optionGET($option_name, $n = '', $type = '') {
	if ($option_name == '') {return false;}	
	switch ($type) {
		case "set_arr": 
			if ($n === '') {$n = '1';}
			$yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
			if (isset($yfym_settings_arr[$n][$option_name])) {
				return $yfym_settings_arr[$n][$option_name];
			} else {
				return false;
			}
		break;
		default:
			if ($n === '1') {$n = '';}
			$option_name = $option_name.$n;
			if (is_multisite()) { 
				return get_blog_option(get_current_blog_id(), $option_name);
			} else {
				return get_option($option_name);
			}
	}
}
/*
* @since 3.0.0
*
* @param string $option_name (require)
* @param string $n (not require)
* @param string $type (not require) (@since 3.5.5)
*
* @return true/false
* Возвращает то, что может быть результатом delete_blog_option, delete_option
*/
function yfym_optionDEL($option_name, $n = '', $type = '') {
	if ($option_name == '') {return false;}	 
	switch ($type) {
		case "set_arr": 
			if ($n === '') {$n = '1';} 
			$yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
			unset($yfym_settings_arr[$n][$option_name]);
			if (is_multisite()) { 
				return update_blog_option(get_current_blog_id(), 'yfym_settings_arr', $yfym_settings_arr);
			} else {
				return update_option('yfym_settings_arr', $yfym_settings_arr);
			}
		break;
		default:
		if ($n === '1') {$n = '';} 
		$option_name = $option_name.$n;
		if (is_multisite()) { 
			return delete_blog_option(get_current_blog_id(), $option_name);
		} else {
			return delete_option($option_name);
		}
	}
} 
/*
* С версии 2.0.0
* C версии 3.0.0 добавлена поддержка нескольких фидов
* Создает tmp файл-кэш товара
* С версии 3.0.2 исправлена критическая ошибка
* C версии 3.1.0 добавлен параметр ids_in_yml
*/
function yfym_wf($result_yml, $postId, $numFeed = '1', $ids_in_yml = '') {
 // $numFeed = '1'; // (string) создадим строковую переменную
 /*$allNumFeed = (int)yfym_ALLNUMFEED;
 for ($i = 1; $i<$allNumFeed+1; $i++) {*/
	$upload_dir = (object)wp_get_upload_dir();
	$name_dir = $upload_dir->basedir.'/yfym';
	if (!is_dir($name_dir)) {
		error_log('WARNING: Папка $name_dir ='.$name_dir.' нет; Файл: functions.php; Строка: '.__LINE__, 0);
		if (!mkdir($name_dir)) {
			error_log('ERROR: Создать папку $name_dir ='.$name_dir.' не вышло; Файл: functions.php; Строка: '.__LINE__, 0);
		}
	}

	$name_dir = $upload_dir->basedir.'/yfym/feed'.$numFeed;
	if (!is_dir($name_dir)) {
		error_log('WARNING: Папка $name_dir ='.$name_dir.' нет; Файл: functions.php; Строка: '.__LINE__, 0);
		if (!mkdir($name_dir)) {
			error_log('ERROR: Создать папку $name_dir ='.$name_dir.' не вышло; Файл: functions.php; Строка: '.__LINE__, 0);
		}
	}
	if (is_dir($name_dir)) {
		$filename = $name_dir.'/'.$postId.'.tmp';
		$fp = fopen($filename, "w");
		fwrite($fp, $result_yml); // записываем в файл текст
		fclose($fp); // закрываем

		/* C версии 3.1.0 */
		$filename = $name_dir.'/'.$postId.'-in.tmp';
		$fp = fopen($filename, "w");
		fwrite($fp, $ids_in_yml);
		fclose($fp);
		/* end с версии 3.1.0 */
	} else {
		error_log('ERROR: Нет папки yfym! $name_dir ='.$name_dir.'; Файл: functions.php; Строка: '.__LINE__, 0);
	}
	/*$numFeed++;
 }*/
}
/*
* С версии 2.0.0
* Функция склейки/сборки
*/
function yfym_gluing($id_arr, $numFeed = '1') {
 /*	
 * $id_arr[$i]['ID'] - ID товара
 * $id_arr[$i]['post_modified_gmt'] - Время обновления карточки товара
 * global $wpdb;
 * $res = $wpdb->get_results("SELECT ID, post_modified_gmt FROM $wpdb->posts WHERE post_type = 'product' AND post_status = 'publish'");	
 */	
 yfym_error_log('FEED № '.$numFeed.'; Стартовала yfym_gluing; Файл: functions.php; Строка: '.__LINE__, 0);
 if ($numFeed === '1') {$prefFeed = '';} else {$prefFeed = $numFeed;} 
 $upload_dir = (object)wp_get_upload_dir();
 $name_dir = $upload_dir->basedir.'/yfym/feed'.$numFeed;
 if (!is_dir($name_dir)) {
	if (!mkdir($name_dir)) {
		error_log('FEED № '.$numFeed.'; Нет папки yfym! И создать не вышло! $name_dir ='.$name_dir.'; Файл: functions.php; Строка: '.__LINE__, 0);
	} else {
		error_log('FEED № '.$numFeed.'; Создали папку yfym! Файл: functions.php; Строка: '.__LINE__, 0);
	}
 }
 
 $yfym_file_file = urldecode(yfym_optionGET('yfym_file_file', $numFeed));
 $yfym_file_ids_in_yml = urldecode(yfym_optionGET('yfym_file_ids_in_yml', $numFeed));

 $yfym_date_save_set = yfym_optionGET('yfym_date_save_set', $numFeed);
 clearstatcache(); // очищаем кэш дат файлов
 // $prod_id
 foreach ($id_arr as $product) {
	$filename = $name_dir.'/'.$product['ID'].'.tmp';
	$filenameIn = $name_dir.'/'.$product['ID'].'-in.tmp'; /* с версии 3.1.0 */
	yfym_error_log('FEED № '.$numFeed.'; RAM '.round(memory_get_usage()/1024, 1).' Кб. ID товара/файл = '.$product['ID'].'.tmp; Файл: functions.php; Строка: '.__LINE__, 0);
	if (is_file($filename) && is_file($filenameIn)) { // if (file_exists($filename)) {
		$last_upd_file = filemtime($filename); // 1318189167			
		if (($last_upd_file < strtotime($product['post_modified_gmt'])) || ($yfym_date_save_set > $last_upd_file)) {
			// Файл кэша обновлен раньше чем время модификации товара
			// или файл обновлен раньше чем время обновления настроек фида
			yfym_error_log('FEED № '.$numFeed.'; NOTICE: Файл кэша '.$filename.' обновлен РАНЬШЕ чем время модификации товара или время сохранения настроек фида! Файл: functions.php; Строка: '.__LINE__, 0);	
			$result_yml_unit = yfym_unit($product['ID'], $numFeed);
			if (is_array($result_yml_unit)) {
				$result_yml = $result_yml_unit[0];
				$ids_in_yml = $result_yml_unit[1];
			} else {
				$result_yml = $result_yml_unit;
				$ids_in_yml = '';
			}	
			yfym_wf($result_yml, $product['ID'], $numFeed, $ids_in_yml);
			file_put_contents($yfym_file_file, $result_yml, FILE_APPEND);			
			file_put_contents($yfym_file_ids_in_yml, $ids_in_yml, FILE_APPEND);
		} else {
			// Файл кэша обновлен позже чем время модификации товара
			// или файл обновлен позже чем время обновления настроек фида
			yfym_error_log('FEED № '.$numFeed.'; NOTICE: Файл кэша '.$filename.' обновлен ПОЗЖЕ чем время модификации товара или время сохранения настроек фида; Файл: functions.php; Строка: '.__LINE__, 0);
			yfym_error_log('FEED № '.$numFeed.'; Пристыковываем файл кэша без изменений; Файл: functions.php; Строка: '.__LINE__, 0);
			$result_yml = file_get_contents($filename);
			file_put_contents($yfym_file_file, $result_yml, FILE_APPEND);
			$ids_in_yml = file_get_contents($filenameIn);
			file_put_contents($yfym_file_ids_in_yml, $ids_in_yml, FILE_APPEND);
		}
	} else { // Файла нет
		yfym_error_log('FEED № '.$numFeed.'; NOTICE: Файла кэша товара '.$filename.' ещё нет! Создаем... Файл: functions.php; Строка: '.__LINE__, 0);		
		$result_yml_unit = yfym_unit($product['ID'], $numFeed);
		if (is_array($result_yml_unit)) {
			$result_yml = $result_yml_unit[0];
			$ids_in_yml = $result_yml_unit[1];
		} else {
			$result_yml = $result_yml_unit;
			$ids_in_yml = '';
		}
		yfym_wf($result_yml, $product['ID'], $numFeed, $ids_in_yml);
		yfym_error_log('FEED № '.$numFeed.'; Создали! Файл: functions.php; Строка: '.__LINE__, 0);
		file_put_contents($yfym_file_file, $result_yml, FILE_APPEND);
		file_put_contents($yfym_file_ids_in_yml, $ids_in_yml, FILE_APPEND);
	}
 }
} // end function yfym_gluing()
/*
* С версии 2.0.0
* Функция склейки
*/
function yfym_onlygluing($numFeed = '1') {
 yfym_error_log('FEED № '.$numFeed.'; NOTICE: Стартовала yfym_onlygluing; Файл: functions.php; Строка: '.__LINE__, 0); 	
 do_action('yfym_before_construct', 'cache');
 $result_yml = yfym_feed_header($numFeed);
 /* создаем файл или перезаписываем старый удалив содержимое */
 $result = yfym_write_file($result_yml, 'w+', $numFeed);
 if ($result !== true) {
	yfym_error_log('FEED № '.$numFeed.'; yfym_write_file вернула ошибку! $result ='.$result.'; Файл: functions.php; Строка: '.__LINE__, 0);
 } 
 
 yfym_optionUPD('yfym_status_sborki', '-1', $numFeed); 
 $whot_export = yfym_optionGET('yfym_whot_export', $numFeed);

 $result_yml = '';
 $step_export = -1;
 $prod_id_arr = array(); 
 
 if ($whot_export === 'vygruzhat') {
	$args = array(
		'post_type' => 'product',
		'post_status' => 'publish',
		'posts_per_page' => $step_export, // сколько выводить товаров
		// 'offset' => $offset,
		'relation' => 'AND',
		'fields'  => 'ids',
		'meta_query' => array(
			array(
				'key' => 'vygruzhat',
				'value' => 'on'
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

 $args = apply_filters('yfym_query_arg_filter', $args, $numFeed);
 yfym_error_log('FEED № '.$numFeed.'; NOTICE: yfym_onlygluing до запуска WP_Query RAM '.round(memory_get_usage()/1024, 1) . ' Кб; Файл: functions.php; Строка: '.__LINE__, 0); 
 $featured_query = new WP_Query($args);
 yfym_error_log('FEED № '.$numFeed.'; NOTICE: yfym_onlygluing после запуска WP_Query RAM '.round(memory_get_usage()/1024, 1) . ' Кб; Файл: functions.php; Строка: '.__LINE__, 0); 
 
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
	yfym_error_log('FEED № '.$numFeed.'; NOTICE: yfym_onlygluing передала управление yfym_gluing; Файл: functions.php; Строка: '.__LINE__, 0);
	yfym_gluing($prod_id_arr, $numFeed);
 }
 
 // если постов нет, пишем концовку файла
 $result_yml = "</offers>". PHP_EOL; 
 $result_yml = apply_filters('yfym_after_offers_filter', $result_yml, $numFeed);
 $result_yml .= "</shop>". PHP_EOL ."</yml_catalog>";
 /* создаем файл или перезаписываем старый удалив содержимое */
 $result = yfym_write_file($result_yml, 'a', $numFeed);
 yfym_rename_file($numFeed);	 
 // выставляем статус сборки в "готово"
 $status_sborki = -1;
 if ($result == true) {
	yfym_optionGET('yfym_status_sborki', $status_sborki, $numFeed);	
	// останавливаем крон сборки
	wp_clear_scheduled_hook('yfym_cron_sborki');
	do_action('yfym_after_construct', 'cache');
 } else {
	yfym_error_log('FEED № '.$numFeed.'; yfym_write_file вернула ошибку! Я не смог записать концовку файла... $result ='.$result.'; Файл: functions.php; Строка: '.__LINE__, 0);
	do_action('yfym_after_construct', 'false');
 }
} // end function yfym_onlygluing()
/*
* С версии 2.0.0
* Записывает файл логов /wp-content/uploads/yfym/yfym.log
*/
function yfym_error_log($text, $i) {
 // $yfym_keeplogs = yfym_optionGET('yfym_keeplogs');	
 if (yfym_KEEPLOGS !== 'on') {return;}
 $upload_dir = (object)wp_get_upload_dir();
 $name_dir = $upload_dir->basedir."/yfym";
 // подготовим массив для записи в файл логов
 if (is_array($text)) {$r = yfym_array_to_log($text); unset($text); $text = $r;}
 if (is_dir($name_dir)) {
	$filename = $name_dir.'/yfym.log';
	file_put_contents($filename, '['.date('Y-m-d H:i:s').'] '.$text.PHP_EOL, FILE_APPEND);		
 } else {
	if (!mkdir($name_dir)) {
		error_log('Нет папки yfym! И создать не вышло! $name_dir ='.$name_dir.'; Файл: functions.php; Строка: '.__LINE__, 0);
	} else {
		error_log('Создали папку yfym!; Файл: functions.php; Строка: '.__LINE__, 0);
		$filename = $name_dir.'/yfym.log';
		file_put_contents($filename, '['.date('Y-m-d H:i:s').'] '.$text.PHP_EOL, FILE_APPEND);
	}
 } 
 return;
}
/*
* С версии 2.1.0
* Позволяте писать в логи массив /wp-content/uploads/yfym/yfym.log
*/
function yfym_array_to_log($text, $i=0, $res = '') {
 $tab = ''; for ($x = 0; $x<$i; $x++) {$tab = '---'.$tab;}
 if (is_array($text)) { 
  $i++;
  foreach ($text as $key => $value) {
	if (is_array($value)) {	// массив
		$res .= PHP_EOL .$tab."[$key] => ";
		$res .= $tab.yfym_array_to_log($value, $i);
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
* С версии 3.0.0
* получить все атрибуты вукомерца 
*/
function yfym_get_attributes() {
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
// клон для работы старых версий PRO
function get_attributes() {
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
* @since 3.1.0
*
* @param string $numFeed (not require)
*
* @return nothing
* Создает пустой файл ids_in_yml.tmp или очищает уже имеющийся
*/
function yfym_clear_file_ids_in_yml($numFeed = '1') {
	$yfym_file_ids_in_yml = urldecode(yfym_optionGET('yfym_file_ids_in_yml', $numFeed));
	if (!is_file($yfym_file_ids_in_yml)) {
		yfym_error_log('FEED № '.$numFeed.'; WARNING: Файла c idшниками $yfym_file_ids_in_yml = '.$yfym_file_ids_in_yml.' нет! Создадим пустой; Файл: function.php; Строка: '.__LINE__, 0);
		$yfym_file_ids_in_yml = yfym_NAME_DIR .'/feed'.$numFeed.'/ids_in_yml.tmp';		
		$res = file_put_contents($yfym_file_ids_in_yml, '');
		if ($res !== false) {
			yfym_error_log('FEED № '.$numFeed.'; NOTICE: Файл c idшниками $yfym_file_ids_in_yml = '.$yfym_file_ids_in_yml.' успешно создан; Файл: function.php; Строка: '.__LINE__, 0);
			yfym_optionUPD('yfym_file_ids_in_yml', urlencode($yfym_file_ids_in_yml), $numFeed);
		} else {
			yfym_error_log('FEED № '.$numFeed.'; ERROR: Ошибка создания файла $yfym_file_ids_in_yml = '.$yfym_file_ids_in_yml.'; Файл: function.php; Строка: '.__LINE__, 0);
		}
	} else {
		yfym_error_log('FEED № '.$numFeed.'; NOTICE: Обнуляем файл $yfym_file_ids_in_yml = '.$yfym_file_ids_in_yml.'; Файл: function.php; Строка: '.__LINE__, 0);
		file_put_contents($yfym_file_ids_in_yml, '');
	}
}
/*
* @since 3.2.1
*
* @return nothing
* Обновляет настройки плагина
* Updates plugin settings
*/
function yfym_set_new_options() {
	wp_clean_plugins_cache();
	wp_clean_update_cache();
	add_filter('pre_site_transient_update_plugins', '__return_null');
	wp_update_plugins();
	remove_filter('pre_site_transient_update_plugins', '__return_null');
		
	$numFeed = '1'; // (string)
	if (!defined('yfym_ALLNUMFEED')) {define('yfym_ALLNUMFEED', '5');}
	$allNumFeed = (int)yfym_ALLNUMFEED;
	for ($i = 1; $i<$allNumFeed+1; $i++) {
		if (yfym_optionGET('yfym_delivery_options2', $numFeed) === false) {yfym_optionUPD('yfym_delivery_options2', '0', $numFeed);}
		if (yfym_optionGET('yfym_delivery_cost2', $numFeed) === false) {yfym_optionUPD('yfym_delivery_cost2', '0', $numFeed);}
		if (yfym_optionGET('yfym_delivery_days2', $numFeed) === false) {yfym_optionUPD('yfym_delivery_days2', '32', $numFeed);}
		if (yfym_optionGET('yfym_order_before2', $numFeed) === false) {yfym_optionUPD('yfym_order_before2', '', $numFeed);}
		if (yfym_optionGET('yfym_vat', $numFeed) === false) {yfym_optionUPD('yfym_vat', 'disabled', $numFeed);}
		if (yfym_optionGET('yfym_clear_get', $numFeed) === false) {yfym_optionUPD('yfym_clear_get', 'no', $numFeed);}
		if (yfym_optionGET('yfym_feed_assignment', $numFeed) === false) {yfym_optionUPD('yfym_feed_assignment', '', $numFeed);}	
		if (yfym_optionGET('yfym_shop_sku', $numFeed) === false) {yfym_optionUPD('yfym_shop_sku', 'disabled', $numFeed);}
		if (yfym_optionGET('yfym_manufacturer', $numFeed) === false) {yfym_optionUPD('yfym_manufacturer', 'disabled', $numFeed);}
		if (yfym_optionGET('yfym_yml_rules', $numFeed) === false) {yfym_optionUPD('yfym_yml_rules', 'yandex_market', $numFeed);}
		if (yfym_optionGET('yfym_behavior_onbackorder', $numFeed) === false) {yfym_optionUPD('yfym_behavior_onbackorder', 'false', $numFeed);}	
		if (yfym_optionGET('yfym_count', $numFeed) === false) {yfym_optionUPD('yfym_count', 'disabled', $numFeed);}	
		if (yfym_optionGET('yfym_the_content', $numFeed) === false) {yfym_optionUPD('yfym_the_content', 'enabled', $numFeed);}
		if (yfym_optionGET('yfym_var_desc_priority', $numFeed) === false) {yfym_optionUPD('yfym_var_desc_priority', 'on', $numFeed);}
		if (yfym_optionGET('yfym_amount', $numFeed) === false) {yfym_optionUPD('yfym_amount', 'disabled', $numFeed);}	
		if (yfym_optionGET('yfym_behavior_stip_symbol', $numFeed) === false) {yfym_optionUPD('yfym_behavior_stip_symbol', 'default', $numFeed);}
		if (yfym_optionGET('yfym_market_sku_status', $numFeed) === false) {yfym_optionUPD('yfym_market_sku_status', 'disabled', $numFeed);}	
		if (yfym_optionGET('yfym_file_extension', $numFeed) === false) {yfym_optionUPD('yfym_file_extension', 'xml', $numFeed, 'no');}	
		if (yfym_optionGET('yfym_wooc_currencies', $numFeed) === false) {yfym_optionUPD('yfym_wooc_currencies', '', $numFeed);}	
		if (yfym_optionGET('yfym_auto_disabled', $numFeed) === false) {yfym_optionUPD('yfym_auto_disabled', 'disabled', $numFeed);}	
		if (yfym_optionGET('yfym_barcode_post_meta', $numFeed) === false) {yfym_optionUPD('yfym_barcode_post_meta', '', $numFeed);}			
		$numFeed++;
	}
	if (defined('yfym_VER')) {
		if (is_multisite()) {
			update_blog_option(get_current_blog_id(), 'yfym_version', yfym_VER);
		} else {
			update_option('yfym_version', yfym_VER);
		}
	}
}
/*
* @since 3.3.0
*
* @return formatted string
*/
function yfym_formatSize($bytes) {
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
	$bytes = '0 KB';
 }
 return $bytes;
}
/*
* @since 3.3.13
*
* @return formatted string
*/
function yfym_replace_symbol($string, $numFeed = '1') {
 $yfym_behavior_stip_symbol = yfym_optionGET('yfym_behavior_stip_symbol', $numFeed);	
 switch ($yfym_behavior_stip_symbol) {
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
* @since 3.3.16
*
* @return formatted string
*/
function yfym_replace_decode($string, $numFeed = '1') {
 $string = str_replace("+", 'yfym', $string);
 //$string = str_replace(";", 'yfymtz', $string);
 $string = urldecode($string);
 $string = str_replace("yfym", '+', $string);
 //$string = str_replace("yfymtz", ';', $string);
 $string = apply_filters('yfym_replace_decode_filter', $string, $numFeed);
 return $string;
}
/*
* @since 3.3.21
*
* @return array
*/
function yfym_possible_problems_list() {
 $possibleProblems = ''; $possibleProblemsCount = 0; $conflictWithPlugins = 0; $conflictWithPluginsList = ''; 
 $check_global_attr_count = wc_get_attribute_taxonomies();
 if (count($check_global_attr_count) < 1) {
	$possibleProblemsCount++;
	$possibleProblems .= '<li>'. __('Your site has no global attributes! This may affect the quality of the YML feed. This can also cause difficulties when setting up the plugin', 'yfym'). '. <a href="https://icopydoc.ru/globalnyj-i-lokalnyj-atributy-v-woocommerce/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=debug-page&utm_term=possible-problems">'. __('Please read the recommendations', 'yfym'). '</a>.</li>';
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
	$possibleProblems .= '<li><p>'. __('Most likely, these plugins negatively affect the operation of', 'yfym'). ' YML for Yandex Market:</p>'.$conflictWithPluginsList.'<p>'. __('If you are a developer of one of the plugins from the list above, please contact me', 'yfym').': <a href="mailto:support@icopydoc.ru">support@icopydoc.ru</a>.</p></li>';
 }
 return array($possibleProblems, $possibleProblemsCount, $conflictWithPlugins, $conflictWithPluginsList);
}
/*
* @since 3.4.0
*
* @param string $array (require)
* @param string/int $key (require)
* @param string/int $default_data (not require)
*
* @return any
*/
function yfym_data_from_arr($array, $key, $default_data = '') {
 if (isset($array[$key])) {return $array[$key];} else {return $default_data;}
}
/*
* @since 3.4.0
*
* @param array $field (require)
*
* Function based woocommerce_wp_select
* https://stackoverflow.com/questions/23287358/woocommerce-multi-select-for-single-product-field
*/
function yfym_woocommerce_wp_select_multiple($field, $blog_option = false) {
 if ($blog_option === false) {
	global $thepostid, $post, $woocommerce;
	$thepostid				= empty( $thepostid ) ? $post->ID : $thepostid;
	$field['value']			= isset( $field['value'] ) ? $field['value'] : ( get_post_meta( $thepostid, $field['id'], true ) ? get_post_meta( $thepostid, $field['id'], true ) : array() );
 } else { // если у нас глобальные настройки, а не метаполя, то данные тащим через yfym_optionGET
	global $woocommerce;
	$field['value']			= isset( $field['value'] ) ? $field['value'] : ( yfym_optionGET($field['id']) ? yfym_optionGET($field['id']) : array() );
 }

 $field['class']			= isset( $field['class'] ) ? $field['class'] : 'select short';
 $field['wrapper_class']	= isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
 $field['name']				= isset( $field['name'] ) ? $field['name'] : $field['id'];
 $field['label']			= isset( $field['label'] ) ? $field['label'] : '';
  
 echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '[]" class="' . esc_attr( $field['class'] ) . '" multiple="multiple">';
  
 foreach ($field['options'] as $key => $value) {
	echo '<option value="' . esc_attr( $key ) . '" ' . ( in_array( $key, $field['value'] ) ? 'selected="selected"' : '' ) . '>' . esc_html( $value ) . '</option>';
 }
 
 echo '</select> ';
  
 if (!empty($field['description'])) { 
	if (isset($field['desc_tip']) && false !== $field['desc_tip']) {
		echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
	} else {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}
 }

 echo '</p>';
}
/*
* @since 3.5.5
*
* @param string $dir (require)
*
* @return nothing
*/
function yfym_remove_directory($dir) {
	if ($objs = glob($dir."/*")) {
		foreach($objs as $obj) {
			is_dir($obj) ? yfym_remove_directory($obj) : unlink($obj);
		}
	}
	rmdir($dir);
}
/*
* @since 3.5.5
*
* @return int
* Возвращает количетсво всех фидов
*/
function yfym_number_all_feeds() {
	$yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
	if ($yfym_settings_arr === false) {
		return -1;
	} else {
		return count($yfym_settings_arr);
	}
}
?>