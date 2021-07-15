<?php
/**
 * Template Name: 3Tuning
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

<?php
$flex = get_field( 'flex' );
$rr = [];
foreach ( $flex as $e ) {
	$rr[$e['acf_fc_layout']] = $e[$e['acf_fc_layout']];
}
?>



<div class="b-wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-breadcreambs">
					<?= breadcrumbs() ?>
				</div>
				<div class="b-wrapper-content">
					<div class="b-content">

						<?php
						$r = $rr['top'];
						?>

						<?= sprintf( '<%1$s>%2$s</%1$s>', $r['title']['tag'], $r['title']['text'] ) ?>

						<div class="b-text-2-columns">
							<div class="b-text-column">
								<?= $r['wysiwyg']['left'] ?>
								<div class="b-price-block">
									<div class="b-price-block__column">
										<?= $r['price']['left'] ?>
									</div>
									<div class="b-price-block__column">
										<?= $r['price']['right'] ?>
									</div>
								</div>
							</div>
							<div class="b-text-column">
								<img src="<?= $r['wysiwyg']['right'] ?>" alt="">
							</div>
						</div>

						<?php
						$r = $rr['middle'];
						?>


						<div class="b-text-2-columns">
							<div class="b-text-column">
								<?= $r['left'] ?>
							</div>
							<div class="b-text-column">
								<?= $r['right'] ?>
							</div>
						</div>
						<?php
						?>
					</div>
				</div>

			</div>
		</div>

		<?php
		$r = $rr['related'];
		?>
		<div class="row">
			<div class="col-xl">
				<div class="b-service-banner__tabs b-service-banner__tabs_as-block">

					<?php
					foreach ( $r as $post ) {
						setup_postdata( $post );
						// TODO: внедрить в ACF
						$title = get_field( 'announce', $e );
						$desc = $title['desc'] ?: 'Обвесы, накладки,  пороги, фары,  зеркала и т.п.';
						$title = $title['title'] ?: get_the_title();
						?>
						<div class="b-service-banner__tabs-item op-tabs-item" data-tabs-tab="1">
							<h5 class="b-service-banner__tabs-title"><?= $title ?></h5>
							<span class="b-service-banner__tabs-text"><?= $desc ?></span>
							<img class="b-service-banner__tabs-img" src="<?= kama_thumb_src( [ 'w' => 188, 'h' => 115 ], get_the_post_thumbnail_url() ) ?>" alt="">
							<a href="<?= get_permalink() ?>" class="b-service-banner__link" tabindex="0">Подробнее</a>
						</div>
					<?php } ?>

				</div>
			</div>
		</div>
	</div>
</div>

<?php ?>

<?= get_footer() ?>