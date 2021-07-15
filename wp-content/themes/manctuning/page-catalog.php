<?php
/**
 * Template name: Catalog
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Редирект:  manctuning.ru/catalog?brand=bmw&model=x5&breed=g05&type=diffuzory -> manctuning.ru/catalog/bmw/x5/g05/diffuzory
$r = [];
foreach ( [ 'brand', 'model', 'breed', 'type' ] as $e ) {
	if ( $$e = sanitize_text_field( $_GET[$e] ) ) {

		$r[] = $$e;
	}
}
if ( !empty( $r[0] ) ) {
	$goto = trailingslashit( get_permalink( $GLOBALS['post'] ) ) . implode( '/', $r );
	wp_safe_redirect( $goto, $status = 301 );
	exit;
}


// Тег заголовка страницы
#$title_tag = get_field( 'title' )['tag'];
#$title_text = get_field( 'title' )['text'] ?: get_the_title();
#$title = sprintf( '<%s>%s</%s>', $title_tag, $title_text, $title_tag );

#exit( print_r( get_fields() ) );
// Пагинация
$paged = sanitize_text_field( $_GET['product_page'] ) ?: 1;

// Записей на страницу
$posts_per_page = 12;

// Фильтр по цене
$price_min = ( int ) sanitize_text_field( $_GET['price_min'] ) ?: 1;
$price_max = ( int ) sanitize_text_field( $_GET['price_max'] ) ?: 999999999;

// Сортировка по цене  // По умолчанию, сортируем по возрастанию цены
$orderby_price = sanitize_text_field( $_GET['price_orderby'] ) ?: 'asc';

// Сортировка по производителю
$orderby_manufacturer = sanitize_text_field( $_GET['manufacturer'] );

// Сортировка по расположению детали
$orderby_location = sanitize_text_field( $_GET['location'] );

// Массив аргументов для запроса Товаров
$args = [
	'post_type'		 => 'product',
	'posts_per_page' => $posts_per_page,
	'paged'			 => $paged, // Текущая страница пагинации
	'meta_query'	 => [],
];

// Сортировка по цене
if ( in_array( $orderby_price, [ 'asc', 'desc' ] ) ) {
	$args['orderby'] = 'meta_value_num'; // $orderby_price;
	$args['meta_key'] = '_price'; // $orderby_price;
	$args['order'] = $orderby_price; // asc or desc;
}

// Фильтр по цене ===============
// Минмимальная граница цен ОТ и ДО
if ( !empty( $price_min ) && is_int( $price_min ) && $price_min > 0 and
		!empty( $price_max ) && is_int( $price_max ) && $price_max > $price_min ) {
	$args['meta_query'][] = [
		'key'		 => '_price',
		'value'		 => array( $price_min, $price_max ),
		'compare'	 => 'BETWEEN',
		'type'		 => 'NUMERIC',
	];
}

// Фильтры по Категориям Woo
$args['tax_query'] = [
	'relation' => 'AND',
];
foreach ( [ 'brand', 'model', 'breed' ] as $e ) {
	if ( $$e = get_query_var( $e ) ) {
		$args['tax_query'][] = [
			'taxonomy'	 => 'product_cat',
			'field'		 => 'slug',
			'terms'		 => $$e,
		];
	}
}

// Тип продукции — Метки
if ( $type = get_query_var( 'type' ) ) {
	$args['tax_query'][] = [
		'taxonomy'	 => 'product_tag',
		'field'		 => 'slug',
		'terms'		 => [ $type ],
	];
}

// Производитель, расположение
foreach ( [ 'manufacturer', 'location' ] as $e ) {

	if ( $$e = sanitize_text_field( $_GET[$e] ) ) {
		$args['tax_query'][] = [
			'taxonomy'	 => 'wppw_wc_' . $e,
			'field'		 => 'slug',
			'terms'		 => [ $$e ],
		];
	}
} unset( $e );

// Получаем список товаров на странице
#exit( print_r( $args ) );
$p = get_posts( $args );

// Общее число товаров
$args['posts_per_page'] = -1;
$products_count = count( get_posts( $args ) );

// Число страниц пагинации
$paged_count = ceil( $products_count / $posts_per_page );


// Если мы находимся на странице Категории или Метки Woo
foreach ( [ 'brand', 'model', 'breed', 'type' ] as $e ) {
	if ( !empty( get_query_var( $e ) ) )
		$last_cat = get_query_var( $e );
}
// Корневой каталог
if ( empty( $last_cat ) ) {

	$title = get_field( 'title' );
	$title_tag = $title['tag'] ?: 'h3';
	$title_text = $title['text'] ?: get_the_title();

	$wysiwyg = get_field( 'wysiwyg' );
}
// Страница Метки Woo
elseif ( !empty( $type ) ) {
	$term = get_term_by( 'slug', $last_cat, 'product_tag' );

	// Тег заголовка страницы
	$title = get_field( 'title', 'product_tag_' . $term -> term_id );
	$title_tag = $title['tag'] ?: 'h1';
	$title_text = $title['text'] ?: $term -> name;

	// Текстовое описание под каталогом
	$wysiwyg = category_description( $term -> term_id );
}
// Страница Категории Woo
else {
	$term = get_term_by( 'slug', $last_cat, 'product_cat' );

	// Тег заголовка страницы
	$title = get_field( 'title', 'product_cat_' . $term -> term_id );
	$title_tag = $title['tag'] ?: 'h3';
	$title_text = $title['text'] ?: $term -> name;

	// Текстовое описание под каталогом
	$wysiwyg = category_description( $term -> term_id );
}
// Итоговый заголовок с тегом и текстом
$title = sprintf( '<%1$s>%2$s</%1$s>', $title_tag, $title_text );
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
				<div class="b-select-button js-filters-button visible">
					<span>Фильтр</span>
					<span class="b-select-button__arrow"><b></b></span>
				</div>
				<div class="b-catalog-filters-area js-filter-area">
					<span class="b-catalog-filters-area__close"></span>

					<?php
					// Если 3 уровень вложенности
					if (
							$brand = get_query_var( 'brand' )
							and
							$model = get_query_var( 'model' )
							and
							$breed = get_query_var( 'breed' )
					) {
						?>
						<?= get_template_part( 'tpl/catalog/4' ) ?>
						<?php
					}
					// Если 2 уровень вложенности
					elseif (
							$brand = get_query_var( 'brand' )
							and
							$model = get_query_var( 'model' )
							and
							!$breed = get_query_var( 'breed' )
					) {
						?>
						<?= get_template_part( 'tpl/catalog/3' ) ?>
						<?php
					}
					// Если 1 уровень вложенности
					elseif (
							$brand = get_query_var( 'brand' )
							and
							!$model = get_query_var( 'model' )
							and
							!$breed = get_query_var( 'breed' )
					) {
						?>
						<?= get_template_part( 'tpl/catalog/2' ) ?>
						<?php
					}
					elseif (
							!$brand = get_query_var( 'brand' )
							and
							!$model = get_query_var( 'model' )
							and
							!$breed = get_query_var( 'breed' )
					) {
						?>
						<?= get_template_part( 'tpl/catalog/1' ) ?>
					<?php } ?>

					<form class="b-catalog-filters" id="wppw_catalog_filters">
						<?php /*div class="b-catalog-price">
							<div class="b-catalog-price__item">
								<span>Цена от</span>
								<input type="text" name="price_min" value="<?= $price_min ?>">
							</div>
							<div class="b-catalog-price__item">
								<span>Цена до</span>
								<input type="text" name="price_max" value="<?= $price_max ?>">
							</div> 							
						</div*/ ?>
						<div class="b-catalog-price__select">
							<select name="price_orderby" class="b-form-select">
								<option value="">Сортировка по цене</option>
								<option value="asc" <?php if ( 'asc' == $orderby_price ) echo ' selected'; ?>>Сначала дешевле</option>
								<option value="desc" <?php if ( 'desc' == $orderby_price ) echo ' selected'; ?>>Сначала дороже</option>
							</select> 
						</div>

						<?php
						// Сортировка по производителю
						if ( $terms = get_terms( [
							'taxonomy'	 => 'wppw_wc_manufacturer',
							'hide_empty' => false,
								] ) ) {
							?>
							<div class="b-catalog-price__select">
								<select name="manufacturer" class="b-form-select">
									<option value="">Производитель</option>
									<?php foreach ( $terms as $term ) { ?>
										<option value="<?= $term -> slug ?>" <?php if ( $term -> slug == $orderby_manufacturer ) echo ' selected'; ?>><?= $term -> name ?></option>
									<?php } ?>
								</select>
							</div>
						<?php } ?>

						<?php
						// Сортировка по расположению
						if ( $terms = get_terms( [
							'taxonomy'	 => 'wppw_wc_location',
							'hide_empty' => false,
								] ) ) {
							?>
							<div class="b-catalog-price__select">
								<select name="location" class="b-form-select">
									<option value="">Расположение</option>
									<?php foreach ( $terms as $term ) { ?>
										<option value="<?= $term -> slug ?>" <?php if ( $term -> slug == $orderby_location ) echo ' selected'; ?>><?= $term -> name ?></option>
									<?php } ?>
								</select>
							</div>	
						<?php } ?>
						<?php /* button class="b-header-booking red-btn" style="min-height: 50px" type="submit">Искать</button */ ?>

						<?php
						// Перебираем возможные параметры $_GET
						foreach ( [ 'brand', 'model', 'breed', 'type' ] as $e ) {

							// Если параметр определён
							if ( $ee = get_query_var( $e ) ) {

								// Прописываем его в форму
								?><input type="hidden" name="<?= $e ?>" value="<?= $ee ?>"><?php
							}
						}
						?>

					</form>
				</div>
				<div class="b-catalog-filters-overlay js-filter-overlay" id="results"></div>
				<div class="b-catalog-search__result">
					<?php /* p><span>Новых</span> товаров для дооснащения автомобиля найдено: <b><?= $products_count ?></b></p */ ?>
					<p><?= $products_count ?> товара <span>новых</span> товаров для дооснащения автомобиля</p>
				</div>
			</div>
		</div>

		<?php
		if ( $p ) { // Товары 
			?>
			<div class="b-items-row row" id="wppw_catalog_products">
				<?php require( TEMPLATEPATH . '/tpl/ajax/catalog.php' ) ?>
			</div>
			<?= get_template_part( 'tpl/modal/goto_woo_cart' ) ?>
		<?php } ?>

		<?php get_template_part( 'tpl/helper/pagination' ) ?>
	</div>
</div>

<?php
// Текстовое описание под категорией
if ( !empty( $wysiwyg ) ) {
	?>
	<section class="section">
		<div class="container">
			<div class="row">
				<div class="col-xl">
					<?= $wysiwyg ?>
				</div>
			</div>
		</div>
	</section>
<?php } ?>

<?= get_template_part( 'tpl/modal/card' ) ?>
<?= get_footer() ?>