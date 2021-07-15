<?php
/**
 * 1 отзыв
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
#exit( print_r( $review ) );
#$name = $review['meta']['name'];
$name = $comment -> comment_author;
if ( $review['meta']['is_anonymouse'] ) {
	$name = 'Анонимно';
}
$experience = [
	'меньше месяца', // Заглушка с нулевым индексом
	'меньше месяца',
	'несколько месяцев',
	'больше года',
];
$experience = $experience[$review['experience']];

$worth = $review['review']['text']['worth'];
$disatvantages = $review['review']['text']['disatvantages'];
$comment = $review['review']['text']['comment'];

// Дата публикации обзора
$date = wp_date( 'd F', strtotime( $review['meta']['date'] ) );
?>
<div class="b-card-reviews__item__top">
	<div class="b-card-reviews__item__img">
		<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/b-card-reviews__item__img.png" alt="">
	</div>
	<div class="b-card-reviews__item__text">
		<b><?= $name ?></b>
		<span>Опыт использования: <?= $experience ?></span>
	</div>
</div>
<div class="b-card-reviews__item__row">
	<?php require TEMPLATEPATH . '/tpl/helper/product-stars.php' ?>							
	<span>Отличный товар</span>
</div>
<div class="b-card-reviews__item__content">
	<b>Достоинства</b>
	<p><?= $worth ?></p>
	<b>Недостатки</b>
	<p><?= $disatvantages ?></p>
	<b>Комментарий</b>
	<p><?= $comment ?></p>																		
</div>
<div class="b-card-reviews__item__bottom">
	<span class="b-card-reviews__item__date"><?= $date ?></span>
</div>