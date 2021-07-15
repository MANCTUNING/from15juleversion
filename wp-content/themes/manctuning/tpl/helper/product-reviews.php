<?php
/**
 * Rating Stars
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

echo get_template_part( 'tpl/helper/product', 'stars' )
?>
<?php //TODO: общий итог по продукту b>4,5</b ?>
<b>4,5</b>
<span><?= $comments_count ?> оценок</span>