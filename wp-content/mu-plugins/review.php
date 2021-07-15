<?php

/**
 * Бек на отзывы
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// AJAX бекенд на форму отправки отзывов ================================================================
add_action( 'wp_ajax_wppw_review_form', 'wppw_review_form' );
add_action( 'wp_ajax_nopriv_wppw_review_form', 'wppw_review_form' );

function wppw_review_form() {

	$form = wp_parse_args( $_POST['form'] );

	$r = wp_parse_args( $form );

	// Очищаем данные $_POST
	$r = array_map( function( $e ) {
		return sanitize_text_field( $e );
	}, $r );

	// Верификация
	wp_verify_nonce( $r['nonce'], WPPW_NONCE ) or exit;

	// Добавляем отзыв 
	$comment = wppw_review_form__add( $r );

	// Благодарим за отзыв
	require TEMPLATEPATH . '/tpl/ajax/review.php';

	wp_send_json_success( [
		'html'		 => $html,
		'msg'		 => 'Спасибо',
		'comment'	 => $comment,
	] );

	exit;
}

function wppw_review_form__add( $r ) {

	$comment_data = [
		'comment_post_ID'	 => $r['post_id'],
		'author'			 => $r['name'],
	];

	// Неавторизованные пользователи получают анонимные emails
	if ( !is_user_logged_in() ) {
		$comment_data['email'] = 'anonymouse@manctuning.ru';
	}

	// Создаём пустой комментарий
	$comment = wp_handle_comment_submission( $comment_data );

	// Наполняем его
	update_field( 'review', [
		'rating'		 => $r['rating'] ?: '',
		'experience'	 => $r['experience'] ?: '',
		'is_recommend'	 => $r['is_recommend'] ?: '',
		'review'		 => [
			'text' => [
				'worth'			 => $r['worth'] ?: '',
				'disatvantages'	 => $r['disatvantages'] ?: '',
				'comment'		 => $r['comment'] ?: '',
			]
		],
		'meta'			 => [
			'is_anonymouse' => $r['is_anonymouse'],
		],
			], 'comment_' . $comment -> comment_ID );


	return $comment;
}
