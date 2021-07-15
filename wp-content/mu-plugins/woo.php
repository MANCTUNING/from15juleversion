<?php
/**
 * Woo
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Настройка WooCommerce ==========================================================================================
// Отключение галереи 
add_action( 'add_meta_boxes', function () {
	remove_meta_box( 'woocommerce-product-images', 'product', 'side' );
}, 40 );

add_action( 'wp', function () {
	remove_theme_support( 'wc-product-gallery-zoom' );
	remove_theme_support( 'wc-product-gallery-lightbox' );
	remove_theme_support( 'wc-product-gallery-slider' );
}, 99 );

// Удаление текстового редактора и выдержки поста ================================================================
add_action( 'init', function () {
	remove_post_type_support( 'product', 'excerpt' );
	remove_post_type_support( 'product', 'editor' );
} );

// Транзитный кеш для таксономий Woo: Моделей и Поколений ================================================================
add_action( 'init', function() {

	$key = 'wppw_wc_tax_models_breeds';

	#delete_transient( $key );
	// Данные получить не удалось, поэтому, создадим их и сохраним
	if ( false === $special_query_results = get_transient( $key ) ) {

		$special_query_results = [];

		if ( $brands = get_terms( [
			'taxonomy'	 => 'product_cat',
			'parent'	 => 0,
			'hide_empty' => false,
				] ) ) {

			// Перебор Марок
			foreach ( $brands as $brand ) {

				// Собираем данные по Марке
				$r[$brand -> slug] = [
					'text'	 => $brand -> name,
					'id'	 => $brand -> term_id,
					'count'	 => $brand -> count,
				];

				// Если Модели есть
				if ( $models = get_terms( [
					'taxonomy'	 => 'product_cat',
					'parent'	 => $brand -> term_id,
					'hide_empty' => false,
						] ) ) {

					// Перебор моделей
					foreach ( $models as $model ) {

						$r[$brand -> slug]['children'][$model -> slug] = [
							'text'	 => $model -> name,
							'id'	 => $model -> term_id,
							'count'	 => $model -> count,
						];

						// Если Поколения есть
						if ( $breeds = get_terms( [
							'taxonomy'	 => 'product_cat',
							'parent'	 => $model -> term_id,
							'hide_empty' => false,
								] ) ) {

							// Перебор Поколений
							foreach ( $breeds as $breed ) {

								// Пишем данные в массив
								$r[$brand -> slug]['children'][$model -> slug]['children'][$breed -> slug] = [
									'text'	 => $breed -> name,
									'id'	 => $breed -> term_id,
									'count'	 => $breed -> count,
								];
							}
						}
					}
				}
			}
		}
		#exit( print_r( $r ) );
		$r = json_encode( $r ); // Кодируем в JSON
		set_transient( $key, $r, MINUTE_IN_SECONDS ); // Сохраняем всё в транзитный кеш на минуту
	}
} );

// AJAX верхний поисковый бар ================================================================
add_action( 'wp_ajax_wppw_top_search_bar', 'wppw_top_search_bar' );
add_action( 'wp_ajax_nopriv_wppw_top_search_bar', 'wppw_top_search_bar' );

function wppw_top_search_bar() {

	// Очищаем данные $_POST
	$r = array_map( function( $e ) {
		return sanitize_text_field( $e );
	}, $_POST );

	// Верификация
	wp_verify_nonce( $r['nonce'], WPPW_NONCE ) or exit;

	// Команда отправлена с 
	$context = $r['context'];

	// Метки/Тип товара // Контекст запроса должен быть одним из следующих
	if ( !empty( $r['wppw_breed'] ) and in_array( $context, [ 'type', ] ) ) {

		// Ключ транзитного кеша // wppw_top_search_bar_brand_bmw
		$key = 'wppw_top_search_bar_' . $context . '_' . $r['tax'];

		delete_transient( get_transient( $key ) );
		if ( false === $models = get_transient( $key ) ) {

			// Продукция в этих терминах
			$product_ids = get_posts( [
				'posts_per_page' => -1,
				"post_type"		 => 'product',
				'product_cat'	 => $r['wppw_breed'],
				'product_tag'	 => $r['tax'],
				'fields'		 => 'ids',
					] );

			// Число продуктов в термине
			$models['products_count'] = count( $product_ids );

			// Из Продукции получаем все подходящие ей Типы 
			$models['product_tags'] = wp_get_object_terms( $product_ids, [ 'product_tag' ] );

			if ( $models )
				set_transient( $key, $models, MINUTE_IN_SECONDS ); // Сохраняем всё в транзитный кеш на минуту
		}
	}
	// Категории товара // Контекст запроса должен быть одним из следующих
	elseif ( in_array( $context, [ 'brand', 'model', 'breed', ] ) ) {

		// Ключ транзитного кеша // wppw_top_search_bar_brand_bmw
		$key = 'wppw_top_search_bar_' . $context . '_' . $r['tax'];

		if ( false === $models = get_transient( $key ) ) {

			// Получаем term_id по slug
			$brand = get_term_by( 'slug', $r['tax'], 'product_cat' );

			// Число продуктов в термине
			$models['products_count'] = $brand -> count;

			// Дочерние термины
			$models['children_terms'] = get_terms( [
				'taxonomy'	 => 'product_cat',
				'parent'	 => $brand -> term_id,
				'hide_empty' => false,
					] );

			// Продукция в этих терминах
			$product_ids = get_posts( [
				'posts_per_page' => -1,
				"post_type"		 => 'product',
				'product_cat'	 => $r['tax'],
				'fields'		 => 'ids',
					] );

			// Из Продукции получаем все подходящие ей Типы 
			$models['product_tags'] = wp_get_object_terms( $product_ids, [ 'product_tag' ] );

			if ( $models )
				set_transient( $key, $models, MINUTE_IN_SECONDS ); // Сохраняем всё в транзитный кеш на минуту
		}
	}
	else {
		wp_send_json_error( [ 'msg' => 'context неверный' ] );
	}

	if ( $models )
		wp_send_json_success( $models );

	exit;
}

// AJAX в корзине: добавить/изменить/удалить ================================================================
add_action( 'wp_ajax_wppw_cart', 'wppw_cart' );
add_action( 'wp_ajax_nopriv_wppw_cart', 'wppw_cart' );

function wppw_cart() {

	// Очищаем данные $_POST
	$r = array_map( function( $e ) {
		return sanitize_text_field( $e );
	}, $_POST );

	// Верификация
	#wp_verify_nonce( $r['nonce'], WPPW_NONCE ) or wp_send_json_error( 'nonce not verified' );
	// Получаем команду
	switch ( $r['command'] ) {

		case 'add': // Добавляем товар в корзину

			if ( $result = WC() -> cart -> add_to_cart( $r['id'] ) ) {
				wp_send_json_success( [
					'msg'			 => 'Добавлено',
					'cart_item_key'	 => $result,
				] );
			}
			else {
				wp_send_json_error( [
					'msg'	 => 'Не добавлено',
					'result' => $result,
				] );
			}

			break;

		case 'delete': // Удаляем товар из корзины

			$result = WC() -> cart -> remove_cart_item( $r['id'] );

			wp_send_json_success( [
				'msg'	 => 'Удалено',
				'result' => $result,
			] );

			break;

		case 'increase': // Увеличиваем число товара в корзине

			$cart_item_key = $r['id'];
			$quantity = $r['quantity'];

			$result = WC() -> cart -> set_quantity( $cart_item_key, $quantity );

			wp_send_json_success( [
				'msg'		 => 'Добавлено',
				'result'	 => $result,
				'quantity'	 => $quantity, // Увеличиваем число товаров в корзине
			] );

			break;

		case 'decrease': // Удаляем товар из корзины

			$cart_item_key = $r['id'];
			$quantity = $r['quantity'];

			$result = WC() -> cart -> set_quantity( $cart_item_key, $quantity );

			wp_send_json_success( [
				'msg'		 => 'Убавлено',
				'result'	 => $result,
				'quantity'	 => $quantity, // Уменьшаем число товаров в корзине
			] );

			break;

		default:
			break;
	}

	exit;
}

// AJAX Спасибо за заказ ================================================================
add_action( 'wp_ajax_wppw_woo_thankyou', 'wppw_woo_thankyou' );
add_action( 'wp_ajax_nopriv_wppw_woo_thankyou', 'wppw_woo_thankyou' );

function wppw_woo_thankyou() {

	$form = wp_parse_args( $_POST['form'] );

	$r = wp_parse_args( $form );

	// Очищаем данные $_POST
	$r = array_map( function( $e ) {
		return sanitize_text_field( $e );
	}, $r );

	// Верификация
	wp_verify_nonce( $r['nonce'], WPPW_NONCE ) or exit;

	// Создаём новый заказ
	$order_id = wppw_wc_create_order( $r );

	// Обрабатываем
	require TEMPLATEPATH . '/tpl/ajax/thankyou.php';

	wp_send_json_success( [
		'html'	 => $html,
		'msg'	 => 'Спасибо',
	] );

	exit;
}

// Хелпер при создании нового заказа
function wppw_wc_create_order( $r ) {

	// Получаем хеш текущей корзины
	#$cart_hash = WC() -> cart -> get_cart_hash() or wp_send_json_error( [ 'msg' => "Корзина пуста" ] );

	$order_id = WC() -> checkout() -> create_order( [] );

	$order = wc_get_order( $order_id );

	// Платёж прошёл
	#$order -> payment_complete();
	#$order -> update_status( 'on-hold' );
	// Очищаем корзину
	#WC() -> cart -> empty_cart();
	// Если пользователь авторизован
	if ( 1 != 1 ) {
		/* global $current_user;


		  $fname = get_user_meta( $current_user -> ID, 'first_name', true );
		  $lname = get_user_meta( $current_user -> ID, 'last_name', true );
		  $email = $current_user -> user_email;
		  $address_1 = get_user_meta( $current_user -> ID, 'billing_address_1', true );
		  $address_2 = get_user_meta( $current_user -> ID, 'billing_address_2', true );
		  $city = get_user_meta( $current_user -> ID, 'billing_city', true );
		  $postcode = get_user_meta( $current_user -> ID, 'billing_postcode', true );
		  $country = get_user_meta( $current_user -> ID, 'billing_country', true );
		  $state = get_user_meta( $current_user -> ID, 'billing_state', true );

		  $billing_address = array(
		  'first_name' => $fname,
		  'last_name'	 => $lname,
		  'email'		 => $email,
		  'address_1'	 => $address_1,
		  'address_2'	 => $address_2,
		  'city'		 => $city,
		  'state'		 => $state,
		  'postcode'	 => $postcode,
		  'country'	 => $country,
		  );
		  $address = array(
		  'first_name' => $fname,
		  'last_name'	 => $lname,
		  'email'		 => $email,
		  'address_1'	 => $address_1,
		  'address_2'	 => $address_2,
		  'city'		 => $city,
		  'state'		 => $state,
		  'postcode'	 => $postcode,
		  'country'	 => $country,
		  ); */
	}
	else { // Неавторизованный
		$r['address'] = $r['address_city'] . $r['address_flat'] . $r['address_floor'] . $r['address_entrance'] . $r['address_intercom'];
		$address = array(
			'first_name' => $r['first_name'],
			'last_name'	 => $r['last_name'],
			'email'		 => $r['email'],
			'address_1'	 => $r['address'],
			'phone'		 => $r['tel'],
				#'phone'		 => $r['tel2'],
				#'city'		 => $r['city'],
				#'state'		 => $r['state'],
				#'postcode'	 => $r['postcode'],
				#'country'	 => $r['country'],
		);
	}

	//TODO // Метод оплаты
	$payment_method = $r['payment_method'];
	$delivery_method = $r['delivery_method'];

	// Адрес оплаты
	$order -> set_address( $address, 'billing' );

	// Адрес доставки
	$order -> set_address( $address, 'shipping' );

	// Заметка от клиента
	$order -> set_customer_note( $r['address_comment'] );

	#$order -> set_payment_method( 'check' ); //
	#$order -> shipping_method_title = $shipping_method;
	#$order -> calculate_totals();
	#$order -> update_status( 'on-hold' );
	#$order -> set_cart_hash( $cart_hash );

	$order -> save();

	return $order_id;
}

