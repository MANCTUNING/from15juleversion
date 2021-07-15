<?php
/**
 * Header
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
?>
<!DOCTYPE html>
<html lang="ru">
	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<meta name="format-detection" content="telephone=no">

		<link href=favicon.png rel=”shortcut icon” type=”image/x-icon”>
		<link rel="shortcut icon" href="<?= get_stylesheet_directory_uri() ?>/images/logo.png" type="image/png" />	

		<?= wp_head(); ?>

		<link href="<?= get_stylesheet_directory_uri() ?>/css/jquery.fancybox.min.css" rel="stylesheet">
		<link href="<?= get_stylesheet_directory_uri() ?>/css/slick.css" rel="stylesheet">
		<link href="<?= get_stylesheet_directory_uri() ?>/css/select2.min.css" rel="stylesheet">
		<link href="<?= get_stylesheet_directory_uri() ?>/css/swiper.min.css" rel="stylesheet">
		<link href="<?= get_stylesheet_directory_uri() ?>/css/swiper-bundle.min.css" rel="stylesheet">
		<link href="<?= get_stylesheet_directory_uri() ?>/css/rating.css" rel="stylesheet">
		<link href="<?= get_stylesheet_directory_uri() ?>/icomoon/style.css" rel="stylesheet">
		<link href="<?= get_stylesheet_directory_uri() ?>/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="<?= get_stylesheet_directory_uri() ?>/css/style.css" rel="stylesheet">
		<link href="<?= get_stylesheet_directory_uri() ?>/css/responsive.css" rel="stylesheet">

		<?php
		// Optabs only on tuning
		if ( is_page_template( 'tpl/1tuning.php' ) ) {
			?>
			<link href="<?= get_stylesheet_directory_uri() ?>/css/cd-image.css" rel="stylesheet">
			<link href="<?= get_stylesheet_directory_uri() ?>/css/op-tabs.css" rel="stylesheet">
		<?php } ?>

		<link href="<?= get_stylesheet_directory_uri() ?>/scss/css.css" rel="stylesheet">
		<link href="<?= get_stylesheet_uri() ?>" rel="stylesheet">

		<script>const ajaxurl = "<?= admin_url( 'admin-ajax.php' ) ?>", wppw_nonce = "<?= wp_create_nonce( WPPW_NONCE ) ?>", site_url = "<?= site_url() ?>", catalog_url = "<?= get_permalink( 143 ) ?>", type_url = "<?= site_url('/type') ?>";</script>

	</head>
	<body <?php body_class() ?>>

		<button class="btn-top"></button>

		<div class="b-container">

			<header class="b-header">	
				<div class="b-header-top">
					<div class="container">
						<div class="row">
							<div class="col-xl">
								<a href="<?= site_url() ?>" class="b-logo"></a>
								<div class="b-header-top__right">
									<div class="b-header-top__right__top">
										<div class="b-header-contacts">
											<a href="tel:<?= get_field( 'tel', 'options' )['num'] ?>"><?= get_field( 'tel', 'options' )['html'] ?></a>
											<span><?= get_field( 'schedule', 'options' ) ?></span>
										</div>
										<div class="b-header-top__right__wrapper">

											<span class="b-header-login">
												<button class="btn-login icon-user" data-toggle="modal" data-target="#modal-login"></button>
											</span>

											<?php // Избранное ?>
											<a href="<?= get_permalink( 435 ) ?>" class="b-header-fav">
												<!-- <span class="b-quantity">1</span> -->
												<span class="b-header-fav__ico icon-heart"></span>
											</a>

											<?php // Корзина ?>
											<?php $cart = WC() -> cart -> get_cart() ?>
											<a href="<?= wc_get_cart_url() ?>" class="b-header-cart">
												<span class="b-quantity"><?= count( $cart ) ?></span>
												<span class="b-header-cart__ico icon-cart"></span>
												<?php if ( $cart ) { ?>
													<p>Товаров: <?= count( $cart ) ?>, <b><?= number_format( WC() -> cart -> total, 0, ',', ' ' ) ?> Руб</b></p>
												<?php } ?>
											</a>

										</div>
									</div>
									<div class="b-header-top__right__bottom">
										<nav class="b-nav">
											<?=
											wp_nav_menu( [
												'theme_location' => 'header_menu',
												'container'		 => false,
											] )
											?>
										</nav>
										<div class="b-header-btns">
											<button class="b-header-booking red-btn" data-toggle="modal" data-target="#modal-booking">Запись онлайн</button>
											<?php
											if ( is_user_logged_in() ) {

												$user = wp_get_current_user() -> data -> display_name;
												?>
												<div class="wppw_user_logout">
													<a href="<?= site_url( '/lk' ) ?>" class="btn-login icon-user"><?= $user ?></a> 
													<a href="<?= wp_logout_url( site_url() ) ?>" class="wppw_logout_link">
														<span class="wppw_logout_rarrow"></span>
													</a>
													<?php
												}
												else {
													?>
													<button class="btn-login icon-user" data-toggle="modal" data-target="#modal-login">Войти</button>
													<a href="<?= get_field( 'soc', 'options' )['yt'] ?>" class="btn-subscribe icon-youtube">Подписаться</a>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>


					<?php // Search Bar ?>
					<?php
					$brands = get_terms( [
						'taxonomy'	 => 'product_cat',
						'parent'	 => 0,
						'hide_empty' => false,
						] );

					$types = get_terms( [
						'taxonomy'	 => 'product_tag',
						'hide_empty' => false,
						] );
					?>
					<div class="b-header-content">
						<div class="container">
							<div class="row">
								<div class="col-xl">
									<div class="b-select-button js-search-button visible">
										<span>Поиск</span>
										<span class="b-select-button__arrow"><b></b></span>
									</div>
									<form action="<?= site_url( '/catalog' ) ?>" class="b-header-content__form" id="wppw_top_search_bar" action="<?= get_permalink( 143 ) ?>">
										<div class="b-select-container">
											<?php if ( $brands ) { ?>
												<select name="brand" class="b-select wppw_brand">
													<option id="wppw_brand_default_selected" value="" selected>Марка</option>
													<?php
													foreach ( $brands as $brand ) {
														?>
														<option value="<?= $brand -> slug ?>"><?= $brand -> name ?></option>
													<?php } ?>
												</select>
											<?php } ?>
										</div>
										<div class="b-select-container">
											<select name="model" class="b-select wppw_model">
												<option value="" selected>Модель</option>
											</select>
										</div>
										<div class="b-select-container">
											<select name="breed" class="b-select wppw_breed">
												<option value=""  selected>Поколение</option>
											</select>
										</div>
										<div class="b-select-container">
											<?php if ( $brands ) { ?>
												<select name="type" class="b-select wppw_type">
													<option value=""  selected>Тип оборудования</option>
													<?php foreach ( $types as $type ) { ?>
														<option value="<?= $type -> slug ?>">
														<img src="https://picsum.photos/150/90" alt="<?= $type -> name ?>" />
														<?= $type -> name ?>
														</option>
													<?php } ?>
												</select>
											<?php } ?>
										</div>																					
										<div class="b-header-content__form__right">
											<input class="b-header-form__btn" type="submit" value="Поиск" />
											<?php /*input id="wppw_products_count" class="b-header-content__form__input" value="Выберите товары"*/ ?>
											<div id="wppw_products_count" class="b-header-content__form__input">Выберите товары</div>
										</div>
									</form>									
								</div>
							</div>
						</div>
					</div>
			</header>
