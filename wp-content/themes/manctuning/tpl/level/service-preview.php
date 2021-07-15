<?php
/**
 * WPPW
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $p;

foreach ( $p as $post ) {
	setup_postdata( $post );
	?>
	<div class="b-services-col col-sm-6 col-lg-4">
		<div class="b-services-item">
			<div class="b-services-item__top">
				<div class="b-services-item__img">
					<img class="lazy" data-original="<?= kama_thumb_src( [ 'w' => 330, 'h' => 200 ], get_the_post_thumbnail_url() ) ?>" alt="">
				</div>
				<div class="b-services-item__text">
					<b><?= get_the_title() ?></b>
					<?= get_field( 'preview' )['desc'] ?>
				</div>
			</div>
			<a href="<?= get_the_permalink() ?>" class="b-services-item__link">подробнее</a>
		</div>
	</div>

<?php } wp_reset_postdata(); ?>