// YITH Wishlist================================================================
if ( defined( 'YITH_WCWL' ) ) {
	if ( !function_exists( 'yith_wcwl_set_visited_wishlist_cookie' ) ) {

		function yith_wcwl_set_visited_wishlist_cookie() {
			$action_params = get_query_var( 'wishlist-action', false );
			$action_params = explode( '/', apply_filters( 'yith_wcwl_current_wishlist_view_params', $action_params ) );
			$action = ( isset( $action_params[0] ) ) ? $action_params[0] : 'view';
			$available_views = apply_filters( 'yith_wcwl_available_wishlist_views', array( 'view', 'user' ) );

			if (
					empty( $action ) ||
					(!empty( $action ) && ( $action == 'view' || $action == 'user' ) ) ||
					(!empty( $action ) && ( $action == 'manage' || $action == 'create' ) && get_option( 'yith_wcwl_multi_wishlist_enable', false ) != 'yes' ) ||
					(!empty( $action ) && !in_array( $action, $available_views ) ) ||
					!empty( $user_id )
			) {
				if ( !empty( $action ) && $action == 'user' ) {
					$user_id = isset( $action_params[1] ) ? $action_params[1] : false;
					$user_id = (!$user_id ) ? get_query_var( $user_id, false ) : $user_id;
					$user_id = (!$user_id ) ? get_current_user_id() : $user_id;

					$wishlists = YITH_WCWL() -> get_wishlists( array( 'user_id' => $user_id, 'is_default' => 1 ) );

					if ( !empty( $wishlists ) && isset( $wishlists[0] ) ) {
						$wishlist_id = $wishlists[0]['wishlist_token'];
					}
					else {
						$wishlist_id = false;
					}
				}
				else {
					$wishlist_id = isset( $action_params[1] ) ? $action_params[1] : false;
					$wishlist_id = (!$wishlist_id ) ? get_query_var( 'wishlist_id', false ) : $wishlist_id;
				}

				$is_user_owner = false;

				if ( !empty( $user_id ) ) {
					if ( get_current_user_id() == $user_id ) {
						$is_user_owner = true;
					}
				}
				elseif ( !is_user_logged_in() ) {
					if ( empty( $wishlist_id ) ) {
						$is_user_owner = true;
					}
					else {
						$is_user_owner = false;
					}
				}
				else {
					if ( empty( $wishlist_id ) ) {
						$is_user_owner = true;
					}
					else {
						$wishlist = YITH_WCWL() -> get_wishlist_detail_by_token( $wishlist_id );
						$is_user_owner = $wishlist['user_id'] == get_current_user_id();
					}
				}

				if ( !$is_user_owner ) {
					wc_setcookie( 'yith_wcwl_visited_wishlist', $wishlist_id, time() + apply_filters( 'yith_wcwl_cookie_expiration', 60 * 60 * 24 * 30 ), false );
				}
			}
		}

		#add_action( 'template_redirect', 'yith_wcwl_set_visited_wishlist_cookie', 10 );
	}
}
// AJAX: Модуль получения только что добавленного продукта из корзины ================================================================

