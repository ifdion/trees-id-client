<?php

	// echo '<pre>';
	// print_r($tree_detail);
	// print_r($lot_detail);
	// echo '</pre>';

?>
<div id="" class="tid-tree-detail tid-shortcode">
	<h1 class="tid-tree-title">Pohon ke <?php echo $tree_offset ?> di Lot <?php echo $lot_detail->nama_lot ?></h1>
	<img class="tid-tree-image" src="<?php echo $tree_detail->img_tree ?>">
	<div id="trees-id-map" class="trees-id-map" data-map-type="tree" data-lot_id="<?php echo $lot_detail->id_lot ?>" data-offset="<?php echo $tree_offset ?>"></div>
	<div class="tid-tree-content"></div>
</div>