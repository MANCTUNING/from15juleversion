<?php
/**
 * Products
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $title, $products;
?>
<section class="section wppw_block__products_cards">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<?= sprintf( '<%1$s>%2$s</%1$s>', $title['tag'], $title['text'] ) ?>
			</div>
		</div>

		<?php if ( $products ) { ?>
			<div class="b-items-row row js-slider-transformToSlider wppw_block__products_cards__row">


				<?php
				// Число продуктов
				$products_count = count( $products );

				foreach ( $products as $i => $post ) {

					setup_postdata( $post );
					?>

					<?= get_template_part( 'tpl/ajax/product', 'card' ) ?>

					<?php
					// Через каждые 4 элемента дробим строку
					$i++;
					if ( $products_count > $i and 0 == $i % 4 ) {
						echo '</div><div class="b-items-row row js-slider-transformToSlider wppw_block__products_cards__row" style="display:none">';
					}
					?>

				<?php } wp_reset_postdata(); ?>
			</div>


			<?php
			// Если позиций больше 4, показываем пагинацию
			if ( $products_count > 4 ) {

				// Максимальное число страниц в пагинации
				$pagination_max = ceil( $products_count / 4 );
				?>
				<div class="row wppw_block__products_cards__pagination">
					<div class="col-xl">
						<div class="b-catalog-bottom">
							<button class="b-catalog-show wppw_block__products_cards__row__show_more" data-index="2">Показать еще</button>
							<div class="b-pagination">
								<a href="#" class="b-pagination-arrow b-pagination-prev wppw_block__products_cards__row__show_prev" data-index="1"></a>
								<ul>
									<?php
									for ( $i = 1; $i <= $pagination_max; $i++ ) {
										$active = '';
										if ( 1 == $i )
											$active = 'active';
										?>
										<li class="<?= $active ?>"><a href="#" data-index="<?= $i ?>"><?= $i ?></a></li>
									<?php } ?>
								</ul>
								<a href="#" class="b-pagination-arrow b-pagination-next wppw_block__products_cards__row__show_next" data-index="2"></a>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

			<?= get_template_part( 'tpl/modal/goto_woo_cart' ) ?>

		<?php } ?>


	</div>
</section>