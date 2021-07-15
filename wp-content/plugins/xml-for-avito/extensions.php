<?php if (!defined('ABSPATH')) {exit;}
function xfavi_extensions_page() { ?>
 <style>.button-primary {text-align: center; margin: 0 auto !important;}</style>
 <div id="xfavi_extensions" class="wrap"> 
  <h1 style="font-size: 32px; text-align: center; color: #5b2942;"><?php _e('Расширения для XML for Avito', 'xfavi'); ?></h1> 
  <div id="dashboard-widgets-wrap"><div id="dashboard-widgets" class="metabox-holder">
   <div id="postbox-container-1" class="postbox-container"><div class="meta-box-sortables">
	<div class="postbox">
	 <div class="inside">
      <table class="form-table"><tbody>
    	<tr>
		 <td class="overalldesc" style="font-size: 18px;">
			<h1 style="text-align: center; color: #5b2942;">XML for Avito Pro</h1>
			<img style="max-width: 100%;" src="<?php echo xfavi_URL; ?>/img/ex1.jpg" alt="img" />
			<ul>
				<li>&#10004; <?php _e('Возможность исключать товары из определенных категорий', 'xfavi'); ?>;</li>
				<li>&#10004; <?php _e('Возможность исключать товары по определенным тегам', 'xfavi'); ?>;</li>
				<li>&#10004; <?php _e('Возможность исключать товары по цене', 'xfavi'); ?>;</li>
				<li>&#10004; <?php _e('Возможность загрузки нескольких изображений для товара вместо одного', 'xfavi'); ?>;</li>
				<li>&#10004; <?php _e('Возможность добавлять видео с YouTube', 'xfavi'); ?>;</li>
				<li>&#10004; <?php _e('Возможность удалять шорткоды Visual Composer из описания', 'xfavi'); ?>;</li>
				<li>&#10004; <?php _e('Еще более стабильная работа', 'xfavi'); ?>!</li>
			</ul>
			<p style="text-align: center;"><a class="button-primary" href="https://icopydoc.ru/product/xml-for-avito-pro/?utm_source=xml-for-avito&utm_medium=organic&utm_campaign=in-plugin-xml-for-avito&utm_content=extensions&utm_term=poluchit-xml-pro" target="_blank"><?php _e('Получить XML for Hotline Pro сейчас', 'xfavi'); ?></a><br /></p>		   
		 </td>
    	</tr>
      </tbody></table>
	 </div>
	</div>
   </div></div>
  </div></div>
 </div>
<?php
} /* end функция расширений xfavi_extensions_page */
?>