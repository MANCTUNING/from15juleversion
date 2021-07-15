<?php

/**
 * Yoast
 * 
 * @author WPPW
 * @link http://wppw.ru
 * 
 * https://gist.github.com/doubleedesign/7224a5e990b8506ddb8ec66de8348b2b
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Настраиваем SEO =======================================================================================

final class WPPW_Yoast {

	public function __construct() {

		// /catalog
		$this -> catalog_post_id = 143;

		// Title Tag
		add_filter( 'wpseo_opengraph_title', '__return_false' );
		add_action( 'wpseo_title', [ $this, 'wpseo_title' ] );

		// Meta desc
		add_filter( 'wpseo_opengraph_desc', '__return_false' );
		add_action( 'wpseo_metadesc', [ $this, 'wpseo_metadesc' ] );

		// Canonical
		add_filter( 'wpseo_canonical', [ $this, 'canonical' ] );

		// Breadcrumbs
		add_filter( 'wpseo_breadcrumb_links', [ $this, 'breadcrumbs' ], 10, 2 );
	}

	function wpseo_title( $title ) {

		// Если мы находимся на странице Метки Woo
		if ( $brand = get_query_var( 'type' ) ) {

			// Категории Woo
			$tax = 'product_tag';

			// Получаем данные рубрики
			$term = get_term_by( 'slug', $brand, $tax );

			// Получаем данные title
			$title = get_option( 'wpseo_taxonomy_meta' )[$tax][$term -> term_id]['wpseo_title'];

			$title = wpseo_replace_vars( $title, [] );
		}
		// Если мы находимся на странице Категории Woo
		elseif ( $brand = get_query_var( 'model' ) or $brand = get_query_var( 'brand' ) ) {

			// Категории Woo
			$tax = 'product_cat';

			// Получаем данные рубрики
			$term = get_term_by( 'slug', $brand, $tax );

			// Получаем данные title
			$title = get_option( 'wpseo_taxonomy_meta' )[$tax][$term -> term_id]['wpseo_title'];

			$title = wpseo_replace_vars( $title, [] );
		}

		return $title;
	}

	function wpseo_metadesc( $title ) {

		// Если мы находимся на странице Метки Woo
		if ( $brand = get_query_var( 'type' ) ) {

			// Категории Woo
			$tax = 'product_tag';

			// Получаем данные рубрики
			$term = get_term_by( 'slug', $brand, $tax );

			// Получаем данные title
			$title = get_option( 'wpseo_taxonomy_meta' )[$tax][$term -> term_id]['wpseo_desc'];

			$title = wpseo_replace_vars( $title, [] );
		}
		// Если мы находимся на странице Категории Woo
		elseif ( $brand = get_query_var( 'model' ) or $brand = get_query_var( 'brand' ) ) {

			// Категории Woo
			$tax = 'product_cat';

			// Получаем данные рубрики
			$term = get_term_by( 'slug', $brand, $tax );

			// Получаем данные title
			$title = get_option( 'wpseo_taxonomy_meta' )[$tax][$term -> term_id]['wpseo_desc'];

			$title = wpseo_replace_vars( $title, [] );
		}

		return $title;
	}

	function canonical( $canonical ) {

		// /catalog
		if ( is_page( 143 ) ) {
			$canonical = site_url( $_SERVER['REQUEST_URI'] );
		}

		return $canonical;
	}

	function breadcrumbs( $links ) {

		// Product ====================================================================================================
		if ( is_singular( [ 'product' ] ) ) {

			global $post;

			// Добавляем ссылку на каталог
			array_splice( $links, $offset = 1, $length = 0, [
				[
					'text'		 => 'Каталог',
					'url'		 => get_permalink( '/catalog' ),
					'allow_html' => '1',
				]
			] );


			// Добавляем метку (тип товара)
			$tax = 'product_tag';

			// Получаем значение метки
			$tag = get_the_terms( $post, $tax )[0];

			// Добавляем элемент в ассоциативный массив ссылок
			array_splice( $links, $offset = 2, $length = 0, [
				[
					'text'		 => $tag -> name,
					'url'		 => get_term_link( $tag, $tax ),
					'allow_html' => '1',
				]
			] );
		}

		// Catalog ====================================================================================================
		if ( is_page( $this -> catalog_post_id ) ) {

			foreach ( [ 'brand', 'model', 'breed', 'type' ] as $i => $e ) {

				// Идентифицируем текущую вложенность категорий
				if ( $$e = get_query_var( $e ) ) {

					$$e = get_term_by( 'slug', $$e, 'product_cat' ) ?: get_term_by( 'slug', $$e, 'product_tag' );

					// Добавляем элемент в ассоциативный массив ссылок
					array_splice( $links, $offset = ++$i + 1, $length = 0, [
						[
							'text'		 => $$e -> name,
							'url'		 => get_term_link( $$e, 'product_cat' ),
							'allow_html' => '1',
						]
					] );
				}
				else
					break;
			} unset( $e );
		}


		// CPT ====================================================================================================
		/* foreach ( [
		  [
		  'cpt'		 => 'wppw_service',
		  'post_id'	 => 237, // Страница "Услуги" // http://manctuning.ru/wp-admin/post.php?post=237&action=edit
		  'breadcrumb' => 'Услуги', // TODO: вытаскивать название из хлебных крошек страницы
		  ],
		  [
		  'cpt'		 => 'wppw_project',
		  'post_id'	 => 254, // Страница "Проекты" // http://manctuning.ru/wp-admin/post.php?post=254&action=edit
		  'breadcrumb' => 'Проекты', // TODO: вытаскивать название из хлебных крошек страницы
		  ],
		  ] as $e ) {

		  if ( is_singular( $e['cpt'] ) ) {

		  // Страница "Услуги"
		  $post = get_post( $e['post_id'] );

		  // Добавляем элемент в ассоциативный массив ссылок
		  array_splice( $links, $offset = 1, $length = 0, [
		  [
		  'text'		 => $e['breadcrumb'],
		  'url'		 => get_permalink( $post ),
		  'allow_html' => '1',
		  ]
		  ] );
		  }
		  } */

		return $links;
	}

}

