<?php
/**
 * Template name: Contacts
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Тег заголовка страницы
$title_tag = get_field( 'title' )['tag'];
$title_text = get_field( 'title' )['text'] ?: get_the_title();
$title = sprintf( '<%s>%s</%s>', $title_tag, $title_text, $title_tag );

$address = get_field( 'address' );
#exit( print_r( get_fields() ) );
?>
<?= get_header() ?>

<div class="b-wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-breadcreambs">
					<?= breadcrumbs() ?>
				</div>
				<div class="b-wrapper-content">
					<div class="b-contacts">
						<?= $title ?>

						<?php
						if ( have_rows( 'flex' ) ) {
							while ( have_rows( 'flex' ) ) {
								the_row();
								?>

								<?php
								if ( 'address' == get_row_layout() ) {
									$r = get_sub_field( 'data' );

									$tel = $r['tel']['num'] ?: get_field( 'tel', 'options' )['num'];
									?>
									<div class="b-contacts-top">
										<div class="b-contacts-title"><?= get_sub_field( 'title' ) ?></div>
										<div class="b-contacts-top__content">

											<div class="b-contacts-top__item">
												<b><?= $r['address']['title'] ?></b>
												<span><?= $r['address']['city'] . '. ' . $r['address']['street'] ?></span>
												<?php if ( $r['address']['href'] ) { ?><a href="<?= $r['address']['href'] ?>" class="b-route">Проложить маршрут</a><?php } ?>
											</div>
											<div class="b-contacts-top__item">
												<b><?= $r['tel']['title'] ?></b>
												<span><a href="tel:<?= $tel ?>"><?= $tel ?></a></span>
											</div>
											<div class="b-contacts-top__item">
												<b><?= $r['schedule']['title'] ?></b>
												<span><?= $r['schedule']['desc'] ?></span>
											</div>
										</div>
									</div>

									<script type="application/ld+json">
										{
										"@context": "https://schema.org",
										"@type": "LocalBusiness",
										"name": "manctuning.ru",
										"image": "<?= get_stylesheet_directory_uri() ?>/images/logo.png",
										"@id": "manctuning.ru",
										"url": "https://manctuning.ru",
										"telephone": "<?= get_field( 'tel', 'options' )['num'] ?>",
										"address": {
										"@type": "PostalAddress",
										"streetAddress": "<?= $r['address']['street'] ?>",
										"addressLocality": "<?= $r['address']['city'] ?>",
										"postalCode": "",
										"addressCountry": "RU"
										},
										"geo": {
										"@type": "GeoCoordinates",
										"latitude": "<?= $r['address']['latitude'] ?>",
										"longitude": "<?= $r['address']['longitude'] ?>"
										},
										"openingHoursSpecification": {
										"@type": "OpeningHoursSpecification",
										"dayOfWeek": [<?php
										foreach ( $r['schedule']['days'] as $i => $day ) {
											if ( ++$i < count( $r['schedule']['days'] ) )
												echo "\"$day\",";
											else
												echo "\"$day\"";
										}
										?>],
										"opens": "<?= $r['schedule']['clock']['opening'] ?>",
										"closes": "<?= $r['schedule']['clock']['closest'] ?>"
										}
										}
									</script>

									<?php
								}
								elseif ( 'about' == get_row_layout() ) {
									$e['title'] = get_sub_field( 'title' );
									$e['desc'] = get_sub_field( 'desc' );
									?>
									<div class="b-about-block">
										<div class="b-contacts-title"><?= $e['title'] ?></div>
										<?= $e['desc'] ?>
									</div>
									<?php
								}

								// Реквизиты компании
								elseif ( 'details' == get_row_layout() ) {
									$title = get_sub_field( 'title' );
									$r = get_sub_field( 'desc' );
									$is_sber = get_sub_field( 'is_sber' );
									?>

									<div class="b-contacts-container">
										<div class="b-contacts-container__top">
											<div class="b-contacts-title"><?= $title ?></div>
											<?php if ( $is_sber ) { ?><img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/sber-image.png" alt=""><?php } ?>
										</div>
										<div class="b-contacts-content">

											<?php foreach ( $r as $e ) {
												?>
												<div class="b-contacts-row">
													<div class="b-contacts-row__left">
														<p><?= $e['left'] ?></p>
													</div>
													<div class="b-contacts-row__right">
														<p><?= $e['right'] ?></p>
													</div>
												</div>
											<?php } unset( $v ) ?>

										</div>
									</div>
									<?php
								}
							}
						}
						?>

						<a href='<?= get_field( 'pdf' ) ?>' class="red-btn btn-download">Скачать файлом</a>

					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<?= get_footer() ?>