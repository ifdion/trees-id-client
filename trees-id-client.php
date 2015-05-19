<?php
/*
Plugin Name: Trees ID Client
Plugin URI: http://trees.id
Description: Trees ID Client.
Version: 1.0
Author: Saklik
Author URI: http://trees.id/
*/

/**
 * add shortcode
 *
 * @return void
 * @author 
 **/
function tid_shortcode( $atts ) {

	$args = array(
		'timeout'     => 500,
		'redirection' => 5,
		'httpversion' => '1.0',
		'user-agent'  => 'WordPress/' . get_bloginfo('version' ) . '; ' . get_bloginfo( 'url' ),
		'blocking'    => true,
		'headers'     => array(),
		'cookies'     => array(),
		'body'        => null,
		'compress'    => false,
		'decompress'  => true,
		'sslverify'   => true,
		'stream'      => false,
		'filename'    => null
	);

	$atts = shortcode_atts(
		array(
			'program_id' => 0,
			'project_id' => 0,
			'block_id' => 0,
			'lot_id' => 0,
		), $atts, 'trees-id' );
	$program_id = $atts['program_id'];
	$project_id = $atts['project_id'];
	$block_id = $atts['block_id'];
	$lot_id = $atts['lot_id'];

	// empty program_id attribute on shortcode
	if ($program_id == 0 && $project_id == 0 && $block_id == 0 && $lot_id == 0) {
		return;
	}

	// get current page data
	global $post;
	$permalink = get_permalink($post->ID );

	// plugin variable
	$api_provider = 'http://api.trees.id/';
	$connector = '&';
	if (get_option('permalink_structure' )) {
		$connector = '?';
	}
	$lot_page = $permalink.$connector.'lot_id=[lot]';
	$tree_page = $permalink.$connector.'lot_id=[lot]&tree_offset=[offset]';

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
			$api_endpoint = $api_provider.'?object='.$entity.'&lot_id='. $lot_id.'&tree_offset='. $tree_offset;

			// lot detail API call
			$lot_endpoint = $api_provider.'?object=lot&lot_id='. $lot_id;
			if ( false == ( $lot_detail = get_transient( 'tid-lot-'.$lot_id ) ) ) {
				$response = wp_remote_get( $lot_endpoint , $args);
				if (is_wp_error($response)) {
					return '<p>'.$response->get_error_message().'</p>';
				} else {
					$lot_detail = json_decode(substr($response['body'], 1, -1));
					$lot_detail = $lot_detail->data[0];
					set_transient( 'tid-lot-'.$lot_id, $lot_detail, 60*10*1 );
				}
			}

			// tree detail API call
			if ( false == ( $tree_detail = get_transient('tid-'. $entity.'-'.$lot_id.'-'.$tree_offset ) ) ) {
				if (is_wp_error($response)) {
					return '<p>'.$response->get_error_message().'</p>';
				} else {
					$response = wp_remote_get( $api_endpoint , $args);
					$tree_detail = json_decode(substr($response['body'], 1, -1));
					$tree_detail = $tree_detail->data[0];
					set_transient( 'tid-tree-'.$lot_id.'-'.$tree_offset, $tree_detail, 60*10*1 );
				}
			}

		} else {

			// entity detail API call
			$api_endpoint = $api_provider.'?object='.$entity.'&id='. $item_id;
			if ( false == ( $item_detail = get_transient('tid-'. $entity.'-'.$item_id ) ) ) {

				$response = wp_remote_get( $api_endpoint , $args);
				if (is_wp_error($response)) {
					return '<p>'.$response->get_error_message().'</p>';
				} else {
					$item_detail = json_decode(substr($response['body'], 1, -1));
					$item_detail = $item_detail->data[0];
					set_transient( 'tid-'. $entity.'-'.$item_id, $item_detail, 60*10*1 );
				}
			}
		}

		if (locate_template('tid/'.$entity.'-detail.php') != '') {
			$template = get_stylesheet_directory().'/tid/'.$entity.'-detail.php';
		} else {
			$template = 'template/'.$entity.'-detail.php';
		}

		ob_start();
		include 'template/'.$entity.'-detail.php';
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	// archive of lot on project, block or 
	if (isset($_GET['view'])) {

		$view = $_GET['view'];

		// main API call
		switch ($view) {
			case 'project':
				$api_endpoint = $api_provider.'?object=project&program_id='. $program_id;
				$title = '<h1 class="tid-lot-archive-title">Daftar Project</h1>';
				break;
			case 'block':
				$api_endpoint = $api_provider.'?object=block&program_id='. $program_id;
				$title = '<h1 class="tid-lot-archive-title">Daftar Desa</h1>';
				break;
			case 'lot':
				$api_endpoint = $api_provider.'?object=lot&program_id='. $program_id;
				$title = '<h1 class="tid-lot-archive-title">Daftar Lot</h1>';
				break;
		}

		// sub API call
		$subtax = '';
		if (isset($_GET['project'])) {
			$api_endpoint = $api_endpoint.'&project_id='.$_GET['project'];
			$subtax = $subtax.'-pj'.$_GET['project'];
		}
		if (isset($_GET['block'])) {
			$api_endpoint = $api_endpoint.'&block_id='.$_GET['block'];
			$subtax = $subtax.'-b'.$_GET['block'];
		}

		// paged API call
		$current_page = get_query_var( 'page' );
		$page = '';
		if ($current_page > 1 ) {
			$api_endpoint = $api_endpoint.'&page='.$current_page;
			$page = '-p'.$current_page;
		}

		// call API
		if ( false == ( $item_archive = get_transient('tid-'.$view.'-pg'. $program_id . $subtax .$page  ) ) ) {
			$response = wp_remote_get( $api_endpoint , $args);

			if (is_wp_error($response)) {
				return '<p>'.$response->get_error_message().'</p>';
			} else {
				$item_archive = json_decode(substr($response['body'], 1, -1));
				set_transient( 'tid-'.$view.'-pg'. $program_id . $subtax .$page , $item_archive, 60*10*1 );
			}
		}

		// setup output
		$output = '';
		foreach ($item_archive->data as $key => $item_detail){

			if (locate_template('tid/'.$entity.'-grid.php') != '') {
				$template = get_stylesheet_directory().'/tid/'.$entity.'-grid.php';
			} else {
				$template = 'template/'.$entity.'-grid.php';
			}

			// setup output
			ob_start();
			include_once $template;
			$output_part = ob_get_contents();
			ob_end_clean();

			$output = $output.$output_part;
		}
		$pagination = '';
		if ($item_archive->totalPage > 1) {

			if (locate_template('tid/template-pagination.php') != '') {
				$template = get_stylesheet_directory().'tid/template-pagination.php';
			} else {
				$template = 'template/pagination.php';
			}


			ob_start();
			include $template;
			$pagination = ob_get_contents();
			ob_end_clean();
		}
		return $title . $output . $pagination;

	} else {


		if ($program_id != 0) {
			$entity = 'program';
			$entity_id = $program_id;
		} else {
			$entity = 'project';
			$entity_id = $project_id;
		}
		// get program data
		$api_endpoint = $api_provider.'?object='.$entity.'&id='. $entity_id;
		if ( false == ( $program_detail = get_transient('tid-program-'.$entity_id ) ) ) {

			$response = wp_remote_get( $api_endpoint , $args);
			if (is_wp_error($response)) {
				return '<p>'.$response->get_error_message().'</p>';
			} else {
				$entity_json = substr($response['body'], 1, -1);
				$item_detail = json_decode($entity_json);
				$item_detail = $item_detail->data[0];
				set_transient( 'tid-'.$entity.'-'.$entity_id, $item_detail, 60*10*1 );
			}
		}

		if (locate_template('tid/'.$entity.'-detail.php') != '') {
			$template = get_stylesheet_directory().'/tid/'.$entity.'-detail.php';
		} else {
			$template = 'template/'.$entity.'-detail.php';
		}

		// setup output
		ob_start();
		include_once $template;
		$output = ob_get_contents();
		ob_end_clean();
		return $output;

	}
}
add_shortcode( 'trees-id', 'tid_shortcode' );