add_action( 'wp_ajax_wppw_get_product_from_cart_item_key', 'wppw_get_product_from_cart_item_key' );
add_action( 'wp_ajax_nopriv_wppw_get_product_from_cart_item_key', 'wppw_get_product_from_cart_item_key' );

function wppw_get_product_from_cart_item_key() {

	// Очищаем данные $_POST
	$r = array_map( function( $e ) {
		return sanitize_text_field( $e );
	}, $_POST );
	?>

	<?php
	// Ключ для идентификации выбранного продукта в корзине и последующего возможного удаления из корзины
	$cart_item_key = $r['cart_item_key'];

	// Продукт
	$product = WC() -> cart -> get_cart_item( $cart_item_key );

	$product_id = $product['product_id'];

	// Количество
	$quantity = $product['quantity'];

	// Стоимость со скидкой
	//$price['current'] = $product['line_total'];
	$price['current'] = $product['data'] -> get_sale_price() * $quantity;
	// Цена без учёта скидки, базовая
	$price['regular'] = $product['data'] -> get_regular_price() * $quantity;

	// Собираем общую цену без скидок из обычных регулярных цен на товар
	$cart_total_current_price += $price['current'];
	?>

	<div class="b-cart-content">

		<div class="b-cart-item">
			<div class="b-cart-item__block">
				<div class="b-cart-item__block__img">
					<a href="<?= get_permalink( $product_id ) ?>"><img class="lazy" src="<?= get_the_post_thumbnail_url( $product_id ) ?>" data-original="<?= get_the_post_thumbnail_url( $product_id ) ?>" alt=""></a>
				</div>
				<div class="b-cart-item__block__text">
					<span><?= $product['data'] -> get_sku() ?></span>
					<b><a href="<?= get_permalink( $product_id ) ?>"><?= $product['data'] -> get_title() ?></a></b>
				</div>
			</div>
			<div class="b-cart-item__right">
				<div class="b-cart-item__price">
					<span class="b-cart-item__price__new"><?= number_format( $price['current'], 0, ',', ' ' ) ?> руб.</span>
					<span class="b-cart-item__price__old">- <?= number_format( $price['regular'], 0, ',', ' ' ) ?> руб.</span>
				</div>
				<div class="b-quant">
					<button class="b-quant-btn minus wppw_decrease_from_cart" data-command="decrease" data-id="<?= esc_attr( $cart_item_key ) ?>" data-nonce="<?= wp_create_nonce( WPPW_NONCE ) ?>">-</button>
					<input class="b-quant-input wppw_product_quantity" type="text" value="<?= $quantity ?>">
					<button class="b-quant-btn plus wppw_increase_to_cart" data-command="increase" data-id="<?= esc_attr( $cart_item_key ) ?>" data-nonce="<?= wp_create_nonce( WPPW_NONCE ) ?>">+</button>
				</div>
				<?php /* button class="btn-delete icon-delete wppw_delete_from_cart" data-nonce="<?= wp_create_nonce( WPPW_NONCE ) ?>" data-command="delete" data-is_window_reload="1" data-id="<?= esc_attr( $cart_item_key ) ?>"></button */ ?>
			</div>
		</div>

	</div>


	<?php
	$html = ob_get_clean();

	wp_send_json_success( [
		'msg'	 => 'Успех',
		'html'	 => $html,
	] );

	exit;
}

