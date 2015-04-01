<?php

	global $post;
	$permalink = get_permalink($post->ID);
	$project_link = $permalink.'?project_id='.$item_detail->id_project;

?>

<h2><a href="<?php echo $project_link ?>"><?php echo $item_detail->nama_project ?></a></h2>
<a href="<?php echo $project_link ?>"><img src="<?php echo $item_detail->img_project ?>"></a>

<h2>Detail Project</h2>
<dl>
	<dt>Total Pohon</dt>
	<dd><?php echo $item_detail->total_pohon ?> pohon</dd>
	<dt>Total Relawan</dt>
	<dd><?php echo $item_detail->total_relawan ?> orang</dd>
	<dt>Total Desa</dt>
	<dd><?php echo $item_detail->total_desa ?> desa</dd>
	<dt>Total Petani</dt>
	<dd><?php echo $item_detail->total_petani ?> petani</dd>
	<dt>Total Lot</dt>
	<dd><?php echo $item_detail->total_lot ?> lot</dd>
</dl>