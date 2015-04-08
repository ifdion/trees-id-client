<?php

	$project_detail = $item_detail->data[0];
	$project_image = 'http://placehold.it/200x200';
	if ($project_detail->img_project) {
		$project_image = $project_detail->img_project;
	}

	// echo '<pre>';
	// print_r($project_detail);
	// echo '</pre>';

?>
<div class="tid-project-detail tid-shortcode">
	<h1 class="tid-project-title"><?php echo $project_detail->nama_project; ?></h1>
	<div class="tid-project-content">
		<?php echo wpautop( $project_detail->content_project, TRUE );  ?>
	</div>
	<img class="tid-project-image" src="<?php echo $project_image ?>">
	<h3 class="tid-statistic-title">Statistik</h3>
	<div class="tid-statistic">
		<dl>
			<dt>Total Pohon</dt>
			<dd><?php echo number_format( $project_detail->total_pohon, 0, ',', '.') ?> pohon</dd>
		</dl>
		<dl>
			<dt>Total Relawan</dt>
			<dd><?php echo number_format( $project_detail->total_relawan, 0, ',', '.') ?> orang</dd>
		</dl>
		<dl>
			<dt>Total Desa</dt>
			<dd><?php echo number_format( $project_detail->total_desa, 0, ',', '.') ?> desa</dd>
		</dl>
		<dl>
			<dt>Total Petani</dt>
			<dd><?php echo number_format( $project_detail->total_petani, 0, ',', '.') ?> petani</dd>
		</dl>
		<dl>
			<dt>Total Lot</dt>
			<dd><?php echo number_format( $project_detail->total_lot, 0, ',', '.') ?> lot</dd>
		</dl>
	</div>

	<a href="trees-id-map" id="render-map-btn" class="render-map-btn">Lihat Peta</a>
	<div id="trees-id-map" class="trees-id-map" data-map-type="archive" data-project_id="<?php echo $project_detail->id_project ?>" data-lot-page="<?php echo $lot_page ?>"></div>

	<nav class="tid-project-nav">
		<ul>
			<li><a class="tid-nav tid-nav-block" href="<?php echo $permalink . $connector. 'view=block&project='.$project_detail->id_project ?>">Block</a></li>
			<li><a class="tid-nav tid-nav-lot" href="<?php echo $permalink . $connector. 'view=lot&project='.$project_detail->id_project ?>">Lot</a></li>
		</ul>
	</nav>

	<h2 class="tid-lot-archive-title">Daftar Lot</h2>
	<div class="tid-lot-archive">
		<?php
			foreach ($project_detail->list_lot as $key => $item_detail){
				ob_start();
				include 'lot-grid.php';
				$sub_output = ob_get_contents();
				ob_end_clean();
				echo $sub_output;
			}
		?>
	</div>
</div>