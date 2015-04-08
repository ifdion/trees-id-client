<?php
	
	global $post;
	$permalink = get_permalink($post->ID);
	$lot_link = $permalink.'?lot_id='.$item_detail->id_lot;

	$thumbnail_bg = '';
	if ($item_detail->img_lot) {
		$thumbnail_bg = 'background-image: url('.$item_detail->img_lot.');';
	}
?>
<div class="tid-lot-grid">
	<div class="grid-thumbnail">
		<a href="<?php echo $lot_link ?>" style="<?php echo $thumbnail_bg ?>"></a>
	</div>
	<div class="grid-title">
		<p><a href="<?php echo $lot_link ?>"><?php echo $item_detail->nama_lot; ?></a></p>
	</div>
</div>	