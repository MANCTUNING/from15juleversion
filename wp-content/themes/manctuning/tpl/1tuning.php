<?php
/**
 * Template Name: 1Tuning
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
								<div class="b-service-banner__description"><?= $r['price'] ?></div>
								<button class="b-item-btn red-btn" data-toggle="modal" data-target="#modal-callback">Заказать</button>
							</div>
						</div>
						<figure class="b-service-banner__banner cd-image-container is-visible">
							<img src="<?= $r['after']['img'] ?>" alt="Modified Image">
							<span class="cd-image-label cd-image-label_right" data-type="original"><?= $r['after']['title'] ?></span>
							<div class="cd-resize-img"> <!-- the resizable image on top -->
								<img src="<?= $r['before']['img'] ?>" alt="Original Image">
								<span class="cd-image-label cd-image-label_left" data-type="original"><?= $r['before']['title'] ?>
								</span>
							</div>
							<span class="cd-handle"></span> <!-- slider handle -->
						</figure> <!-- cd-image-container -->
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
		elseif ( 'gallery' == get_row_layout() ) {
			$gallery = get_sub_field( 'gallery' );
			?>

			<section class="section section_dark">
				<div class="container">
					<div class="row">
						<div class="col-xl">
							<div class="single-article-slider-wrapper">
								<div class="b-article-slider">
									<div class="b-article-slider__nav"></div>
									<div class="b-article-slider__big">
										<?php
										foreach ( $gallery as $img ) {
											?>
											<a data-fancybox="images" class="b-article-slider__big__item" href="<?= $img ?>">
												<img data-lazy="<?= $img ?>" alt="">
											</a>
										<?php } unset( $img ) ?>	

									</div>
									<div class="b-article-slider__preview">

										<?php
										foreach ( $gallery as $img ) {
											?>
											<div class="b-article-slider__preview__item">
												<img data-lazy="<?= kama_thumb_src( [ 'w' => 188, 'h' => 137 ], $img ) ?>" alt="">
											</div>

										<?php } unset( $img ) ?>		

									</div>
								</div>
							</div>
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

								<?php foreach ( $r['blocks'] as $block ) {
									?>
									<div class="b-complex-item">
										<div class="b-complex-item__container">
											<div class="b-complex-item__info col-lg-4">
												<h5 class="b-complex-item__title"><?= $block['title'] ?></h5>
												<?php /* h5 class="b-complex-item__title">Комплект рестайлинга 63 AMG 2018 Mercedes G-Class W463</h5 */ ?>
												<div class="b-complex-item__description">
													<?= $block['desc'] ?>
													<?php /* <ul>
													  <li>- спойлер переднего бампера</li>
													  <li>- накладки на передний бампер</li>
													  <li>- расширители передних крыльев</li>
													  <li>- пороги</li>
													  <li>- накладки на двери</li>
													  <li>- расширители задних колесных арок</li>
													  <li>- задний бампер</li>
													  <li>- насадки на глушители</li>
													  <li>- логотипы</li>
													  </ul> */ ?>
												</div>
												<div class="b-complex-item__price"><?= number_format( $block['price'], 0, ',', ' ' ) ?> руб.</div>
												<!--<div class="b-complex-item__price">120 000 руб.</div>-->
												<div class="b-complex-item__controls">
													<button class="b-main-btn" onclick="window.location.href='<?=$block['href']?>'">Подробнее</button>
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