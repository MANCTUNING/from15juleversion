<?php

/**
 * Микроразметка
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Произвольные типы записей, в которых правим microdata
define( 'WPPW_CPT', [ 'wppw_project', 'wppw_service', 'wppw_tuning' ] );
#const WPPW_CPT = [ 'wppw_project', 'wppw_service', 'wppw_tuning' ];

// Полностью отключаем вывод JSON-LD в Yoast SEO
add_filter( 'wpseo_json_ld_output', '__return_empty_array' );

/**
 * LD JSON Article
 */
add_action( 'wp_head', function () {

	// Только на страницах записей
	if ( !is_singular( WPPW_CPT ) ) {
		return;
	}

	global $post;

	// Пишем разметку Article
	$publisher = [
		'@type'	 => 'Organization',
		'name'	 => get_bloginfo( 'name' ),
		'logo'	 => [
			'@type'	 => 'ImageObject',
			'url'	 => get_stylesheet_directory_uri() . '/images/logo.png',
			'width'	 => 84,
			'height' => 84,
		],
	];

	$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'full' );
	$image = [
		'@type'	 => 'ImageObject',
		'url'	 => $img[0],
		'width'	 => $img[1],
		'height' => $img[2],
	];

	$author = [
		'@type'	 => 'Organization',
		'name'	 => get_bloginfo( 'name' ),
	];

	$r = ( object ) [
				'@context'			 => 'http://schema.org',
				'@type'				 => 'Article',
				'headline'			 => get_the_title(),
				'datePublished'		 => get_the_date( 'c' ),
				'dateModified'		 => get_the_modified_date( 'c' ),
				'mainEntityOfPage'	 => [
					'@type'	 => 'WebPage',
					'@id'	 => get_the_permalink(),
				],
				'publisher'			 => $publisher,
				'image'				 => $image,
				'author'			 => $author,
	];

	echo '<script type="application/ld+json">' . wp_json_encode( $r ) . '</script>';
} );


/**
 * Add schema to yoast seo breadcrumbs
 */
add_filter( 'wpseo_breadcrumb_links', function ( $crumbs ) {

	// Только на страницах записей
	if ( !is_singular( WPPW_CPT ) ) {
		return $crumbs;
	}

	if ( !is_array( $crumbs ) || $crumbs === array() ) {
		return $crumbs;
	}
	$listItems = [];
	$j = 1;
	foreach ( $crumbs as $i => $crumb ) {
		$item = [];
		if ( isset( $crumb['id'] ) ) {
			$item = [
				'@id'	 => get_permalink( $crumb['id'] ),
				'name'	 => strip_tags( get_the_title( $id ) )
			];
		}
		if ( isset( $crumb['term'] ) ) {
			$term = $crumb['term'];
			$item = [
				'@id'	 => get_term_link( $term ),
				'name'	 => $term -> name
			];
		}
		if ( isset( $crumb['ptarchive'] ) ) {
			$postType = get_post_type_object( $crumb['ptarchive'] );
			$item = [
				'@id'	 => get_post_type_archive_link( $crumb['ptarchive'] ),
				'name'	 => $postType -> label
			];
		}
		if ( isset( $crumb['url'] ) ) {
			if ( $crumb['text'] !== '' ) {
				$title = $crumb['text'];
			}
			else {
				$title = get_bloginfo( 'name' );
			}
			$item = [
				'@id'	 => $crumb['url'],
				'name'	 => $title
			];
		}
		$listItem = [
			'@type'		 => 'ListItem',
			'position'	 => $j,
			'item'		 => $item
		];
		$listItems[] = $listItem;
		$j++;
	}
	$schema = [
		'@context'			 => 'http://schema.org',
		'@type'				 => 'BreadcrumbList',
		'itemListElement'	 => $listItems
	];
	$html = '<script type="application/ld+json">' . json_encode( $schema ) . '</script> ';
	echo $html;
	return $crumbs;
}, 10, 1 );