new WPPW_Yoast();


// Добавляем ссылки на архивы CPT (projects, services) =====================
add_filter( 'wpseo_breadcrumb_links', function ( $links ) {

	foreach ( [
 [
	'cpt'		 => 'wppw_service',
	'post_id'	 => 237, // Страница "Услуги" // http://manctuning.ru/wp-admin/post.php?post=237&action=edit
	'breadcrumb' => 'Услуги', // TODO: вытаскивать название из хлебных крошек страницы
],
 [
	'cpt'		 => 'wppw_project',
	'post_id'	 => 254, // Страница "Проекты" // http://manctuning.ru/wp-admin/post.php?post=254&action=edit
	'breadcrumb' => 'Проекты', // TODO: вытаскивать название из хлебных крошек страницы
],
	] as $e ) {



		if ( is_singular( $e['cpt'] ) ) {

			// Страница "Услуги"
			$post = get_post( $e['post_id'] );

			// Добавляем элемент в ассоциативный массив ссылок
			array_splice( $links, $offset = 1, $length = 0, [
				[
					'text'		 => $e['breadcrumb'],
					'url'		 => get_permalink( $post ),
					'allow_html' => '1',
				]
			] );
		}
	}

	return $links;
}, 10, 2 );

// Breadcrumbs ==============================================================
function breadcrumbs() {

	if ( !is_front_page() and function_exists( 'yoast_breadcrumb' ) ) {
		return yoast_breadcrumb( '<ul id="breadcrumbs">', '</ul>' );
	}
	else
		return'';
}

/**
 * Filter the output of Yoast breadcrumbs so each item is an <li> with schema markup
 * @param $link_output
 * @param $link
 *
 * @return string
 */
function doublee_filter_yoast_breadcrumb_items( $link_output, $link ) {

	$new_link_output = '<li>';
	$new_link_output .= '<a href="' . $link['url'] . '">' . $link['text'] . '</a>';
	$new_link_output .= '</li>';

	return $new_link_output;
}

add_filter( 'wpseo_breadcrumb_single_link', 'doublee_filter_yoast_breadcrumb_items', 10, 2 );

/**
 * Filter the output of Yoast breadcrumbs to remove <span> tags added by the plugin
 * @param $output
 *
 * @return mixed
 */
function doublee_filter_yoast_breadcrumb_output( $output ) {

	$from = '<span>';
	$to = '</span>';
	$output = str_replace( $from, $to, $output );

	return $output;
}

add_filter( 'wpseo_breadcrumb_output', 'doublee_filter_yoast_breadcrumb_output' );

/**
 * Shortcut function to output Yoast breadcrumbs
 * wrapped in the appropriate markup
 */
function doublee_breadcrumbs() {
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<ul>', '</ul>' );
	}
}
