<?php

	global $post;
	$permalink = get_permalink($post->ID);
	$block_link = $permalink.'?block_id='.$item_detail->id_block;

	$thumbnail_bg = '';
	if ($item_detail->img_block) {
		$thumbnail_bg = 'background-image: url('.$item_detail->img_block.');';
	}
?>

<div class="tid-block-grid">
	<div class="grid-thumbnail">
		<a href="<?php echo $block_link ?>" style="<?php echo $thumbnail_bg ?>"></a>
	</div>
	<div class="grid-title">
		<p><a href="<?php echo $block_link ?>"><?php echo $item_detail->nama_block; ?></a></p>
	</div>
</div>	