jQuery( document ).ready( function ( $ ) {


	// jquery.mask.min.js ====================================================================================
	( function () {

		$( 'input[type="tel"]' ).mask( '+7 (000) 000-00-00' );

	} )();

	// Работаем с поисковым баром сверху ====================================================================================
	( function () {

		$( '#wppw_top_search_bar' ).on( 'submit', function ( e ) {

			e.preventDefault();

			var $form = $( this ),
				form_data = $form.serialize(),
				form_data_r = $form.serializeArray(),
				q = 1;

			// Запрос уходит в каталог по умолчанию
			this.action = catalog_url;

			// Отфильтровываем пустые запросы
			form_data_r = form_data_r.filter( e => {

				if ( e.value.length > 0 )
					return true;

				return false;
			} );

			// Пустую форму никуда не отправляем
			if ( form_data_r.length <= 0 )
				return;

			// Если запрос только по type
			if ( 1 == form_data_r.length && "type" == form_data_r[0].name && form_data_r[0].value.length > 0 ) {
				
				// Отправляем пользователя туда
				window.location.href = type_url + '/' + form_data_r[0].value;
				return;
			}


			// А полную фоорму отправляем в бекенд
			this.submit();

		} );

		//
		/*$( '.wppw_brand', '#wppw_top_search_bar' ).on( 'select2:opening', function () {
		 console.info(3241234 )
		 $( '#wppw_brand_default_selected' ).hide()
		 } );*/

	} )();

	// WC AJAX добавление и удаление из корзины ====================================================================================
	( function () {

		// Добавляем товар в корзину
		// Удаляем товар из корзины
		$( '.wppw_add_to_cart, .wppw_delete_from_cart' ).on( 'click', async function ( e ) {

			e.preventDefault();

			let $this = $( this );

			// Удаление из корзины проверять
			/*if ( $this.hasClass( 'wppw_delete_from_cart' ) && !confirm( 'Уверены, что хотите удалить товар из корзины?' ) )
			 return;*/

			// Если это процесс удаления товара из корзины
			if ( $this.hasClass( 'wppw_delete_from_cart' ) ) {

				// Ждём данные из модального окна
				let r = await confirm_is_delete_from_cart();

				if ( !r )
					// Удалять товар из корзины не нужно
					return false;
			}

			// Отправляем данные по операции с корзиной
			cart( $this, {
				action : 'wppw_cart',
				nonce : $this.data( 'nonce' ),
				id : $this.data( 'id' ),
				command : $this.data( 'command' ),
				is_change_text : $this.data( 'is_change_text' ), // Изменять ли текст кнопки ответным месседжем
				is_window_reload : $this.data( 'is_window_reload' ), // Перезагружать ли страницу после ajax
			} );
		} );

		// Увеличиваем число товара в корзине
		// Уменьшаем число товара в корзине
		//$( '.wppw_increase_to_cart, .wppw_decrease_from_cart' ).on( 'click', increase_decrease );
		$( 'body' ).on( 'click', '.wppw_increase_to_cart', increase_decrease );
		$( 'body' ).on( 'click', '.wppw_decrease_from_cart', increase_decrease );

		function increase_decrease( e ) {

			e.preventDefault();

			let $this = $( this );

			// Отправляем команду в корзину
			cart( $this, {
				action : 'wppw_cart',
				nonce : $this.data( 'nonce' ),
				id : $this.data( 'id' ),
				quantity : $this.siblings( '.wppw_product_quantity' ).val(),
				command : $this.data( 'command' ),
				is_change_text : $this.data( 'is_change_text' ), // Изменять ли текст кнопки ответным месседжем
				is_window_reload : false, // Перезагружать ли страницу после ajax
			} );
		}

		// Работа с корзиной
		function cart( $this, data ) {

			/*let
			 $quantity_input = $( '.wppw_product_quantity' );*/

			// Backend
			$.post( ajaxurl, data, e => {

				console.info( e )

				if ( e.success ) {

					// Операция "Добавление в корзину"
					if ( 'add' == data.command ) {

						// Оповещаем систему о том, что продукт добавлен // cart_item_key - ключ товара в корзине
						$( document.body ).trigger( 'wppw_added_to_cart', [ e.data.cart_item_key ] );
					}

					// Меняем число товаров в корзине
					/*if ( e.data.quantity )
					 $quantity_input.val( e.data.quantity );*/


					// Отражать ли статус в кнопке
					if ( data.is_change_text )
						$this.text( e.data.msg );


					// Перезагружать ли страницу после успешного прохождения ajax запроса
					if ( data.is_window_reload )
						window.location.reload();
				}
			} )

		}

		// Helper
		// Подтвердить удаление из корзины
		async function confirm_is_delete_from_cart() {

			// Показываем попап
			$( '#modal-remove_from_cart' ).modal( 'show' );

			return new Promise( ( resolve, reject ) => {

				// Удаляем
				$( 'body' ).on( 'click', '#confirm_is_delete_from_cart__yes', () => {

					// Закрываем модальное окно
					$( '#modal-remove_from_cart' ).modal( 'hide' );

					console.info( 'Удаляем товар' );
					resolve( true );
				} );

			} );

			// По умолчанию, не удаляем товар из корзины
			console.info( '// По умолчанию, не удаляем товар из корзины' );
			return false;
		}

	} )();

	// WC wppw_checkout ====================================================================================
	( function () {

		$( '#wppw_checkout' ).on( 'submit', function ( e ) {

			e.preventDefault();

			var
				$this = $( this ),
				$submit_button = $( '#wppw_checkout__submit', $this ),
				$form = $this.serialize(),
				data = {
					action : 'wppw_woo_thankyou',
					form : $form,
				};

			$submit_button.text( 'Отправка, ждите...' ).attr( 'disabled', 'disabled' ).css( { background : '#B00' } );

			$.post( ajaxurl, data, function ( e ) {

				console.info( e );

				if ( e.success ) {
					$( '.b-cart-content__right__top__change' ).hide();
					$( '.b-cart-content__left' ).html( e.data.html );
					window.location.href = '#wppw_checkout_thankyou';
				}
			} );

		} );


	} )();

	// Доставка  ====================================================================================
	( function () {

		$( '.b-order-transport' ).show();
		$( '.wppw_shipping_address' ).hide();

		$( 'input[name="delivery_method"]', '#wppw_checkout' ).on( 'change', function () {

			var
				$this = $( this ),
				$delivery_method = $this.val(),
				$tk = $( '.b-order-transport' ), // Транспортная компания
				$sa = $( '.wppw_shipping_address' ); // Адрес доставки

			if ( 1 == $delivery_method ) {
				$tk.slideDown();
				$sa.slideUp();
			} else if ( 2 == $delivery_method ) {
				$tk.slideUp();
				$sa.slideDown();
			}

		} );

	} )();

	// Контекст ====================================================================================
	( function () {

		$( "#wppw_catalog_filters :input" ).on( 'change', function () {
			$( this ).closest( 'form' ).submit();
		} );
	} )();

	// SMS Auth ====================================================================================
	( function () {

		$( '.wppw_modal_sms' ).on( 'submit', function ( e ) {

			e.preventDefault();

			let
				$this = $( this ),
				$form = $this.serialize(),
				data = {
					action : 'wppw_sms',
					form : $form,
				};

			// Backend
			$.post( ajaxurl, data, e => {

				console.info( e )

				if ( e.success ) {

					$( '#modal-login' ).modal( 'hide' );
					$( '#modal-login2' ).modal( 'show' ).attr( 'data-tel', e.data.tel );
				}
			} );
		} );
	} )();

	// Auth after SMS ====================================================================================
	( function () {

		$( '.wppw_modal_auth' ).on( 'submit', function ( e ) {

			e.preventDefault();

			let
				$this = $( this ),
				tel = $this.attr( 'data-tel' ),
				$form = $this.serialize(),
				data = {
					action : 'wppw_auth',
					form : $form,
					tel : tel,
				};

			// Backend
			$.post( ajaxurl, data, e => {

				console.info( e )

				// Успешная авторизация
				if ( e.success ) {

					// Перенаправляем в ЛК
					window.location.href = window.site_url + '/lk';
				}
			} );
		} );
	} )();

	// Форма отправки отзыва ====================================================================================
	( function () {

		$( '#wppw_review_form' ).on( 'submit', function ( e ) {
			e.preventDefault();

			var
				$this = $( this ),
				$form = $this.serialize(),
				data = {
					action : 'wppw_review_form',
					form : $form,
				};

			// Backend
			$.post( ajaxurl, data, e => {

				console.info( e )

				// Успешная авторизация
				if ( e.success ) {

					// HTML
					$this.html( e.data.html );
				}
			} );
		} )
	} )();

	// Пагинатор wppw_block__products_cards__row ====================================================================================
	( function () {

		var
			$rows = $( '.wppw_block__products_cards__row' ), // Строка с продуктами
			$pagination = $( '.wppw_block__products_cards__pagination' ), // Страница пагинации
			q = 1;

		// Клик по пагинации
		$pagination.on( 'click', 'a,button', function ( e ) {

			e.preventDefault();

			var
				$this = $( this );
			row_index = $this.attr( 'data-index' );

			// Прописываем класс active новой кнопке
			$( '.active', $pagination ).removeClass( 'active' );

			// Клик по кнопке с индексом пагинации
			if ( $this.closest( 'li' ).length )
				$this.closest( 'li' ).addClass( 'active' );
			else {
				// Клик по дополнительной кнопке (не с числом индекса пагинации)
				var index_val = $this.attr( 'data-index' );
				$( 'li a', $pagination ).each( function () {
					var $this = $( this );
					if ( index_val == $this.attr( 'data-index' ) )
						$this.closest( 'li' ).addClass( 'active' );
				} );
			}

			// Проверка, что индекс пагинации есть
			if ( undefined !== row_index ) {

				// Скрываем все строки и показываем с индексом row_index
				// Все строки перебираем
				$rows.each( function ( i ) {

					var $this = $( this );

					if ( row_index == i + 1 ) {

						// Показываем нужную строку
						$this.show();

					} else {

						// Остальные скрываем
						$this.hide();
					}
				} );
			}
		} );
	} )();

	// ====================================================================================
	( function () {
		$( '.content_toggle' ).click( function () {
			$( '.content_block' ).toggleClass( 'hide' );
			if ( $( '.content_block' ).hasClass( 'hide' ) ) {
				$( '.content_toggle' ).html( 'Читать далее' );
			} else {
				$( '.content_toggle' ).html( 'Скрыть' );
			}
			return false;
		} );

		$( '.content_toggle' ).trigger( 'click' );
	} )();

	// Favorites ====================================================================================
	( function () {

		$( '.add_to_wishlist' ).on( 'click', function () {

			var
				$this = $( this ),
				$product_id = $this.attr( 'data-product-id' );

			// Сердечко красное
			$( this ).addClass( 'active' );

			// Получаем ID добавляемого в избранное продукта
			$product_id;

			// Прописываем нужное
			var data = {
				action : 'wppw_get_product_from_id',
				nonce : wppw_nonce,
				product_id : $product_id,
			}

			// Backend
			$.post( ajaxurl, data, e => {

				console.info( e )

				if ( e.success ) {

					// Вызываем модальное окно
					//$( '#modal-add_to_wishlist' ).modal( 'show' ).attr( 'data-tel', e.data.tel );
				}
			} )


		} );

	} )();

	// На триггере "Продукт добавлен в корзину" // wppw_added_to_cart ====================================================================================
	( function () {

		$( document.body ).on( 'wppw_added_to_cart', function ( e, cart_item_key ) {

			// Прописываем нужное
			var data = {
				action : 'wppw_get_product_from_cart_item_key',
				nonce : wppw_nonce,
				cart_item_key : cart_item_key,
			}

			// Backend
			$.post( ajaxurl, data, e => {

				console.info( e )

				if ( e.success ) {

					// Отрисовываем модальное окно
					$( '.b-modal-content', '#modal-goto_woo_cart' ).html( e.data.html );
					// Вызываем модальное окно
					$( '#modal-goto_woo_cart' ).modal( 'show' );
				}
			} );

		} );
	} )();

	// На триггере "Продукт добавлен в Избранное" // added_to_wishlist ====================================================================================
	( function () {

		$( document.body ).on( 'added_to_wishlist', function ( e, t, el_wrap ) {

			// Прописываем нужное
			var data = {
				action : 'wppw_add_product_to_favorites',
				nonce : wppw_nonce,
				product_id : t.attr( 'data-product-id' ),
			}

			// Backend
			$.post( ajaxurl, data, e => {

				console.info( e )

				if ( e.success ) {

					// Отрисовываем модальное окно
					$( '.b-modal-content', '#modal-add_to_wishlist' ).html( e.data.html );
					// Вызываем модальное окно
					$( '#modal-add_to_wishlist' ).modal( 'show' );
				}
			} );

		} );
	} )();

	// На триггере "Продукт удалён из Избранного" // removed_from_wishlist ====================================================================================
	( function () {

		$( document.body ).on( 'removed_from_wishlist', function ( e, el, row ) {

			// Прописываем нужное
			var data = {
				action : 'wppw_add_product_to_favorites',
				nonce : wppw_nonce,
				product_id : row.data( 'row-id' ),
			}

			// Backend
			$.post( ajaxurl, data, e => {

				console.info( e )

				if ( e.success ) {

					// Отрисовываем модальное окно
					$( '.b-modal-content', '#modal-remove_from_wishlist' ).html( e.data.html );
					// Вызываем модальное окно
					$( '#modal-remove_from_wishlist' ).modal( 'show' );
				}
			} );

		} );
	} )();

	// ====================================================================================
	( function () {

	} )();
	// ====================================================================================
	( function () {

	} )();



} )