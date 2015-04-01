<?php
	$lot_detail = $item_detail->data[0];

	$lot_image = 'http://placehold.it/200x200';
	if ($lot_detail->img_lot) {
		$lot_image = $lot_detail->img_lot;
	}

?>
<h1><?php echo $lot_detail->nama_lot ?></h1>
<p><?php echo $lot_detail->content_lot ?></p>
<img src="<?php echo $lot_image ?>">
<dl>
	<dt>Petani</dt>
	<dd><?php echo $lot_detail->nama_petani ?></dd>
	<dt>Relawan</dt>
	<dd><?php echo $lot_detail->nama_relawan ?></dd>
	<dt>Desa</dt>
	<dd><?php echo $lot_detail->nama_block ?></dd>
	<dt>Pemilik Lahan</dt>
	<dd><?php echo $lot_detail->nama_pemilik ?></dd>
	<dt>Verifikator</dt>
	<dd><?php echo $lot_detail->nama_verifikator ?></dd>
	<dt>ID Lot</dt>
	<dd><?php echo $lot_detail->nama_lot ?></dd>
</dl>
<dl>
	<dt>Status Lot</dt>
	 
	<dd><?php echo $lot_detail->status_name ?></dd>
	<dt>Tanaman</dt>
	<dd><?php echo $lot_detail->jenis_tanaman ?></dd>
	<dt>Tanggal Tanam</dt>
	<dd><?php echo $lot_detail->tanggal_tanam ?></dd>
	<dt>Rencana Penanaman</dt>
	<dd><?php echo $lot_detail->jumlah_pohon_rencana ?></dd>
	<dt>Luas Lahan</dt>
	<dd><?php echo $lot_detail->luas_lahan_realisasi ?> hektar <small>(polygon)</small></dd>
	<dd><?php echo $lot_detail->luas_lahan ?> hektar </dd>
</dl>