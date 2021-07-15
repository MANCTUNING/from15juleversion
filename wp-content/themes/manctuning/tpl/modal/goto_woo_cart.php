<?php
/**
 * modal-goto_woo_cart
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
?>
<?php /* div class="modal b-modal fade" id="modal-goto_woo_cart" tabindex="-1" role="dialog" aria-labelledby="modal-callback" aria-hidden="true">
  <div class="modal-dialog">
  <button class="btn-close" data-dismiss="modal">+</button>
  <div class="b-modal-title text-center">
  <b>Товар добавлен в корзину</b>
  </div>
  <div class="b-modal-content">
  <div class="b-input-item">
  <button class="b-modal-send red-btn" onclick="window.location.href = '<?= wc_get_cart_url() ?>'">Оформить заказ</button>
  </div>
  <div class="b-input-item">
  <button class="b-modal-send red-btn" data-dismiss="modal">Продолжить покупки</button>
  </div>
  </div>
  </div>
  </div */ ?>
<style>.b-cart-content{width:100%;}</style>
<div class="modal b-modal fade wppw_modal_base" id="modal-goto_woo_cart">
    <div class="modal-dialog">
		<button class="btn-close" data-dismiss="modal">+</button>
		<div class="b-modal-title text-center goto_woo_cart__title wppw_modal_base__title">Товар добавлен в корзину</div>
		<div class="b-modal-content">


		</div>
		<div class="wppw_modal_base__buttons">
			<button class="b-modal-send red-btn checkout_button" onclick="window.location.href = '<?= wc_get_cart_url() ?>'">Перейти в корзину</button>
			<button class="b-modal-send dismiss_modal" data-dismiss="modal">Продолжить покупки</button>
		</div>
    </div>
</div>

<div class="modal b-modal fade wppw_modal_base" id="modal-add_to_wishlist">
    <div class="modal-dialog">
		<button class="btn-close" data-dismiss="modal">+</button>
		<div class="b-modal-title text-center woo_cart_fav__title wppw_modal_base__title">Товар добавлен в избранное</div>
		<div class="b-modal-content">

		</div>
		<div class="wppw_modal_base__buttons">
			<button class="b-modal-send red-btn checkout_button" onclick="window.location.href = '<?= get_permalink( 435 ) ?>'">Перейти в избранное</button>
			<button class="b-modal-send dismiss_modal" data-dismiss="modal">Продолжить выбор</button>
		</div>
    </div>
</div>

<div class="modal b-modal fade wppw_modal_base" id="modal-remove_from_wishlist">
    <div class="modal-dialog">
		<button class="btn-close" data-dismiss="modal">+</button>
		<div class="b-modal-title text-center woo_cart_fav_remove__title wppw_modal_base__title">Вы удалили товар из избранного</div>
		<div class="b-modal-content">

		</div>
		<div class="wppw_modal_base__buttons">
			<button class="b-modal-send red-btn" data-dismiss="modal" onclick="window.location.reload()">Перейти на сайт</button>
		</div>
    </div>
</div>


<div class="modal b-modal fade wppw_modal_base" id="modal-remove_from_cart">
    <div class="modal-dialog">
		<button class="btn-close" data-dismiss="modal">+</button>
		<div class="b-modal-title text-center woo_cart_fav_remove__title wppw_modal_base__title">Вы действительно хотите удалить товар из корзины?</div>
		<div class="b-modal-content">

		</div>
		<div class="wppw_modal_base__buttons">
			<button class="b-modal-send red-btn checkout_button" id="confirm_is_delete_from_cart__yes">Да, удалить</button>
			<button class="b-modal-send dismiss_modal" data-dismiss="modal">Нет, закрыть окно</button>
		</div>
    </div>
</div>