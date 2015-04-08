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

	global $post;
	$permalink = get_permalink($post->ID );
	if (get_option('permalink_structure' )) {
		$connector = '?';
	} else {
		$connector = '&';
	}
	$lot_page = $permalink.$connector.'lot_id=[lot]';

	// empty program_id attribute on shortcode
	if ($program_id == 0 || !is_numeric($program_id)) {
		return;
	}

	// single lot, block, or project
	if (isset($_GET['lot_id'])) {
		if (isset($_GET['tree_offset'])) {
			$lot_id = $_GET['lot_id'];
			$tree_offset = $_GET['tree_offset'];
			$entity = 'tree';
		} else {
			$item_id = $_GET['lot_id'];
			$entity = 'lot';
		}

	} elseif (isset($_GET['block_id'])) {
		$item_id = $_GET['block_id'];
		$entity = 'block';

	} elseif (isset($_GET['project_id'])) {
		$item_id = $_GET['project_id'];
		$entity = 'project';
	}

	if (isset($entity)) {
		if ($entity == 'tree') {
			$api_endpoint = 'http://api.trees.id/?object='.$entity.'&lot_id='. $lot_id.'&tree_offset='. $tree_offset;

			// addtional call to lot data, although the call should be already saved to transient on previous call
			$lot_endpoint = 'http://api.trees.id/?object=lot&lot_id='. $lot_id;
			if ( false === ( $lot_output = get_transient( 'tid-lot-'.$lot_id ) ) ) {
				$lot_output = wp_remote_get( $lot_endpoint );
				set_transient( 'tid-lot-'.$lot_id, $lot_output, 60*10*1 );
			}
			$lot_result = get_transient( 'tid-lot-'.$lot_id );
			$lot_detail = json_decode(substr($lot_result['body'], 1, -1));

			// API call
			if ( false === ( $response_output = get_transient('tid-'. $entity.'-'.$lot_id.'-'.$tree_offset ) ) ) {
				$response_output = wp_remote_get( $api_endpoint );
				set_transient( 'tid-tree-'.$lot_id.'-'.$tree_offset, $response_output, 60*10*1 );
			}
			$response_result = get_transient('tid-tree-'.$lot_id.'-'.$tree_offset );

		} else {
			$api_endpoint = 'http://api.trees.id/?object='.$entity.'&id='. $item_id;
			if ( false === ( $response_output = get_transient('tid-'. $entity.'-'.$item_id ) ) ) {
				$response_output = wp_remote_get( $api_endpoint );
				set_transient( 'tid-'. $entity.'-'.$item_id, $response_output, 60*10*1 );
			}
			$response_result = get_transient('tid-'. $entity.'-'.$item_id );

		}

		if (!is_wp_error($response_result)) {
			$item_detail = json_decode(substr($response_result['body'], 1, -1));

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
				$title = '<h1 class="tid-lot-archive-title">Daftar Project</h1>';
				break;
			case 'block':
				$api_endpoint = 'http://api.trees.id/?object=block&program_id='. $program_id;
				$title = '<h1 class="tid-lot-archive-title">Daftar Desa</h1>';
				break;
			case 'lot':
				$api_endpoint = 'http://api.trees.id/?object=lot&program_id='. $program_id;
				$title = '<h1 class="tid-lot-archive-title">Daftar Lot</h1>';

				break;
		}

		$subtax = '';
		if (isset($_GET['project'])) {
			$api_endpoint = $api_endpoint.'&project_id='.$_GET['project'];
			$subtax = $subtax.'-pj'.$_GET['project'];
		}
		if (isset($_GET['block'])) {
			$api_endpoint = $api_endpoint.'&block_id='.$_GET['block'];
			$subtax = $subtax.'-b'.$_GET['block'];
		}

		$current_page = get_query_var( 'page' );
		$page = '';
		if ($current_page > 1 ) {
			$api_endpoint = $api_endpoint.'&page='.$current_page;
			$page = '-p'.$current_page;
		}


		if ( false === ( $response_output = get_transient('tid-'.$view.'-pg'. $program_id . $subtax .$page  ) ) ) {
			$response_output = wp_remote_get( $api_endpoint );
			set_transient( 'tid-'.$view.'-pg'. $program_id . $subtax .$page , $response_output, 60*10*1 );
		}

		$response_result = get_transient('tid-'.$view.'-pg'. $program_id . $subtax .$page  );

		if (!is_wp_error($response_result)) {
			$item_archive = json_decode(substr($response_result['body'], 1, -1));

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
			print_r($response_result);
			echo '</pre>';
		}
	} else {

		$api_endpoint = 'http://api.trees.id/?object=program&id='. $program_id;
		

		if ( false === ( $response_output = get_transient('tid-program-'.$program_id ) ) ) {
			$response_output = wp_remote_get( $api_endpoint );
			set_transient( 'tid-program-'.$program_id, $response_output, 60*10*1 );
		}

		$response_result = get_transient('tid-program-'.$program_id );

		if (!is_wp_error($response_result)) {
			$program_json = substr($response_result['body'], 1, -1);
			$program_detail = json_decode($program_json);
			$program_detail = $program_detail->data[0];

			ob_start();
			include_once 'template/program-detail.php';
			$output = ob_get_contents();
			ob_end_clean();
			return $output;

		} else {
			echo '<pre>';
			print_r($response_result);
			echo '</pre>';

		}
	}
}
add_shortcode( 'trees-id', 'trees_id_client' );

function custom_shortcode_scripts() {
	global $post;
	if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'trees-id') ) {


		if (WP_DEBUG == true) {
			wp_register_style( 'trees-id', plugins_url( 'trees-id-wp-client/css/trees-id.css' ) );

			wp_register_script( 'trees-id-map-plugin',plugins_url( 'trees-id-wp-client/js/trees-id-map-plugin.js' ), array(),'0.0',true);
			wp_register_script( 'trees-id-map',plugins_url( 'trees-id-wp-client/js/trees-id-map.js' ), array('trees-id-map-plugin'),'0.0',true);
			wp_register_script( 'trees-id',plugins_url( 'trees-id-wp-client/js/main.js' ),array('underscore','trees-id-map','trees-id-map-plugin'),'0.0',true);

		} else {
			wp_register_style( 'trees-id', plugins_url( 'trees-id-wp-client/css/trees-id.min.css' ) );
			wp_register_script( 'trees-id',plugins_url( 'trees-id-wp-client/js/trees-id.min.js' ),array('underscore'),'0.0',true);

		}

		wp_enqueue_style('trees-id' );
		wp_enqueue_script('trees-id' );

	}
}
add_action( 'wp_enqueue_scripts', 'custom_shortcode_scripts');



// function delete_trees_transients(){

// 	global $wpdb;
// 	$namespace = 'http\:\/\/api\.trees\.id\/';

// 	// delete all "namespace" transients
// 	$sql = "
// 		DELETE 
// 		FROM {$wpdb->options}
// 		WHERE option_name like '\_transient\_$namespace\_%'
// 		OR option_name like '\_transient\_timeout\_$namespace\_%'
// 	";

// 	$wpdb->query($sql);

// 	die();
// 	exit();
// }

// add_action( 'wp_admin_ajax_delete_trees_transient', 'delete_trees_transients');
?>