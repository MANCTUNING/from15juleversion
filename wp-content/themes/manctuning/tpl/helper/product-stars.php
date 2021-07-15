<?php

/**
 * Product Rating Stars
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $post;

// $post должен быть определён
$post or exit;

$product = wc_get_product( $post -> ID );
$rating = $product -> get_average_rating() ?: 5;
$count = $product -> get_rating_count() ?: 1;

require TEMPLATEPATH . '/tpl/helper/stars.php';
?>