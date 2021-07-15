<?php
/**
 * Rating Stars
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;

global $rating;

// Если $rating пустой
if ( empty( $rating ) )
	$rating = 4;
?>
<div class="stars" style="margin-right: 1em">
	<?php
	for ( $i = 1; $i <= 5; $i++ ) {
		$fullStar = $i <= $rating ? ' fullStar' : '';
		?>
		<div class="star<?= $fullStar ?>"></div>
	<?php } ?>
</div>