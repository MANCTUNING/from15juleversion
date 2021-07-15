<?php
/**
 * Template Name: 2Tuning
 * Template Post Type: wppw_tuning
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Тег заголовка страницы
$title_tag = get_field( 'title' )['tag'] ?: 'h1';
$title_text = get_field( 'title' )['text'] ?: get_the_title();
$title = sprintf( '<%s>%s</%s>', $title_tag, $title_text, $title_tag );

#exit( print_r( get_fields() ) );
?>
<?= get_header() ?>

<div class="container">
	<div class="row">
        <div class="col-xl">
			<div class="b-breadcreambs">
				<?= breadcrumbs() ?>
			</div>
        </div>
	</div>
</div>


<?php
if ( have_rows( 'flex' ) ) {
	while ( have_rows( 'flex' ) ) {
		the_row();
		?>

		<?php
		if ( 'top' == get_row_layout() ) {
			$r = get_sub_field( 'top' );
			?>
			<section class="section b-service-banner op-tabs-wrapper">
				<div class="b-service-banner__content op-content">
					<div class="b-service-banner__content-item op-content-item">
						<div class="b-service-banner__info">
							<div class="container">
								<h1 class="b-service-banner__title"><?= $r['title'] ?></h1>
								<div class="b-service-banner__subtitle"><?= $r['subtitle'] ?></div>
								<button class="b-item-btn red-btn" data-toggle="modal" data-target="#modal-callback">Заказать</button>
							</div>
						</div>
						<figure class="b-service-banner__banner">
							<img src="<?= $r['img'] ?>" alt="Modified Image">
						</figure>
					</div>
				</div>

				<?php // usluga-tuning2 ?>
				<?php if ( $r['children'] ) { ?>
					<?php require TEMPLATEPATH . '/tpl/tuning/top-children.php' ?>
				<?php } ?>

			</section>

			<?php
		}
		elseif ( 'desc' == get_row_layout() ) {
			$r = get_sub_field( 'desc' );
			?>

			<section class="section b-article-content">
				<div class="container">
					<div class="row">
						<div class="col-x1">
							<?= sprintf( '<%1$s>%2$s</%1$s>', $r['title']['tag'], $r['title']['text'] ) ?>
							<!--<h3>Внешний тюнинг Mercedes-Benz G-Class</h3>-->
							<div class="row">
								<div class="col-md-6">
									<div class="b-col_bd-top">
										<?= $r['left'] ?>
									</div>
								</div>
								<div class="col-md-6">
									<div class="b-col_bd-top">
										<?= $r['right'] ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<?php
		}
		elseif ( 'projects' == get_row_layout() ) {
			$r = get_sub_field( 'projects' );
			?>

			<section class="section section_dark">
				<div class="container">
					<div class="row">
						<div class="col-xl">
							<?= sprintf( '<%1$s>%2$s</%1$s>', $r['title']['tag'], $r['title']['text'] ) ?>
						</div>
					</div>

					<div class="row">
						<div class="col-xl">
							<div class="b-projects-section">
								<div class="row">

									<?php
									foreach ( $r['projects'] as $post ) {
										setup_postdata( $post );

										$preview = get_field( 'preview' );
										?>
										<div class="b-services-col col-xl-4 col-lg-4">
											<div class="b-services-item">
												<div class="b-services-item__top">
													<div class="b-services-item__img">
														<img class="lazy" data-original="<?= kama_thumb_src( [ 'w' => 332, 'h' => 204 ], get_the_post_thumbnail_url() ) ?>" alt="">
													</div>
													<div class="b-services-item__text">
														<b><?= $preview['title'] ?: get_the_title() ?></b>
														<?= $preview['desc'] ?>
													</div>
												</div>
												<a href="<?= get_permalink() ?>" class="b-services-item__link">подробнее</a>
											</div>
										</div>
									<?php } ?>

								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xl">
							<button class="b-catalog-show" onclick="window.location.href = '<?= site_url( '/project' ) ?>'">Показать еще</button>
						</div>
					</div>
				</div>
			</section>

			<?php
		}
		elseif ( 'photos_prices' == get_row_layout() ) {
			$r = get_sub_field( 'photos_prices' );
			?>

			<section class="section section-complexes">
				<div class="container">
					<div class="row">
						<div class="col-xl">

							<?php printf( '<%1$s>%2$s</%1$s>', $r['title']['tag'], $r['title']['text'] ) ?>
							<!--<h3>Комплекты аэродинамических обвесов Mercedes G-Class</h3>-->
						</div>
					</div>
					<div class="row">
						<div class="col-xl">
							<div class="b-complexes-row">

								<?php foreach ( $r['blocks'] as $block ) { ?>
									<div class="b-complex-item">
										<div class="b-complex-item__container">
											<div class="b-complex-item__info col-lg-4">
												<h5 class="b-complex-item__title"><?= $block['title'] ?></h5>
												<?php /* h5 class="b-complex-item__title">Комплект рестайлинга 63 AMG 2018 Mercedes G-Class W463</h5 */ ?>
												<div class="b-complex-item__description">
													<?= $block['desc'] ?>
													<!--<ul>
																										<li>- спойлер переднего бампера</li>
																										<li>- накладки на передний бампер</li>
																										<li>- расширители передних крыльев</li>
																										<li>- пороги</li>
																										<li>- накладки на двери</li>
																										<li>- расширители задних колесных арок</li>
																										<li>- задний бампер</li>
																										<li>- насадки на глушители</li>
																										<li>- логотипы</li>
																									</ul>-->
												</div>
												<div class="b-complex-item__price"><?= number_format( $block['price'], 0, ',', ' ' ) ?> руб.</div>
												<!--<div class="b-complex-item__price">120 000 руб.</div>-->
												<div class="b-complex-item__controls">
													<button class="b-main-btn" data-toggle="modal" data-target="#modal-callback">Подробнее</button>
													<button class="b-item-btn red-btn" data-toggle="modal" data-target="#modal-callback">Заказать</button>
												</div>
											</div>

											<?php if ( $block['gallery'] ) { ?>
												<div class="b-complex-item__photos col-lg-8">
													<div class="b-article-slider">
														<div class="b-article-slider__nav"></div>
														<div class="b-article-slider__big">
															<?php foreach ( $block['gallery'] as $img ) { ?>
																<a data-fancybox="images" class="b-article-slider__big__item" href="<?= $img ?>">
																	<img data-lazy="<?= $img ?>" alt="">
																</a>
															<?php } ?>

														</div>
														<div class="b-article-slider__preview">
															<?php foreach ( $block['gallery'] as $img ) { ?>
																<div class="b-article-slider__preview__item">
																	<img data-lazy="<?= kama_thumb_src( [ 'w' => 186, 'h' => 135 ], $img ) ?>" alt="">
																</div>
															<?php } ?>

														</div>
													</div>
												</div>
											<?php } ?>

										</div>
									</div>
								<?php } ?>

							</div>
						</div>
					</div>
				</div>
			</section>

			<?php
		}
		elseif ( 'products' == get_row_layout() ) {
			$title = get_sub_field( 'title' );
			$products = get_sub_field( 'products' );
			?>
			<?php require TEMPLATEPATH . '/tpl/tuning/products.php'; ?>
			<?php
		}
		elseif ( 'tuning' == get_row_layout() ) {
			$r = [];
			$r['title'] = get_sub_field( 'title' );
			$p = get_sub_field( 'tuning' );
			?>
			<?php require TEMPLATEPATH . '/tpl/tuning/services.php'; ?>
			<?php
		}
		elseif ( 'another' == get_row_layout() ) {
			$r = get_sub_field( 'another' );
			$p = $r['another'];
			?>
			<?php require TEMPLATEPATH . '/tpl/tuning/services.php'; ?>
			<?php
		}
	}
}
?>
<?= get_footer() ?>