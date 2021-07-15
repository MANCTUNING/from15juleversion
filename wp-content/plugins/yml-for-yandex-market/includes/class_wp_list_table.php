<?php // https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html https://wp-kama.ru/function/wp_list_table
class YmlforYandexMarket_WP_List_Table extends WP_List_Table {
 /*	Метод get_columns() необходим для маркировки столбцов внизу и вверху таблицы. 
 *	Ключи в массиве должны быть теми же, что и в массиве данных, 
 *	иначе соответствующие столбцы не будут отображены.
 */
 function get_columns() {
	$columns = array(
		'cb'				=> '<input type="checkbox" />', // флажок сортировки. см get_bulk_actions и column_cb
		'ID'				=> __('Feed ID', 'yfym'),
		'yfym_url_yml_file'	=> __('YML File', 'yfym'),
		'yfym_run_cron'		=> __('Start automatic export', 'yfym'),
		'yfym_step_export'	=> __('Step of export', 'yfym'),
	);
	return $columns;
 }
 /*	
 *	Метод вытаскивает из БД данные, которые будут лежать в таблице
 *	$this->table_data();
 */
 private function table_data() {
	$yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
	$result_arr = array();
	$yfym_settings_arr_keys_arr = array_keys($yfym_settings_arr);
	for ($i = 0; $i < count($yfym_settings_arr_keys_arr); $i++) {
		$key = (int)$yfym_settings_arr_keys_arr[$i];
		$yfym_status_cron = $yfym_settings_arr[$key]['yfym_status_cron'];
		switch($yfym_status_cron) {
			case 'off': $text = __("Don't start", "yfym"); break;
			case 'five_min':  $text = __('Every five minutes', 'yfym'); break;
			case 'hourly':  $text = __('Hourly', 'yfym'); break;
			case 'six_hours':  $text = __('Every six hours', 'yfym'); break;
			case 'twicedaily':  $text = __('Twice a day', 'yfym'); break;
			case 'daily':  $text = __('Daily', 'yfym'); break;
			default: $text = __("Don't start", "yfym"); 
		}
		$result_arr[$i] = array(
			'ID' => $key,
			'yfym_url_yml_file' => $yfym_settings_arr[$key]['yfym_url_yml_file'],
			'yfym_run_cron' => $text,
			'yfym_step_export' => $yfym_settings_arr[$key]['yfym_step_export'].' '. __('sec', 'yfym')
		);
	}

	$numFeed = '1';
	$yfym_file_url = urldecode(yfym_optionGET('yfym_file_url', $numFeed));
	$yfym_status_cron = yfym_optionGET('yfym_status_cron', $numFeed);

	for ($i = 1; $i < (int)yfym_ALLNUMFEED + 1; $i++) {
		$result_arr[$i] = array(
			'ID' => $i,
			'yfym_url_yml_file' => $yfym_file_url,
			'yfym_run_cron' => $yfym_status_cron,
			'yfym_step_export' => ' '. __('sec', 'yfym')
		);
	}

	return $result_arr;
 }
 /*
 *	prepare_items определяет два массива, управляющие работой таблицы:
 *	$hidden определяет скрытые столбцы https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html#screen-options
 *	$sortable определяет, может ли таблица быть отсортирована по этому столбцу.
 *
 */
 function prepare_items() {
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = $this->get_sortable_columns(); // вызов сортировки
	$this->_column_headers = array($columns, $hidden, $sortable);
	// пагинация 
	$per_page = 5;
	$current_page = $this->get_pagenum();
	$total_items = count($this->table_data());
	$found_data = array_slice($this->table_data(), (($current_page - 1) * $per_page), $per_page);
	$this->set_pagination_args(array(
		'total_items' => $total_items, // Мы должны вычислить общее количество элементов
		'per_page'	  => $per_page // Мы должны определить, сколько элементов отображается на странице
	));
	// end пагинация 
	$this->items = $found_data; // $this->items = $this->table_data() // Получаем данные для формирования таблицы
 }
 /*
 * 	Данные таблицы.
 *	Наконец, метод назначает данные из примера на переменную представления данных класса — items.
 *	Прежде чем отобразить каждый столбец, WordPress ищет методы типа column_{key_name}, например, function column_yfym_url_yml_file. 
 *	Такой метод должен быть указан для каждого столбца. Но чтобы не создавать эти методы для всех столбцов в отдельности, 
 *	можно использовать column_default. Эта функция обработает все столбцы, для которых не определён специальный метод:
 */ 
 function column_default($item, $column_name) {
	switch( $column_name ) {
		case 'ID':
		case 'yfym_url_yml_file':
		case 'yfym_run_cron':
		case 'yfym_step_export':
			return $item[ $column_name ];
		default:
			return print_r( $item, true ) ; //Мы отображаем целый массив во избежание проблем
	}
 }
 /*
 * 	Функция сортировки.
 *	Второй параметр в массиве значений $sortable_columns отвечает за порядок сортировки столбца. 
 *	Если значение true, столбец будет сортироваться в порядке возрастания, если значение false, столбец сортируется в порядке 
 *	убывания, или не упорядочивается. Это необходимо для маленького треугольника около названия столбца, который указывает порядок
 *	сортировки, чтобы строки отображались в правильном направлении
 */
 function get_sortable_columns() {
	$sortable_columns = array(
		'yfym_url_yml_file'	=> array('yfym_url_yml_file', false),
		// 'yfym_run_cron'		=> array('yfym_run_cron', false)
	);
	return $sortable_columns;
  }
 /*
 * 	Действия.
 *	Эти действия появятся, если пользователь проведет курсор мыши над таблицей
 *	column_{key_name} - в данном случае для колонки yfym_url_yml_file - function column_yfym_url_yml_file
 */
 function column_yfym_url_yml_file($item) {
	if ($item['ID'] === 0) {
		$actions = array(
			'edit'		=> sprintf('<a href="?page=%s&action=%s&numFeed=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['ID'])
		);
	} else {
		$actions = array(
			'edit'		=> sprintf('<a href="?page=%s&action=%s&numFeed=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['ID']),
			'delete'	=> sprintf('<a href="?page=%s&action=%s&numFeed=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['ID']),
		);
	}
	return sprintf('%1$s %2$s', $item['yfym_url_yml_file'], $this->row_actions($actions) );
 }
 /*
 * 	Массовые действия.
 *	Bulk action осуществляются посредством переписывания метода get_bulk_actions() и возврата связанного массива
 *	Этот код просто помещает выпадающее меню и кнопку «применить» вверху и внизу таблицы
 *	ВАЖНО! Чтобы работало нужно оборачивать вызов класса в form:
 *	<form id="events-filter" method="get"> 
 *	<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /> 
 *	<?php $wp_list_table->display(); ?> 
 *	</form> 
 */
 function get_bulk_actions() {
	$actions = array(
		'delete'	=> __('Delete', 'yfym')
	);
	return $actions;
 }
 // Флажки для строк должны быть определены отдельно. Как упоминалось выше, есть метод column_{column} для отображения столбца. cb-столбец – особый случай:
 function column_cb($item) {
	if ($item['ID'] === 0) {
		return sprintf(
			'<input type="checkbox" name="checkbox_yml_file[]" value="%s" disabled />', $item['ID']
		);
	 } else {
		return sprintf(
			'<input type="checkbox" name="checkbox_yml_file[]" value="%s" />', $item['ID']
		);
	}
 }
 /*
 * Нет элементов.
 * Если в списке нет никаких элементов, отображается стандартное сообщение «No items found.». Если вы хотите изменить это сообщение, вы можете переписать метод no_items():
 */
 function no_items() {
	_e('No YML feed found', 'yfym');
 }
}
?>