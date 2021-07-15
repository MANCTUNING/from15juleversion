<?php
/**
 * Template name: Checkout
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Тег заголовка страницы
$title_tag = get_field( 'title' )['tag'];
$title_text = get_field( 'title' )['text'] ?: get_the_title();
$title = sprintf( '<%s>%s</%s>', $title_tag, $title_text, $title_tag );


$cart = WC() -> cart -> get_cart();
$cart_count = count( $cart );
$cart_total = WC() -> cart -> total;

// Считаем общий ценник без скидки
$cart_total_current_price = 0;
array_map( function ( $e ) use( &$cart_total_current_price ) {
	$cart_total_current_price += $e['data'] -> get_price();
}, $cart );

$discount = 0; // Скидка
if ( is_user_logged_in() )
	$discount = 5; // Скидка за регистрацию

$title = sprintf( '<%s>В корзине товаров: %d</%s>', $title_tag, $cart_count, $title_tag );

// Ближайшая доставка завтра
$delivery_date = wp_date( 'd F', strtotime( '+24 hours' ) );
$delivery_day = wp_date( 'l', strtotime( '+24 hours' ) );


// Если пользователь авторизован
if ( is_user_logged_in() ) {

	// Вытаскиваем данные пользователя
	$user_data = get_field( 'user_data', 'user_' . get_current_user_id() );

	// Адрес пользователя
	foreach ( [ 'address_city', 'address_flat', 'address_floor', 'address_entrance', 'address_intercom', ] as $e ) {

		$$e = $user_data['address'][$e];
	} unset( $e );

	// Данные пользователя
	foreach ( [ 'first_name', 'last_name', 'tel', 'tel2', 'email', ] as $e ) {

		$$e = $user_data['pd'][$e];
	} unset( $e );
}
?>
<?= get_header() ?>

<div class="b-wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-breadcreambs">
					<?= breadcrumbs() ?>
				</div>
				<div class="b-cart b-wrapper-content">
					<div class="b-cart-content__left">
						<h3>Оформление заказа</h3>

						<form class="b-order-content" id="wppw_checkout">
							<div class="b-order-method">
								<div class="b-order-title">Выберите способ получения</div>
								<div class="b-order-method__content">
									<div class="b-order-method__item">
										<div class="b-order-method__item__radio">
											<input type="radio" id="v1" checked="" name="delivery_method" value="2">
											<label for="v1"></label>
										</div>
										<div class="b-order-method__item__left">
											<b>Доставка курьером</b>
											<span>Ближайшая доставка 
												<?= $delivery_date ?> </span>
										</div>
										<strong>Бесплатно</strong>
									</div>
									<div class="b-order-method__item">
										<div class="b-order-method__item__radio">
											<input type="radio" id="v2" name="delivery_method" value="1" checked>
											<label for="v2"></label>
										</div>											
										<div class="b-order-method__item__left">
											<b>Самовывоз</b>
											<span>Москва, ул. Иркутская, дом 1, офис 1</span>
										</div>
										<strong>Бесплатно</strong>
									</div>										
								</div>
							</div>

							<?php /* div class="b-order-transport">
							  <div class="b-order-transport__top">
							  <div class="b-order-title">Транспортной компанией</div>
							  <p>Доставка транспортными компаниями осуществляется за счет клиента. Все расходы по доставке
							  товаров со склада транспортными компаниями включит в свой счет - будьте внимательны!</p>
							  <div class="b-order-transport__top__wrap">
							  <a href="" class="b-order-transport__top__link">Подробнее</a>
							  </div>
							  </div>
							  <div class="b-order-transport__content">
							  <div class="b-order-transport__content__col">
							  <div class="b-order-transport__item">
							  <div class="b-radio">
							  <input type="radio" checked id="a1" value="1" name="delivery_tk" />
							  <label for="a1"><span></span><i><b>ПЭК</b></i></label>
							  </div>
							  <a href="#" class="b-order-transport__item__link" data-toggle="modal" data-target="#modal-callback" >Расчет стоимости</a>
							  </div>
							  <div class="b-order-transport__item">
							  <div class="b-radio">
							  <input type="radio" id="a2" value="2" name="delivery_tk" />
							  <label for="a2"><span></span><i><b>СДЭК</b></i></label>
							  </div>
							  <a href="#" class="b-order-transport__item__link" data-toggle="modal" data-target="#modal-callback" >Расчет стоимости</a>
							  </div>
							  <div class="b-order-transport__item">
							  <div class="b-radio">
							  <input type="radio" id="a3" value="3" name="delivery_tk" />
							  <label for="a3"><span></span><i><b>Деловые линии</b></i></label>
							  </div>
							  <a href="#" class="b-order-transport__item__link" data-toggle="modal" data-target="#modal-callback" >Расчет стоимости</a>
							  </div>
							  <div class="b-order-transport__item">
							  <div class="b-radio">
							  <input type="radio" id="a4" value="4" name="delivery_tk" />
							  <label for="a4"><span></span><i><b>Возовоз</b></i></label>
							  </div>
							  <a href="#" class="b-order-transport__item__link" data-toggle="modal" data-target="#modal-callback" >Расчет стоимости</a>
							  </div>
							  </div>
							  <div class="b-order-transport__content__col">
							  <div class="b-order-transport__item">
							  <div class="b-radio">
							  <input type="radio" id="a5" value="5" name="delivery_tk" />
							  <label for="a5"><span></span><i><b>Байка сервис</b></i></label>
							  </div>
							  <a href="#" class="b-order-transport__item__link" data-toggle="modal" data-target="#modal-callback" >Расчет стоимости</a>
							  </div>
							  <div class="b-order-transport__item">
							  <div class="b-radio">
							  <input type="radio" id="a6" value="6" name="delivery_tk" />
							  <label for="a6"><span></span><i><b>ЖелДорАльянс</b></i></label>
							  </div>
							  <a href="#" class="b-order-transport__item__link" data-toggle="modal" data-target="#modal-callback" >Расчет стоимости</a>
							  </div>
							  <div class="b-order-transport__item">
							  <div class="b-radio">
							  <input type="radio" id="a7" value="7" name="delivery_tk" />
							  <label for="a7"><span></span><i><b>Автотрейдинг</b></i></label>
							  </div>
							  <a href="#" class="b-order-transport__item__link" data-toggle="modal" data-target="#modal-callback" >Расчет стоимости</a>
							  </div>
							  <div class="b-order-transport__item">
							  <div class="b-radio">
							  <input type="radio" id="a8" value="8" name="delivery_tk" />
							  <label for="a8"><span></span><i><b>Кит</b></i></label>
							  </div>
							  <a href="#" class="b-order-transport__item__link" data-toggle="modal" data-target="#modal-callback" >Расчет стоимости</a>
							  </div>
							  </div>
							  </div>

							  </div */ ?>

							<div class="b-order-inputs wppw_shipping_address">
								<div class="b-order-title">Адрес доставки</div>
								<div class="b-order-inputs__content">
									<div class="b-input-item">
										<span>Город, улица, дом</span>
										<input name="address_city" class="b-input-text" type="text" maxlength="100" placeholder="" value="<?= $address_city ?>">
									</div>
									<div class="b-input-item b-col-25">
										<span>Квартира</span>
										<input name="address_flat" class="b-input-text input-number" type="text" maxlength="5" placeholder="" value="<?= $address_flat ?>">
									</div>
									<div class="b-input-item b-col-25">
										<span>Этаж</span>
										<input name="address_floor" class="b-input-text input-number" type="text" maxlength="5" placeholder="" value="<?= $address_floor ?>">
									</div>
									<div class="b-input-item b-col-25">
										<span>Подъезд</span>
										<input name="address_entrance" class="b-input-text input-number" type="text" maxlength="5" placeholder="" value="<?= $address_entrance ?>">
									</div>
									<div class="b-input-item b-col-25">
										<span>Домофон</span>
										<input name="address_intercom" class="b-input-text input-number" type="text" maxlength="5" placeholder="" value="<?= $address_intercom ?>">
									</div>
									<div class="b-input-item">
										<span>Комментарии курьеру</span>
										<input name="address_comment" class="b-input-text" type="text" maxlength="100" placeholder="" value="">
									</div>																							
								</div>									
							</div>
							<div class="b-order-inputs">
								<div class="b-order-title">Получатель</div>
								<div class="b-order-inputs__content">
									<div class="b-input-item b-col-50">
										<span>Имя <i>*</i></span>
										<input name="first_name" class="b-input-text input-text" type="text" placeholder="" value="<?= $first_name ?>">
									</div>
									<div class="b-input-item b-col-50">
										<span>Фамилия <i>*</i></span>
										<input name="last_name" class="b-input-text input-text" type="text" placeholder="" value="<?= $last_name ?>">
									</div>
									<div class="b-input-item b-col-50">
										<span>Телефон <i>*</i></span>
										<input name="tel" class="b-input-text phone" type="tel" placeholder="" value="<?= $tel ?>" required>
									</div>	
									<div class="b-input-item b-col-50">
										<span>Дополнительный телефон</span>
										<input name="tel2" class="b-input-text phone" type="tel" placeholder="<?= $tel2 ?>" value="">
									</div>																				
									<div class="b-input-item b-col-50">
										<span>Электронная почта</span>
										<input name="email" class="b-input-text" type="email" placeholder="" value="<?= $email ?>">
									</div>
								</div>
							</div>

							<div class="b-order-inputs">
								<div class="b-order-title">Дата и время доставки</div>
								<div class="b-order-method__item b-order-method__item__two">
									<div class="b-order-method__item__left">
										<b><?= $delivery_date ?>, <?= $delivery_day ?></b>
										<span>с 10:00 до 18:00</span>
									</div>
									<strong>Изменить</strong>
								</div>									
							</div>

							<div class="b-order-pay">
								<div class="b-order-title">Способы оплаты</div>
								<div class="b-order-pay__content">
									<div class="b-order-pay__left">
										<div class="b-radio">
											<input type="radio" id="b2" name="payment_method" checked value="1">
											<label for="b2"><span></span><i><b>Оплатить при получении</b>Наличными или банковской картой</i></label>  
										</div>
										<div class="b-radio">
											<input type="radio" id="b3" name="payment_method" value="2">
											<label for="b3"><span></span><i><b>На счет организации</b>На счет ИП или ООО</i></label>  
										</div>
										<?php /* div class="b-radio">
										  <input type="radio" id="b4" name="payment_method" value="3">
										  <label for="b4"><span></span><i><b>В кредит от тинькоф банка</b>До 300 000 руб. <br/>Решение за 30 минут</i></label>
										  </div */ ?>																			
									</div>
								</div>
							</div>
							<div class="b-order-bottom">
								<div class="b-check">
									<input type="checkbox" checked id="a11">
									<label for="a11"><span></span><i>Я соглашаюсь с <a href="">условиями заказа и доставки</a>, на обработку персональных данных в соответствии 
											с <a href="">условиями использования сайта, политикой обработки персональных данных</a> и на получение 
											сообщений в процессе обработки заказа</i></label>  
								</div>									
								<button class="b-order-btn red-btn" id="wppw_checkout__submit">Оформить заказ</button>
							</div>

							<?= wp_nonce_field( $action = WPPW_NONCE, $name = 'nonce' ) ?>

						</form>

					</div>

					<div class="b-cart-content__right">
						<div class="b-cart-content__right__top">
							<h3>Ваш заказ</h3>
							<a href="<?= wc_get_cart_url() ?>" class="b-cart-content__right__top__change">Изменить</a>
						</div>		
						<div class="b-cart-content__order">
							<div class="b-cart-content__order__top">
								<ul>
									<?php /* li><p>Товаров<span>(<?= $cart_count ?>)</span></p><b><?= number_format( $cart_total * 100 / (100 - $discount), 0, ',', ' ' ) ?>  руб.</b></li>
									  <li><p>Скидка за регистрацию</p><strong>- <?= number_format( 0.01 * $discount * $cart_total * 100 / (100 - $discount), 0, ',', ' ' ) ?> руб.</strong></li */ ?>
									<li><p>Товаров<span>(<?= $cart_count ?>)</span></p><b><?= number_format( $cart_total_current_price, 0, ',', ' ' ) ?>  руб.</b></li>
									<?php if ( is_user_logged_in() ) { ?>
										<li><p>Скидка за регистрацию</p><strong>- 5%</strong></li>
									<?php } ?>
									<?php /* li><p>Скидка за промокод</p><strong>- 1 200 руб.</strong></li */ ?>
								</ul>
							</div>
							<div class="b-cart-content__order__total">
								<div class="b-cart-content__order__total__top">
									<span>Итого</span>
									<b><?= number_format( $cart_total, 0, ',', ' ' ) ?> руб.</b>
								</div> 									
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?= get_template_part( 'tpl/modal/callback' ) ?>

<?= get_footer() ?>