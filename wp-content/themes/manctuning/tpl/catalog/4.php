<?php
/**
 * 4 уровень каталога /catalog/brand/model/breed/
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $brand, $model, $breed;
?>

<?php
// Родительская категория
if ( $brand = get_term_by( 'slug', $brand, 'product_cat' ) and $model = get_term_by( 'slug', $model, 'product_cat' ) and $breed = get_term_by( 'slug', $breed, 'product_cat' ) ) {
	$href = get_term_link( $brand, 'product_cat' );
	$ico = wp_get_attachment_url( get_term_meta( $brand -> term_id, 'thumbnail_id', true ) );
	?>
	<div class="b-catalog-items">
		<div class="b-catalog-item">
			<div class="b-catalog-item__img">
				<img class="lazy" data-original="<?= $ico ?>" alt="" src="<?= $ico ?>" style="display: block;">
			</div>
			<span><?= $brand -> name ?></span>
			<button class="b-catalog-item__delete" onclick="window.location.href = '<?= get_permalink( 143 ) ?>'">+</button>
		</div>

		<?php
		$href = get_term_link( $brand, 'product_cat' );
		?>
		<div class="b-catalog-item">
			<div class="b-catalog-item__img">
				<img class="lazy" data-original="<?= $ico ?>" alt="" src="<?= $ico ?>" style="display: block;">
			</div>
			<span><?= $model -> name ?></span>
			<button class="b-catalog-item__delete" onclick="window.location.href = '<?= $href ?>'">+</button>
		</div>

		<?php
		$href = get_term_link( $model, 'product_cat' );
		?>
		<div class="b-catalog-item">
			<div class="b-catalog-item__img">
				<img class="lazy" data-original="<?= $ico ?>" alt="" src="<?= $ico ?>" style="display: block;">
			</div>
			<span><?= $breed -> name ?></span>
			<button class="b-catalog-item__delete" onclick="window.location.href = '<?= $href ?>'">+</button>
		</div>
	</div>


	<?php
	// Ссылки на Модели, дочерние текущей Марке       
	/*if ( $breeds = get_terms( [
		'taxonomy'	 => 'product_cat',
		'hide_empty' => false,
		'parent'	 => $model -> term_id,
		'number'	 => 17,
			] ) ) {

		// Разбиваем массив
		$breeds = array_chunk( $breeds, 4 );
		?>
		<div class="b-catalog-list">

			<?php
			$breeds_count = count( $breeds );
			foreach ( $breeds as $i => $terms ) {
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

					<?php if ( $i + 1 == $breeds_count and $j + 1 == $terms_count ) { ?>
						<li><a href="<?= $href ?>">Все модели</a></li>
					<?php } ?>
				</ul>
			<?php } ?>

		</div>
	<?php }*/ ?>
<?php } ?>