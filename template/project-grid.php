<?php

	global $post;
	$permalink = get_permalink($post->ID);
	$project_link = $permalink.'?project_id='.$item_detail->id_project;

	$project_image = 'http://placehold.it/200x200';
	if ($item_detail->img_project) {
		$project_image = $item_detail->img_project;
	}

?>
<div class="tid-project-grid tid-grid">
	<h2 class="tid-project-grid-title"><a href="<?php echo $project_link ?>"><?php echo $item_detail->nama_project ?></a></h2>
	<a href="<?php echo $project_link ?>">
		<img class="tid-project-image" src="<?php echo $project_image ?>">
	</a>
	<div class="tid-statistic">
		<dl>
			<dt>Total Pohon</dt>
			<dd><?php echo number_format( $item_detail->total_pohon, 0, ',', '.') ?> pohon</dd>
		</dl>
		<dl>
			<dt>Total Relawan</dt>
			<dd><?php echo number_format( $item_detail->total_relawan, 0, ',', '.') ?> orang</dd>
		</dl>
		<dl>
			<dt>Total Desa</dt>
			<dd><?php echo number_format( $item_detail->total_desa, 0, ',', '.') ?> desa</dd>
		</dl>
		<dl>
			<dt>Total Petani</dt>
			<dd><?php echo number_format( $item_detail->total_petani, 0, ',', '.') ?> petani</dd>
		</dl>
		<dl>
			<dt>Total Lot</dt>
			<dd><?php echo number_format( $item_detail->total_lot, 0, ',', '.') ?> lot</dd>
		</dl>
	</div>
</div>