<?php
	global $post;
	$permalink = get_permalink( $post->ID );

	$connector = '?';
	if (strpos($permalink, '?')) {
		$connector = '&';
	}
?>
<div class="tid-program-detail tid-shortcode">
	<h1><?php echo $program_detail->nama_program ?></h1>
	<div class="tid-program-content">
		<?php echo $program_detail->content_program ?>
		<?php echo wpautop( $program_detail->content_program, TRUE );  ?>
	</div>
	<img class="tid-program-image" src="<?php echo $program_detail->img_program ?>">
	<h3 class="tid-statistic-title">Statistik</h3>
	<div class="tid-statistic">
		<dl>
			<dt>Total Pohon</dt>
			<dd><?php echo number_format($program_detail->total_pohon, 0, ',', '.') ?> pohon</dd>
		</dl>
		<dl>
			<dt>Total Relawan</dt>
			<dd><?php echo number_format($program_detail->total_relawan, 0, ',', '.') ?> orang</dd>
		</dl>
		<dl>
			<dt>Total Desa</dt>
			<dd><?php echo number_format($program_detail->total_desa, 0, ',', '.') ?> desa</dd>
		</dl>
		<dl>
			<dt>Total Petani</dt>
			<dd><?php echo number_format($program_detail->total_petani, 0, ',', '.') ?> petani</dd>
		</dl>
		<dl>
			<dt>Total Lot</dt>
			<dd><?php echo number_format($program_detail->total_lot, 0, ',', '.') ?> lot</dd>
		</dl>
	</div>

	<a href="trees-id-map" id="render-map-btn" class="render-map-btn">Lihat Peta</a>
	<div id="trees-id-map" class="trees-id-map" data-map-type="archive" data-program_id="<?php echo $program_id ?>" data-lot-page="<?php echo $lot_page ?>"></div>

	<nav class="tid-program-nav">
		<ul>
			<li><a class="tid-nav tid-nav-project" href="<?php echo $permalink . $connector. 'view=project'?>">Project</a></li>
			<li><a class="tid-nav tid-nav-block" href="<?php echo $permalink . $connector. 'view=block'?>">Block</a></li>
			<li><a class="tid-nav tid-nav-lot" href="<?php echo $permalink . $connector. 'view=lot'?>">Lot</a></li>
		</ul>
	</nav>

	<h2 class="tid-lot-archive-title">Daftar Lot</h2>

	<div class="tid-lot-archive">
		<?php
			foreach ($program_detail->list_lot as $key => $item_detail){
				ob_start();
				include 'lot-grid.php';
				$output = ob_get_contents();
				ob_end_clean();
				echo $output;
			}
		?>
	</div>
</div>