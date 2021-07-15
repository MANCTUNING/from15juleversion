<?php
/**
 * Страница продукта
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $product;
?>
<?php
// Отзывы  
if ( $comments = get_comments( [
	'post_id'	 => $product -> get_id(),
	'number'	 => 200,
		] ) ) {
	$comments_count = count( $comments );
	?>
	<div class="b-card-reviews">
		<div class="b-card-reviews__top">
			<div class="b-card-reviews__left">
				<div class="b-card-reviews__left__top">
					<h4>Отзывы и оценки</h4>
					<span><?= $comments_count ?></span>
				</div>
				<div class="b-card-reviews__row">
					<?php require TEMPLATEPATH . '/tpl/helper/product-reviews.php' ?>
				</div>
			</div>
			<button class="b-card-reviews__btn" data-fancybox data-src="#modal-product_review">Оставить отзыв</button>
		</div>

		<div id="reviews" class="b-card-reviews__container">

			<div class="b-card-reviews__item">

				<?php
				foreach ( $comments as $comments_index => $comment ) {

					// Данные обзора
					$review = get_field( 'review', 'comment_' . $comment -> comment_ID );

					require TEMPLATEPATH . '/tpl/reviews/review.php';

					if ( ++$comments_index >= 1 and $comments_index != $comments_count )
						echo '<hr>';
				}
				?>	
				<?php //TODO: все отзывы ?>
				<div class="b-card-reviews__item__bottom">
					<a href="" class="b-card-reviews__item__all">Посмотреть все отзывы</a>
				</div>
			</div>

		</div>
	</div>
	<?php
}
// Если отзывов нет
else {
	?>
	<div class="b-card-reviews">
		<div class="b-card-reviews__top">
			<button class="b-card-reviews__btn" data-fancybox data-src="#modal-product_review">Оставить отзыв</button>
		</div>
	</div>
<?php }
?>



<?= get_template_part( 'tpl/modal/product-review' ) ?>