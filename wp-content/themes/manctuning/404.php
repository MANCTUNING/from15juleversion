<?php
/**
 * 404
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
?>
<?= get_header() ?>

<div class="b-wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-breadcreambs">
					<ul>
						<?= breadcrumbs() ?>
					</ul>
				</div>
				<div class="b-wrapper-content">
					<div class="b-content">
						<div class="error-page">
							<div class="error-page__inner">
								<div class="error-page__text">
									<h3 class="error-page__title">Страница не найдена!</h3>
									<p>Кажется, страница была удалена или перенесена</p>
									<a href="<?= site_url() ?>" class="b-main-btn b-main-btn_back">На главную</a>
								</div>
								<img src="<?= get_stylesheet_directory_uri() ?>/images/404-weel.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?= get_footer() ?>
