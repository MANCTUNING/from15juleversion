<?php
/**
 * Template name: Archive Services
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Тег заголовка страницы
$title_tag = get_field( 'title' )['tag'];
$title_text = get_field( 'title' )['text'] ?: get_the_title();
$title = sprintf( '<%s>%s</%s>', $title_tag, $title_text, $title_tag );

?>
<?= get_header() ?>

<div class="b-content">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-breadcreambs">
					<?= breadcrumbs() ?>
				</div>
				<?= $title ?>
			</div>
		</div>

		<?php
		if ( $p = get_posts( [
			'post_type'		 => 'wppw_service',
			'posts_per_page' => -1,
			] ) ) {
			?>
			<div class="b-services-page">
				<div class="row">

					<?= get_template_part( 'tpl/level/service', 'preview' ) ?>

				</div>  				
			</div>	
		<?php } ?>
	</div>
</div>

<?= get_footer() ?>