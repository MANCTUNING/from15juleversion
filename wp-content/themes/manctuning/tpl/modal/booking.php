<?php
/**
 * modal-booking
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
?>
<div class="modal b-modal fade" id="modal-booking" tabindex="-1" role="dialog" aria-labelledby="modal-booking" aria-hidden="true">
    <div class="modal-dialog">
		<button class="btn-close" data-dismiss="modal">+</button>
		<div class="b-modal-title">
			<b>Приехать на тюннинг просто 
				как 1,2,3. </b>
			<strong>Выберите услугу и запишитесь онлайн </strong>
			<p>С вами свяжется менеджер, подтвердит визит и подготовит 
				все необходимое для тюнинга или дополнительных услуг</p>
		</div>
		<?= do_shortcode( '[contact-form-7 id="94" title="Приехать на тюнинг просто как 1,2,3 — modal-booking" html_class="b-modal-content"]' ) ?>
	</div>
</div>