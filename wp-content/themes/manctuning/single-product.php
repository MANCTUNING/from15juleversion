<?php
/**
 * Single Product
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Тег заголовка страницы
$title_tag = get_field( 'title' )['tag'];
$title_text = get_field( 'title' )['text'] ?: get_the_title();
$title = sprintf( '<%s>%s</%s>', $title_tag, $title_text, $title_tag );

// WC
$product = wc_get_product( get_queried_object_id() );

// Product ID
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
$price['regular'] = number_format( $product -> get_regular_price(), 0, ',', ' ' ); // Перечёркнутая
$price['current'] = number_format( $product -> get_price(), 0, ',', ' ' ); // Текущая
// Установка
$install = get_field( 'install' );
$install['regular'] = number_format( $install['regular'], 0, ',', ' ' ); // Перечёркнутая
$install['current'] = number_format( $install['current'], 0, ',', ' ' ); // Перечёркнутая
#exit( print_r( get_fields() ) );
#
// Ищем, нет ли продукта в корзине
// https://www.businessbloomer.com/woocommerce-easily-check-product-id-cart/
#$cart_id = array_keys( WC() -> cart -> get_cart() )[0] ;
$cart_id = WC() -> cart -> generate_cart_id( $product_id ); // ID корзины
$in_cart = WC() -> cart -> find_product_in_cart( $cart_id ); // Должен содержать ID корзины или false
?>
<?= get_header() ?>

<div class="b-wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-breadcreambs">
					<?= breadcrumbs() ?>
				</div>

				<div class="b-wrapper-content b-card-content">

					<?= get_template_part( 'tpl/ajax/product', 'single' ) ?>

				</div>

				<?php
				if ( have_rows( 'specifications' ) ) {
					?>
					<div class="b-card-info">

						<?php
						while ( have_rows( 'specifications' ) ) {
							the_row();

							if ( 'wysiwyg' == get_row_layout() ) {
								$title = get_sub_field( 'title' );
								$title = sprintf( '<%s>%s</%s>', $title['tag'], $title['text'], $title['tag'] );

								$wysiwyg = get_sub_field( 'wysiwyg' );
								?>
								<?= $title ?>
								<?= $wysiwyg ?>
								<?php
							}
							elseif ( 'main' == get_row_layout() ) {

								$title = get_sub_field( 'title' );
								$title = sprintf( '<%s>%s</%s>', $title['tag'], $title['text'], $title['tag'] );
								?>
								<?= $title ?>
								<?php if ( $r = get_sub_field( 'r' ) ) { ?>
									<ul>
										<?php foreach ( $r as $e ) {
											?>
											<li><?= $e['left'] ?><div class="b-dot"></div><span><?= $e['right'] ?></span></li>
										<?php } unset( $e ) ?>
									</ul>
									<?php
								}
							}
						}
						?>
					</div>
					<?php
				}
				?>

				<?php // Отзывы ?>
				<?= get_template_part( 'tpl/reviews/product' ) ?>
			</div>
		</div>
	</div>
</div>

<section class="ya-catalog">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/ya-img.jpg" alt="">
			</div>
		</div>
	</div>
</section>


<?php // Рекомендуемое  ?>
<?php if ( $p = get_field( 'recommended' ) ) { ?>
	<section class="section">
		<div class="container">
			<div class="row">
				<div class="col-xl"><h4>С этим товаром смотрят</h4></div>
			</div>
			<div class="b-items-row row js-slider-transformToSlider">

				<?php
				foreach ( $p as $post ) {
					setup_postdata( $post );
					?>

					<?= get_template_part( 'tpl/ajax/product', 'card' ) ?>

				<?php } wp_reset_postdata(); ?>

			</div>
	</section>

<?php } ?>


<?= get_footer() ?>
