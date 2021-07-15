<?php
/**
 * Services
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $p;
?>
<section class="b-main-services section">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-main-services__top">
					<?= sprintf( '<%1$s>%2$s</%1$s>', $r['title']['tag'], $r['title']['text'] ) ?>
				</div>
			</div>
		</div>

		<?php if ( $p ) { ?>
			<div class="b-services-slider">
				<div class="row">

					<?php
					foreach ( $p as $post ) {
						setup_postdata( $post );
						?>
						<div class="b-services-col">
							<div class="b-services-item">
								<div class="b-services-item__top">
									<div class="b-services-item__img">
										<img data-lazy="<?= kama_thumb_src( [ 'w' => 330, 'h' => 200 ], get_the_post_thumbnail_url() ) ?>" alt="">
									</div>
									<div class="b-services-item__text">
										<?php
										$title = get_field( 'announce' );
										$desc = $title['desc'];
										$title = $title['title'];
										?>
										<b><?= $title ?></b>
										<?= $desc ?>

									</div>
								</div>
								<a href="<?= get_permalink() ?>" class="b-services-item__link">подробнее</a>
							</div>
						</div>
					<?php } wp_reset_postdata() ?>

				</div>
			</div>
		<?php } ?>
	</div>
</section>
