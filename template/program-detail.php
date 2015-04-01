<?php
	global $post;
	$permalink = get_permalink( $post->ID );

	$connector = '?';
	if (strpos($permalink, '?')) {
		$connector = '&';
	}


?>

<h1><?php echo $program_detail->nama_program ?></h1>
<p><?php echo $program_detail->deskripsi ?></p>
<img src="<?php echo $program_detail->img_program ?>">
<h2>Detail Program</h2>
<dl>
	<dt>Total Pohon</dt>
	<dd><?php echo $program_detail->total_pohon ?> pohon</dd>
	<dt>Total Relawan</dt>
	<dd><?php echo $program_detail->total_relawan ?> orang</dd>
	<dt>Total Desa</dt>
	<dd><?php echo $program_detail->total_desa ?> desa</dd>
	<dt>Total Petani</dt>
	<dd><?php echo $program_detail->total_petani ?> petani</dd>
	<dt>Total Lot</dt>
	<dd><?php echo $program_detail->total_lot ?> lot</dd>
</dl>

<ul>
	<li><a href="<?php echo $permalink . $connector. 'view=project'?>">Lihat Daftar Project</a></li>
	<li><a href="<?php echo $permalink . $connector. 'view=block'?>">Lihat Daftar Block</a></li>
	<li><a href="<?php echo $permalink . $connector. 'view=lot'?>">Lihat Daftar Lot</a></li>
</ul>

<h2>Daftar Lot</h2>

<?php
	foreach ($program_detail->list_lot as $key => $item_detail){
		ob_start();
		include 'lot-grid.php';
		$output = ob_get_contents();
		ob_end_clean();
		echo $output;
	}
?>