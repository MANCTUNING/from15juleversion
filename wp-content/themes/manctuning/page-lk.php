<?php
/**
 * Template name: ЛК
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Неавторизованный отправляется на авторизацию
if ( !is_user_logged_in() ) {
	wp_redirect( site_url( '/login' ) );
	exit;
}

// Текущий пользователь
$user = wp_get_current_user();
$user_id = $user -> ID;

#exit( print_r( get_fields( 'user_' . $user_id  ) ) );
// Адрес пользователя
foreach ( [ 'address_city', 'address_flat', 'address_floor', 'address_entrance', 'address_intercom', ] as $e ) {

	if ( $addr = sanitize_text_field( $_POST[$e] ) ) {

		$r['address'][$e] = $addr;

		update_field( 'user_data', $r, 'user_' . $user_id );
	}
} unset( $e );

// Данные пользователя
foreach ( [ 'first_name', 'last_name', 'tel', 'tel2', 'email', ] as $e ) {

	// tel сокращаем до чисел
	if ( in_array( $e, [ 'tel', 'tel2' ] ) ) {
		$_POST[$e] = preg_replace( '/[^\d]/', '', $_POST[$e] );
	}

	if ( $pd = sanitize_text_field( $_POST[$e] ) ) {

		$r['pd'][$e] = $pd;

		update_field( 'user_data', $r, 'user_' . $user_id );
	}
} unset( $e );


// Вытаскиваем данные пользователя
$user_data = get_field( 'user_data', 'user_' . $user_id );

// Адрес пользователя
foreach ( [ 'address_city', 'address_flat', 'address_floor', 'address_entrance', 'address_intercom', ] as $e ) {

	$$e = $user_data['address'][$e];
} unset( $e );

// Данные пользователя
foreach ( [ 'first_name', 'last_name', 'tel', 'tel2', 'email', ] as $e ) {

	$$e = $user_data['pd'][$e];
} unset( $e );

// Тег заголовка страницы
$title_tag = get_field( 'title' )['tag'];
$title_text = get_field( 'title' )['text'] ?: get_the_title();
$title = sprintf( '<%s>%s</%s>', $title_tag, $title_text, $title_tag );
?>
<?= get_header() ?>

<div class="b-personal-area">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-breadcreambs">
					<?= breadcrumbs() ?>
				</div>
				<?= $title ?>
				<div class="b-personal-area__top">
					<div class="b-personal-area__top__item">
						<b><?= get_current_user_id() ?></b>
						<span>Номер личного
							кабинета</span>
					</div>
					<div class="b-personal-area__top__item">
						<b>5%</b>
						<span>Персональный<br/>
							номинал скидок</span>
					</div>
					<div class="b-personal-area__top__item">
						<b><?= number_format( wc_get_customer_total_spent( $user_id ), 0, ',', ' ' ) ?> руб.</b>
						<span>Общая<br/>
							сумма покупок</span>
					</div>
					<div class="b-personal-area__top__item">
						<b>500 000 руб.</b>
						<span>До увеличения<br/>
							номинала скидки до 7%</span>
					</div>																					
				</div>



				<div class="b-personal-area__content">
					<form method="POST" class="b-personal-area__col" id="wppw_lk_pd">
						<div class="b-personal-area__col__top">
							<h4>Личные данные</h4>
							<div class="b-input-item">
								<span>Имя <i>*</i></span>
								<input name="first_name" class="b-input-text input-text" type="text" placeholder="" value="<?= $first_name ?>">
							</div>
							<div class="b-input-item ">
								<span>Фамилия <i>*</i></span>
								<input name="last_name" class="b-input-text input-text" type="text" placeholder="" value="<?= $last_name ?>">
							</div>
							<div class="b-input-item">
								<span>Телефон <i>*</i></span>
								<input name="tel" class="b-input-text phone" type="tel" placeholder="" value="<?= $tel ?>" required>
							</div>
							<div class="b-input-item ">
								<span>Дополнительный телефон</span>
								<input name="tel2" class="b-input-text phone" type="tel" placeholder="" value="<?= $tel2 ?>">
							</div>
							<div class="b-input-item">
								<span>Электронная почта</span>
								<input name="email" class="b-input-text" type="email" placeholder="" value="<?= $email ?>">
							</div>
						</div>
						<input class="btn-change" type="submit" value="Изменить">														
					</form>

					<form method="POST" class="b-personal-area__col" id="wppw_lk_address">
						<div class="b-personal-area__col__top">
							<h4>Адрес доставки</h4>
							<div class="b-input-item">
								<span>Город, улица, дом</span>
								<input name="address_city" class="b-input-text" type="text" maxlength="100" placeholder="" value="<?= $address_city ?>">
							</div>
							<div class="b-input-item">
								<span>Квартира</span>
								<input name="address_flat" class="b-input-text input-number" type="text" maxlength="5" placeholder="" value="<?= $address_flat ?>">
							</div>
							<div class="b-input-item">
								<span>Этаж</span>
								<input name="address_floor" class="b-input-text input-number" type="text" maxlength="5" placeholder="" value="<?= $address_floor ?>">
							</div>
							<div class="b-input-item">
								<span>Подъезд</span>
								<input name="address_entrance" class="b-input-text input-number" type="text" maxlength="5" placeholder="" value="<?= $address_entrance ?>">
							</div>
							<div class="b-input-item">
								<span>Домофон</span>
								<input name="address_intercom" class="b-input-text input-number" type="text" maxlength="5" placeholder="" value="<?= $address_intercom ?>">
							</div>
						</div>
						<input class="btn-change" type="submit" value="Изменить">														
					</form>

				</div>

				<?php /* div class="b-personal-area__content">
				  <div class="b-personal-area__col">
				  <div class="b-personal-area__col__top">
				  <h4>Личные данные</h4>
				  <div class="b-input-item">
				  <span>ФИО</span>
				  <input class="b-input-text input-text" type="text" placeholder="" value="<?= $fio ?>">
				  </div>
				  <div class="b-input-item">
				  <span>Телефон</span>
				  <input class="b-input-text phone input-number" type="text" placeholder="" value="<?= $tel ?>">
				  </div>
				  <div class="b-input-item">
				  <span>Дата рождения</span>
				  <input class="b-input-text input-number date-mask" type="text" placeholder="" value="<?= $dr ?>">
				  </div>
				  </div>
				  <input class="btn-change" type="submit" value="Изменить">
				  </div>
				  <div class="b-personal-area__col">
				  <div class="b-personal-area__col__top">
				  <h4>Адрес доставки</h4>
				  <div class="b-input-item">
				  <span>Город</span>
				  <input class="b-input-text" type="text" placeholder="" value="<?= $city ?>">
				  </div>
				  <div class="b-input-item">
				  <span>Улица</span>
				  <input class="b-input-text" type="text" placeholder="" value="<?= $street ?>">
				  </div>
				  </div>
				  <input class="btn-change" type="submit" value="Изменить">
				  </div>
				  </div */ ?>


				<?php
				// Если есть история заказов
				if ( $orders = wc_get_orders( [
					'customer_id'	 => $user_id,
					'limit'			 => -1,
					'orderby'		 => 'date',
					'order'			 => 'DESC',
					//'return'	 => 'ids',
					] ) ) {
					?>
					<div class="b-personal-area__history">
						<div class="b-personal-area__history__top">
							<h4>История заказов</h4>
						</div>
						<table>
							<thead>
								<tr>
									<td><b>Номер заказа</b></td>
									<td><b>Дата заказа</b></td>
									<td><b>Статус</b></td>
								</tr>
							</thead>
							<tbody>

								<?php
								foreach ( $orders as $order ) {

									// Номер заказа
									$order_id = $order -> get_id();

									// Дата создания заказа
									$order_date = $order -> get_date_created() -> format( get_option( 'date_format' ) );

									// Статус заказа
									$order_status = $order -> get_status();
									$statuses = [
										'completed'					 => [ 'paid', 'Завершен' ],
										'pending'					 => [ 'await', 'Ожидание оплаты' ],
										'failed'					 => [ 'fail', 'Отказ' ],
										'processing'				 => [ 'await', 'В обработке' ],
										'on-hold'					 => [ 'await', 'На удержании' ],
										'canceled'					 => [ 'fail', 'Отменён' ],
										'cancelled'					 => [ 'fail', 'Отменён' ],
										'refunded'					 => [ 'paid', 'Возврат' ],
										'authentication-required'	 => [ 'fail', 'Необходима авторизация' ],
									];
									$order_status = $statuses[$order_status];
									?>
									<tr>
										<td><?= $order_id ?></td>
										<td><?= $order_date ?></td>
										<td><span class="b-order-status <?= $order_status[0] ?>"><?= $order_status[1] ?></span></td>
									</tr>
								<?php } unset( $order ) ?>
								<?php /* tr>
								  <td>234554</td>
								  <td>28.04.2015</td>
								  <td><span class="b-order-status paid">оплачен</span></td>
								  </tr>
								  <tr>
								  <td>234554</td>
								  <td>28.04.2015</td>
								  <td><span class="b-order-status await">ожидает оплаты</span></td>
								  </tr */ ?>																
							</tbody>
						</table>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?= get_footer() ?>