<?php
/**
 * Корень каталога /catalog/
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
?>

<?php
// Список иконок брендов   
if ( $brands = get_field( 'ico' ) ) {
	?>
	<div class="b-catalog-brands">
		<ul>
			<?php
			foreach ( $brands as $term ) {
				$href = get_term_link( $term, 'product_cat' );
				$ico = wp_get_attachment_url( get_term_meta( $term -> term_id, 'thumbnail_id', true ) );
				?>
				<li><a href="<?= $href ?>"><img class="lazy" data-original="<?= $ico ?>" alt=""></a></li>
			<?php } ?>
		</ul>
	</div>
<?php } ?>

<?php // Ссылки на бренды           ?>
<?php
if ( $brands = get_field( 'brands' ) ) {
	?>
	<div class="b-catalog-list">

		<?php
		$brands_count = count( $brands );
		foreach ( $brands as $i => $brand ) {
			$terms = $brand['brand'];
			$terms_count = count( $terms );
			?>
			<ul>
				<?php
				foreach ( $terms as $j => $term ) {

					/* if ( $i + 1 == $brands_count and $j + 1 == $terms_count ) {
					  ?>
					  <li><a href="<?= site_url( '/catalog' ) ?>">Все марки</a></li>
					  <?php
					  }
					  else {
					  ?>
					  <li><a href="<?= str_replace( 'product-category', 'catalog', get_term_link( $term, 'product_cat' ) ) ?>"><?= $term -> name ?></a></li>
					  <?php
					  }
					 */
					?>
					<li><a href="<?= str_replace( 'product-category', 'catalog', get_term_link( $term, 'product_cat' ) ) ?>"><?= $term -> name ?></a></li>
					<?php
				}
				?>
			</ul>
		<?php } ?>

	</div>
<?php } ?>