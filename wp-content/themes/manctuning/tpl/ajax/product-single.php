<?php
/**
 * Раскрытые данные по товару
 * 
 * Принимает 1 глобальный объект - post
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $post;

// $post должен быть определён
$post or exit;

$comments_count = wp_count_comments( $post -> ID ) -> approved;

// WC
$product = wc_get_product( $post -> ID );

// ID товара
$product_id = $product -> get_id();

// sku
$sku = $product -> get_sku();

// Rating
$rating = $product -> get_average_rating();
$count = $product -> get_rating_count();
if ( $rating <= 0 )
	$rating = 5;

// Наличие остатков
$stock_status = $product -> get_stock_status();
$stock_statuses = [
	'instock'	 => '<div class="b-card-status inStock">В наличии на складе</div>',
	'outofstock' => '<div class="b-card-status not">Нет в наличии</div>',
	'onrequest'	 => '<div class="b-card-status">Предзаказ</div>',
];

// Цены
$price['regular'] = number_format( $product -> get_regular_price(), 0, ',', ' ' );
$price['current'] = number_format( $product -> get_price(), 0, ',', ' ' );

// Установка
$install = get_field( 'install' );
$install['regular'] = number_format( $install['regular'], 0, ',', ' ' );
$install['current'] = number_format( $install['current'], 0, ',', ' ' );

// Ищем, нет ли продукта в корзине
// https://www.businessbloomer.com/woocommerce-easily-check-product-id-cart/
// ID корзины
$cart_id = WC() -> cart -> generate_cart_id( $product_id );
// Должен содержать ID корзины или false
$in_cart = WC() -> cart -> find_product_in_cart( $cart_id );

// Ближайшая доставка завтра
$delivery_date = wp_date( 'd F', strtotime( '+24 hours' ) );
$delivery_day = wp_date( 'l', strtotime( '+24 hours' ) );
?>
<?php
if ( $gallery = get_field( 'gallery' ) ) {
	?>
	<div class="b-card-slider">
		<div class="b-card-slider__preview">
			<?php
			foreach ( $gallery as $img ) {
				?>
				<div class="b-card-slider__preview__item">
					<img data-lazy="<?= kama_thumb_src( [ 'w' => 188, 'h' => 137 ], $img ) ?>" alt="">
				</div>
			<?php } unset( $img ) ?>
		</div>							
		<div class="b-card-slider__content">
			<?php
			foreach ( $gallery as $img ) {
				?>
				<a href="<?= $img ?>" data-fancybox="images/card" class="wppw_gallery_img b-card-slider__content__item" data-toggle="modal">
					<img data-lazy="<?= $img ?>" alt="">
				</a>
			<?php } unset( $img ) ?>
		</div>
	</div>
<?php } ?>

<div class="b-card-right-top">
	<span class="b-card-name"><h1><?= get_the_title() ?></h1></span>
	<div class="b-card-article">Артикул: <span class="wppw_sku"><?= $sku ?></span></div>
</div>

<div class="b-card-right">
	<div class="b-card-row b-card-row__info">

		<div class="b-card-row__rating">
			<?= get_template_part( 'tpl/helper/product', 'stars' ) ?>

			<div class="b-card-row__block">
				<a href="#reviews" class="b-card-row__reviews">Отзывов: <?= $comments_count ?></a>
				<?php if ( $units_sold = $product -> get_total_sales() ) { ?>
					<span>купили <?= $units_sold ?> раз</span>
				<?php } ?>
			</div>
		</div>


		<?php /* button class="b-card-fav"><span>В избранное</span></button */ ?>
		<button class="btn_add_to_wishlist"><span><?= do_shortcode( '[yith_wcwl_add_to_wishlist]' ) ?></span></button>

		<?= $stock_statuses[$stock_status] ?>
	</div>

	<div class="b-card-row">
		<div class="b-best-price">
			<div class="b-best-price__ico icon-kubok"></div>
			<div class="b-best-price__text">
				<span>Лучшая цена по рынку</span>
				<p>Цена на товар уже включает 
					персональную скидку вашего 
					личного кабинета</p>
			</div>
		</div>
		<?= get_template_part( 'tpl/helper/share' ) ?>
	</div>
	<div class="b-card-price">
		<div class="b-card-price__item">
			<p>Цена товара</p>
			<span class="b-card-price__item__new wppw_price"><?= $price['current'] ?> ₽</span>
			<span class="b-card-price__item__old"><?= $price['regular'] ?> ₽</span>
		</div>

		<?php if ( $install['current'] or $install['regular'] ) { ?>
			<div class="b-card-price__item">
				<p>Цена установки</p>
				<?php if ( $install['current'] ) { ?><span class="b-card-price__item__new">От <?= $install['current'] ?> ₽</span><?php } ?>
				<?php if ( $install['regular'] ) { ?><span class="b-card-price__item__old"><?= $install['regular'] ?> ₽</span><?php } ?>
			</div>
		<?php } ?>
	</div>

	<div class="b-card-btns">


		<?php
		// Если продукт в корзине
		if ( $in_cart ) {
			?><button class="red-btn b-card-btn" data-is_change_text="1" onclick="window.location.href = '<?= esc_url( wc_get_cart_url() ) ?>'">Оформить заказ</button>
			<?php
		}
		else {
			?>
			<button class="red-btn b-card-btn wppw_add_to_cart" data-is_change_text="1" data-nonce="<?= wp_create_nonce( WPPW_NONCE ) ?>" data-command="add" data-id="<?= esc_attr( $post -> ID ) ?>">Добавить в корзину</button>
		<?php } ?>

		<?php
		// Если это не страница продукта
		if ( !is_singular( [ 'product' ] ) ) {
			?><a href="<?= get_permalink() ?>" style="text-decoration: none" class="btn-card-info">Подробнее</a>
		<?php }
		?>

	</div>

	<div class="b-card-items">
		<?php /* div class="b-card-city">Ваш город: Симферополь</div */ ?>
		<div class="b-card-items__container">
			<div class="b-card-item">
				<div class="b-card-item__ico icon-bus"></div>
				<div class="b-card-item__text">
					<b>Доставка</b>
					<span><?= $delivery_day ?>, <?= $delivery_date ?></span>
					<i>1000 Руб.</i>
					<a href="#" data-toggle="modal" data-target="#modal-callback">Подробнее о доставке</a>
				</div>
			</div>
			<?php if ( $manufacturer = get_field( 'specifications' )[1]['manufacturer'] -> name ) { ?>
				<div class="b-card-item">
					<div class="b-card-item__ico icon-marker"></div>
					<div class="b-card-item__text">
						<b>Производитель</b>
						<span><?= $manufacturer ?></span>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