/**
 * register additional stylesheet and script file if shortcode is present
 *
 * @return void
 * @author 
 **/
function tid_shortcode_scripts() {
	global $post;
	if( (is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'trees-id')) || (is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'trees-id-view-tree')) ) {
		if (WP_DEBUG == true) {
			wp_register_style( 'trees-id', plugins_url( 'trees-id-client/css/trees-id-client.css' ), array(),'0.0' );
			wp_register_script( 'trees-id-client-plugin',plugins_url( 'trees-id-client/js/trees-id-client-plugin.js' ), array(),'0.0',true);
			wp_register_script( 'trees-id',plugins_url( 'trees-id-client/js/main.js' ),array('underscore','trees-id-client-plugin'),'0.0',true);

		} else {
			wp_register_style( 'trees-id', plugins_url( 'trees-id-client/css/trees-id-client.min.css' ), array(),'0.0' );
			wp_register_script( 'trees-id',plugins_url( 'trees-id-client/js/trees-id-client.min.js' ),array('underscore'),'0.0',true);
		}

		wp_enqueue_style('trees-id' );
		wp_enqueue_script('trees-id' );

	}
}
add_action( 'wp_enqueue_scripts', 'tid_shortcode_scripts');

/**
 * delete saved transient for debuging purposes
 *
 * @return void
 * @author 
 **/
function process_delete_tid_transient() {
	global $wpdb;
	$namespace = '_tid-';
	$sql = "DELETE FROM {$wpdb->options} WHERE option_name like '%$namespace%' ";
	$wpdb->query($sql);

	echo 'deleted transient';

	die();
	exit();
}
add_action('wp_ajax_delete_tid_transient', 'process_delete_tid_transient');
add_action('wp_ajax_nopriv_delete_tid_transient', 'process_delete_tid_transient');

/**
 * shortcode view tree
 *
 * @return void
 * @author 
 **/
function tid_view_tree_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'foo' => 'no foo',
		'tree' => null,
		//'project' => 'no project'
	), $atts, 'bartag' );

	$tree = "{$atts['tree']}";

	global $post;
	$post_id = $post->ID;
	$post_meta_treeID = get_post_meta( $post_id, 'tree_id', true );

	if ( !empty($post_meta_treeID) ){
		if (is_array($post_meta_treeID)) {
			$tree_id_str = implode(",", $post_meta_treeID);
		} else {
			$tree_id_str = $post_meta_treeID;
		}
	}
	
	ob_start();
	include 'template/tree-multiple.php';
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
add_shortcode( 'trees-id-view-tree', 'tid_view_tree_shortcode' );


?>