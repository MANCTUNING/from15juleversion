<?php if (!defined('ABSPATH')) {exit;}
/* функция песочницы yfym_run_sandbox */
function yfym_run_sandbox() { 
 $x = 1; // установите 0, чтобы вернуть исключение
 /* вставьте ваш код ниже */
/*
 $offer_id = 27;
 $offer = new WC_Product_Variation($offer_id); // получим вариацию 
  
 $product_id = absint($offer->get_parent_id());
 $offer_id = absint($offer->get_id());
 $offer_image_id = absint($offer->get_image_id());
 $has_variation_gallery_images = (bool)get_post_meta($offer_id, 'rtwpvg_images', true);
 if ($has_variation_gallery_images) {
	$gallery_images = (array)get_post_meta($offer_id, 'rtwpvg_images', true);
 } 

 var_dump($gallery_images);
*/
 
 /* дальше не редактируем */
 if (!$x) {
	throw new Exception('The sandbox is working correctly');
 }
 echo 1/$x;
} /* end функция песочницы yfym_run_sandbox */
?>