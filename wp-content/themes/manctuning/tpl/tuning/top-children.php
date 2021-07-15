<?php
/**
 * top-children
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $r;
?>
<div class="container">
	<div class="row">
		<div class="col-xl">
			<div class="b-service-banner__tabs b-service-banner__tabs_as-block op-tabs">

				<?php
				foreach ( $r['children'] as $e ) {

					if ( $e -> ID == get_queried_object_id() )
						$is_active = ' active';
					else
						$is_active = '';

					// TODO: внедрить в ACF
					$title = get_field( 'announce', $e );
					$desc = $title['desc'] ?: 'Обвесы, накладки,  пороги, фары,  зеркала и т.п.';
					$title = $title['title'] ?: get_the_title( $e );
					?>
					<div class="b-service-banner__tabs-item op-tabs-item<?= $is_active ?>" data-tabs-tab="1">
						<h5 class="b-service-banner__tabs-title"><?= $title ?></h5>
						<span class="b-service-banner__tabs-text"><?= $desc ?></span>
						<img class="b-service-banner__tabs-img" src="<?= kama_thumb_src( [ 'w' => 188, 'h' => 115 ], get_the_post_thumbnail_url( $e, 'full' ) ) ?>" alt="">
						<a href="<?= get_permalink( $e ) ?>" class="b-service-banner__link" tabindex="0">Подробнее</a>
					</div>
				<?php } unset( $e ); ?>

			</div>
		</div>
	</div>
</div>
