<?php
/**
 * Template name: Frontpage
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
#exit( print_r( get_fields() ) );
?>
<?= get_header() ?>

<?php
if ( have_rows( 'flex' ) ) {
	while ( have_rows( 'flex' ) ) {
		the_row();
		?>

		<?php
		if ( 'l1' == get_row_layout() ) {
			$r = get_sub_field( 'l1' );
			?>

			<section class="b-main">
				<div class="container">
					<div class="row">
						<div class="col-xl">
							<div class="b-main-content">
								<div class="b-main-content__name">G63</div>
								<div class="b-main-text">
									<span><?= $r['prefix'] ?></span>
									<?= $r['title'] ?>
									<button class="b-main-btn" data-toggle="modal" data-target="#modal-callback"><?= $r['call_to_me_text'] ?></button>
								</div>
							</div>
							<div class="b-main-img">
								<img class="lazy" data-original="<?= $r['img'] ?>" alt="">
							</div>					
						</div>
					</div>
				</div>
			</section>

			<?php
		}
		elseif ( 'l2' == get_row_layout() ) {
			$r = get_sub_field( 'l2' );
			?>
			<section class="b-shop section">
				<div class="container">
					<div class="row">
						<div class="col-xl">
							<div class="b-shop-top">
								<?= $r['title'] ?>
								<?= $r['desc'] ?>
							</div>

							<div class="b-shop-content">


								<?php // Список марок ?>
								<?php
								if ( $brands = $r['brands'] ) {
									?>
									<div class="b-shop-items">

										<?php
										// Чистим пустые значения
										$brands = array_filter( $brands, function ( $e ) {
											if (empty($e['brand']))
												return false;
											return true;
										} );
										foreach ( $brands as $e ) {

											$brand = $e['brand'];
											$title = $brand -> name;
											$ico = wp_get_attachment_url( get_term_meta( $brand -> term_id, 'thumbnail_id', true ) );

											$models = $e['models'];
											?>
											<div class="b-shop-col">
												<div class="b-shop-item">
													<a href="<?= get_term_link( $brand ) ?>" class="b-shop-item__img">
														<img class="lazy" data-original="<?= $ico ?>" alt="">
													</a>
													<a href="<?= get_term_link( $brand ) ?>" class="b-shop-item__name"><?= $title ?></a>
													<?php
													if ( $models ) {

														// Если число моделей больше 4, показываем ссылку на все
														$see_more_models = '';
														if ( count( $models ) > 4 ) {
															$see_more_models = sprintf( '<a href="%s" class="b-shop-item__link">Показать все</a>', get_term_link( $brand ) );
															$models = array_splice( $models, 0, 3 );
														}
														?>
														<ul>
															<?php
															foreach ( $models as $model ) {
																?>
																<li><a href="<?= get_term_link( $model ) ?>"><?= $model -> name ?></a></li>
															<?php } ?>
														</ul>
														<?= $see_more_models ?>
													<?php } ?>
												</div>
											</div>
										<?php } unset( $e ) ?>

									</div>
								<?php } ?>

								<a class="b-shop-content__more pokazat_vse_mob" href="<?= get_permalink( 143 ) ?>">Показать все</a>

								<div class="b-shop-right">
									<div class="b-shop-right__top">
										<b><?= $r['projects']['title'] ?></b>
										<span><?= $r['projects']['desc'] ?></span>
									</div>

									<?php
									if ( $p = get_posts( [
										'post_type'		 => 'wppw_project',
										'posts_per_page' => 100,
										] ) ) {
										?>
										<div class="b-shop-slider">

											<?php
											foreach ( $p as $post ) {
												setup_postdata( $post );
												?>

												<div class="b-shop-slider__item">
													<a href="<?= get_the_permalink() ?>" class="b-shop-slider__item__link"></a>
													<div class="b-shop-slider__item__img">
														<img data-lazy="<?= get_the_post_thumbnail_url() ?>" alt="">
													</div>
													<div class="b-shop-slider__item__text">
														<b><?= get_the_title() ?></b>
													</div>
												</div>

											<?php } wp_reset_postdata(); ?>

										</div>
										<div class="b-shop-slider__nav"></div>

									<?php } ?>

								</div>
							</div>
							<a class="b-shop-content__more pokazat_vse_desk" href="<?= get_permalink( 143 ) ?>">Полный каталог</a>
						</div>
					</div>
				</div>
			</section>
			<?php
		}
		elseif ( 'l3' == get_row_layout() ) {

			$header = get_sub_field( 'header' );
			$r = get_sub_field( 'l3' );
			?>
			<section class="b-offers section">
				<div class="container">
					<div class="row">
						<div class="col-xl">
							<div class="b-offers-container">
								<div class="b-offers-text"><?= $header['prefix'] ?></div>
								<div class="b-offers-top">
									<?= $header['title'] ?>
								</div>

								<?php if ( $r ) { ?>
									<div class="swiper-container b-offers-slider">
										<button class="b-arrow b-arrow__prev">prev</button>
										<button class="b-arrow b-arrow__next">next</button>
										<div class="swiper-wrapper">

											<?php
											foreach ( $r as $e ) {
												?>

												<div class="b-offers-col swiper-slide">
													<div class="b-offers-item">
														<div class="b-offers-item__left">
															<div class="b-offers-item__left__top">
																<b><?= $e['title'] ?></b>
																<div class="content_block">
																	<?= $e['desc'] ?>
																</div>
																<span class="content_toggle" href="#">Читать далее</span>															</div>
															<div class="b-offers-item__bottom">
																<div class="b-offers-item__price">
																	<div class="b-offers-item__price__old"><b><?= $e['price']['old'] ?></b> руб.</div>
																	<div class="b-offers-item__price__new"><b><?= $e['price']['new'] ?></b> руб.</div>
																</div>
																<a href="<?= $e['href'] ?>" class="b-offers-item__btn red-btn">Подробнее</a>
															</div>
														</div>
														<div class="b-offers-item__img">
															<a class="b-offers-video icon-youtube fancybox-media" href="https://www.youtube.com/embed/<?= $e['yt'] ?>?rel=0&showinfo=0&autoplay=1"></a>
															<img class="swiper-lazy" data-src="<?= $e['img'] ?>" alt="">
														</div>
													</div>						
												</div>

											<?php } ?>

										</div>
									</div>
									<div class="b-offers-nav"></div>

								<?php } ?>

							</div>
						</div>
					</div>
				</div>
			</section>

			<?php
		}
		elseif ( 'l4' == get_row_layout() ) { // Приехать на тюнинг просто как 1,2,3
			$r = get_sub_field( 'l4' );
			?>
			<div class="b-block">
				<div class="container">
					<div class="row">
						<div class="col-xl">
							<div class="b-block-content">
								<div class="b-block-text"><?= $r['backround']['text'] ?></div>
								<div class="b-block-img">
									<img class="lazy" data-original="<?= $r['backround']['img'] ?>" alt="">
								</div>
								<div class="b-block-right">
									<div class="b-block-right__top">
										<?= $r['title'] ?>
										<span><?= $r['desc'] ?></span>
									</div>
									<div class="b-block-right__form manc_cf7_feedback">
										<?= $r['wysiwyg'] ?>
										<?= do_shortcode( $r['shortcode'] ) ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		elseif ( 'l5' == get_row_layout() ) { // Дополнительные услуги
			$r = get_sub_field( 'l5' );
			?>
			<section class="b-main-services section">
				<div class="container">
					<div class="row">
						<div class="col-xl">
							<div class="b-main-services__top">
								<?= $r['title'] ?>
							</div>
						</div>
					</div>

					<?php
					if ( $p = get_posts( [
						'post_type'		 => 'wppw_service',
						'posts_per_page' => 10,
						] ) ) {
						?>
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
													<img data-lazy="<?= kama_thumb_src( [ 'w' => 330, 'h' => 202 ], get_the_post_thumbnail_url() ) ?>" alt="">
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

							</div>
						</div>
					<?php } ?>

					<div class="row">
						<div class="col-xl"></div>
					</div>
				</div>
			</section>
			<?php
		}
		elseif ( 'l6' == get_row_layout() ) {

			$r = get_sub_field( 'l6' );
			?>
			<section class="section">
				<div class="container">
					<div class="row">
						<div class="col-xl">
							<?= $r['title'] ?>
							<?= $r['desc'] ?>
						</div>
					</div>
				</div>
			</section>
			<?php
		}
	}
}
?>

<?= get_footer() ?>
