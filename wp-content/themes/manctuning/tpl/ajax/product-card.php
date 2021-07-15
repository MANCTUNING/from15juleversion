<?php
/**
 * Карточка товара
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $post;

// $post должен быть определён
$post or exit;

// Число комментариев к записи
$comments_count = wp_count_comments( $post -> ID ) -> approved;

// WC
$product = wc_get_product( $post );

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
?>
<div class="b-item-col">
	<div class="b-item__container">
		<div class="b-item">
			<?php if ( is_user_logged_in() ) { ?>
				<div class="b-item-sale">-5%</div>
			<?php } ?>
			<?php
			// Собираем данные по избранным продуктам
			$wishlists = YITH_WCWL_Wishlist_Factory::get_wishlists();
			foreach ( $wishlists as $wishlist ) {
				$items = $wishlist -> get_items();
				$fav_product_ids = array_keys( $items );
			}


			// Если товар уже в Избранном
			if ( is_page_template( 'page-favorites.php' ) or in_array( $post -> ID, $fav_product_ids ) ) {
				?>
				<div id="yith-wcwl-row-<?= $post -> ID ?>" data-row-id="<?= $post -> ID ?>">
					<div class="wishlist_table">
						<a href="<?= esc_url( add_query_arg( 'remove_from_wishlist', $post -> ID ) ) ?>" class="b-item-fav icon-heart active remove remove_from_wishlist" data-product-id="<?= $post -> ID ?>" data-original-product-id="<?= $post -> ID ?>" data-product-type="simple" title="Из избранного"  style="text-decoration: none"></a>
					</div>
				</div>
				<?php
			}
			else { // Добавить в избранное
				?>
				<button class="b-item-fav icon-heart add_to_wishlist single_add_to_wishlist" data-product-id="<?= $post -> ID ?>" data-original-product-id="<?= $post -> ID ?>" data-product-type="simple" title="В избранное"></button>
			<?php } ?>
			<div data-toggle="modal" data-target="#modal_product_<?= $post -> ID ?>" class="b-item-img">
				<?php /*img class="lazy" data-original="<?= get_the_post_thumbnail_url() ?>" alt=""*/ ?>
				<img src="<?= get_the_post_thumbnail_url() ?>" alt="">
			</div>
			<div class="b-item-content">
				<span class="b-item-number"><?= $sku ?></span>
				<a href="<?= get_permalink() ?>" class="b-item-name"><?= get_the_title() ?></a>
				<div class="b-item-price">
					<span class="b-item-price__new"><?= $price['current'] ?> руб.</span>
					<span class="b-item-price__old">- <?= $price['regular'] ?>  руб.</span>
				</div>
			</div>
			<div class="b-item-bottom">
				<div class="b-item-row">
					<?= get_template_part( 'tpl/helper/product', 'stars' ) ?>							
					<a href="<?= get_permalink() ?>" class="b-item-review__link">Отзывов: <?= $comments_count ?></a>	
				</div>
				<button data-product-id="<?= $post -> ID ?>" class="b-item-btn red-btn wppw_add_to_cart" data-command="add" data-is_change_text="1" data-id="<?= esc_attr( $post -> ID ) ?>" data-nonce="<?= wp_create_nonce( WPPW_NONCE ) ?>">В корзину</button>
				<?php /* a href="<?php echo $product -> add_to_cart_url() ?>" value="<?php echo esc_attr( $product -> get_id() ); ?>" class="ajax_add_to_cart add_to_cart_button" data-product_id="<?php echo get_the_ID(); ?>" data-product_sku="<?php echo esc_attr( $sku ) ?>">В корзину</a */ ?>
			</div>
		</div>
	</div>
</div>

<div class="modal b-modal-card fade" id="modal_product_<?= $post -> ID ?>" tabindex="-1" role="dialog" aria-labelledby="modal-card2" aria-hidden="true">
	<div class="modal-dialog">
		<button class="btn-close" data-dismiss="modal">+</button>
		<div class="b-card-content">

			<?= get_template_part( 'tpl/ajax/product', 'single' ) ?>

		</div>	
	</div>
</div>