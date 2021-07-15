<?php
/**
 * Template name: Cart
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Тег заголовка страницы
$title_tag = get_field( 'title' )['tag'];
$title_text = get_field( 'title' )['text'] ?: get_the_title();
$title = sprintf( '<%s>%s</%s>', $title_tag, $title_text, $title_tag );

#exit( print_r( get_fields() ) );

$cart_total_current_price = 0; // Цена без скидок

$discount = 0; // Скидка
if ( is_user_logged_in() )
	$discount = 5; // Скидка за регистрацию

$cart = WC() -> cart -> get_cart();
$cart_count = count( $cart );
$cart_total = WC() -> cart -> total;

$title = sprintf( '<%s>В корзине товаров: %d</%s>', $title_tag, $cart_count, $title_tag );
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
						<?= $title ?>


						<?php // Корзина  ?>
						<?php if ( $cart_count > 0 ) { ?>

							<div class="b-cart-content">

								<?php
								foreach ( $cart as $i => $e ) {
									$product_id = $e['product_id'];
									
									// Количество
									$quantity = $e['quantity'];

									// Стоимость со скидкой
									//$price['current'] = $e['line_total'];
									$price['current'] = $e['data'] -> get_sale_price() * $quantity;
									// Цена без учёта скидки, базовая
									$price['regular'] = $e['data'] -> get_regular_price() * $quantity;

									// Собираем общую цену без скидок из обычных регулярных цен на товар
									$cart_total_current_price += $price['current'];

									// Ключ для идентификации выбранного продукта в корзине и последующего возможного удаления из корзины
									$cart_item_key = $e['key'];
									?>

									<div class="b-cart-item">
										<div class="b-cart-item__block">
											<div class="b-cart-item__block__img">
												<a href="<?= get_permalink( $product_id ) ?>"><img class="lazy" data-original="<?= get_the_post_thumbnail_url( $product_id ) ?>" alt=""></a>
											</div>
											<div class="b-cart-item__block__text">
												<span><?= $e['data'] -> get_sku() ?></span>
												<b><a href="<?= get_permalink( $product_id ) ?>"><?= $e['data'] -> get_title() ?></a></b>
											</div>
										</div>
										<div class="b-cart-item__right">
											<div class="b-cart-item__price">
												<span class="b-cart-item__price__new"><?= number_format( $price['current'], 0, ',', ' ' ) ?> руб.</span>
												<span class="b-cart-item__price__old">- <?= number_format( $price['regular'], 0, ',', ' ' ) ?> руб.</span>
											</div>
											<div class="b-quant">
												<button class="b-quant-btn minus wppw_decrease_from_cart" data-command="decrease" data-id="<?= esc_attr( $cart_item_key ) ?>" data-nonce="<?= wp_create_nonce( WPPW_NONCE ) ?>">-</button>
												<input class="b-quant-input wppw_product_quantity" type="text" value="<?= $quantity ?>">
												<button class="b-quant-btn plus wppw_increase_to_cart" data-command="increase" data-id="<?= esc_attr( $cart_item_key ) ?>" data-nonce="<?= wp_create_nonce( WPPW_NONCE ) ?>">+</button>
											</div>
											<button class="btn-delete icon-delete wppw_delete_from_cart" data-nonce="<?= wp_create_nonce( WPPW_NONCE ) ?>" data-command="delete" data-is_window_reload="1" data-id="<?= esc_attr( $cart_item_key ) ?>"></button>
										</div>
									</div>

								<?php } ?>

							</div>
						<?php } ?>

					</div>
					<div class="b-cart-content__right">
						<h3>Ваш заказ</h3>
						<div class="b-cart-content__order">
							<div class="b-cart-content__order__top">
								<ul>
									<li><p>Товаров<span>(<?= $cart_count ?>)</span></p><b><?= number_format( $cart_total_current_price, 0, ',', ' ' ) ?> руб.</b></li>
									<?php /*if ( is_user_logged_in() ) { ?><li><p>Скидка за регистрацию</p><b><?= number_format( $cart_total_current_price * $discount / 100, 0, ',', ' ' ) ?> руб.</b></li><?php } */ ?>
									<?php if ( is_user_logged_in() ) { ?><li><p>Скидка за регистрацию</p><b>5%</b></li><?php } ?>
								</ul>
								<?php if ( !is_user_logged_in() ) { ?>
									<a class="b-cart-content__order__top__sale" href="#" data-toggle="modal" data-target="#modal-login">Зарегистрироваться и получить
										скидку 5%</a>
								<?php } ?>
							</div>
							<div class="b-cart-content__order_promocode">
								<div class="b-cart-content__order_promocode__top">
									<span>Промокод</span>
								</div>
								<input class="b-input-text" type="text" value="">
								<div class="b-cart-content__order_promocode__bottom">
									<?php // TODO ?>
									  <span>Скидка за промокод</span>
									  <b>1 200 руб.</b>
								</div>
							</div>
							<div class="b-cart-content__order__total">
								<div class="b-cart-content__order__total__top">
									<span>Итого</span>
									<b><?= number_format( $cart_total, 0, ',', ' ' ) ?> руб.</b>
								</div>
								<button class="b-cart-content__order__total__btn red-btn" onclick="window.location.href = '<?= wc_get_checkout_url() ?>'">Оформить заказ</button>
								<div class="b-check">
									<input type="checkbox" checked id="a1" name="a1" />
									<label for="a1"><span></span><i>Я согласен на обработку
											персональных данных</i></label>  
								</div>	 									
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?= get_template_part( 'tpl/modal/goto_woo_cart' ) ?>

<?= get_footer() ?>