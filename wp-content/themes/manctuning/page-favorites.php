<?php
/**
 * Template name: Favorites
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Тег заголовка страницы
$title_tag = get_field( 'title' )['tag'] ?: 'h3';
$title_text = get_field( 'title' )['text'] ?: get_the_title();
$title = sprintf( '<%s>%s</%s>', $title_tag, $title_text, $title_tag );

#exit( print_r( get_fields() ) );
// Собираем данные по Wishlist
$wishlists = YITH_WCWL() -> get_wishlists();

if ( !empty( $wishlists ) && isset( $wishlists[0] ) ) {
	$wishlist_id = $wishlists[0]['wishlist_token'];
}
else {
	$wishlist_id = false;
}

$wishlist_items = [];
if ( $wishlist = YITH_WCWL() -> get_wishlist_detail_by_token( $wishlist_id ) )
	$wishlist_items = $wishlist -> get_items();
?>
<?= get_header() ?>

<div class="b-content">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-breadcreambs">
					<?= breadcrumbs() ?>
				</div>
				<?= $title ?>
			</div>
		</div>

		<?#= do_shortcode( '[yith_wcwl_wishlist]' ) ?>

		<?php // Список Избранное    ?>
		<?php
		if ( $wishlist_items and $p = get_posts( [
			'post_type'		 => 'product',
			'post__in'		 => array_keys( $wishlist_items ),
			'posts_per_page' => -1,
				] ) ) {
			?>

			<div class="b-favorites-page">
				<div class="b-items-row row">

					<?php require( TEMPLATEPATH . '/tpl/ajax/catalog.php' ) ?>

				</div>			  				
			</div>	

		<?php } ?>		

	</div>
</div>

<?= get_template_part( 'tpl/modal/card' ) ?>
<?= get_template_part( 'tpl/modal/goto_woo_cart' ) ?>
<?= get_footer() ?>