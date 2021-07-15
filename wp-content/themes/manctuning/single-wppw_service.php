<?php
/**
 * Template name: Contacts
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Тег заголовка страницы
$title_tag = get_field( 'title_tag' );
$title = sprintf( '<%s>%s</%s>', $title_tag, get_the_title(), $title_tag );

$address = get_field( 'address' );
#exit( print_r( get_fields() ) );
?>
<?= get_header() ?>

<div class="b-service-page">
	<div class="b-wrapper">
		<div class="container">
			<div class="row">
				<div class="col-xl">
					<div class="b-breadcreambs">
						<?= breadcrumbs() ?>
					</div>
					<div class="b-wrapper-content">
						<div class="b-article">
							<div class="b-article-content">
								<?= $title ?>

								<?php
								if ( have_rows( 'flex' ) ) {
									while ( have_rows( 'flex' ) ) {
										the_row();
										?>

										<?php
										if ( 'wysiwyg' == get_row_layout() ) {
											the_sub_field( 'wysiwyg' );
										}

										// Слайдер
										elseif ( 'slider' == get_row_layout() ) {
											$r = get_sub_field( 'slider' );
											?> 

											<div class="b-article-slider">
												<div class="b-article-slider__nav"></div>
												<div class="b-article-slider__big">
													<?php
													foreach ( $r as $e ) {
														?>
														<a data-fancybox="images" class="b-article-slider__big__item" href="<?= $e['url'] ?>">
															<img data-lazy="<?= $e['url'] ?>" alt="">
														</a>
													<?php } unset( $e ) ?>

												</div>
												<div class="b-article-slider__preview">
													<?php
													foreach ( $r as $e ) {
														?>
														<div class="b-article-slider__preview__item">
															<img data-lazy="<?= kama_thumb_src( [ 'w' => 188, 'h' => 137 ], $e['url'] ) ?>" alt="">
														</div>
													<?php } unset( $e ) ?>

												</div>									
											</div>

											<?php
										}
									}
								}
								?>

							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>


<?= get_template_part( 'tpl/level/feedback' ) ?>

<?= get_footer() ?>