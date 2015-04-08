<?php

	$block_detail = $item_detail->data[0];

	$block_image = 'http://placehold.it/200x200';
	if ($block_detail->img_block) {
		$block_image = $block_detail->img_block;
	}

	// echo '<pre>';
	// print_r($block_detail);
	// echo '</pre>';

?>
<div class="tid-block-detail tid-shortcode">

	<h1 class="tid-block-title" ><?php echo $block_detail->nama_block; ?></h1>
	<p class="tid-block-location"><?php echo $block_detail->district.', '.$block_detail->regency.', '.$block_detail->province ?> </p>
	<div class="tid-block-content">
		<?php echo wpautop( $block_detail->content_block, TRUE );  ?>
	</div>
	<img class="tid-block-image" src="<?php echo $block_image ?>">

	<a href="trees-id-map" id="render-map-btn" class="render-map-btn">Lihat Peta</a>
	<div id="trees-id-map" class="trees-id-map" data-map-type="archive" data-block_id="<?php echo $block_detail->id_block ?>" data-lot-page="<?php echo $lot_page ?>"></div>

	<h2 class="tid-lot-archive-title">Daftar Lot</h2>
	<div class="tid-lot-archive">
		<?php
			foreach ($block_detail->list_lot as $key => $item_detail){
				ob_start();
				include 'lot-grid.php';
				$sub_output = ob_get_contents();
				ob_end_clean();
				echo $sub_output;
			}
		?>
	</div>
</div>