<?php
/**
 * Page
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
	</div>
</div>

<section class="section">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<?= the_content() ?>
			</div>
		</div>
	</div>
</section>

<?= get_footer() ?>