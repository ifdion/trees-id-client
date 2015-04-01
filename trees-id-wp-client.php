<?php
/*
Plugin Name: Trees ID WP Client
Plugin URI: http://trees.id
Description: Trees ID WP Client.
Version: 1.0
Author: Saklik
Author URI: http://trees.id/
*/


function trees_id_client( $atts ) {
	$atts = shortcode_atts(
		array(
			'program_id' => 0,
		), $atts, 'trees-id' );

	$program_id = $atts['program_id'];

	// empty program_id attribute on shortcode
	if ($program_id == 0 || !is_numeric($program_id)) {
		return;
	}

	// single lot, block, or project
	if (isset($_GET['lot_id'])) {
		$item_id = $_GET['lot_id'];
		$entity = 'lot';

	} elseif (isset($_GET['block_id'])) {
		$item_id = $_GET['block_id'];
		$entity = 'block';

	} elseif (isset($_GET['project_id'])) {
		$item_id = $_GET['project_id'];
		$entity = 'project';
	}

	if (isset($entity)) {
		$api_endpoint = 'http://api.trees.id/?object='.$entity.'&id='. $item_id;

		$response = wp_remote_get( $api_endpoint );

		if (!is_wp_error($response )) {
			$item_detail = json_decode(substr($response['body'], 1, -1));

			ob_start();
			include 'template/'.$entity.'-detail.php';
			$output = ob_get_contents();
			ob_end_clean();
			return $output;

		} else {
			return 'error 734';
		}
	}

	// archive of lot on project, block or 
	if (isset($_GET['view'])) {
		$view = $_GET['view'];
		switch ($view) {
			case 'project':
				$api_endpoint = 'http://api.trees.id/?object=project&program_id='. $program_id;
				$title = '<h1>Daftar Project</h1>';
				break;
			case 'block':
				$api_endpoint = 'http://api.trees.id/?object=block&program_id='. $program_id;
				$title = '<h1>Daftar Desa</h1>';
				break;
			case 'lot':
				$api_endpoint = 'http://api.trees.id/?object=lot&program_id='. $program_id;
				$title = '<h1>Daftar Lot</h1>';

				if (isset($_GET['project'])) {
					$api_endpoint = $api_endpoint.'&project_id='.$_GET['project'];
				}
				if (isset($_GET['block'])) {
					$api_endpoint = $api_endpoint.'&block_id='.$_GET['block'];
				}

				break;
		}

		$current_page = get_query_var( 'page' );
		if ($current_page > 1 ) {
			$api_endpoint = $api_endpoint.'&page='.$current_page;
		}

		$response = wp_remote_get( $api_endpoint );

		if (!is_wp_error($response )) {
			$item_archive = json_decode(substr($response['body'], 1, -1));

			$output = '';
			foreach ($item_archive->data as $key => $item_detail){
				ob_start();
				include 'template/'.$view.'-grid.php';
				$output_part = ob_get_contents();
				ob_end_clean();

				$output = $output.$output_part;
			}

			$pagination = '';
			if ($item_archive->totalPage > 1) {
				ob_start();
				include 'template/pagination.php';
				$pagination = ob_get_contents();
				ob_end_clean();
			}

			return $title . $output . $pagination;

		} else {
			echo '<pre>';
			print_r($response);
			echo '</pre>';
		}
	} else {

		$api_endpoint = 'http://api.trees.id/?object=program&id='. $program_id;
		$response = wp_remote_get( $api_endpoint );

		if (!is_wp_error($response )) {
			$program_json = substr($response['body'], 1, -1);
			$program_detail = json_decode($program_json);
			$program_detail = $program_detail->data[0];

			ob_start();
			include_once 'template/program-detail.php';
			$output = ob_get_contents();
			ob_end_clean();
			return $output;

		} else {
			echo '<pre>';
			print_r($response);
			echo '</pre>';

		}
	}
}

add_shortcode( 'trees-id', 'trees_id_client' );


function custom_shortcode_scripts() {
	global $post;
	if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'trees-id') ) {
		wp_register_style( 'trees-id', plugins_url( 'trees-id-wp-client/css/trees-id.css' ) );
		wp_enqueue_style( 'trees-id' );
	}
}
add_action( 'wp_enqueue_scripts', 'custom_shortcode_scripts');


?>