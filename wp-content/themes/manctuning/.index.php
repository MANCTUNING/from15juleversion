<?php
/**
 * Index
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
?>
<?= get_header() ?>

<section class="b-main">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-main-content">
					<div class="b-main-content__name">G63 </div>
					<div class="b-main-text">
						<span>МАГАЗИН ТЮНИНГА НОМЕР ОДИН В МОСКВЕ</span>
						<h1>Ремонт, сервис 
							и тюнинг вашего 
							автомобиля</h1>
						<button class="b-main-btn" data-toggle="modal" data-target="#modal-callback">перезвоните мне</button>
					</div>
				</div>
				<div class="b-main-img">
					<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/main-img.png" alt="">
				</div>					
			</div>
		</div>
	</div>
</section>



<section class="b-shop section">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-shop-top">
					<h2>Магазин тюнинга №1<br/>
						в Москве</h2>
					<p>Свой большой склад и 2500 кв. м. <br/>
						для дооснащения вашего авто</p>
				</div>
				<div class="b-shop-content">
					<div class="b-shop-items">
						<div class="b-shop-col">
							<div class="b-shop-item">
								<div class="b-shop-item__img">
									<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/b-shop-item__img.png" alt="">
								</div>
								<div class="b-shop-item__name">Audi</div>
								<ul>
									<li><a href="">Q7</a></li>
									<li><a href="">A7</a></li>
									<li><a href="">A5</a></li>
									<li><a href="">A4</a></li>
								</ul>
							</div>
						</div>
						<div class="b-shop-col">
							<div class="b-shop-item">
								<div class="b-shop-item__img">
									<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/b-shop-item__img2.png" alt="">
								</div>
								<div class="b-shop-item__name">Mercedes</div>
								<ul>
									<li><a href="">A-class</a></li>
									<li><a href="">C-class</a></li>
									<li><a href="">C-class Coupe</a></li>
								</ul>
								<a href="" class="b-shop-item__link">Показать все</a>
							</div>
						</div>
						<div class="b-shop-col">
							<div class="b-shop-item">
								<div class="b-shop-item__img">
									<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/b-shop-item__img3.png" alt="">
								</div>
								<div class="b-shop-item__name">Lexus</div>
								<ul>
									<li><a href="">LX</a></li>
									<li><a href="">NX</a></li>
									<li><a href="">RX</a></li>
									<li><a href="">GX</a></li>
								</ul>
							</div>
						</div>
						<div class="b-shop-col">
							<div class="b-shop-item">
								<div class="b-shop-item__img">
									<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/b-shop-item__img4.png" alt="">
								</div>
								<div class="b-shop-item__name">Mitsubishi</div>
								<ul>
									<li><a href="">Q7</a></li>
									<li><a href="">A7</a></li>
									<li><a href="">A5</a></li>
									<li><a href="">A4</a></li>
								</ul>
							</div>
						</div>
						<div class="b-shop-col">
							<div class="b-shop-item">
								<div class="b-shop-item__img">
									<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/b-shop-item__img5.png" alt="">
								</div>
								<div class="b-shop-item__name">BMW</div>
								<ul>
									<li><a href="">X3</a></li>
									<li><a href="">X4</a></li>
									<li><a href="">X5</a></li>
								</ul>
								<a href="" class="b-shop-item__link">Показать все</a>
							</div>
						</div>
						<div class="b-shop-col">
							<div class="b-shop-item">
								<div class="b-shop-item__img">
									<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/b-shop-item__img6.png" alt="">
								</div>
								<div class="b-shop-item__name">Land Rover</div>
								<ul>
									<li><a href="">Discovery</a></li>
									<li><a href="">Freelander</a></li>
									<li><a href="">Range Rover</a></li>
								</ul>
								<a href="" class="b-shop-item__link">Показать все</a>
							</div>
						</div>
						<div class="b-shop-col">
							<div class="b-shop-item">
								<div class="b-shop-item__img">
									<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/b-shop-item__img7.png" alt="">
								</div>
								<div class="b-shop-item__name">Toyota</div>
								<ul>
									<li><a href="">Camry</a></li>
									<li><a href="">Highlander</a></li>
									<li><a href="">Land Cruiser</a></li>
								</ul>
								<a href="" class="b-shop-item__link">Показать все</a>
							</div>
						</div>
						<div class="b-shop-col">
							<div class="b-shop-item">
								<div class="b-shop-item__img">
									<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/b-shop-item__img8.png" alt="">
								</div>
								<div class="b-shop-item__name">Nissan</div>
								<ul>
									<li><a href="">Patrol</a></li>
									<li><a href="">Pathfinder</a></li>
									<li><a href="">Qashqai</a></li>
								</ul>
							</div>
						</div>
					</div>

					<div class="b-shop-right">
						<div class="b-shop-right__top">
							<b>Последние<br/>
								проекты</b>
							<span>Разобрали, затюнили, собрали</span>
						</div>
						<div class="b-shop-slider">
							<div class="b-shop-slider__item">
								<a href="" class="b-shop-slider__item__link"></a>
								<div class="b-shop-slider__item__img">
									<img data-lazy="<?= get_stylesheet_directory_uri() ?>/images/b-shop-slider__item__img.jpg" alt="">
								</div>
								<div class="b-shop-slider__item__text">
									<b href="">Аэродинамический обвес Brabus 
										для новогоa Mercedes G63 G500 
										G350 W464 / W463A</b>
								</div>
							</div>
							<div class="b-shop-slider__item">
								<a href="" class="b-shop-slider__item__link"></a>
								<div class="b-shop-slider__item__img">
									<img data-lazy="<?= get_stylesheet_directory_uri() ?>/images/b-shop-slider__item__img.jpg" alt="">
								</div>
								<div class="b-shop-slider__item__text">
									<b href="">Аэродинамический обвес Brabus 
										для новогоa Mercedes G63 G500 
										G350 W464 / W463A</b>
								</div>
							</div>
							<div class="b-shop-slider__item">
								<a href="" class="b-shop-slider__item__link"></a>
								<div class="b-shop-slider__item__img">
									<img data-lazy="<?= get_stylesheet_directory_uri() ?>/images/b-shop-slider__item__img.jpg" alt="">
								</div>
								<div class="b-shop-slider__item__text">
									<b href="">Аэродинамический обвес Brabus 
										для новогоa Mercedes G63 G500 
										G350 W464 / W463A</b>
								</div>
							</div>
							<div class="b-shop-slider__item">
								<a href="" class="b-shop-slider__item__link"></a>
								<div class="b-shop-slider__item__img">
									<img data-lazy="<?= get_stylesheet_directory_uri() ?>/images/b-shop-slider__item__img.jpg" alt="">
								</div>
								<div class="b-shop-slider__item__text">
									<b href="">Аэродинамический обвес Brabus 
										для новогоa Mercedes G63 G500 
										G350 W464 / W463A</b>
								</div>
							</div>																								
						</div>
						<div class="b-shop-slider__nav"></div>
					</div>
				</div>
				<a class="b-shop-content__more" href="<?= get_permalink( 143 ) ?>">Полный каталог</a>
			</div>
		</div>
	</div>
</section>


<section class="b-offers section">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-offers-container">
					<div class="b-offers-text">W464</div>
					<div class="b-offers-top">
						<h2 class="white">Специальные предложения<br/> 
							в августе</h2>
					</div>
					<div class="swiper-container b-offers-slider">
						<button class="b-arrow b-arrow__prev">prev</button>
						<button class="b-arrow b-arrow__next">next</button>
						<div class="swiper-wrapper">
							<div class="b-offers-col swiper-slide">
								<div class="b-offers-item">
									<div class="b-offers-item__left">
										<div class="b-offers-item__left__top">
											<b>Обвес Brabus полный Mercedes 
												G-class W464</b>
											<p>Комплектующие обвеса изготовлены из ABS пластика. Обвес 
												устанавливается в штатные места без доработок. Обвес поставляется 
												под окрас.</p>
											<p><strong>В комплект обвеса входит:</strong> 
												передний бампер Brabus в сборе с ходовыми огнями, решетка радиатора, 
												расширители колесных арок, козырек на крышу с ходовыми огнями, 
												накладка Brabus на капот, законцовки порогов с подсветкой, задний 
												бампер Brabus в сборе, спойлер Brabus, комплект крепежа.</p>
										</div>
										<div class="b-offers-item__bottom">
											<div class="b-offers-item__price">
												<div class="b-offers-item__price__old"><b>550 000</b> руб.</div>
												<div class="b-offers-item__price__new"><b>400 000</b> руб.</div>
											</div>
											<button class="b-offers-item__btn red-btn">Подробнее</button>
										</div>
									</div>
									<div class="b-offers-item__img">
										<a class="b-offers-video icon-youtube fancybox-media" href="https://www.youtube.com/embed/gq5VdIhWxaQ?rel=0&showinfo=0&autoplay=1"></a>
										<img class="swiper-lazy" data-src="<?= get_stylesheet_directory_uri() ?>/images/b-offers-item__img.png" alt="">
									</div>
								</div>						
							</div>
							<div class="b-offers-col swiper-slide">
								<div class="b-offers-item">
									<div class="b-offers-item__left">
										<div class="b-offers-item__left__top">
											<b>Обвес Brabus полный Mercedes 
												G-class W464</b>
											<p>Комплектующие обвеса изготовлены из ABS пластика. Обвес 
												устанавливается в штатные места без доработок. Обвес поставляется 
												под окрас.</p>
											<p><strong>В комплект обвеса входит:</strong> 
												передний бампер Brabus в сборе с ходовыми огнями, решетка радиатора, 
												расширители колесных арок, козырек на крышу с ходовыми огнями, 
												накладка Brabus на капот, законцовки порогов с подсветкой, задний 
												бампер Brabus в сборе, спойлер Brabus, комплект крепежа.</p>
										</div>
										<div class="b-offers-item__bottom">
											<div class="b-offers-item__price">
												<div class="b-offers-item__price__old"><b>550 000</b> руб.</div>
												<div class="b-offers-item__price__new"><b>400 000</b> руб.</div>
											</div>
											<button class="b-offers-item__btn red-btn">Подробнее</button>
										</div>
									</div>
									<div class="b-offers-item__img">
										<a class="b-offers-video icon-youtube fancybox-media" href="https://www.youtube.com/embed/gq5VdIhWxaQ?rel=0&showinfo=0&autoplay=1"></a>
										<img class="swiper-lazy" data-src="<?= get_stylesheet_directory_uri() ?>/images/b-offers-item__img.png" alt="">
									</div>
								</div>						
							</div>
							<div class="b-offers-col swiper-slide">
								<div class="b-offers-item">
									<div class="b-offers-item__left">
										<div class="b-offers-item__left__top">
											<b>Обвес Brabus полный Mercedes 
												G-class W464</b>
											<p>Комплектующие обвеса изготовлены из ABS пластика. Обвес 
												устанавливается в штатные места без доработок. Обвес поставляется 
												под окрас.</p>
											<p><strong>В комплект обвеса входит:</strong> 
												передний бампер Brabus в сборе с ходовыми огнями, решетка радиатора, 
												расширители колесных арок, козырек на крышу с ходовыми огнями, 
												накладка Brabus на капот, законцовки порогов с подсветкой, задний 
												бампер Brabus в сборе, спойлер Brabus, комплект крепежа.</p>
										</div>
										<div class="b-offers-item__bottom">
											<div class="b-offers-item__price">
												<div class="b-offers-item__price__old"><b>550 000</b> руб.</div>
												<div class="b-offers-item__price__new"><b>400 000</b> руб.</div>
											</div>
											<button class="b-offers-item__btn red-btn">Подробнее</button>
										</div>
									</div>
									<div class="b-offers-item__img">
										<a class="b-offers-video icon-youtube fancybox-media" href="https://www.youtube.com/embed/gq5VdIhWxaQ?rel=0&showinfo=0&autoplay=1"></a>
										<img class="swiper-lazy" data-src="<?= get_stylesheet_directory_uri() ?>/images/b-offers-item__img.png" alt="">
									</div>
								</div>							
							</div>
						</div>
					</div>
					<div class="b-offers-nav"></div>
				</div>
			</div>
		</div>
	</div>
</section>


<div class="b-block">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-block-content">
					<div class="b-block-text">Manc</div>
					<div class="b-block-img">
						<img class="lazy" data-original="<?= get_stylesheet_directory_uri() ?>/images/b-block-img.png" alt="">
					</div>
					<div class="b-block-right">
						<div class="b-block-right__top">
							<h2>Приехать на тюннинг 
								просто как 1,2,3. </h2>
							<span>Выберите услугу и запишитесь онлайн </span>
						</div>
						<div class="b-block-right__form manc_cf7_feedback">
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


<section class="b-main-services section">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<div class="b-main-services__top">
					<h2>Дополнительные<br/>
						услуги</h2>
				</div>
			</div>
		</div>
		<div class="b-services-slider">
			<div class="row">
				<div class="b-services-col">
					<div class="b-services-item">
						<div class="b-services-item__top">
							<div class="b-services-item__img">
								<img data-lazy="<?= get_stylesheet_directory_uri() ?>/images/b-services-item__img.jpg" alt="">
							</div>
							<div class="b-services-item__text">
								<b>Оклейка кузова автомобиля
									пленкой</b>
								<ul>
									<li>- Антигравийной пленкой</li>
								</ul>
							</div>
						</div>
						<a href="" class="b-services-item__link">подробнее</a>
					</div>
				</div>
				<div class="b-services-col">
					<div class="b-services-item">
						<div class="b-services-item__top">
							<div class="b-services-item__img">
								<img data-lazy="<?= get_stylesheet_directory_uri() ?>/images/b-services-item__img2.jpg" alt="">
							</div>
							<div class="b-services-item__text">
								<b>Оклейка кузова автомобиля
									пленкой</b>
								<ul>
									<li>- Виниловой пленкой</li>
								</ul>
							</div>
						</div>
						<a href="" class="b-services-item__link">подробнее</a>
					</div>
				</div>
				<div class="b-services-col">
					<div class="b-services-item">
						<div class="b-services-item__top">
							<div class="b-services-item__img">
								<img data-lazy="<?= get_stylesheet_directory_uri() ?>/images/b-services-item__img3.jpg" alt="">
							</div>
							<div class="b-services-item__text">
								<b>Оклейка кузова автомобиля
									пленкой</b>
								<ul>
									<li>- Жидким стеклом</li>
								</ul>
							</div>
						</div>
						<a href="" class="b-services-item__link">подробнее</a>
					</div>
				</div>
				<div class="b-services-col">
					<div class="b-services-item">
						<div class="b-services-item__top">
							<div class="b-services-item__img">
								<img data-lazy="<?= get_stylesheet_directory_uri() ?>/images/b-services-item__img3.jpg" alt="">
							</div>
							<div class="b-services-item__text">
								<b>Оклейка кузова автомобиля
									пленкой</b>
								<ul>
									<li>- Жидким стеклом</li>
								</ul>
							</div>
						</div>
						<a href="" class="b-services-item__link">подробнее</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xl"></div>
		</div>
	</div>
</section>


<section class="section">
	<div class="container">
		<div class="row">
			<div class="col-xl">
				<h2>Тюнинг Audi</h2>
				<p>Ауди – одна из легендарных марок немецкого автопрома: эти машины отличаются плавным ходом, мощными двигателями, стильным дизайном. Если 
					характеристики авто по каким-то причинам не устраивают владельца, тюнинг Audi придаст автомобилю неповторимый вид, улучшит 
					аэродинамические характеристики.</p>
				<p>Компания «MANC Tuning & Performance» – это тюнинг-ателье и интернет-магазин. У нас можно заказать запчасти, элементы дооснащения, 
					аэродинамические обвесы, альтернативную оптику, тюнинг-пакеты. Мастера нашего ателье установят элементы на автомобиль, выполнят внешний 
					тюнинг, установят акустику, оклеят авто пленкой, выполнят брендирование.</p>
				<h3>Тюнинг Audi: особенности</h3>
				<p>Автомобили марки Audi не раз побеждали на международных автогонках, что повлияло на популярность моделей этого концерна. Автолюбители 
					ценят надежность машин, мощный двигатель, стильный экстерьер. Тюнинг Audi – это модификация авто, которая придает ему уникальность, улучшает 
					аэродинамические свойства модели. Многие мастера тюнинга работают с автомобилями Audi. Известные ателье: ABT Sportsline, PPI – разработали 
					обвесы для машин этой марки, тюнинг-пакеты, которые оценили владельцы авто в разных странах.
				</p>
				<h4>Тюнинг автомобилей Audi может состоять из таких этапов:</h4>
				<ul class="list">
					<li>Чип-тюнинг. Это оптимизация программного обеспечения машины, которая увеличивает крутящий момент, повышает мощность автомобиля, оптимизирует работу двигателя, снижает расход топлива.</li>
					<li>Доработка экстерьера. Альтернативная оптика, хромированные накладки, дуги, пороги, решетки радиатора улучшат внешний вид авто. Аэродинамические спойлеры и обвес повысят обтекаемость автомобиля, что важно для любителей быстрой езды.</li>
				</ul>
			</div>
		</div>
	</div>
</section>


<?= get_footer() ?>
