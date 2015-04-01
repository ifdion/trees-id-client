<?php

	global $post;
	$permalink = get_permalink($post->ID);
	$block_link = $permalink.'?block_id='.$item_detail->id_block;

	$block_image = 'http://placehold.it/200x200';
	if ($item_detail->img_block) {
		$block_image = $item_detail->img_block;
	}
?>

<div id="" class="">
	<h3><a href="<?php echo $block_link ?>"><?php echo $item_detail->nama_block ?></a></h3>
	<a href="<?php echo $block_link ?>"><img src="<?php echo $block_image ?>"></a>
</div>