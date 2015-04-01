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
<h1><?php echo $block_detail->nama_block; ?></h1>
<p><?php echo $block_detail->district.', '.$block_detail->regency.', '.$block_detail->province ?> </p>
<p><?php echo $block_detail->content_block ?></p>
<img src="<?php echo $block_image ?>">

<h2>Daftar Lot</h2>

<?php
	foreach ($block_detail->list_lot as $key => $item_detail){
		ob_start();
		include 'lot-grid.php';
		$sub_output = ob_get_contents();
		ob_end_clean();
		echo $sub_output;
	}
?>