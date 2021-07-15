<?php
/**
 * Template name: Login
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Восстановление пароля
if ( isset( $_GET['recover'] ) ) {

	// Если телефон определён
	if ( $tel = sanitize_text_field( $_POST['tel'] ) ) {

		// Телефон
		$tel = preg_replace( '/[^\d]/', '', $tel );

		// Ищем пользователей с телефоном $tel
		$user = get_users( [
					'meta_key'	 => 'user_data_pd_tel',
					'meta_value' => $tel,
				] )[0];

		// Если пользователь найден
		if ( !empty( $user -> ID ) and is_numeric( $user -> ID ) and $user -> ID > 0 ) {

			// Авторизовываем
			wp_set_auth_cookie( $user -> ID, $remember = true );

			// Авторизованный отправляется в ЛК
			wp_redirect( site_url( '/lk' ) );
			exit;
		}
		// Пользователь не найден
		else {
			$is_user_not_found = true;
		}
	}
}

// Авторизация
if ( $tel = sanitize_text_field( $_POST['tel'] ) and $pwd = sanitize_text_field( $_POST['pwd'] ) ) {

	// TODO: доделать авторизацию по паролю
	// Телефон
	$tel = preg_replace( '/[^\d]/', '', $tel );

	// Ищем пользователей с телефоном $tel
	$user = get_users( [
				'meta_key'	 => 'user_data_pd_tel',
				'meta_value' => $tel,
			] )[0];

	// Если пользователь найден
	if ( !empty( $user -> ID ) and is_numeric( $user -> ID ) and $user -> ID > 0 ) {

		// Авторизовываем
		wp_set_auth_cookie( $user -> ID, $remember = true );

		// Авторизованный отправляется в ЛК
		wp_redirect( site_url( '/lk' ) );
		exit;
	}
	// Пользователь не найден
	else {
		$is_user_not_found = true;
	}
}

// Авторизованный отправляется в ЛК
if ( is_user_logged_in() ) {
	wp_redirect( site_url( '/lk' ) );
	exit;
}

// Тег заголовка страницы
$title_tag = get_field( 'title' )['tag'];
$title_text = get_field( 'title' )['text'] ?: get_the_title();
$title = sprintf( '<%s>%s</%s>', $title_tag, $title_text, $title_tag );
?>
<?= get_header() ?>

<div class="modal-dialog">
	<div class="b-modal-title text-center">

		<?php
		// Восстановление пароля
		if ( isset( $_GET['recover'] ) ) {
			?>
			<b>Восстановление пароля</b>
			<span>Нужен телефон (<a href="<?= site_url( '/login' ) ?>">или авторизоваться</a>)</span>
			<?php
		}
		// Авторизация
		else {
			?>
			<b>Авторизация</b>
			<span>Нужен телефон и пароль (<a href="<?= site_url( '/login?recover' ) ?>">восстановить</a>)</span>
			<?php
		}
		?>

		<?php if ( $is_user_not_found ) {
			?>
			<span style="color:red;margin-top:1em;font-weight: bold">Пользователь не найден!</span>
		<?php } ?>
	</div>
	<form method="POST" id="wppw_login" class="b-modal-content">
		<div class="b-input-item">
			<input class="b-input-text text-center" name="tel" type="tel" placeholder="Телефон" value="" required>
		</div>
		<?php
		// Восстановление пароля
		if ( !isset( $_GET['recover'] ) ) {
			?>
			<div class="b-input-item">
				<input class="b-input-text text-center" name="pwd" type="text" placeholder="Пароль" value="" required>
			</div>
			<?php
		}
		?>
		<div class="b-input-item">
			<button class="b-modal-send red-btn">Отправить</button>
		</div>
	</form>
</div>

<?= get_footer() ?>