// AJAX View: Товар добавлен в избранное  ================================================================
add_action( 'wp_ajax_wppw_add_product_to_favorites', 'wppw_add_product_to_favorites' );
add_action( 'wp_ajax_nopriv_wppw_add_product_to_favorites', 'wppw_add_product_to_favorites' );

function wppw_add_product_to_favorites() {

	// Очищаем данные $_POST
	$r = array_map( function( $e ) {
		return sanitize_text_field( $e );
	}, $_POST );
	?>

	<?php
	// product_id
	$product_id = $r['product_id'];

	// Продукт
	$product = wc_get_product( $product_id );

	// Стоимость со скидкой
	//$price['current'] = $product['line_total'];
	$price['current'] = $product -> get_sale_price();
	// Цена без учёта скидки, базовая
	$price['regular'] = $product -> get_regular_price();
	?>

	<div class="b-cart-content">

		<div class="b-cart-item">
			<div class="b-cart-item__block">
				<div class="b-cart-item__block__img">
					<a href="<?= get_permalink( $product_id ) ?>"><img class="lazy" src="<?= get_the_post_thumbnail_url( $product_id ) ?>" data-original="<?= get_the_post_thumbnail_url( $product_id ) ?>" alt=""></a>
				</div>
				<div class="b-cart-item__block__text">
					<span><?= $product -> get_sku() ?></span>
					<b><a href="<?= get_permalink( $product_id ) ?>"><?= $product -> get_title() ?></a></b>
				</div>
			</div>
			<div class="b-cart-item__right">
				<div class="b-cart-item__price">
					<span class="b-cart-item__price__new"><?= number_format( $price['current'], 0, ',', ' ' ) ?> руб.</span>
					<span class="b-cart-item__price__old">- <?= number_format( $price['regular'], 0, ',', ' ' ) ?> руб.</span>
				</div>
			</div>
		</div>

	</div>


	<?php
	$html = ob_get_clean();

	wp_send_json_success( [
		'msg'	 => 'Успех',
		'html'	 => $html,
	] );

	exit;
}

// ================================================================

// ================================================================




