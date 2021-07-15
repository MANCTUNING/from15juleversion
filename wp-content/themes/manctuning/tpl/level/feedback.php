<?php
/**
 * Форма обратной связи MANC
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
?>
<div class="b-block b-block-2 manc_cf7_feedback">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-block-content align-items-start">
					<div class="b-block-text">Manc</div>
					<div class="b-block-img">
						<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/b-block-img.png" alt="">
					</div>
					<div class="b-block-right">
						<div class="b-block-right__form">
							<p>С вами свяжется менеджер, подтвердит визит
								и подготовит все необходимое для тюнинга или 
								дополнительных услуг</p>
							<?= do_shortcode( '[contact-form-7 id="41" title="Приехать на тюнинг просто как 1,2,3"]' ) ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>