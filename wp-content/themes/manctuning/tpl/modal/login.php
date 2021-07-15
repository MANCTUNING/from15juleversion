<?php
/**
 * modal-login
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
?>
<form class="modal b-modal fade wppw_modal_sms" id="modal-login" tabindex="-1" role="dialog" aria-labelledby="modal-login" aria-hidden="true" >
	<div class="modal-dialog">
		<button class="btn-close" data-dismiss="modal">+</button>
		<div class="b-modal-title text-center">
			<b>Войти в кабинет</b>
			<span>Пожалуйста введите свой номер телефона</span>
		</div>
		<div class="b-modal-content">
			<div class="b-input-item">
				<input name="tel" class="b-input-text text-center" type="tel" placeholder="" value="" required>
			</div>
			<div class="b-input-item">
				<input type="hidden" name="nonce" value="<?= wp_create_nonce( WPPW_NONCE ) ?>">
				<button type="submit" class="b-modal-send red-btn">Далее</button>
			</div>
		</div>
		<div class="b-modal-bottom">
			<p>Из личного кабинета можно оформить заказ со скидкой 5% 
				на все товары, а также оттуда открывается доступ к скрытой 
				категории предложений</p>
		</div>
	</div>
</form>


<form class="modal b-modal fade wppw_modal_auth" id="modal-login2" tabindex="-1" role="dialog" aria-labelledby="modal-login2" aria-hidden="true">
    <div class="modal-dialog">
		<button class="btn-close" data-dismiss="modal">+</button>
		<div class="b-modal-title text-center">
			<b>Войти в кабинет</b>
			<span>Отправили вам код в смс, введите его</span>
		</div>
		<div id="modal-login2-form" class="b-modal-content">
			<div class="b-input-item">
				<input type="number" maxlength="4" class="b-input-text text-center" name="code" type="text" placeholder="" value="">
			</div>
			<div class="b-input-item">
				<input type="hidden" name="nonce" value="<?= wp_create_nonce( WPPW_NONCE ) ?>">
				<button type="submit" class="b-modal-send red-btn">Войти</button>
			</div>
		</div>
		<div class="b-modal-bottom">
			<p>Из личного кабинета можно оформить заказ со скидкой 5% 
				на все товары а также открывается доступ к скрытой 
				категории предложений</p>
		</div>
    </div>
</form>