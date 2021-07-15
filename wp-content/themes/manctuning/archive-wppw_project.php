<?php
/**
 * Template name: Archive Projects
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

// Тег заголовка страницы
$title_tag = get_field( 'title' )['tag'];
$title_text = get_field( 'title' )['text'] ?: get_the_title();
$title = sprintf( '<%s>%s</%s>', $title_tag, $title_text, $title_tag );

// Пагинация
$paged = sanitize_text_field( $_GET['product_page'] ) ?: 1;

// Число элементов на страницу 
$posts_per_page = 6;

$args = [
	'post_type'		 => 'wppw_project',
	'posts_per_page' => $posts_per_page,
	'paged'			 => $paged, // Текущая страница пагинации
];

// Получаем порцию нужных данных
$p = get_posts( $args );

// Общее число продукции
$args['posts_per_page'] = -1; // Собираем все данные
$products_count = count( get_posts( $args ) );

// Число страниц пагинации
$paged_count = ceil( $products_count / $posts_per_page );
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

		<?php
		if ( $p ) {
			?>
			<div class="b-projects-page">
				<div class="row">
					<?= get_template_part( 'tpl/level/service', 'preview' ) ?>
				</div>

				<?php get_template_part( 'tpl/helper/pagination' ) ?>

				<?php /* div class="row">
				  <div class="col-xl">
				  <div class="b-catalog-bottom">
				  <button class="b-catalog-show">Показать еще</button>
				  <div class="b-pagination">
				  <a href="" class="b-pagination-arrow b-pagination-prev"></a>
				  <ul>
				  <li class="active"><a href="">1</a></li>
				  <li><a href="">2</a></li>
				  <li><a href="">3</a></li>
				  <li><a href="">4</a></li>
				  <li><a href="">...</a></li>
				  <li><a href="">33</a></li>
				  </ul>
				  <a href="" class="b-pagination-arrow b-pagination-next"></a>
				  </div>
				  </div>
				  </div>
				  </div */ ?>				  				
			</div>	
		<?php } ?>		
	</div>
</div>

<?= get_footer() ?>