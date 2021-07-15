<?php if (!defined('ABSPATH')) {exit;}
function yfym_debug_page() { 
 wp_clean_plugins_cache();
 wp_clean_update_cache();
 add_filter('pre_site_transient_update_plugins', '__return_null');
 wp_update_plugins();
 remove_filter('pre_site_transient_update_plugins', '__return_null');
 if (isset($_REQUEST['yfym_submit_debug_page'])) {
	if (!empty($_POST) && check_admin_referer('yfym_nonce_action','yfym_nonce_field')) {
		if (isset($_POST['yfym_keeplogs'])) {
			yfym_optionUPD('yfym_keeplogs', sanitize_text_field($_POST['yfym_keeplogs']));
			yfym_error_log('NOTICE: Логи успешно включены; Файл: debug.php; Строка: '.__LINE__, 0);
		} else {
			yfym_error_log('NOTICE: Логи отключены; Файл: debug.php; Строка: '.__LINE__, 0);
			yfym_optionUPD('yfym_keeplogs', '0');
		}
		if (isset($_POST['yfym_disable_notices'])) {
			yfym_optionUPD('yfym_disable_notices', sanitize_text_field($_POST['yfym_disable_notices']));
		} else {
			yfym_optionUPD('yfym_disable_notices', '0');
		}
		if (isset($_POST['yfym_enable_five_min'])) {
			yfym_optionUPD('yfym_enable_five_min', sanitize_text_field($_POST['yfym_enable_five_min']));
		} else {
			yfym_optionUPD('yfym_enable_five_min', '0');
		}		
	}
 }	
 $yfym_keeplogs = yfym_optionGET('yfym_keeplogs');
 $yfym_disable_notices = yfym_optionGET('yfym_disable_notices');
 $yfym_enable_five_min = yfym_optionGET('yfym_enable_five_min');
 ?>
 <div class="wrap"><h1><?php _e('Debug page', 'yfym'); ?> v.<?php echo yfym_optionGET('yfym_version'); ?></h1>
  <div id="dashboard-widgets-wrap"><div id="dashboard-widgets" class="metabox-holder">
  <div id="postbox-container-1" class="postbox-container"><div class="meta-box-sortables">
     <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">	 
	 <div class="postbox">
	   <h2 class="hndle"><?php _e('Logs', 'yfym'); ?></h2>
	   <div class="inside">	   
		<p><?php if ($yfym_keeplogs === 'on') {echo '<strong>'. __("Log-file here", 'yfym').':</strong><br />'. yfym_UPLOAD_DIR .'/yfym/yfym.log';	} ?></p>		
		<table class="form-table"><tbody>
		 <tr>
			<th scope="row"><label for="yfym_keeplogs"><?php _e('Keep logs', 'yfym'); ?></label><br />
				<input class="button" id="yfym_submit_clear_logs" type="submit" name="yfym_submit_clear_logs" value="<?php _e('Clear logs', 'yfym'); ?>" />
			</th>
			<td class="overalldesc">
				<input type="checkbox" name="yfym_keeplogs" id="yfym_keeplogs" <?php checked($yfym_keeplogs, 'on' ); ?>/><br />
				<span class="description"><?php _e('Do not check this box if you are not a developer', 'yfym'); ?>!</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_disable_notices"><?php _e('Disable notices', 'yfym'); ?></label></th>
			<td class="overalldesc">
				<input type="checkbox" name="yfym_disable_notices" id="yfym_disable_notices" <?php checked($yfym_disable_notices, 'on' ); ?>/><br />
				<span class="description"><?php _e('Disable notices about YML-construct', 'yfym'); ?>!</span>
			</td>
		 </tr>
		 <tr>
			<th scope="row"><label for="yfym_enable_five_min"><?php _e('Enable', 'yfym'); ?> five_min</label></th>
			<td class="overalldesc">
				<input type="checkbox" name="yfym_enable_five_min" id="yfym_enable_five_min" <?php checked($yfym_enable_five_min, 'on' ); ?>/><br />
				<span class="description"><?php _e('Enable the five minute interval for CRON', 'yfym'); ?></span>
			</td>
		 </tr>		 
		 <tr>
			<th scope="row"><label for="button-primary"></label></th>
			<td class="overalldesc"></td>
		 </tr>		 
		 <tr>
			<th scope="row"><label for="button-primary"></label></th>
			<td class="overalldesc"><?php wp_nonce_field('yfym_nonce_action', 'yfym_nonce_field'); ?><input id="button-primary" class="button-primary" type="submit" name="yfym_submit_debug_page" value="<?php _e( 'Save', 'yfym'); ?>" /><br />
			<span class="description"><?php _e('Click to save the settings', 'yfym'); ?></span></td>
		 </tr>         
        </tbody></table>
       </div>
     </div>
     </form> 
  </div></div>
  <div id="postbox-container-2" class="postbox-container"><div class="meta-box-sortables">
  	<div class="postbox">
	  <h2 class="hndle"><?php _e('Reset plugin settings', 'yfym'); ?></h2>
	  <div class="inside">	  	
		<p><?php _e('Reset plugin settings can be useful in the event of a problem', 'yfym'); ?>.</p>
		<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field('yfym_nonce_action_reset', 'yfym_nonce_field_reset'); ?><input class="button-primary" type="submit" name="yfym_submit_reset" value="<?php _e('Reset plugin settings', 'yfym'); ?>" />	 
		</form>
	  </div>
	</div>
	<div class="postbox">
	  <h2 class="hndle"><?php _e('Request simulation', 'yfym'); ?></h2>
	  <div class="inside">		
		<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
		 <?php $resust_simulated = '';
		 if (isset($_POST['yfym_num_feed'])) {$numFeed = sanitize_text_field($_POST['yfym_num_feed']);} else {$numFeed = '1';} 
		 if (isset($_POST['yfym_simulated_post_id'])) {$yfym_simulated_post_id = sanitize_text_field($_POST['yfym_simulated_post_id']);} else {$yfym_simulated_post_id = '';}
		 if (isset($_REQUEST['yfym_submit_simulated'])) {
			if (!empty($_POST) && check_admin_referer('yfym_nonce_action_simulated', 'yfym_nonce_field_simulated')) {	 
				$postId = (int)$yfym_simulated_post_id;
				$simulated_header = yfym_feed_header($numFeed);
				$simulated = yfym_unit($postId, $numFeed);
				if (is_array($simulated)) {
					$resust_simulated = $simulated_header.$simulated[0];
					$resust_simulated .= "</offers>". PHP_EOL; 
					$resust_simulated = apply_filters('yfym_after_offers_filter', $resust_simulated, $numFeed);
					$resust_simulated .= "</shop>". PHP_EOL ."</yml_catalog>";					
				} else {
					$resust_simulated = $simulated_header.$simulated;
					$resust_simulated .= "</offers>". PHP_EOL; 
					$resust_simulated = apply_filters('yfym_after_offers_filter', $resust_simulated, $numFeed);
					$resust_simulated .= "</shop>". PHP_EOL ."</yml_catalog>";
				}
			}
		 } ?>		
		 <table class="form-table"><tbody>
		 <tr>
			<th scope="row"><label for="yfym_simulated_post_id">postId</label></th>
			<td class="overalldesc">
				<input type="number" min="1" name="yfym_simulated_post_id" value="<?php echo $yfym_simulated_post_id; ?>">
			</td>
		 </tr>			
		 <tr>
			<th scope="row"><label for="yfym_enable_five_min">numFeed</label></th>
			<td class="overalldesc">
				<select style="width: 100%" name="yfym_num_feed" id="yfym_num_feed">
					<?php if (is_multisite()) {$cur_blog_id = get_current_blog_id();} else {$cur_blog_id = '0';}		
					$allNumFeed = (int)yfym_ALLNUMFEED; $ii = '1';
					for ($i = 1; $i<$allNumFeed+1; $i++) : ?>
					<option value="<?php echo $i; ?>" <?php selected($numFeed, $i); ?>><?php _e('Feed', 'yfym'); ?> <?php echo $i; ?>: feed-yml-<?php echo $cur_blog_id; ?>.xml <?php $assignment = yfym_optionGET('yfym_feed_assignment', $ii); if ($assignment === '') {} else {echo '('.$assignment.')';} ?></option>
					<?php $ii++; endfor; ?>
				</select>
			</td>
		 </tr>			
		 <tr>
			<th scope="row" colspan="2"><textarea rows="16" style="width: 100%;"><?php echo htmlspecialchars($resust_simulated); ?></textarea></th>
		 </tr>			       
         </tbody></table>
		 <?php wp_nonce_field('yfym_nonce_action_simulated', 'yfym_nonce_field_simulated'); ?><input class="button-primary" type="submit" name="yfym_submit_simulated" value="<?php _e('Simulated', 'yfym'); ?>" />
		</form>			
	  </div>
	</div>
  </div></div>  
  <div id="postbox-container-3" class="postbox-container"><div class="meta-box-sortables">
  <div class="postbox">
  	  <h2 class="hndle"><?php _e('Possible problems', 'yfym'); ?></h2>
  	  <div class="inside">
		<?php
			$possible_problems_arr = yfym_possible_problems_list();
			if ($possible_problems_arr[1] > 0) { // $possibleProblemsCount > 0) {
				echo '<ol>'.$possible_problems_arr[0].'</ol>';
			} else {
				echo '<p>'. __('Self-diagnosis functions did not reveal potential problems', 'yfym').'.</p>';
			}
		?>
	  </div>
     </div>	  
	 <div class="postbox">
	  <h2 class="hndle"><?php _e('Sandbox', 'yfym'); ?></h2>
	  <div class="inside">	  	
			<?php
				require_once plugin_dir_path(__FILE__).'/sandbox.php';
				try {
					yfym_run_sandbox();
				} catch (Exception $e) {
					echo 'Exception: ',  $e->getMessage(), "\n";
				}
			?>
		</div>
     </div>	  
  </div></div>  
  <div id="postbox-container-4" class="postbox-container"><div class="meta-box-sortables">
  	<?php do_action('yfym_before_support_project'); ?>
	  <div class="postbox">
	  <h2 class="hndle"><?php _e('Send data about the work of the plugin', 'yfym'); ?></h2>
	  <div class="inside">	  
		<p><?php _e('Sending statistics you help make the plugin even better', 'yfym'); ?>! <?php _e('The following data will be transferred', 'yfym'); ?>:</p>
		<ul>
			<li>- <?php _e('Site URL', 'yfym'); ?></li>
			<li>- <?php _e('File generation status', 'yfym'); ?></li>
			<li>- <?php _e('URL YML-feed', 'yfym'); ?></li>
			<li>- <?php _e('Is the multisite mode enabled', 'yfym'); ?>?</li>
		</ul>
		<p><?php _e('The plugin helped you download the products to the Yandex Market', 'yfym'); ?>?</p>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
		 <p>
			<input type="radio" name="yfym_its_ok" value="yes"><?php _e('Yes', 'yfym'); ?><br />
			<input type="radio" name="yfym_its_ok" value="no"><?php _e('No', 'yfym'); ?><br />
		 </p>
		 <p><?php _e("If you don't mind to be contacted in case of problems, please enter your email address", "yfym"); ?>. <span style="font-weight: 700;"><?php _e('And if you want a response, be sure to include your email address', 'yfym'); ?></span>.</p>
		 <p><input type="email" name="yfym_email"></p>
		 <p><?php _e("Your message", "yfym"); ?>:</p>
		 <p><textarea rows="5" cols="40" name="yfym_message" placeholder="<?php _e('Enter your text to send me a message (You can write me in Russian or English). I check my email several times a day', 'yfym'); ?>"></textarea></p>
		 <?php wp_nonce_field('yfym_nonce_action_send_stat', 'yfym_nonce_field_send_stat'); ?><input class="button-primary" type="submit" name="yfym_submit_send_stat" value="<?php _e('Send data', 'yfym'); ?>" />
		</form>
	  </div>
	 </div>	  
  </div></div>
  </div></div>



 </div>
<?php
} /* end функция страницы debug-а yfym_debug_page */
?>