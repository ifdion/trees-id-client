<?php
	$tree_page = $tree_page.$connector;
	$request = '';
	foreach ($query_var as $key => $value) {
		$request = $request. 'data-'.$key.'="'.$value.'" ' ;
	}
?>
<div id="trees-id-map" data-map-type="single-tree" <?php echo $request ?> data-tree-page="<?php echo $tree_page ?>lot_id=[lot]&offset=[offset]"></div>


