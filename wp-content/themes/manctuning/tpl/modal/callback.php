<?php
/**
 * modal-callback
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
?>
<?php /* div class="modal b-modal fade" id="modal-callback" tabindex="-1" role="dialog" aria-labelledby="modal-callback" aria-hidden="true">
  <div class="modal-dialog">
  <button class="btn-close" data-dismiss="modal">+</button>
  <div class="b-modal-title text-center">
  <b>Перезвоните мне</b>
  <span>Пожалуйста введите свой номер телефона</span>
  </div>
  <div class="b-modal-content">
  <div class="b-input-item">
  <input class="b-input-text text-center" type="text" placeholder="ваше имя" value="">
  </div>
  <div class="b-input-item">
  <input class="b-input-text text-center" type="text" placeholder="+7 (800) 22-33-33" value="">
  </div>
  <div class="b-input-item">
  <button class="b-modal-send red-btn">Отправить</button>
  </div>
  </div>
  </div>
  </div */ ?>
<div class="modal b-modal fade" id="modal-callback" tabindex="-1" role="dialog" aria-labelledby="modal-callback" aria-hidden="true">
    <div class="modal-dialog">
		<button class="btn-close" data-dismiss="modal">+</button>
		<div class="b-modal-title text-center">
			<b>Перезвоните мне</b>
			<!--<span>Пожалуйста введите свой номер телефона</span>-->
		</div>
		<?= do_shortcode( '[contact-form-7 id="39" title="Перезвоните мне — modal-callback"]' ) ?>
    </div>
</div>