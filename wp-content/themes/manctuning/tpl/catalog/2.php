<?php
/**
 * 2 уровень каталога /catalog/brand/
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $brand;
?>

<?php
// Родительская категория
if ( $brand = get_term_by( 'slug', $brand, 'product_cat' ) ) {
	$href = get_permalink( 143 );
	$ico = wp_get_attachment_url( get_term_meta( $brand -> term_id, 'thumbnail_id', true ) );
	?>
	<div class="b-catalog-items">
		<div class="b-catalog-item">
			<div class="b-catalog-item__img">
				<img class="lazy" data-original="<?= $ico ?>" alt="" src="<?= $ico ?>" style="display: block;">
			</div>
			<span><?= $brand -> name ?></span>
			<button class="b-catalog-item__delete" onclick="window.location.href = '<?= $href ?>'">+</button>
		</div>
	</div>


	<?php
	// Ссылки на Модели, дочерние текущей Марке       
	if ( $models = get_terms( [
		'taxonomy'	 => 'product_cat',
		'hide_empty' => false,
		'parent'	 => $brand -> term_id,
		'number'	 => 17,
			] ) ) {

		// Разбиваем массив
		$models = array_chunk( $models, 3 );
		?>
		<div class="b-catalog-list">

			<?php
			$models_count = count( $models );
			foreach ( $models as $i => $terms ) {
				$terms_count = count( $terms );
				?>
				<ul>
					<?php
					foreach ( $terms as $j => $term ) {
						?>
						<li><a href="<?= get_term_link( $term, 'product_cat' ) ?>"><?= $term -> name ?></a></li>
						<?php
					}
					?>

					<?php /*if ( $i + 1 == $models_count and $j + 1 == $terms_count ) { ?>
						<li><a href="<?= $href ?>">Все марки</a></li>
					<?php }*/ ?>
				</ul>
			<?php } ?>

		</div>
	<?php } ?>
<?php } ?>