<?php

/**
 * CPT
 * 
 * @author WPPW
 * @link http://wppw.ru
 * 
 * https://sheensay.ru/?p=1713
 * **************************** */
defined( 'ABSPATH' ) or exit;

final class WPPW_CPT {

	function __construct( $post_type = "" ) {

		/*
		 * Регистрируем Custom Post Type
		 */
		add_action( 'init', array( $this, 'wppw_cpt' ) );

		/**
		 * Добавляем колонку с миниатюрами
		 * 
		 * woocommerce/includes/admin/class-wc-admin-taxonomies.php
		 */
		#add_filter( 'manage_edit-wppw_wc_brand_columns', array( $this, 'product_cat_columns' ) );
		#add_filter( 'manage_wppw_wc_brand_custom_column', array( $this, 'product_cat_column' ), 10, 3 );

		/*
		 * Фильтруем URL
		 */
		#add_filter( 'post_type_link', array( $this, 'product_permalink_structure' ), 10, 2 );

		/*
		 * Чтобы работала пагинация
		 */
		#add_action( 'generate_rewrite_rules', array( $this, 'fix_product_category_pagination' ) );
	}

	function wppw_cpt() {

		/**
		 * Регистрируем произвольные таксономии к WC Product: 
		 * Производитель
		 */
		foreach ( [
	[
		'cpt'	 => 'wppw_wc_manufacturer',
		'label'	 => 'Производитель',
	],
	[
		'cpt'	 => 'wppw_wc_location',
		'label'	 => 'Расположение',
	],
	
		] as $e ) {
			register_taxonomy(
					$e['cpt'], 'product', array(
				'label'			 => $e['label'],
				'hierarchical'	 => false,
					#'query_var'		 => true,
					#'rewrite'		 => array( 'slug' => '' ),
					)
			);
		}

		// CPT Проекты, Услуги
		foreach ( [
	[
		'cpt'				 => 'wppw_project',
		'labels'			 => [
			'name'			 => 'Проекты', // Основное название
			'singular_name'	 => 'Проект', // Добавить
			'add_new'		 => 'Добавить новый', // Имя ссылки на новый запись в сайдбаре 
			'add_new_item'	 => 'Добавить новый проект', // Заголовок в редакторе при добавлении новой записи
		],
		'slug'				 => 'project',
		'capability_type'	 => 'post', // По образцу Post или Page
		'hierarchical'		 => false, // Включить ли иерархическую вложенность деревом
	], [
		'cpt'				 => 'wppw_service',
		'labels'			 => [
			'name'			 => 'Услуги', // Основное название
			'singular_name'	 => 'Услугу', // Добавить
			'add_new'		 => 'Добавить новую', // Имя ссылки на новый запись в сайдбаре 
			'add_new_item'	 => 'Добавить новую услугу', // Заголовок в редакторе при добавлении новой записи
		],
		'slug'				 => 'service',
		'capability_type'	 => 'post', // По образцу Post или Page
		'hierarchical'		 => false, // Включить ли иерархическую вложенность деревом
	], [
		'cpt'				 => 'wppw_tuning',
		'labels'			 => [
			'name'			 => 'Тюнинг', // Основное название
			'singular_name'	 => 'Тюнинг', // Добавить
			'add_new'		 => 'Добавить', // Имя ссылки на новый запись в сайдбаре 
			'add_new_item'	 => 'Добавить новый Тюнинг', // Заголовок в редакторе при добавлении новой записи
		],
		'slug'				 => 'tuning',
		'capability_type'	 => 'page', // По образцу Post или Page
		'hierarchical'		 => true, // Включить ли иерархическую вложенность деревом
	],
		] as $e ) {

			// Регистрируем новый тип записи
			register_post_type( $e['cpt'], [
				'labels'			 => $e['labels'],
				'public'			 => true,
				'publicly_queryable' => true,
				'show_ui'			 => true,
				'query_var'			 => true,
				'capability_type'	 => $e['capability_type'],
				'hierarchical'		 => $e['hierarchical'],
				'menu_position'		 => 4,
				'menu_icon'			 => 'dashicons-welcome-write-blog',
				'supports'			 => array( 'title', 'thumbnail', 'author' ),
				'rewrite'			 => array(
					'slug'		 => $e['slug'],
					'with_front' => false,
				),
					#'has_archive'		 => $e['slug'],
					#'taxonomies'		 => [ 'sheensay_product_type' ],
			] );
		}





		if ( current_user_can( 'manage_options' ) )
		// Вот с этой функцией осторожней. Она сбрасывает все правила определения URL. Лучше её закомментировать после завершения всех работ
			flush_rewrite_rules();
	}

	function product_permalink_structure( $post_link, $post ) {
		if ( FALSE !== strpos( $post_link, '%sheensay_product_type%' ) ) {
			$product_type_term = get_the_terms( $post -> ID, 'sheensay_product_type' );
			if ( !empty( $product_type_term ) )
				$post_link = str_replace( '%sheensay_product_type%', $product_type_term[0] -> slug, $post_link );
		}
		return $post_link;
	}

	function fix_product_category_pagination( $wp_rewrite ) {
		unset( $wp_rewrite -> rules[$this -> post_type . '/([^/]+)/page/?([0-9]{1,})/?$'] );
		$wp_rewrite -> rules = array(
			$this -> post_type . '/?$'							 => $wp_rewrite -> index . '?post_type=sheensay_product',
			$this -> post_type . '/page/?([0-9]{1,})/?$'		 => $wp_rewrite -> index . '?post_type=sheensay_product&paged=' . $wp_rewrite -> preg_index( 1 ),
			$this -> post_type . '/([^/]+)/page/?([0-9]{1,})/?$' => $wp_rewrite -> index . '?sheensay_product_type=' . $wp_rewrite -> preg_index( 1 ) . '&paged=' . $wp_rewrite -> preg_index( 2 ),
				) + $wp_rewrite -> rules;
	}

	function product_cat_columns( $columns ) {
		$new_columns = array();

		if ( isset( $columns['cb'] ) ) {
			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}

		$new_columns['thumb'] = __( 'Image', 'woocommerce' );

		$columns = array_merge( $new_columns, $columns );
		$columns['handle'] = '';

		return $columns;
	}

	/**
	 * Thumbnail column value added to category admin.
	 *
	 * @param string $columns Column HTML output.
	 * @param string $column Column name.
	 * @param int    $id Product ID.
	 *
	 * @return string
	 */
	public function product_cat_column( $columns, $column, $id ) {
		if ( 'thumb' === $column ) {
			// Prepend tooltip for default category.
			$default_category_id = absint( get_option( 'default_product_cat', 0 ) );

			if ( $default_category_id === $id ) {
				$columns .= wc_help_tip( __( 'This is the default category and it cannot be deleted. It will be automatically assigned to products with no category.', 'woocommerce' ) );
			}

			$thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			}
			else {
				$image = wc_placeholder_img_src();
			}

			// Prevent esc_url from breaking spaces in urls for image embeds. Ref: https://core.trac.wordpress.org/ticket/23605 .
			$image = str_replace( ' ', '%20', $image );
			$columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'woocommerce' ) . '" class="wp-post-image" height="48" width="48" />';
		}
		if ( 'handle' === $column ) {
			$columns .= '<input type="hidden" name="term_id" value="' . esc_attr( $id ) . '" />';
		}
		return $columns;
	}

}

/*
 * Запускаем класс
 * В скобках можно определить название ярлыка типа записи
 */
new WPPW_CPT();
