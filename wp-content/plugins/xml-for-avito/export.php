<?php if (!defined('ABSPATH')) {exit;} // Защита от прямого вызова скрипта
function xfavi_export_page() { 
 $numFeed = '1'; // (string)
 if (isset($_REQUEST['xfavi_submit_send_select_feed'])) {
  if (!empty($_POST) && check_admin_referer('xfavi_nonce_action_send_select_feed', 'xfavi_nonce_field_send_select_feed')) {
	$numFeed = $_POST['xfavi_num_feed'];
  } 
 }

 $status_sborki = (int)xfavi_optionGET('xfavi_status_sborki', $numFeed);
 if (isset($_REQUEST['xfavi_submit_action'])) {
  if (!empty($_POST) && check_admin_referer('xfavi_nonce_action', 'xfavi_nonce_field')) {
	do_action('xfavi_prepend_submit_action', $numFeed);  
	
	$numFeed = sanitize_text_field($_POST['xfavi_num_feed_for_save']);
	
	$unixtime = current_time('timestamp', 1); // 1335808087 - временная зона GMT (Unix формат)
	xfavi_optionUPD('xfavi_date_save_set', $unixtime, $numFeed);

	if (isset($_POST['xfavi_ufup'])) {
		xfavi_optionUPD('xfavi_ufup', sanitize_text_field($_POST['xfavi_ufup']), $numFeed);
	} else {
		xfavi_optionUPD('xfavi_ufup', '0', $numFeed);
	} 
	xfavi_optionUPD('xfavi_whot_export', sanitize_text_field($_POST['xfavi_whot_export']), $numFeed);
	xfavi_optionUPD('xfavi_feed_assignment', sanitize_text_field($_POST['xfavi_feed_assignment']), $numFeed);
	xfavi_optionUPD('xfavi_allowEmail', sanitize_text_field($_POST['xfavi_allowEmail']), $numFeed);
	xfavi_optionUPD('xfavi_managerName', sanitize_text_field($_POST['xfavi_managerName']), $numFeed);
	xfavi_optionUPD('xfavi_contactPhone', sanitize_text_field($_POST['xfavi_contactPhone']), $numFeed);
	xfavi_optionUPD('xfavi_address', sanitize_text_field($_POST['xfavi_address']), $numFeed);

	xfavi_optionUPD('xfavi_desc', sanitize_text_field($_POST['xfavi_desc']), $numFeed);
	xfavi_optionUPD('xfavi_the_content', sanitize_text_field($_POST['xfavi_the_content']), $numFeed);
	xfavi_optionUPD('xfavi_behavior_strip_symbol', sanitize_text_field($_POST['xfavi_behavior_strip_symbol']), $numFeed);
	if (isset($_POST['xfavi_var_desc_priority'])) {
		xfavi_optionUPD('xfavi_var_desc_priority', sanitize_text_field($_POST['xfavi_var_desc_priority']), $numFeed);
	} else {
		xfavi_optionUPD('xfavi_var_desc_priority', '0', $numFeed);
	}
	xfavi_optionUPD('xfavi_main_product', sanitize_text_field($_POST['xfavi_main_product']), $numFeed);
	if (isset($_POST['xfavi_no_default_png_products'])) {
		xfavi_optionUPD('xfavi_no_default_png_products', sanitize_text_field($_POST['xfavi_no_default_png_products']), $numFeed);
	} else {
		xfavi_optionUPD('xfavi_no_default_png_products', '0', $numFeed);
	}
	if (isset($_POST['xfavi_skip_products_without_pic'])) {
		xfavi_optionUPD('xfavi_skip_products_without_pic', sanitize_text_field($_POST['xfavi_skip_products_without_pic']), $numFeed);
	} else {
		xfavi_optionUPD('xfavi_skip_products_without_pic', '0', $numFeed);
	}
	if (isset($_POST['xfavi_skip_missing_products'])) {
		xfavi_optionUPD('xfavi_skip_missing_products', sanitize_text_field($_POST['xfavi_skip_missing_products']), $numFeed);
	} else {
		xfavi_optionUPD('xfavi_skip_missing_products', '0', $numFeed);
	}	
	if (isset($_POST['xfavi_skip_backorders_products'])) {
		xfavi_optionUPD('xfavi_skip_backorders_products', sanitize_text_field($_POST['xfavi_skip_backorders_products']), $numFeed);
	} else {
		xfavi_optionUPD('xfavi_skip_backorders_products', '0', $numFeed);
	}
	xfavi_optionUPD('xfavi_size', sanitize_text_field($_POST['xfavi_size']), $numFeed);
	xfavi_optionUPD('xfavi_condition', sanitize_text_field($_POST['xfavi_condition']), $numFeed);

	xfavi_optionUPD('xfavi_step_export', sanitize_text_field($_POST['xfavi_step_export']), $numFeed);	
	$arr_maybe = array("off", "five_min", "hourly", "six_hours", "twicedaily", "daily");
	$xfavi_run_cron = sanitize_text_field($_POST['xfavi_run_cron']);
	if (in_array($xfavi_run_cron, $arr_maybe)) {		
		xfavi_optionUPD('xfavi_status_cron', $xfavi_run_cron, $numFeed);
		if ($xfavi_run_cron === 'off') {
			// отключаем крон
			wp_clear_scheduled_hook('xfavi_cron_period', array($numFeed));
			xfavi_optionUPD('xfavi_status_cron', 'off', $numFeed);
			
			wp_clear_scheduled_hook('xfavi_cron_sborki', array($numFeed));
			xfavi_optionUPD('xfavi_status_sborki', '-1', $numFeed);
		} else {
			$recurrence = $xfavi_run_cron;
			wp_clear_scheduled_hook('xfavi_cron_period', array($numFeed));
			wp_schedule_event(time(), $recurrence, 'xfavi_cron_period', array($numFeed));
			xfavi_error_log('FEED № '.$numFeed.'; xfavi_cron_period внесен в список заданий; Файл: export.php; Строка: '.__LINE__, 0);
		}
	} else {
		xfavi_error_log('Крон '.$xfavi_run_cron.' не зарегистрирован. Файл: export.php; Строка: '.__LINE__, 0);
	}
  }
 } 

 $xfavi_status_cron = xfavi_optionGET('xfavi_status_cron', $numFeed);
 $xfavi_ufup = xfavi_optionGET('xfavi_ufup', $numFeed);
 $xfavi_whot_export = xfavi_optionGET('xfavi_whot_export', $numFeed); 
 $xfavi_feed_assignment = xfavi_optionGET('xfavi_feed_assignment', $numFeed); 
 $xfavi_desc = xfavi_optionGET('xfavi_desc', $numFeed);
 $xfavi_the_content = xfavi_optionGET('xfavi_the_content', $numFeed);
 $xfavi_behavior_strip_symbol = xfavi_optionGET('xfavi_behavior_strip_symbol', $numFeed);
 $xfavi_var_desc_priority = xfavi_optionGET('xfavi_var_desc_priority', $numFeed);
 
 $xfavi_allowEmail = stripslashes(htmlspecialchars(xfavi_optionGET('xfavi_allowEmail', $numFeed))); 
 $xfavi_managerName = stripslashes(htmlspecialchars(xfavi_optionGET('xfavi_managerName', $numFeed)));
 $xfavi_contactPhone = stripslashes(htmlspecialchars(xfavi_optionGET('xfavi_contactPhone', $numFeed)));

 $xfavi_address = stripslashes(htmlspecialchars(xfavi_optionGET('xfavi_address', $numFeed)));

 $xfavi_main_product = xfavi_optionGET('xfavi_main_product', $numFeed);
 $xfavi_step_export = xfavi_optionGET('xfavi_step_export', $numFeed);
 $xfavi_no_default_png_products = xfavi_optionGET('xfavi_no_default_png_products', $numFeed);
 $xfavi_skip_products_without_pic = xfavi_optionGET('xfavi_skip_products_without_pic', $numFeed);
 $xfavi_skip_missing_products = xfavi_optionGET('xfavi_skip_missing_products', $numFeed); 
 $xfavi_skip_backorders_products = xfavi_optionGET('xfavi_skip_backorders_products', $numFeed); 

 $xfavi_size = xfavi_optionGET('xfavi_size', $numFeed);
 $xfavi_condition = xfavi_optionGET('xfavi_condition', $numFeed);
 
 $xfavi_file_url = urldecode(xfavi_optionGET('xfavi_file_url', $numFeed));
 $xfavi_date_sborki = xfavi_optionGET('xfavi_date_sborki', $numFeed);
?>
<div class="wrap">
 <h1><?php _e('Экспорт Avito', 'xfavi'); ?></h1>
 <div class="notice notice-info">
  <p><span class="xfavi_bold">XML for Avito Pro</span> - <?php _e('необходимое расширение для тех, кто хочет', 'xfavi'); ?> <span class="xfavi_bold" style="color: green;"><?php _e('экономить рекламный бюджет', 'xfavi'); ?></span> <?php _e('на Avito', 'xfavi'); ?>! <a href="https://icopydoc.ru/product/xml-for-avito-pro/?utm_source=xml-for-avito&utm_medium=organic&utm_campaign=in-plugin-xml-for-avito&utm_content=settings&utm_term=about-xml-pro"><?php _e('Подробнее', 'xfavi'); ?></a>.</p>
 </div>
 <?php do_action('xfavi_before_poststuff', $numFeed); ?>
 <div id="poststuff"><div id="post-body" class="columns-2">
  <div id="postbox-container-1" class="postbox-container"><div class="meta-box-sortables">
  	<?php do_action('xfavi_prepend_container_1', $numFeed); ?>
	<div class="postbox"> 
	 <div class="inside">	
	  <p style="text-align: center;"><strong style="color: green;"><?php _e('Инструкция', 'xfavi'); ?>:</strong> <a href="https://icopydoc.ru/kak-sozdat-fid-dlya-avito-instruktsiya/?utm_source=xml-for-avito&utm_medium=organic&utm_campaign=in-plugin-xml-for-avito&utm_content=settings&utm_term=main-instruction" target="_blank"><?php _e('Как создать XML-фид', 'xfavi'); ?></a>.</p>
	  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">	
		<select style="width: 100%" name="xfavi_num_feed" id="xfavi_num_feed">
			<?php if (is_multisite()) {$cur_blog_id = get_current_blog_id();} else {$cur_blog_id = '0';}		
			$allNumFeed = (int)xfavi_ALLNUMFEED; $ii = '1';
			for ($i = 1; $i<$allNumFeed+1; $i++) : ?>
			<option value="<?php echo $i; ?>" <?php selected($numFeed, $i); ?>><?php _e('Фид', 'xfavi'); ?> <?php echo $i; ?>: feed-avito-<?php echo $cur_blog_id; ?>.xml <?php $assignment = xfavi_optionGET('xfavi_feed_assignment', $ii); if ($assignment === '') {} else {echo '('.$assignment.')';} ?></option>
			<?php $ii++; endfor; ?>
		</select>
		<?php wp_nonce_field('xfavi_nonce_action_send_select_feed', 'xfavi_nonce_field_send_select_feed'); ?>
		<input style="width: 100%; margin: 10px 0 10px 0;" class="button" type="submit" name="xfavi_submit_send_select_feed" value="<?php _e('Выбрать фид', 'xfavi'); ?>" />
	  </form>
  	 </div>
	</div>
	<?php do_action('xfavi_before_support_project'); ?>
	<div class="postbox">
	 <h2 class="hndle"><?php _e('Пожалуйста, поддержите проект', 'xfavi'); ?>!</h2>
	 <div class="inside">	  
		<p><?php _e('Спасибо за использование плагина', 'xfavi'); ?> <strong>XML for Avito</strong></p>
		<p><?php _e('Пожалуйста, помогите сделать плагин лучше', 'xfavi'); ?> <a href="https://forms.gle/rtdDcK94C9tuqdbw5" target="_blank" ><?php _e('ответив на 6 вопросов', 'xfavi'); ?>!</a></p>
		<p><?php _e('Если этот плагин полезен вам, пожалуйста, поддержите проект', 'xfavi'); ?>:</p>
		<ul class="xfavi_ul">
			<li><a href="https://wordpress.org/support/plugin/xml-for-avito/reviews/" target="_blank"><?php _e('Оставьте комментарий на странице плагина', 'xfavi'); ?></a>.</li>
			<li><?php _e('Поддержите проект деньгами', 'xfavi'); ?>. <a href="https://sobe.ru/na/xml_for_avito" target="_blank"> <?php _e('Поддержать проект', 'xfavi'); ?></a>.</li>
			<li><?php _e('Заметили ошибку или есть идея как улучшить качество плагина', 'xfavi'); ?>? <a href="mailto:support@icopydoc.ru"><?php _e('Напишите мне', 'xfavi'); ?></a>.</li>
		</ul>
		<p><?php _e('С уважением, Максим Глазунов', 'xfavi'); ?>.</p>
		<p><span style="color: red;"><?php _e('Принимаю заказы на индивидуальные доработки плагина', 'xfavi'); ?></span>:<br /><a href="mailto:support@icopydoc.ru"><?php _e('Оставить заявку', 'xfavi'); ?></a>.</p>
	  </div>
	</div>		
	<?php do_action('xfavi_between_container_1', $numFeed); ?>
	<div class="postbox">
	<h2 class="hndle"><?php _e('Отправить данные о работе плагина', 'xfavi'); ?></h2>
	  <div class="inside">
		<p><?php _e('Отправляя статистику, вы помогаете сделать плагин еще лучше', 'xfavi'); ?>! <?php _e('Следующие данные будут переданы', 'xfavi'); ?>:</p>
		<ul class="xfavi_ul">
			<li><?php _e('УРЛ XML-фида', 'xfavi'); ?>;</li>
			<li><?php _e('Статус генерации фида', 'xfavi'); ?>;</li>
			<li><?php _e('Включен ли режим multisite', 'xfavi'); ?>?</li>
		</ul>
		<p><?php _e('Помог ли Вам плагин загрузить продукцию на', 'xfavi'); ?> Avito?</p>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
		 <input type="hidden" name="xfavi_num_feed_for_save" value="<?php echo $numFeed; ?>">
		 <p>
			<input type="radio" name="xfavi_its_ok" value="yes"><?php _e('Да', 'xfavi'); ?><br />
			<input type="radio" name="xfavi_its_ok" value="no"><?php _e('Нет', 'xfavi'); ?>
		 </p>
		 <p><?php _e("Если вы не возражаете, чтобы с Вами связались в случае возникновения дополнительных вопросов по поводу работы плагина, то укажите Ваш адрес электронной почты", "xfavi"); ?>. <span class="xfavi_bold"><?php _e('И если вы хотите получить ответ, не забудьте указать свой адрес электронной почты', 'xfavi'); ?></span>.</p>
		 <p><input type="email" name="xfavi_email"></p>
		 <p><?php _e("Ваше сообщение", "xfavi"); ?>:</p>
		 <p><textarea rows="6" cols="32" name="xfavi_message" placeholder="<?php _e('Введите текст, чтобы отправить мне сообщение (Вы можете написать мне на русском или английском языке). Я проверяю свою электронную почту несколько раз в день', 'xfavi'); ?>"></textarea></p>
		 <?php wp_nonce_field('xfavi_nonce_action_send_stat', 'xfavi_nonce_field_send_stat'); ?><input class="button-primary" type="submit" name="xfavi_submit_send_stat" value="<?php _e('Отправить данные', 'xfavi'); ?>" />
	  </form>
	  </div>
	</div>
	<?php do_action('xfavi_append_container_1', $numFeed); ?>
  </div></div>

  <div id="postbox-container-2" class="postbox-container"><div class="meta-box-sortables">
  	<?php do_action('xfavi_prepend_container_2', $numFeed); ?>
	  <div class="postbox">
	 <h2 class="hndle"><?php _e('Фид', 'xfavi'); ?> <?php echo $numFeed; ?>: <?php if ($numFeed !== '1') {echo $numFeed;} ?>feed-avito-<?php echo $cur_blog_id; ?>.xml <?php $assignment = xfavi_optionGET('xfavi_feed_assignment', $numFeed); if ($assignment === '') {} else {echo '('.$assignment.')';} ?> <?php if (empty($xfavi_file_url)) : ?><?php _e('еще не создавался', 'xfavi'); ?><?php else : ?><?php if ($status_sborki !== -1) : ?><?php _e('обновляется', 'xfavi'); ?><?php else : ?><?php _e('создан', 'xfavi'); ?><?php endif; ?><?php endif; ?></h2>	
	 <div class="inside">
		<?php if (empty($xfavi_file_url)) : ?> 
			<?php if ($status_sborki !== -1) : ?>
				<p><?php _e('Идет автоматическое создание файла. XML-фид в скором времени будет создан', 'xfavi'); ?>.</p>
			<?php else : ?>
				<p><span class="xfavi_bold"><?php _e('Перейдите в "Товары" -> "Категории". Отредактируйте имющиеся у вас на сайте категории выбрав соответсвующие значения напротив пунктов: "Обрабатывать согласно правилам Авито", "Авито Category" и "Авито GoodsType"', 'xfavi'); ?>.</span></p>
				<p><?php _e('После вернитесь на данную страницу и в поле "Автоматическое создание файла" выставите значение, отличное от значения "отключено", при необходимости измените значение других полей и нажмите "Сохранить"', 'xfavi'); ?>.</p>
				<p><?php _e('Через 1 - 7 минут (зависит от числа товаров), фид будет сгенерирован и вместо данного сообщения появится ссылка', 'xfavi'); ?>.</p>
			<?php endif; ?>
		<?php else : ?>
			<?php if ($status_sborki !== -1) : ?>
				<p><?php _e('Идет автоматическое создание файла. XML-фид в скором времени будет создан', 'xfavi'); ?>.</p>
			<?php else : ?>
				<p><strong><?php _e('Ваш XML фид здесь', 'xfavi'); ?>:</strong><br/><a target="_blank" href="<?php echo $xfavi_file_url; ?>"><?php echo $xfavi_file_url; ?></a>
				<br/><?php _e('Размер файла', 'xfavi'); ?>: <?php clearstatcache();
				if ($numFeed === '1') {$prefFeed = '';} else {$prefFeed = $numFeed;}
				$upload_dir = (object)wp_get_upload_dir();
				if (is_multisite()) {
					$filename = $upload_dir->basedir."/xml-for-avito/".$prefFeed."feed-avito-".get_current_blog_id().".xml";
				} else {
					$filename = $upload_dir->basedir."/xml-for-avito/".$prefFeed."feed-avito-0.xml";				
				}
				if (is_file($filename)) {echo xfavi_formatSize(filesize($filename));} else {echo '0 KB';} ?>
				<br/><?php _e('Сгенерирован', 'xfavi'); ?>: <?php echo $xfavi_date_sborki; ?></p>
			<?php endif; ?>		
		<?php endif; ?>
		<p><?php _e('Обратите внимание, что Avito проверяет XML не более 3 раз в день! Это означает, что изменения на Avito не являются мгновенными', 'xfavi'); ?>!</p>
	  </div>
	</div>	  
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
	 <?php do_action('xfavi_prepend_form_container_2', $numFeed); ?>
	 <input type="hidden" name="xfavi_num_feed_for_save" value="<?php echo $numFeed; ?>">
	 <div class="postbox">
	  <h2 class="hndle"><?php _e('Основные параметры', 'xfavi'); ?></h2>
	   <div class="inside">	    
		<table class="form-table"><tbody>
		<tr>
			<th scope="row"><label for="xfavi_run_cron"><?php _e('Автоматическое создание файла', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<select name="xfavi_run_cron" id="xfavi_run_cron">
					<option value="off" <?php selected($xfavi_status_cron, 'off'); ?>><?php _e('Отключено', 'xfavi'); ?></option>
					<?php $xfavi_enable_five_min = xfavi_optionGET('xfavi_enable_five_min'); if ($xfavi_enable_five_min === 'on') : ?>
					<option value="five_min" <?php selected($xfavi_status_cron, 'five_min' );?> ><?php _e('Каждые пять минут', 'xfavi'); ?></option>
					<?php endif; ?>
					<option value="hourly" <?php selected($xfavi_status_cron, 'hourly' );?> ><?php _e('Раз в час', 'xfavi'); ?></option>
					<option value="six_hours" <?php selected($xfavi_status_cron, 'six_hours' ); ?> ><?php _e('Каждые 6 часов', 'xfavi'); ?></option>
					<option value="twicedaily" <?php selected($xfavi_status_cron, 'twicedaily' );?> ><?php _e('2 раза в день', 'xfavi'); ?></option>
					<option value="daily" <?php selected($xfavi_status_cron, 'daily' );?> ><?php _e('Раз в день', 'xfavi'); ?></option>
				</select><br />
				<span class="description"><?php _e('Интервал обновления вашего фида', 'xfavi'); ?></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="xfavi_ufup"><?php _e('Обновить фид при обновлении карточки товара', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="xfavi_ufup" id="xfavi_ufup" <?php checked($xfavi_ufup, 'on' ); ?>/>
			</td>
		 </tr>
		 <?php do_action('xfavi_after_ufup_option', $numFeed); ?>
		 <tr>
			<th scope="row"><label for="xfavi_feed_assignment"><?php _e('Назначение фида', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<input type="text" maxlength="20" name="xfavi_feed_assignment" id="xfavi_feed_assignment" value="<?php echo $xfavi_feed_assignment; ?>" placeholder="<?php _e('Для Авито', 'xfavi');?>" /><br />
				<span class="description"><?php _e('Не используется в фиде. Внутренняя заметка для вашего удобства', 'xfavi'); ?>.</span>
			</td>
		 </tr>		 
		 <tr class="xfavi_tr">
			<th scope="row"><label for="xfavi_whot_export"><?php _e('Что экспортировать', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<select name="xfavi_whot_export" id="xfavi_whot_export">
					<option value="all" <?php selected($xfavi_whot_export, 'all'); ?>><?php _e('Вариативные и обычные товары'); ?></option>
					<option value="simple" <?php selected($xfavi_whot_export, 'simple'); ?>><?php _e('Только обычные товары', 'xfavi'); ?></option>
					<?php do_action('xfavi_after_whot_export_option', $xfavi_whot_export, $numFeed); ?>
				</select><br />
				<span class="description"><?php _e('Что экспортировать', 'xfavi'); ?></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="xfavi_desc"><?php _e('Описание товара', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<select name="xfavi_desc" id="xfavi_desc">
				<option value="excerpt" <?php selected($xfavi_desc, 'excerpt'); ?>><?php _e('Только Краткое описание', 'xfavi'); ?></option>
				<option value="full" <?php selected($xfavi_desc, 'full'); ?>><?php _e('Только Полное описание', 'xfavi'); ?></option>
				<option value="excerptfull" <?php selected($xfavi_desc, 'excerptfull'); ?>><?php _e('Краткое или Полное описание', 'xfavi'); ?></option>
				<option value="fullexcerpt" <?php selected($xfavi_desc, 'fullexcerpt'); ?>><?php _e('Полное или Краткое описание', 'xfavi'); ?></option>
				<option value="excerptplusfull" <?php selected($xfavi_desc, 'excerptplusfull'); ?>><?php _e('Краткое плюс Полное описание', 'xfavi'); ?></option>
				<option value="fullplusexcerpt" <?php selected($xfavi_desc, 'fullplusexcerpt'); ?>><?php _e('Полное плюс Краткое описание', 'xfavi'); ?></option>
				<?php do_action('xfavi_append_select_xfavi_desc', $xfavi_desc, $numFeed); ?>
				</select><br />
				<?php do_action('xfavi_after_select_xfavi_desc', $xfavi_desc, $numFeed); ?>
				<span class="description"><?php _e('Источник описания товара', 'xfavi'); ?>
				</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="xfavi_the_content"><?php _e('Задействовать фильтр', 'xfavi'); ?> the_content</label></th>
			<td class="overalldesc">
				<select name="xfavi_the_content" id="xfavi_the_content">
				<option value="disabled" <?php selected($xfavi_the_content, 'disabled'); ?>><?php _e('Отключено', 'xfavi'); ?></option>
				<option value="enabled" <?php selected($xfavi_the_content, 'enabled'); ?>><?php _e('Включено', 'xfavi'); ?></option>
				</select><br />
				<span class="description"><?php _e('По умолчанию', 'xfavi'); ?>: <?php _e('Включено', 'xfavi'); ?></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="xfavi_behavior_strip_symbol"><?php _e('В атрибутах', 'xfavi'); ?> <?php _e('амперсанд', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<select name="xfavi_behavior_strip_symbol" id="xfavi_behavior_strip_symbol">
					<option value="default" <?php selected($xfavi_behavior_strip_symbol, 'default'); ?>><?php _e('По умолчанию', 'xfavi'); ?></option>
					<option value="del" <?php selected($xfavi_behavior_strip_symbol, 'del'); ?>><?php _e('Удалить', 'xfavi'); ?></option>
					<option value="slash" <?php selected($xfavi_behavior_strip_symbol, 'slash'); ?>><?php _e('Заменить на', 'xfavi'); ?> /</option>
					<option value="amp" <?php selected($xfavi_behavior_strip_symbol, 'amp'); ?>><?php _e('Заменить на', 'xfavi'); ?> amp;</option>
				</select><br />
				<span class="description"><?php _e('По умолчанию', 'xfavi'); ?> "<?php _e('Удалить', 'xfavi'); ?>"</span>
			</td>
		 </tr>		 
		 <tr>
			<th scope="row"><label for="xfavi_var_desc_priority"><?php _e('Описание вариации имеет приоритет над другими', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="xfavi_var_desc_priority" id="xfavi_var_desc_priority" <?php checked($xfavi_var_desc_priority, 'on'); ?>/>
			</td>
		 </tr>		 	 
		 <tr class="xfavi_tr">
			<th scope="row"><label for="xfavi_allowEmail"><?php _e('Связь по Email', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<select name="xfavi_allowEmail" id="xfavi_allowEmail">
					<option value="Да" <?php selected($xfavi_allowEmail, 'Да'); ?>><?php _e('Да', 'xfavi'); ?></option>
					<option value="Нет" <?php selected($xfavi_allowEmail, 'Нет'); ?>><?php _e('Нет', 'xfavi'); ?></option>					
				</select><br />
				<span class="description"><?php _e('Элемент', 'xfavi'); ?> <strong>Allow Email</strong>. <?php _e('Возможность написать сообщение по объявлению через сайт', 'xfavi'); ?></span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="xfavi_managerName"><?php _e('Имя менеджера', 'xfavi'); ?></label></th>
			<td class="overalldesc">
			 <input maxlength="40" type="text" name="xfavi_managerName" id="xfavi_managerName" value="<?php echo $xfavi_managerName; ?>" /><br />
			 <span class="description"><?php _e('Элемент', 'xfavi'); ?> <strong>ManagerName</strong>. <?php _e('Имя менеджера, контактного лица компании по данному объявлению — строка не более 40 символов', 'xfavi'); ?>.</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="xfavi_contactPhone"><?php _e('Телефон', 'xfavi'); ?></label></th>
			<td class="overalldesc">
			 <input maxlength="40" type="text" name="xfavi_contactPhone" id="xfavi_contactPhone" value="<?php echo $xfavi_contactPhone; ?>" /><br />
			 <span class="description"><?php _e('Элемент', 'xfavi'); ?> <strong>ContactPhone</strong>.</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="xfavi_address"><?php _e('Адрес', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<input maxlength="256" type="text" name="xfavi_address" id="xfavi_address" value="<?php echo $xfavi_address; ?>" /><br />
				<span class="description"><?php _e('Обязательный элемент', 'xfavi'); ?> <strong>Address</strong>. <?php _e('Полный адрес объекта — строка до 256 символов', 'xfavi'); ?>.</span>
			</td>
		 </tr>	 
		 <tr>
			<th scope="row"><label for="xfavi_main_product"><?php _e('Какие товары вы продаёте', 'xfavi'); ?>?</label></th>
			<td class="overalldesc">
					<select name="xfavi_main_product" id="xfavi_main_product">
					<option value="electronics" <?php selected($xfavi_main_product, 'electronics'); ?>><?php _e('Электроника', 'xfavi'); ?></option>
					<option value="computer" <?php selected($xfavi_main_product, 'computer'); ?>><?php _e('Компьютеры', 'xfavi'); ?></option>
					<option value="clothes_and_shoes" <?php selected($xfavi_main_product, 'clothes_and_shoes'); ?>><?php _e('Одежда и обувь', 'xfavi'); ?></option>
					<option value="auto_parts" <?php selected($xfavi_main_product, 'auto_parts'); ?>><?php _e('Автозапчасти', 'xfavi'); ?></option>
					<option value="products_for_children" <?php selected($xfavi_main_product, 'products_for_children'); ?>><?php _e('Детские товары', 'xfavi'); ?></option>
					<option value="sporting_goods" <?php selected($xfavi_main_product, 'sporting_goods'); ?>><?php _e('Спортивные товары', 'xfavi'); ?></option>
					<option value="goods_for_pets" <?php selected($xfavi_main_product, 'goods_for_pets'); ?>><?php _e('Товары для домашних животных', 'xfavi'); ?></option>
					<option value="sexshop" <?php selected($xfavi_main_product, 'sexshop'); ?>><?php _e('Секс-шоп (товары для взрослых)', 'xfavi'); ?></option>
					<option value="books" <?php selected($xfavi_main_product, 'books'); ?>><?php _e('Книги', 'xfavi'); ?></option>
					<option value="health" <?php selected($xfavi_main_product, 'health'); ?>><?php _e('Товары для здоровья', 'xfavi'); ?></option>	
					<option value="food" <?php selected($xfavi_main_product, 'food'); ?>><?php _e('Еда', 'xfavi'); ?></option>
					<option value="construction_materials" <?php selected($xfavi_main_product, 'construction_materials'); ?>><?php _e('Строительные материалы', 'xfavi'); ?></option>
					<option value="other" <?php selected($xfavi_main_product, 'other'); ?>><?php _e('Прочее', 'xfavi'); ?></option>					
				</select><br />
				<span class="description"><?php _e('Укажите основную категорию', 'xfavi'); ?></span>
			</td>
		 </tr>
		 <tr class="xfavi_tr">
			<th scope="row"><label for="xfavi_step_export"><?php _e('Шаг экспорта', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<select name="xfavi_step_export" id="xfavi_step_export">
				<option value="80" <?php selected($xfavi_step_export, '80'); ?>>80</option>
				<option value="200" <?php selected($xfavi_step_export, '200'); ?>>200</option>
				<option value="300" <?php selected($xfavi_step_export, '300'); ?>>300</option>
				<option value="450" <?php selected($xfavi_step_export, '450'); ?>>450</option>
				<option value="500" <?php selected($xfavi_step_export, '500'); ?>>500</option>
				<option value="800" <?php selected($xfavi_step_export, '800'); ?>>800</option>
				<option value="1000" <?php selected($xfavi_step_export, '1000'); ?>>1000</option>
				<?php do_action('xfavi_step_export_option', $numFeed); ?>
				</select><br />
				<span class="description"><?php _e('Значение влияет на скорость создания XML фида', 'xfavi'); ?>. <?php _e('Если у вас возникли проблемы с генерацией файла - попробуйте уменьшить значение в данном поле', 'xfavi'); ?>. <?php _e('Более 500 можно устанавливать только на мощных серверах', 'xfavi'); ?>.</span>
			</td>
		 </tr>
		 <tr class="xfavi_tr">
			<th scope="row"><label for="xfavi_no_default_png_products"><?php _e('Удалить default.png из XML', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="xfavi_no_default_png_products" id="xfavi_no_default_png_products" <?php checked($xfavi_no_default_png_products, 'on' ); ?>/>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="xfavi_skip_products_without_pic"><?php _e('Пропустить товары без картинок', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="xfavi_skip_products_without_pic" id="xfavi_skip_products_without_pic" <?php checked($xfavi_skip_products_without_pic, 'on' ); ?>/>
			</td>
		 </tr>
		 <tr class="xfavi_tr">
			<th scope="row"><label for="xfavi_skip_missing_products"><?php _e('Исключать товары которых нет в наличии', 'xfavi'); ?> (<?php _e('за исключением товаров, для которых разрешен предварительный заказ', 'xfavi'); ?>)</label></th>
			<td class="overalldesc">
				<input type="checkbox" name="xfavi_skip_missing_products" id="xfavi_skip_missing_products" <?php checked($xfavi_skip_missing_products, 'on'); ?>/>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="xfavi_skip_backorders_products"><?php _e('Исключать из фида товары для предзаказа', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="xfavi_skip_backorders_products" id="xfavi_skip_backorders_products" <?php checked($xfavi_skip_backorders_products, 'on'); ?>/>
			</td>
		 </tr>
		 <?php do_action('xfavi_after_skip_products_without_pic', $numFeed); ?>
		 <tr class="xfavi_tr">
			<th scope="row"><label for="xfavi_size"><?php _e('Размер', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<select name="xfavi_size" id="xfavi_size">
				<option value="off" <?php selected($xfavi_size, 'off'); ?>><?php _e('Отключено', 'xfavi'); ?></option>
				<?php foreach (xfavi_get_attributes() as $attribute) : ?>
				<option value="<?php echo $attribute['id']; ?>" <?php selected($xfavi_size, $attribute['id'] ); ?>><?php echo $attribute['name']; ?></option>	<?php endforeach; ?>
				</select><br />
				<span class="description"><?php _e('Элемент', 'xfavi'); ?> <strong>Size</strong></span>
			</td>
		 </tr>
		 <tr >
			<th scope="row"><label for="xfavi_condition"><?php _e('Состояние товара', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<select name="xfavi_condition" id="xfavi_condition">
				<option value="new" <?php selected($xfavi_condition, 'new'); ?>><?php _e('Новый', 'xfavi'); ?></option>
				<option value="bu" <?php selected($xfavi_condition, 'bu'); ?>><?php _e('Б/у', 'xfavi'); ?></option>
				<?php do_action('xfavi_condition_option', $numFeed); ?>
				</select><br />
				<span class="description"><?php _e('Обязательный элемент', 'xfavi'); ?> <strong>Condition</strong>. <?php _e('Задайте значение по умолчанию', 'xfavi'); ?></span>
			</td>
		 </tr>
		</tbody></table>
	   </div>
	 </div>	
	 <?php do_action('xfavi_before_button_primary_submit', $numFeed); ?>	 
	 <div class="postbox">
	  <div class="inside">
		<table class="form-table"><tbody>
		 <tr>
			<th scope="row"><label for="button-primary"></label></th>
			<td class="overalldesc"><?php wp_nonce_field('xfavi_nonce_action','xfavi_nonce_field'); ?><input id="button-primary" class="button-primary" type="submit" name="xfavi_submit_action" value="<?php _e('Сохранить', 'xfavi'); ?>" /><br />
			<span class="description"><?php _e('Нажмите, чтобы сохранить настройки', 'xfavi'); ?></span></td>
		 </tr>
		</tbody></table>
	  </div>
	 </div>	 
	 <?php do_action('xfavi_append_form_container_2', $numFeed); ?>
	</form>
	<?php do_action('xfavi_append_container_2', $numFeed); ?>
  </div></div>
 </div><!-- /post-body --><br class="clear"></div><!-- /poststuff -->
 <?php do_action('xfavi_after_poststuff', $numFeed); ?>

 <div id="icp_slides" class="clear">
  <div class="icp_wrap">
	<input type="radio" name="icp_slides" id="icp_point1">
	<input type="radio" name="icp_slides" id="icp_point2">
	<input type="radio" name="icp_slides" id="icp_point3" checked>
	<input type="radio" name="icp_slides" id="icp_point4">
	<input type="radio" name="icp_slides" id="icp_point5">
	<input type="radio" name="icp_slides" id="icp_point6">
	<input type="radio" name="icp_slides" id="icp_point7">
	<div class="icp_slider">
		<div class="icp_slides icp_img1"><a href="//wordpress.org/plugins/yml-for-yandex-market/" target="_blank"></a></div>
		<div class="icp_slides icp_img2"><a href="//wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"></a></div>
		<div class="icp_slides icp_img3"><a href="//wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"></a></div>
		<div class="icp_slides icp_img4"><a href="//wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"></a></div>
		<div class="icp_slides icp_img5"><a href="//wordpress.org/plugins/xml-for-avito/" target="_blank"></a></div>
		<div class="icp_slides icp_img6"><a href="//wordpress.org/plugins/xml-for-o-yandex/" target="_blank"></a></div>
		<div class="icp_slides icp_img7"><a href="//wordpress.org/plugins/import-from-yml/" target="_blank"></a></div>
	</div>
	<div class="icp_control">
		<label for="icp_point1"></label>
		<label for="icp_point2"></label>
		<label for="icp_point3"></label>
		<label for="icp_point4"></label>
		<label for="icp_point5"></label>
		<label for="icp_point6"></label>
		<label for="icp_point7"></label>
	</div>
  </div> 
 </div>
 <?php do_action('xfavi_after_icp_slides', $numFeed); ?>

 <div class="metabox-holder">
  <div class="postbox">
  	<h2 class="hndle"><?php _e('Мои плагины, которые могут вас заинтересовать', 'xfavi'); ?></h2>
	<div class="inside">
		<p><span class="xfavi_bold">XML for Google Merchant Center</span> - <?php _e('Создает XML-фид для загрузки в Google Merchant Center', 'xfavi'); ?>. <a href="https://wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a>.</p> 
		<p><span class="xfavi_bold">YML for Yandex Market</span> - <?php _e('Создает YML-фид для импорта ваших товаров на Яндекс Маркет', 'xfavi'); ?>. <a href="https://wordpress.org/plugins/yml-for-yandex-market/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a>.</p>
		<p><span class="xfavi_bold">Import from YML</span> - <?php _e('Импортирует товары из YML в ваш магазин', 'xfavi'); ?>. <a href="https://wordpress.org/plugins/import-from-yml/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a>.</p>		
		<p><span class="xfavi_bold">XML for Hotline</span> - <?php _e('Создает XML-фид для импорта ваших товаров на Hotline', 'xfavi'); ?>. <a href="https://wordpress.org/plugins/xml-for-hotline/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a>.</p>
		<p><span class="xfavi_bold">Gift upon purchase for WooCommerce</span> - <?php _e('Этот плагин добавит маркетинговый инструмент, который позволит вам дарить подарки покупателю при покупке', 'xfavi'); ?>. <a href="https://wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a>.</p>
		<p><span class="xfavi_bold">Import products to ok.ru</span> - <?php _e('С помощью этого плагина вы можете импортировать товары в свою группу на ok.ru', 'xfavi'); ?>. <a href="https://wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a>.</p>
		<p><span class="xfavi_bold">XML for Avito</span> - <?php _e('Создает XML-фид для импорта ваших товаров на', 'xfavi'); ?> Avito. <a href="https://wordpress.org/plugins/xml-for-avito/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a>.</p>
		<p><span class="xfavi_bold">XML for O.Yandex (Яндекс Объявления)</span> - <?php _e('Создает XML-фид для импорта ваших товаров на', 'xfavi'); ?> Яндекс.Объявления. <a href="https://wordpress.org/plugins/xml-for-o-yandex/" target="_blank"><?php _e('Подробнее', 'xfavi'); ?></a>.</p>
	</div>
  </div>
 </div>
 <?php do_action('xfavi_append_wrap', $numFeed); ?>
</div><!-- /wrap -->
<?php
} /* end функция настроек xfavi_export_page */ ?>