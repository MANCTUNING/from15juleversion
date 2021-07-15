<?php
/**
 * Footer
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
?>
<footer class="b-footer">
	<div class="b-footer-top">
		<div class="container">
			<div class="row">
				<div class="col-xl">
					<div class="b-footer-top__title">КАТАЛОГ</div>
				</div>
			</div>
			<div class="row">
				<div class="b-footer-col">
					<?=
					wp_nav_menu( [
						'theme_location' => 'footer_menu_1',
						'container'		 => false,
					] )
					?>
				</div>
				<div class="b-footer-col">
					<?=
					wp_nav_menu( [
						'theme_location' => 'footer_menu_2',
						'container'		 => false,
					] )
					?>
				</div>
				<div class="b-footer-col">
					<?=
					wp_nav_menu( [
						'theme_location' => 'footer_menu_3',
						'container'		 => false,
					] )
					?>
				</div>
				<div class="b-footer-col">
					<?=
					wp_nav_menu( [
						'theme_location' => 'footer_menu_4',
						'container'		 => false,
					] )
					?>
				</div>
				<div class="b-footer-col">
					<?=
					wp_nav_menu( [
						'theme_location' => 'footer_menu_5',
						'container'		 => false,
					] )
					?>
				</div>
				<div class="b-footer-col">
					<?=
					wp_nav_menu( [
						'theme_location' => 'footer_menu_6',
						'container'		 => false,
					] )
					?>
				</div>															
			</div>
		</div>
	</div>
	<div class="b-footer-content">
		<div class="container">
			<div class="row">
				<div class="col-xl">
					<div class="b-footer-left">
						<div class="b-footer-logo"></div>
						<span>manctuning.ru © 2015-<?= date( 'Y' ) ?></span>
					</div>
					<div class="b-footer-nav">
						<?=
						wp_nav_menu( [
							'theme_location' => 'bottom_menu',
							'container'		 => false,
						] )
						?>
					</div>
					<div class="b-footer-nav">
						<?=
						wp_nav_menu( [
							'theme_location' => 'bottom_menu_2',
							'container'		 => false,
						] )
						?>
					</div>
					<div class="b-footer-right">
						<div class="b-social">
							<ul>
								<li><a class="icon-vk" href="<?= get_field( 'soc', 'options' )['vk'] ?>"></a></li>
								<li><a class="icon-yt" href="<?= get_field( 'soc', 'options' )['yt'] ?>"></a></li>
								<li><a class="icon-fc" href="<?= get_field( 'soc', 'options' )['fb'] ?>"></a></li>
								<li><a class="icon-soc-inst" href="<?= get_field( 'soc', 'options' )['ig'] ?>"></a></li>
							</ul>
						</div>
						<a href="tel:<?= get_field( 'tel', 'options' )['num'] ?>" class="b-footer-phone"><?= get_field( 'tel', 'options' )['html'] ?></b></a>
						<span><?= get_field( 'schedule', 'options' ) ?></span>
					</div>
					<div>
						&nbsp;
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>

</div>

<?php // Данные из транзитного кеша для Woo Моделей и Поколений ?>
<script>var wppw_wc_tax_models_breeds = '<?= get_transient( 'wppw_wc_tax_models_breeds' ) ?>';</script>

<?= get_template_part( 'tpl/modal/booking' ) ?>
<?= get_template_part( 'tpl/modal/login' ) ?>
<?= get_template_part( 'tpl/modal/callback' ) ?>

<?= wp_footer(); ?>
</body>
</html>
