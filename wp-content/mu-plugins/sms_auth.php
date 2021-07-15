<?php

/**
 * SMS авторизация на сайте
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

final class WPPW_SMS_Auth {

	private $api_id = '4E78BAB7-D2BB-BDB9-3C23-FBC8847CD026'; // sms.ru api key
	private $sms_auth_prefix = 'wppw_sms_auth_tel__'; // Префикс для будущего транзитного кеша
	private $transient_expiration = 15 * MINUTE_IN_SECONDS; // Время экспирации транзитного кеша
	private $sms_prefix = 'Auth code manctuning.ru: '; // Префикс формы авторизации

	function __construct() {

		add_action( 'admin_action_q', [ $this, 'post' ] );

		// Генерация и отправка в sms кода авторизации
		add_action( 'wp_ajax_wppw_sms', [ $this, 'sms' ] );
		add_action( 'wp_ajax_nopriv_wppw_sms', [ $this, 'sms' ] );

		// Авторизация
		add_action( 'wp_ajax_wppw_auth', [ $this, 'auth' ] );
		add_action( 'wp_ajax_nopriv_wppw_auth', [ $this, 'auth' ] );
	}

	function sms() {

		// Получаем данные формы
		$r = wp_parse_args( $_POST['form'] );

		// Очищаем данные $_POST
		$r = array_map( function( $e ) {
			return sanitize_text_field( $e );
		}, $r );

		// Верификация
		wp_verify_nonce( $r['nonce'], WPPW_NONCE ) or exit;

		// +7 (342) 432-42-34 -> 73424324234
		$tel = preg_replace( '/[^\d]/', '', $r['tel'] );

		// Ключ, который нужно ввести
		$transient_key = $this -> sms_auth_prefix . $tel;

		// Устанавливаем транзитный кеш с ключом авторизации, если он ещё не существует
		#delete_transient( $transient_key );
		if ( false === $transient = get_transient( $transient_key ) ) {

			// Ключ авторизации, который отправляем позже в SMS
			$transient = rand( 1111, 9999 );

			// Сохраняем ключ в транзитном кеше
			set_transient( $transient_key, $transient, $this -> transient_expiration );
		}

		// $msg
		$msg = $this -> sms_prefix . $transient;

		// TODO: удалить после отладки
		#wp_send_json_success( [ 'msg' => 'Сообщение успешно отправлено', 'tel' => $tel, 'tra' => $transient ] );
		// TODO: заменить на отправку // https://sms.ru/sms/send
		#$url = "https://sms.ru/auth/check";
		$url = "https://sms.ru/sms/send";
		$args = [
			'body' => [
				'api_id' => $this -> api_id,
				'to'	 => $tel,
				'msg'	 => $msg,
				'json'	 => 1,
			],
		];

		$r = wp_remote_post( $url, $args );

		if ( !empty( $r['response']['code'] ) and 200 == $r['response']['code'] ) {
			wp_send_json_success( [ 'msg' => 'Сообщение успешно отправлено', 'tel' => $tel ] );
		}
		else {
			wp_send_json_error( [ 'msg' => 'Сообщение не отправлено' ] );
		}

		exit;
	}

	function auth() {

		// Получаем данные формы
		$r = wp_parse_args( $_POST['form'] );
		$r['tel'] = $_POST['tel'];

		// Очищаем данные $_POST
		$r = array_map( function( $e ) {
			return sanitize_text_field( $e );
		}, $r );

		// Верификация
		wp_verify_nonce( $r['nonce'], WPPW_NONCE ) or exit;

		// Выуживаем данные по коду из транзитного кеша
		$transient_key = $this -> sms_auth_prefix . $r['tel'];
		$transient = get_transient( $transient_key );

		// Если ключ есть и он соответствует тому, что сохранён в транзитном кеше
		if ( !empty( $r['code'] ) and $r['code'] == $transient ) {

			// Авторизовываем пользователя
			$result = $this -> login( $r['tel'] );
		}

		// Успешная авторизация
		if ( !empty( $result ) and 'success' == $result ) {
			wp_send_json_success( [ 'msg' => 'Авторизация успешна' ] );
		}
		else {
			wp_send_json_error( [ 'msg' => 'Авторизация безуспешна' ] );
		}

		exit;
	}

	/**
	 * Авторизация в поле
	 */
	private function login( $tel ) {

		// Ищем пользователя с нужным № телефона
		$user = get_users( [
				'meta_key'	 => 'user_data_pd_tel',
				'meta_value' => $tel,
			] )[0];

		// Если пользователь найден
		if ( !empty( $user -> ID ) and is_numeric( $user -> ID ) and $user -> ID > 0 ) {
			// Авторизовываем
			wp_set_auth_cookie( $user -> ID, $remember = true );
		}
		// Пользователь не существует, регистрируем
		else {

			$user_login = $tel;
			$user_email = $tel . '@manctuning.ru';
			$user_id = register_new_user( $user_login, $user_email );

			// Добавляем № телефона в данные пользователя
			$r['pd']['tel'] = $tel;
			update_field( 'user_data', $r, 'user_' . $user_id );

			// Авторизовываем
			wp_set_auth_cookie( $user_id, $remember = true );
		}

		return 'success';
	}

}

new WPPW_SMS_Auth();
