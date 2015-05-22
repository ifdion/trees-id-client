<?php //$tree_id_str = "3260,3261,3262,3263,3264"; ?>
<div id="" class="tid-tree-detail tid-shortcode">
	<?php if ($search_by): ?>
		<form action="" method="GET" role="form">
			<div class="form-group">
				<label for="">No Telepon</label>
				<input type="text" class="form-control" id="" placeholder="Masukkan No HP" name="<?php echo $search_by ?>">
			</div>
			<button type="submit" class="btn btn-primary">Cari</button>
		</form>
	<?php endif ?>
	<?php if (isset($tree_id_str )): ?>
		<h3>Pohon Donatur <?php echo ucwords(strtolower($nama_donatur)); ?> Sejumlah <?php echo $totalCountTree; ?> Pohon</h3>
		<div id="trees-id-map" class="trees-id-map" data-map-type="tree" data-id="<?php echo $tree_id_str ?>"></div>
		<br>
		<h3>Pohon ini berada di lot
		<?php foreach ($tree_lot as $key => $value): ?>
			<a href="<?php echo $lot_page.$connector ?>lot_id=<?php echo $key ?>"><?php echo $value ?></a> 
		<?php endforeach ?>
		</h3>
	<?php endif ?>
	<div class="tid-tree-content"></div>
</div>