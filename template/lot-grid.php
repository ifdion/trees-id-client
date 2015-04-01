<?php
	
	global $post;
	$permalink = get_permalink($post->ID);
	$lot_link = $permalink.'?lot_id='.$item_detail->id_lot;

	$lot_image = 'http://placehold.it/200x200';
	if ($item_detail->img_lot) {
		$lot_image = $item_detail->img_lot;
	}
?>
<div id="" class="">
	<h3><a href="<?php echo $lot_link ?>"><?php echo $item_detail->nama_lot; ?></a></h3>
	<a href="<?php echo $lot_link ?>"><img src="<?php echo $lot_image ?>"></a>
</div>	