<?php

/**
 * WP Rewrite
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Фильтруем ссылки терминов таксономий
add_filter( 'term_link', function ( $termlink, $term ) {

	// Правим Категории Woo: product-category -> catalog
	$termlink = str_replace( 'product-category', 'catalog', $termlink );

	// Правим Метки Woo: product-tag -> tag
	$termlink = str_replace( 'product-tag', 'type', $termlink );
	// Ищем любую запись, что хранится
	/* $product = get_posts( [
	  'post_type'		 => 'product',
	  'posts_per_page' => 1,
	  'product-tag'	 => $term -> term_slug,
	  ] );
	  $product_permalink = get_permalink( $product[0] );
	  exit( print_r( $product_permalink ) );
	  $termlink = str_replace( 'product-category', 'catalog', $termlink ); */

	return $termlink;
}, 10, 2 );

// Фильтруем ссылки продуктов Woo
add_filter( 'post_type_link', function ( $post_link, $post, $leavename, $sample ) {

	// Работаем с товарами
	if ( 'product' == get_post_type( $post ) ) {

		// Получаем список Категорий
		$cats = wp_get_post_terms( $post -> ID, $taxonomy = 'product_cat', [ 'order' => 'ASC', 'orderby' => 'term_id' ] );
		$cats = array_map( function ( $term ) {
			return $term -> slug;
		}, $cats );
		$cats = implode( '/', $cats );

		// Добавляем Метку
		$tag = get_the_terms( $post, $taxonomy = 'product_tag' )[0] -> slug;

		// Конечный URL
		$url .= 'catalog/' . $cats . '/' . $tag;

		// Правим Товар Woo: manctuning.ru/product/porogi-4m-audi-q7-s-2015-goda -> manctuning.ru/catalog/audi/q7/2-pokolenie/porogi/porogi-4m-audi-q7-s-2015-goda
		$post_link = str_replace( 'product', $url, $post_link );
	}

	return $post_link;
}, 10, 4 );

// manctuning.ru/catalog?brand=bmw&model=x5&breed=g05&type=diffuzory
add_action( 'init', function () {
	// Правило перезаписи
	// manctuning.ru/catalog?brand=bmw&model=x5&breed=g05&type=diffuzory
	// manctuning.ru/catalog/audi/q7/2-pokolenie/porogi/porogi-4m-audi-q7-s-2015-goda
	add_rewrite_rule( '^(catalog)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/?$', 'index.php?product=$matches[6]', 'top' );

	// manctuning.ru/catalog?brand=bmw&model=x5&breed=g05&type=diffuzory
	// manctuning.ru/catalog/audi/q7/2-pokolenie/porogi
	// manctuning.ru/catalog/typeq/porogi
	add_rewrite_rule( '^(catalog)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/?$', 'index.php?pagename=$matches[1]&brand=$matches[2]&model=$matches[3]&breed=$matches[4]&type=$matches[5]', 'top' );
	add_rewrite_rule( '^type/([^/]*)/?$', 'index.php?pagename=catalog&type=$matches[1]', 'top' );

	// manctuning.ru/catalog?brand=bmw&model=x5&breed=g05
	// manctuning.ru/catalog/audi/q7/2-pokolenie
	add_rewrite_rule( '^(catalog)/([^/]*)/([^/]*)/([^/]*)/?$', 'index.php?pagename=$matches[1]&brand=$matches[2]&model=$matches[3]&breed=$matches[4]', 'top' );

	// manctuning.ru/catalog?brand=bmw&model=x5
	// manctuning.ru/catalog/audi/q7
	add_rewrite_rule( '^(catalog)/([^/]*)/([^/]*)/?$', 'index.php?pagename=$matches[1]&brand=$matches[2]&model=$matches[3]', 'top' );

	// manctuning.ru/catalog?brand=bmw
	// manctuning.ru/catalog/audi
	add_rewrite_rule( '^(catalog)/([^/]*)/?$', 'index.php?pagename=$matches[1]&brand=$matches[2]', 'top' );

	// нужно указать ?p=123 если такое правило создается для записи 123
	// первый параметр для записей: p или name, для страниц: page_id или pagename
	// скажем WP, что есть новые параметры запроса
	add_filter( 'query_vars', function( $vars ) {
		$vars[] = 'brand';
		$vars[] = 'model';
		$vars[] = 'breed';
		$vars[] = 'type';
		return $vars;
	} );
} );



