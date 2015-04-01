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
<h1><?php echo $project_detail->nama_project; ?></h1>
<p><?php echo $project_detail->content_project ?></p>
<img src="<?php echo $project_image ?>">

<h2>Daftar Lot</h2>

<?php
	foreach ($project_detail->list_lot as $key => $item_detail){
		ob_start();
		include 'lot-grid.php';
		$sub_output = ob_get_contents();
		ob_end_clean();
		echo $sub_output;
	}
?>