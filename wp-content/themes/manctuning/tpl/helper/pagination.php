<?php
/**
 * Pagination
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $paged, $paged_count;

if ( $paged_count > 1 ) { // Если пагинацию есть смысл показывать 
	if ( $qs = $_SERVER['QUERY_STRING'] ) {
		// Удаляем из QUERY_STRING product_page
		wp_parse_str( $qs, $r );
		unset( $r['product_page'] );
		if ( $qs = http_build_query( $r ) )
			$qs .= '&';
	}
	?>
	<div class="row">
		<div class="col-xl">
			<div class="b-catalog-bottom">
				<?php
				// Если впереди есть ещё пагинация
				if ( $paged < $paged_count ) {
					?>
					<button class="b-catalog-show" onclick="window.location.href = '?<?= $qs ?>product_page=<?= $paged + 1 ?>'">Показать еще</button>
					<?php
				}
				else {
					?>
					<button class="b-catalog-show" onclick="window.location.href = '?<?= $qs ?>product_page=<?= $paged - 1 ?>'">Вернуться назад</button>
				<?php } ?>
				<div class="b-pagination">
					<?php
					// Если впереди есть ещё пагинация
					if ( $paged > 1 ) {
						?>
						<a href="?<?= $qs ?>product_page=<?= $paged - 1 ?>" class="b-pagination-arrow b-pagination-prev"></a>
					<?php } ?>
					<ul>


						<?php // 1 показываем всегда   ?>
						<li <?php if ( 1 == $paged ) { ?>class="active"<?php } ?>><a href="?<?= $qs ?>product_page=1">1</a></li>

						<?php
						// Многоточие
						// Если страниц пагинации больше 5
						if ( $paged_count > 5 and $paged > 3 ) {
							?><li><a href="?<?= $qs ?>product_page=<?= $paged - 1 ?>">...</a></li><?php
						}
						?>

						<?php
						$i = 1;
						if ( $paged > 1 )
							$i = $paged;

						// Перебор 2, 3, 4, ...
						for ( $pp = -2; $pp <= 2; $pp++ ) {
							$j = $i;
							$i += $pp;

							if ( $i > 1 and $i < $paged_count ) {
								?>
								<li <?php if ( $i == $paged ) { ?>class="active"<?php } ?>><a href="?<?= $qs ?>product_page=<?= $i ?>"><?= $i ?></a></li>
								<?php
							}
							$i = $j + 0;
						}
						?>

						<?php
						// Многоточие
						// Если страниц пагинации больше 5
						if ( $paged_count > 5 and $paged + 3 < $paged_count ) {
							?><li><a href="?<?= $qs ?>product_page=<?= $paged + 1 ?>">...</a></li><?php
						}
						?>

						<?php // Последний показываем всегда       
						?>
						<li <?php if ( $paged_count == $paged ) { ?>class="active"<?php } ?>><a href="?<?= $qs ?>product_page=<?= $paged_count ?>"><?= $paged_count ?></a></li>


					</ul>
					<?php
					// Если впереди есть ещё пагинация
					if ( $paged < $paged_count ) {
						?>
						<a href="?<?= $qs ?>product_page=<?= $paged + 1 ?>" class="b-pagination-arrow b-pagination-next"></a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>