<?php

	$lot_detail = $item_detail;
	$lot_image = 'http://placehold.it/200x200';
	if ($lot_detail->img_lot) {
		$lot_image = $lot_detail->img_lot;
	}

	// echo '<pre>';
	// print_r($lot_detail);
	// echo '</pre>';
	// wp_die($api_endpoint );

?>
<div id="" class="tid-lot-detail tid-shortcode">
	<h1 class="tid-lot-title"><?php echo $lot_detail->nama_lot ?></h1>

	<img class="tid-lot-image" src="<?php echo $lot_image ?>">

	<a href="#modal-map" id="render-map-btn" class="render-map-btn">Lihat Peta</a>
	<div id="modal-map" style="display:none">
		<div id="trees-id-map" class="trees-id-map" data-map-type="single" data-id="<?php echo $lot_detail->id_lot ?>" data-tree-page="<?php echo $tree_page ?>"></div>
	</div>
	<div class="modal">
		<div class="modal-inner">
			<a rel="modal:close">&times;</a>
			<div class="modal-content"></div>
		</div>
	</div>

	<div id="" class="tid-statistic">
		<?php if (isset($lot_detail->nama_petani)): ?>
			<dl>
				<dt>Petani</dt>
				<dd><?php echo $lot_detail->nama_petani ?></dd>
			</dl>
		<?php endif ?>
		<?php if (isset($lot_detail->nama_relawan)): ?>
			<dl>
				<dt>Relawan</dt>
				<dd><?php echo $lot_detail->nama_relawan ?></dd>
			</dl>			
		<?php endif ?>
		<?php if (isset($lot_detail->nama_block)): ?>
			<dl>
				<dt>Desa</dt>
				<dd><?php echo $lot_detail->nama_block ?></dd>
			</dl>			
		<?php endif ?>
		<?php if (isset($lot_detail->nama_pemilik)): ?>
			<dl>
				<dt>Pemilik Lahan</dt>
				<dd><?php echo $lot_detail->nama_pemilik ?></dd>
			</dl>
		<?php endif ?>
		<?php if (isset($lot_detail->nama_verifikator)): ?>
			<dl>
				<dt>Verifikator</dt>
				<dd><?php echo $lot_detail->nama_verifikator ?></dd>
			</dl>			
		<?php endif ?>
		<?php if (isset($lot_detail->nama_lot)): ?>
			<dl>
				<dt>ID Lot</dt>
				<dd><?php echo $lot_detail->nama_lot ?></dd>
			</dl>
		<?php endif ?>
		<?php if (isset($lot_detail->status_name)): ?>
			<dl>
				<dt>Status Lot</dt>
				<dd><?php echo $lot_detail->status_name ?></dd>
			</dl>			
		<?php endif ?>
		<?php if (isset($lot_detail->jenis_tanaman)): ?>
			<dl>
				<dt>Tanaman</dt>
				<dd><?php echo $lot_detail->jenis_tanaman ?></dd>
			</dl>
		<?php endif ?>
		<?php if (isset($lot_detail->tanggal_tanam)): ?>
			<dl>
				<dt>Tanggal Tanam</dt>
				<dd><?php echo $lot_detail->tanggal_tanam ?></dd>
			</dl>
		<?php endif ?>
		<?php if (isset($lot_detail->jumlah_pohon_rencana)): ?>
			<dl>
				<dt>Rencana Penanaman</dt>
				<dd><?php echo number_format($lot_detail->jumlah_pohon_rencana, 0, ',', '.') ?> pohon</dd>
			</dl>
		<?php endif ?>
		<?php if (isset($lot_detail->luas_lahan)): ?>
			<dl>
				<dt>Luas Lahan</dt>
				<dd><?php echo number_format($lot_detail->luas_lahan_realisasi, 2,',','.') ?> hektar <small>(polygon)</small></dd>
				<dd><?php echo number_format($lot_detail->luas_lahan, 2,',','.') ?> hektar </dd>
			</dl>
		<?php endif ?>
	</div>
	<div class="tid-lot-content">
		<?php echo wpautop( $lot_detail->content_lot, TRUE );  ?>
	</div>
</div>