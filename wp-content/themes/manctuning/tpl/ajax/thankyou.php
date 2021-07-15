<?php
/**
 * Благодарим за заказ
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Имя и фамилия заказчика
if ( $r['first_name'] or $r['last_name'] )
	$getter = sprintf( '%s %s', $r['first_name'], $r['last_name'] );
else
	$getter = $r['tel'];

// Адрес доставки
$address = '';
$address .= $r['address_city'] ?: '';
$address .= $r['address_flat'] ? ', кв.' . $r['address_flat'] : '';
$address .= $r['address_floor'] ? ', этаж ' . $r['address_floor'] : '';
$address .= $r['address_entrance'] ? ', подъезд ' . $r['address_entrance'] : '';
$address .= $r['address_intercom'] ? ', домофон ' . $r['address_intercom'] : '';

// Способ оплаты
switch ( $r['payment_method'] ) {
	case 1:

		$payment_method = 'Оплата при получении';
		break;
	case 2:

		$payment_method = 'На счет организации';
		break;
	case 3:

		$payment_method = 'В кредит от тинькоф банка';
		break;

	default:
		$payment_method = 'Оплата при получении';
		break;
}

// Ближайшая доставка завтра
$delivery_date = wp_date( 'd F', strtotime( '+48 hours' ) );
$delivery_day = mb_strtolower( wp_date( 'l', strtotime( '+48 hours' ) ) );

ob_start();
?>

<div class="b-order-content" id="wppw_checkout_thankyou">
	<h3>Спасибо за заказ</h3>
	<div class="b-cart-thx">
		<div class="b-cart-thx__top">
			<div class="b-cart-thx__top__left">
				<b>Ваш заказ №<?= $order_id ?> оформлен!</b>
				<span>В ближайшее время ваш заказ будет подтвержден. </span>
				<span>В случае необходимости мы позвоним вам по номеру </span>
				<a href=""><?= $r['tel'] ?></a>
			</div>
			<div class="b-cart-thx__top__ico"></div>
		</div>
		<div class="b-cart-thx__row">
			<div class="b-cart-thx__row__top">Ваши данные</div>
			<div class="b-cart-thx__row__wrapper">
				<div class="b-cart-thx__row__block">
					<span>Получатель</span>
					<b><?= $getter ?></b>
				</div>
				<div class="b-cart-thx__row__block">
					<span>Тип оплаты</span>
					<b><?= $payment_method ?></b>
				</div>										
			</div>
			<div class="b-cart-thx__row__block">
				<span>Телефон</span>
				<b><?= $r['tel'] ?></b>
			</div>									
		</div>
		<div class="b-cart-thx__row">
			<div class="b-cart-thx__row__top">Информация о доставке</div>
			<div class="b-cart-thx__row__block">
				<span>Адрес</span>
				<b><?= $address ?></b>
			</div>									
		</div>

		<div class="b-cart-thx__bottom">
			<div class="b-cart-thx__row__block">
				<span>Получение</span>
				<b>Доставка курьером <?= $delivery_date ?>, <?= $delivery_day ?> с 10:00 до 18:00</b>
			</div>
			<div class="b-cart-thx__bottom__block">
				<div class="b-cart-thx__bottom__block__ico">?</div>
				<div class="b-cart-thx__bottom__block__text">
					<span>Остались вопросы или нашли ошибку</span>
					<?php $tel = get_field( 'tel', 'options' ); ?>
					<a href="tel:<?= $tel['num'] ?>"><?= $tel['html'] ?></a>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$html = ob_get_clean();
