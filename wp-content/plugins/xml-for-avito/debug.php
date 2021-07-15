<?php if (!defined('ABSPATH')) {exit;}
function xfavi_debug_page() { 
 wp_clean_plugins_cache();
 wp_clean_update_cache();
 add_filter('pre_site_transient_update_plugins', '__return_null');
 wp_update_plugins();
 remove_filter('pre_site_transient_update_plugins', '__return_null');
 if (isset($_REQUEST['xfavi_submit_debug_page'])) {
	if (!empty($_POST) && check_admin_referer('xfavi_nonce_action','xfavi_nonce_field')) {
		if (isset($_POST['xfavi_keeplogs'])) {
			xfavi_optionUPD('xfavi_keeplogs', sanitize_text_field($_POST['xfavi_keeplogs']));
			xfavi_error_log('NOTICE: Логи успешно включены; Файл: debug.php; Строка: '.__LINE__, 0);
		} else {
			xfavi_error_log('NOTICE: Логи отключены; Файл: debug.php; Строка: '.__LINE__, 0);
			xfavi_optionUPD('xfavi_keeplogs', '0');
		}
		if (isset($_POST['xfavi_disable_notices'])) {
			xfavi_optionUPD('xfavi_disable_notices', sanitize_text_field($_POST['xfavi_disable_notices']));
		} else {
			xfavi_optionUPD('xfavi_disable_notices', '0');
		}
		if (isset($_POST['xfavi_enable_five_min'])) {
			xfavi_optionUPD('xfavi_enable_five_min', sanitize_text_field($_POST['xfavi_enable_five_min']));
		} else {
			xfavi_optionUPD('xfavi_enable_five_min', '0');
		}		
	}
 }	
 $xfavi_keeplogs = xfavi_optionGET('xfavi_keeplogs');
 $xfavi_disable_notices = xfavi_optionGET('xfavi_disable_notices');
 $xfavi_enable_five_min = xfavi_optionGET('xfavi_enable_five_min');
 ?>
 <div class="wrap"><h1><?php _e('Страница отладки', 'xfavi'); ?> v.<?php echo xfavi_optionGET('xfavi_version'); ?></h1>
  <div id="dashboard-widgets-wrap"><div id="dashboard-widgets" class="metabox-holder">
  	<div id="postbox-container-1" class="postbox-container"><div class="meta-box-sortables">
     <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">	 
	 <div class="postbox">
	   <div class="inside">
		<h1><?php _e('Логи', 'xfavi'); ?></h1>
		<p><?php if ($xfavi_keeplogs === 'on') {echo '<strong>'. __("Логи тут", 'xfavi').':</strong><br />'. xfavi_UPLOAD_DIR .'/xml-for-avito/xml-for-avito.log';	} ?></p>		
		<table class="form-table"><tbody>
		 <tr>
			<th scope="row"><label for="xfavi_keeplogs"><?php _e('Вести логи', 'xfavi'); ?></label><br />
				<input class="button" id="xfavi_submit_clear_logs" type="submit" name="xfavi_submit_clear_logs" value="<?php _e('Очистить логи', 'xfavi'); ?>" />
			</th>
			<td class="overalldesc">
				<input type="checkbox" name="xfavi_keeplogs" id="xfavi_keeplogs" <?php checked($xfavi_keeplogs, 'on' ); ?>/><br />
				<span class="description"><?php _e('Не устанавливайте этот флажок, если вы не разработчик', 'xfavi'); ?>!</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="xfavi_disable_notices"><?php _e('Отключить уведомления', 'xfavi'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="xfavi_disable_notices" id="xfavi_disable_notices" <?php checked($xfavi_disable_notices, 'on' ); ?>/><br />
				<span class="description"><?php _e('Отключить уведомления о XML-сборке', 'xfavi'); ?>!</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="xfavi_enable_five_min"><?php _e('Включить', 'xfavi'); ?> five_min</label></th>
			<td class="overalldesc">
				<input type="checkbox" name="xfavi_enable_five_min" id="xfavi_enable_five_min" <?php checked($xfavi_enable_five_min, 'on' ); ?>/><br />
				<span class="description"><?php _e('Включить пятиминутный интервал для CRON', 'xfavi'); ?></span>
			</td>
		 </tr>		 
		 <tr>
			<th scope="row"><label for="button-primary"></label></th>
			<td class="overalldesc"></td>
		 </tr>		 
		 <tr>
			<th scope="row"><label for="button-primary"></label></th>
			<td class="overalldesc"><?php wp_nonce_field('xfavi_nonce_action', 'xfavi_nonce_field'); ?><input id="button-primary" class="button-primary" type="submit" name="xfavi_submit_debug_page" value="<?php _e('Сохранить', 'xfavi'); ?>" /><br />
			<span class="description"><?php _e('Нажмите, чтобы сохранить настройки', 'xfavi'); ?></span></td>
		 </tr>         
        </tbody></table>
       </div>
     </div>
     </form>
	</div></div>
  	<div id="postbox-container-2" class="postbox-container"><div class="meta-box-sortables">
  	 <div class="postbox">
	  <div class="inside">
		<h1><?php _e('Сбросить настройки плагина', 'xfavi'); ?></h1>
		<p><?php _e('Сброс настроек плагина может быть полезен в случае возникновения проблем', 'xfavi'); ?>.</p>
		<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field('xfavi_nonce_action_reset', 'xfavi_nonce_field_reset'); ?><input class="button-primary" type="submit" name="xfavi_submit_reset" value="<?php _e('Сбросить настройки плагина', 'xfavi'); ?>" />	 
		</form>
	  </div>
	 </div>	
	 <div class="postbox">
	  <h2 class="hndle"><?php _e('Симуляция запроса', 'xfavi'); ?></h2>
	  <div class="inside">		
		<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
		 <?php 	 
		 if (isset($_POST['xfavi_num_feed'])) {$numFeed = sanitize_text_field($_POST['xfavi_num_feed']);} else {$numFeed = '1';} 
		 if (isset($_POST['xfavi_simulated_post_id'])) {$xfavi_simulated_post_id = sanitize_text_field($_POST['xfavi_simulated_post_id']);} else {$xfavi_simulated_post_id = '';}
		 $resust_simulated = '';
		 if (isset($_REQUEST['xfavi_submit_simulated'])) {
			if (!empty($_POST) && check_admin_referer('xfavi_nonce_action_simulated', 'xfavi_nonce_field_simulated')) {		 
				$postId = (int)$xfavi_simulated_post_id;
				$simulated_header = xfavi_feed_header($numFeed);
				$simulated = xfavi_unit($postId, $numFeed);
				if (is_array($simulated)) {
					$resust_simulated = $simulated_header.$simulated[0];
					$resust_simulated = apply_filters('xfavi_after_offers_filter', $resust_simulated, $numFeed);
					$resust_simulated .= "</Ads>";				
				} else {
					$resust_simulated = $simulated_header.$simulated;
					$resust_simulated = apply_filters('xfavi_after_offers_filter', $resust_simulated, $numFeed);
					$resust_simulated .= "</Ads>";
				}
			}
		 } ?>		
		 <table class="form-table"><tbody>
		 <tr>
			<th scope="row"><label for="xfavi_simulated_post_id">postId</label></th>
			<td class="overalldesc">
				<input type="number" min="1" name="xfavi_simulated_post_id" value="<?php echo $xfavi_simulated_post_id; ?>">
			</td>
		 </tr>			
		 <tr>
			<th scope="row"><label for="xfavi_enable_five_min">numFeed</label></th>
			<td class="overalldesc">
				<select style="width: 100%" name="xfavi_num_feed" id="xfavi_num_feed">
					<?php if (is_multisite()) {$cur_blog_id = get_current_blog_id();} else {$cur_blog_id = '0';}		
					$allNumFeed = (int)xfavi_ALLNUMFEED; $ii = '1';
					for ($i = 1; $i<$allNumFeed+1; $i++) : ?>
					<option value="<?php echo $i; ?>" <?php selected($numFeed, $i); ?>><?php _e('Фид', 'xfavi'); ?> <?php echo $i; ?>: feed-avito-<?php echo $cur_blog_id; ?>.xml <?php $assignment = xfavi_optionGET('xfavi_feed_assignment', $ii); if ($assignment === '') {} else {echo '('.$assignment.')';} ?></option>
					<?php $ii++; endfor; ?>
				</select>
			</td>
		 </tr>			
		 <tr>
			<th scope="row" colspan="2"><textarea rows="16" style="width: 100%;"><?php echo htmlspecialchars($resust_simulated); ?></textarea></th>
		 </tr>			       
		 </tbody></table>
		 <?php wp_nonce_field('xfavi_nonce_action_simulated', 'xfavi_nonce_field_simulated'); ?><input class="button-primary" type="submit" name="xfavi_submit_simulated" value="<?php _e('Симуляция', 'xfavi'); ?>" />
		</form>			
	  </div>
	 </div>	 
	</div></div>
	 <div id="postbox-container-3" class="postbox-container"><div class="meta-box-sortables">
	 <div class="postbox">
	  <div class="inside">
	  	<h1><?php _e('Возможные проблемы', 'xfavi'); ?></h1>
		  <?php
			$possible_problems_arr = xfavi_possible_problems_list();
			if ($possible_problems_arr[1] > 0) { // $possibleProblemsCount > 0) {
				echo '<ol>'.$possible_problems_arr[0].'</ol>';
			} else {
				echo '<p>'. __('Функции самодиагностики не выявили потенциальных проблем', 'xfavi').'.</p>';
			}
		  ?>
	  </div>
     </div>	 
	 <div class="postbox">
	  <div class="inside">
	  	<h1><?php _e('Песочница', 'xfavi'); ?></h1>
			<?php
				require_once plugin_dir_path(__FILE__).'/sandbox.php';
				try {
					xfavi_run_sandbox();
				} catch (Exception $e) {
					echo 'Exception: ',  $e->getMessage(), "\n";
				}
			?>
		</div>
     </div>
  	</div></div>	  	 
	<div id="postbox-container-4" class="postbox-container"><div class="meta-box-sortables">
  	 <?php do_action('xfavi_before_support_project'); ?>
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
  </div></div>
  </div></div>
 </div>
<?php
} /* end функция страницы debug-а xfavi_debug_page */
?>