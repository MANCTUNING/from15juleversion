<?php

/**
 * Functions
 * **************************** */
defined( 'ABSPATH' ) or exit;

// HTML5
add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );

// Title
add_theme_support( 'title-tag' );

// Отключаем автоматическую обёртку <p>
/* remove_filter( 'the_content', 'wpautop' );

  // Отключаем обработку кавычек и других спецсимволов
  remove_filter( 'the_content', 'wptexturize' );
  remove_filter( 'the_title', 'wptexturize' );
  remove_filter( 'comment_text', 'wptexturize' ); */

// Удаляем рубрики
/* add_action( 'init', function () {
  register_taxonomy( 'category', [] );
  } ); */

/*
 * Scripts & styles
 */
add_action( 'wp_enqueue_scripts', function () {

	if ( defined( 'WP_DEBUG' ) and WP_DEBUG )
		$min = '.';
	else
		$min = '.min.';

	$src = get_stylesheet_directory_uri();

	// Bootstrap
	/* wp_enqueue_style( 'bootstrap', $src . '/bootstrap.min.css' );

	  if ( is_single() ) {

	  // https://highlightjs.org
	  wp_enqueue_style( 'highlight', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.14.2/styles/default.min.css' );
	  wp_enqueue_script( 'highlight', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.14.2/highlight.min.js' );
	  wp_add_inline_script( 'highlight', "hljs.initHighlightingOnLoad();" );
	  } */

	// Theme layout
	foreach ( [
	"lazyload"		 => "/js/jquery.lazyload.min.js",
	"fancybox"		 => "/js/jquery.fancybox.min.js",
	"select2"		 => "/js/select2.min.js",
	"slick"			 => "/js/slick.min.js",
	//"maskedinput"	 => "/js/jquery.maskedinput.min.js",
	"mask"			 => "/js/jquery.mask.min.js",
	"swiper"		 => "/js/swiper.min.js",
	"swiper-bundle"	 => "/js/swiper-bundle.min.js",
	"rating"		 => "/js/rating.js",
	"bootstrap"		 => "/bootstrap/js/bootstrap.min.js",
	"layout-script"	 => "/js/script.js",
	] as $k => $v ) {
		wp_enqueue_script( $k, $src . $v, [ 'jquery' ], NULL, TRUE );
	} unset( $v );

	// Optabs only on tuning
	if ( is_page_template( 'tpl/1tuning.php' ) or is_page_template( 'tpl/2tuning.php' ) ) {
		wp_enqueue_script( 'slider-comparsion', $src . '/js/slider-comparsion.js', [ 'jquery' ], NULL, TRUE );
		wp_enqueue_script( 'optabs', $src . '/js/optabs.js', [ 'jquery' ], NULL, TRUE );
	}

	// Только на странице каталога
	if ( is_page( 143 ) ) {
		// LD+JSON AggregateOffer
		wp_enqueue_script( 'ld_json_catalog', $src . '/js/ld_json/catalog.js', [ 'jquery' ], NULL, TRUE );
	}
	// Только на странице Товара
	if ( is_singular( [ 'product' ] ) ) {
		// LD+JSON AggregateOffer
		wp_enqueue_script( 'ld_json_product', $src . '/js/ld_json/product.js', [ 'jquery' ], NULL, TRUE );
	}


	// Main
	wp_enqueue_script( 'wppw', $src . '/js' . $min . 'js', array(), NULL, TRUE );
	#wp_enqueue_style( 'wppw', $src . '/scss/css.css' );
	#wp_enqueue_style( 'manctuning', get_stylesheet_uri() );
} );

// Регистрация навигационных меню
add_action( 'after_setup_theme', function () {
	register_nav_menus( [
		'header_menu'	 => 'Меню в шапке',
		'footer_menu_1'	 => 'Меню в подвале 1',
		'footer_menu_2'	 => 'Меню в подвале 2',
		'footer_menu_3'	 => 'Меню в подвале 3',
		'footer_menu_4'	 => 'Меню в подвале 4',
		'footer_menu_5'	 => 'Меню в подвале 5',
		'footer_menu_6'	 => 'Меню в подвале 6',
		'bottom_menu'	 => '1 меню в самом низу',
		'bottom_menu_2'	 => '2 меню в самом низу',
	] );
} );

// Options page
if ( function_exists( 'acf_add_options_page' ) ) {

	acf_add_options_page( array(
		'page_title' => 'Theme General Settings',
		'menu_title' => 'Настройки Manctuning',
		'menu_slug'	 => 'manctuning-general-settings',
		'capability' => 'edit_posts',
		'redirect'	 => false
	) );

	/* acf_add_options_sub_page( array(
	  'page_title'	 => 'Theme Header Settings',
	  'menu_title'	 => 'Header',
	  'parent_slug'	 => 'manctuning-general-settings',
	  ) );

	  acf_add_options_sub_page( array(
	  'page_title'	 => 'Theme Footer Settings',
	  'menu_title'	 => 'Footer',
	  'parent_slug'	 => 'manctuning-general-settings',
	  ) ); */
}

// CF7: Добавляем time picker // https://wphuntrz.com/d/1-how-to-add-custom-tag-time-in-contact-form-7
add_action( 'wpcf7_init', function () {
	wpcf7_add_form_tag( 'time', function ( $tag ) {

		if ( !$tag instanceof WPCF7_FormTag )
			return '';

		$name = $tag -> name;
		if ( empty( $name ) ) {
			$name = 'time';
		}

		return '<input class="b-form-input icon-time" type="time" placeholder="">';
	} );
} );
