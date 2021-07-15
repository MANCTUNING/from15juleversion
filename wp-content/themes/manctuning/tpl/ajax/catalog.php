<?php

/**
 * Каталог товаров
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $p;

ob_start();
foreach ( $p as $post ) {
	setup_postdata( $post );

	echo get_template_part( 'tpl/ajax/product', 'card' );
	
} wp_reset_postdata();

ob_get_flush();
?>
