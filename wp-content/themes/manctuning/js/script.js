jQuery( document ).ready( function ( $ ) {


	/*$( '.input-number' ).on( 'input', function () {
	 $( this ).val( $( this ).val().replace( /[A-Za-zА-Яа-яЁё]/, '' ) )
	 } );
	 $( '.input-text' ).on( 'keypress', function () {
	 var that = this;
	 
	 setTimeout( function () {
	 var res = /[^а-я ]/g.exec( that.value );
	 console.log( res );
	 that.value = that.value.replace( res, '' );
	 }, 0 );
	 } );*/

	// Верхний Поиск товаров ===================================================================================
	( function () {

		let r, // Данные по Таксономиям Модели и Поколения
		$products_count = $( "#wppw_products_count" ), // Число найденной продукции
		// 
		$context = $( '#wppw_top_search_bar' ), // Поисковый бар
		$brand = $( '.wppw_brand', $context ), // Марка
		$model = $( '.wppw_model', $context ), // Модель
		$breed = $( '.wppw_breed', $context ), // Поколение
		$type = $( '.wppw_type', $context ), // Тип товара
		q = [ ];
		// Подбор параметров из URL // https://stackoverflow.com/a/901144
		const urlParams = new URLSearchParams( window.location.search );
		for ( const e of [ 'brand', 'model', 'breed', 'type' ] ) {

			// Подбираем значение из URL
			q[e] = urlParams.get( e );
		}

		// Вставка картинки в Поколение
		function formatBreeds( state ) {

			if ( !state.id ) {
				return state.text;
			}
			var baseUrl = "/wp-content/uploads/breeds/";
			var $state = $(
			`<span class="select2-results__custom">
					<img src="${baseUrl}/${state.element.value.toLowerCase()}.png" />
					${state.text}
				</span>`
			);
			return $state;
		}
		;


		// Select2
		$brand.select2( {
			minimumResultsForSearch : -1
		} );
		$model.select2( {
			minimumResultsForSearch : -1
		} );
		$breed.select2( {
			templateResult : formatBreeds,
			minimumResultsForSearch : -1,
			dropdownAutoWidth : true,
		} );
		$type.select2( {
			minimumResultsForSearch : -1
		} );


		// Выбрали Марку
		$brand.on( 'select2:select', function ( e ) {

			// Сбрасываем списки на дефолтные
			$model.empty().append( '<option value="" disabled selected>Загрузка</option>' ).trigger( 'change' );
			$breed.empty().append( '<option value="" disabled selected>Поколение</option>' ).trigger( 'change' );

			// Возвращаем статус disabled на Модели и Поколения
			$model.prop( 'disabled', true );
			// Возвращаем статус disabled на поколения
			$breed.prop( 'disabled', true );

			var
			data = {
				action : 'wppw_top_search_bar',
				nonce : wppw_nonce,
				tax : e.params.data.id, // brand slug
				context : 'brand',
			};

			// Собираем данные из бекенда
			$.post( ajaxurl, data, e => {

				console.info( e )

				if ( e.success ) { // Есть данные для заполнения моделей

					// Обновляем число найденных товаров
					console.info( e.data );
					if ( e.data.products_count > 0 )
						$products_count.html( 'Товаров: ' + e.data.products_count );
					else
						$products_count.html( 'Товаров нет' );

					// Обновляем список Моделей
					$model.empty().append( '<option value="" disabled selected>Модель</option>' ).trigger( 'change' );

					// Данные с дочерними терминами
					const children = e.data.children_terms;

					// Перебор дочерних таксономий
					for ( let k in children ) {

						const
						child = children[k], // Данные конкретной модели
						name = child.name, // Название опции
						slug = child.slug; // Ярлык опции

						let newOption;

						// Набираем элементы в модель
						if ( slug == q['model'] ) { // Значение по умолчанию (данные из URL)
							newOption = new Option( name, slug, true, true );
						} else {
							newOption = new Option( name, slug, false, false );
						}

						$model.append( newOption ).trigger( 'change' );
					}

					// Снимаем статус disabled с моделей
					$model.prop( 'disabled', false );

				} else {

					$products_count.html( 'Товаров не найдено' );

					// Возвращаем статус disabled на Модели и Поколения
					$model.prop( 'disabled', true );
					$breed.prop( 'disabled', true );
					console.info( 'Brand has not children' );

					$model.empty().append( '<option value="" disabled selected>Моделей нет</option>' ).trigger( 'change' );
				}
			} );


		} );

		// Выбрали Модель
		$model.on( 'select2:select', function ( e ) {

			// Сбрасываем Поколения на дефолтные
			$breed.empty().append( '<option value="" disabled selected>Загрузка</option>' ).trigger( 'change' );

			// Возвращаем статус disabled на Поколения
			$breed.prop( 'disabled', true );

			let
			data = {
				action : 'wppw_top_search_bar',
				nonce : wppw_nonce,
				tax : e.params.data.id, // model slug
				context : 'model',
			};

			// Собираем данные из бекенда
			$.post( ajaxurl, data, e => {

				if ( e.success ) { // Есть данные для заполнения моделей

					// Обновляем данные по товарам справа
					if ( e.data.products_count > 0 )
						$products_count.html( 'Товаров: ' + e.data.products_count );
					else
						$products_count.html( 'Товаров нет' );

					// Обновляем список Поколений
					$breed.empty().append( '<option value="" disabled selected>Поколение</option>' ).trigger( 'change' );

					// Данные с дочерними терминами
					const children = e.data.children_terms;

					// Перебор дочерних таксономий
					for ( let k in children ) {

						const
						child = children[k], // Данные конкретной модели
						name = child.name, // Название опции
						slug = child.slug; // Ярлык опции

						let newOption;

						// Набираем элементы в модель
						if ( slug == q['breed'] ) { // Значение по умолчанию (данные из URL)
							newOption = new Option( name, slug, true, true );
						} else {
							newOption = new Option( name, slug, false, false );
						}

						// Наполняем опции
						$breed.append( newOption ).trigger( 'change' );
					}

					// Снимаем статус disabled с моделей
					$breed.prop( 'disabled', false );

				} else {
					// Возвращаем статус disabled на Поколения
					$breed.prop( 'disabled', true );
					console.info( 'Model has not children' );

					$breed.empty().append( '<option value="" disabled selected>Поколений нет</option>' ).trigger( 'change' );
				}
			} );

		} );

		// Выбрали Поколение
		$breed.on( 'select2:select', function ( e ) {

			$type.empty().append( '<option value="" disabled selected>Тип оборудования</option>' ).trigger( 'change' );

			let
			data = {
				action : 'wppw_top_search_bar',
				nonce : wppw_nonce,
				tax : e.params.data.id, // model slug
				context : 'breed',
			};

			// Собираем данные из бекенда
			$.post( ajaxurl, data, e => {
				
				console.info( e );

				if ( e.success ) { // Есть данные для заполнения моделей

					// Обновляем список "Тип оборудования"
					change_list_of_types( e.data.product_tags, $type );

					// Обновляем данные по товарам справа
					if ( e.data.products_count > 0 )
						$products_count.html( 'Товаров: ' + e.data.products_count );
					else
						$products_count.html( 'Товаров нет' );

				} else {
					// Возвращаем статус disabled на Поколения
					$breed.prop( 'disabled', true );
					console.info( 'Model has not children' );

					$breed.empty().append( '<option value="" disabled selected>Поколений нет</option>' ).trigger( 'change' );
				}
			} );

		} );
		
		// Выбрали Тип
		$type.on( 'select2:select', function ( e ) {
			
			let
			data = {
				action : 'wppw_top_search_bar',
				nonce : wppw_nonce,
				tax : e.params.data.id, // model slug
				context : 'type',
				wppw_breed : $('.wppw_breed').val(), // Получаем выбранное Поколение
			};

			// Собираем данные из бекенда
			$.post( ajaxurl, data, e => {
				
				console.info( e );

				if ( e.success ) { // Есть данные для заполнения моделей

					// Обновляем данные по товарам справа
					if ( e.data.products_count > 0 )
						$products_count.html( 'Товаров: ' + e.data.products_count );
					else
						$products_count.html( 'Товаров нет' );

				} else {
					// Возвращаем статус disabled на Поколения
					$breed.prop( 'disabled', true );
					console.info( 'Model has not children' );
				}
			} );

		} );		

		//////////////////////////////////////
		// Если в URL есть Марка
		if ( null !== q['brand'] ) {

			console.info( '// в URL есть Марка' )

			$brand
			.trigger( {
				type : 'select2:select',
				params : {
					data : {
						id : q['brand'],
					}
				}
			} )
			.val( q['brand'] ).trigger( 'change' );
		}


		//////////////////////////////////////
		// Если в URL есть Модель
		if ( null !== q['model'] ) {

			console.info( '// в URL есть Модель' )
			$model.trigger( {
				type : 'select2:select',
				params : {
					data : {
						id : q['model'],
					}
				}
			} )
			.val( q['model'] ).trigger( 'change' );
		}


		//////////////////////////////////////
		// Если в URL есть Тип
		if ( null !== q['type'] ) {

			console.info( '// в URL есть Тип' )
			$type.val( q['type'] ).trigger( 'change' );
		}


		// Хелперы ///////////////////////////////////////////
		function change_list_of_types( types, $type ) {

			// Перебор таксономий
			for ( let k in types ) {

				const
				child = types[k], // Данные конкретной модели
				name = child.name, // Название опции
				slug = child.slug; // Ярлык опции

				let newOption;

				newOption = new Option( name, slug, false, false );

				$type.append( newOption ).trigger( 'change' );
			}

			// Снимаем статус disabled с моделей
			$type.prop( 'disabled', false );

		}

	} )();

	// ===================================================================================

	$( "[data-fancybox]" ).fancybox( {
		/*thumbs : {
			autoStart : true,
		},*/
	} );


	/*$( ".phone" ).mask( "99.99.9999", {
	 placeholder : "99.99.9999"
	 } );
	 
	 $( ".date-mask" ).mask( "99.99.9999", {
	 placeholder : "01.12.2000"
	 } );
	 
	 $( ".kredit-mask" ).mask( "9999-9999-9999-9999", {
	 placeholder : "9999.9999.9999.9999"
	 } );*/

	$( "img.lazy" ).lazyload( {
		effect : "fadeIn"
	} );

	$( '.b-shop-slider' ).slick( {
		slidesToShow : 1,
		slidesToScroll : 1,
		arrows : true,
		dots : false,
		fade : true,
		lazyLoad : 'ondemand',
		appendArrows : '.b-shop-slider__nav',
		variableWidth : false
	} );



	if ( window.innerWidth <= 520 ) {
		$( '.js-slider-transformToSlider' ).slick( {
			slidesToShow : 1,
			slidesToScroll : 1,
			arrows : true,
			dots : false,
			lazyLoad : 'ondemand',
			variableWidth : false,
		} );
	}


	/*$('.b-offers-slider').slick({
	 centerMode: true,
	 centerPadding: '35px',
	 slidesToShow: 1,
	 initialSlide: 1,
	 lazyLoad: 'ondemand',
	 });*/

	var swiper = new Swiper( '.b-offers-slider', {
		slidesPerView : 'auto',
		centeredSlides : true,
		effect : 'coverflow',
		grabCursor : true,
		loop : true,
		lazy : true,
		navigation : {
			nextEl : '.b-arrow__next',
			prevEl : '.b-arrow__prev',
		},
	} );


	$( ".fancybox-media" ).fancybox( {
		"width" : 620, // or whatever
		"height" : 420,
		"type" : "iframe"
	} );

	const bServisesSliderConfig = {
		slidesToShow : 3,
		slidesToScroll : 1,
		arrows : false,
		dots : false,
		// lazyLoad : 'ondemand',
		variableWidth : false,
		responsive : [ {
				breakpoint : 1200,
				settings : {
					slidesToShow : 3,
					slidesToScroll : 1,
					variableWidth : false,
				}
			},
			{
				breakpoint : 992,
				settings : {
					slidesToShow : 3,
					variableWidth : false,
					slidesToScroll : 2
				}
			},
			{
				breakpoint : 768,
				settings : {
					slidesToShow : 2,
					variableWidth : false,
					slidesToScroll : 2
				}
			},
			{
				breakpoint : 621,
				settings : {
					slidesToShow : 1,
					variableWidth : false,
					slidesToScroll : 1
				}
			}
		]
	}

	$( '.single .b-services-slider .row' ).slick( bServisesSliderConfig );
	$( '.home .b-services-slider .row' ).slick( {
		...bServisesSliderConfig,
		arrows : true,
		dots : true,
	} );


	var scrolledpx = parseInt( $( window ).scrollTop() );
	$( window ).scroll( function () {

		scrolledpx = parseInt( $( window ).scrollTop() );

		if ( scrolledpx > 1230 ) {
			$( '.btn-top' ).addClass( 'active' );
		} else if ( scrolledpx < 1231 ) {
			$( '.btn-top' ).removeClass( 'active' );
		}

	} );


	$( ".btn-top" ).click( function () {
		$( 'html, body' ).animate( {
			scrollTop : 0
		}, 'slow' );
	} );

	$( '.star-rating' ).rating();


	$( '.b-card-slider__content' ).slick( {
		slidesToShow : 1,
		slidesToScroll : 1,
		fade : false,
		dots : false,
		arrows : false,
		lazyLoad : 'ondemand',
		infinite : false,
		swipe : false,
		asNavFor : '.b-card-slider__preview',
		responsive : [ {
				breakpoint : 1200,
				settings : {
					slidesToShow : 1,
					slidesToScroll : 1
				}
			},
			{
				breakpoint : 992,
				settings : {
					slidesToShow : 1,
					dots : true,
					slidesToScroll : 1
				}
			},
			{
				breakpoint : 768,
				settings : {
					slidesToShow : 1,
					// dots : true,
					slidesToScroll : 1
				}
			}
		]
	} );


	$( '.b-card-slider__preview' ).slick( {
		slidesToShow : 3,
		slidesToScroll : 1,
		arrows : false,
		dots : false,
		infinite : false,
		lazyLoad : 'ondemand',
		asNavFor : '.b-card-slider__content',
		verticalSwiping : false,
		vertical : true,
		focusOnSelect : true,
		variableWidth : false,
		responsive : [ {
				breakpoint : 1369,
				settings : {
					slidesToShow : 3,
					slidesToScroll : 1
				}
			},
			{
				breakpoint : 992,
				settings : {
					slidesToShow : 3,
					slidesToScroll : 1
				}
			},
			{
				breakpoint : 768,
				settings : {
					slidesToShow : 3,
					slidesToScroll : 1
				}
			}
		]
	} );

	$( '.b-modal-card' ).on( 'shown.bs.modal', function () {

		$( '.b-card-slider__content' ).slick( 'refresh' );
		$( '.b-card-slider__preview' ).slick( 'refresh' );
	} )


	$( 'body' ).on( 'click', '.b-quant .minus', function () {
		var $input = $( this ).parent().find( 'input' );
		var count = parseInt( $input.val() ) - 1;
		count = count < 1 ? 1 : count;
		$input.val( count );
		$input.change();
		return false;
	} );
	$( 'body' ).on( 'click', '.b-quant .plus', function () {
		var $input = $( this ).parent().find( 'input' );
		$input.val( parseInt( $input.val() ) + 1 );
		$input.change();
		return false;
	} );





	$( ".b-article-slider" ).each( function ( index ) {
		$( '.b-article-slider__big', $( this ) ).slick( {
			slidesToShow : 1,
			slidesToScroll : 1,
			fade : false,
			dots : false,
			arrows : true,
			lazyLoad : 'ondemand',
			infinite : true,
			asNavFor : $( this ).find( '.b-article-slider__preview' ),
			responsive : [ {
					breakpoint : 1200,
					settings : {
						variableWidth : true,
						slidesToShow : 1,
						slidesToScroll : 1
					}
				},
				{
					breakpoint : 992,
					settings : {
						slidesToShow : 1,
						dots : true,
						slidesToScroll : 1
					}
				},
				{
					breakpoint : 768,
					settings : {
						slidesToShow : 1,
						dots : true,
						slidesToScroll : 1
					}
				}
			]
		} );


		$( '.b-article-slider__preview', $( this ) ).slick( {
			slidesToShow : 3,
			slidesToScroll : 1,
			arrows : false,
			dots : false,
			infinite : true,
			lazyLoad : 'ondemand',
			asNavFor : $( this ).find( '.b-article-slider__big' ),
			verticalSwiping : true,
			vertical : true,
			focusOnSelect : true,
			variableWidth : false,
			responsive : [ {
					breakpoint : 1369,
					settings : {
						slidesToShow : 3,
						slidesToScroll : 1
					}
				},
				{
					breakpoint : 1200,
					settings : {
						verticalSwiping : false,
						vertical : false,
						variableWidth : true,
						slidesToShow : 3,
						slidesToScroll : 1
					}
				},
			]
		} );

	} );

	// Адаптивное vменю
	function responsiveHeader() {
		var headerBtn = $( `
      <div class='b-header-mob-btn'>
        <span></span><span></span><span></span>
      </div>
    ` )
		var headerContacts = $( ".b-header-contacts" )
		headerContacts.clone().insertBefore( $( ".b-header-btns" ) )
		headerBtn.insertAfter( $( ".b-header-top__right" ) )
		$( ".b-header-mob-btn" ).on( "click", function () {
			$( ".b-header-top__right__bottom" ).toggle()
		} )

		$( ".js-search-button" ).on( "click", function () {
			$( this ).next( ".b-header-content__form" ).toggleClass( "visible" )
			$( this ).toggleClass( "visible" )
		} )


		$( ".js-filters-button, .b-catalog-filters-area__close, .js-filter-overlay" ).on( "click", function () {
			$( ".js-filter-area" ).toggleClass( "visible" )
			$( ".js-filter-overlay" ).toggleClass( "visible" )
		} )
	}
	responsiveHeader()

	if ( $( ".op-tabs-wrapper" ).length ) {
		new OpTabs( ".op-tabs-wrapper" )
	}

	// Адаптивное главное меню
	function responsiveMainSubMenu() {
		var arrow = `<span class="menu-item__arrow"></span>`
		$( ".sub-menu" ).before( arrow )
		$( ".menu-item__arrow" ).on( "click", function () {
			var dropDown = $( this ).next( ".sub-menu" );
			if ( $( this ).hasClass( "open" ) ) {
				$( this ).removeClass( "open" )
				dropDown.slideUp()
			} else {
				$( this ).addClass( "open" )
				dropDown.slideDown()
			}
		} )
	}
	responsiveMainSubMenu()
} );
