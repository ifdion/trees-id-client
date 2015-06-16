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
			'template' => '',
		), $atts, 'trees-id' );
	$program_id = $atts['program_id'];
	$project_id = $atts['project_id'];
	$block_id = $atts['block_id'];
	$lot_id = $atts['lot_id'];
	$custom_template = $atts['template'];

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
			$lot_endpoint = $api_provider.'?object=lot&id='. $lot_id;

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
				$response = wp_remote_get( $api_endpoint , $args);
				if (is_wp_error($response)) {
					return '<p>'.$response->get_error_message().'</p>';
				} else {
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

		$template = 'template/'.$entity.'-detail.php';

		// check for custom default template
		if (locate_template('trees-id-client/'.$entity.'-detail.php') != '') {
			$template = get_stylesheet_directory().'/trees-id-client/'.$entity.'-detail.php';
		}

		// check for custom template
		if ($custom_template != '' && locate_template($custom_template) != '') {
			$template = get_stylesheet_directory().'/'.$custom_template;
		}

		ob_start();
		include $template;
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
		$output .= '<div class="tid-lot-archive">';
		foreach ($item_archive->data as $key => $item_detail){

			if (locate_template('trees-id-client/'.$view.'-grid.php') != '') {
				$template = get_stylesheet_directory().'/trees-id-client/'.$view.'-grid.php';
			} else {
				$template = 'template/'.$view.'-grid.php';
			}

			// setup output
			ob_start();
			include $template;
			$output_part = ob_get_contents();
			ob_end_clean();

			$output = $output.$output_part;
		}
		$output .= '</div>';
		$pagination = '';
		if ($item_archive->totalPage > 1) {

			if (locate_template('trees-id-client/template-pagination.php') != '') {
				$template = get_stylesheet_directory().'trees-id-client/template-pagination.php';
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
		if ( false == ( $item_detail = get_transient('tid-program-'.$entity_id ) ) ) {

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

		$template = 'template/'.$entity.'-detail.php';

		// check for custom default template
		if (locate_template('trees-id-client/'.$entity.'-detail.php') != '') {
			$template = get_stylesheet_directory().'/trees-id-client/'.$entity.'-detail.php';
		}

		// check for custom template
		if ($custom_template != '' && locate_template($custom_template) != '') {
			$template = get_stylesheet_directory().'/'.$custom_template;
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
 * shortcode view tree
 *
 * @return void
 * @author 
 **/
function tid_view_tree_shortcode( $atts ) {
	global $post;

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
			// archive parameter
			'lot_id'=> 0,
			'donatur_email'=> 0,
			'code'=> 0,
			'invoice'=> 0,
			'nohp'=> 0,
			'affiliate'=> 0,
			// single parameter
			'tree_offset' => 0,
			'single_id' => 0,
			// old parameter
			'search' => 0,
			'meta_key' => 0,
			'lot_page' => 0,
			//display paramater
			'template' => '',
		), $atts, 'trees-id-view-tree' );
	
	$custom_template = $atts['template'];
	$view = 'none';
	// get lot page

	// echo '<pre>';
	// print_r($post);
	// echo '</pre>';
	$tree_page = get_permalink($post);
	$connector = '&';
	if (get_option('permalink_structure' )) {
		$connector = '?';
	}

	// check for archive attribute
	$archive_variable = array(
		'lot_id' => $atts['lot_id'],
		'donatur_email' => $atts['donatur_email'],
		'code' => $atts['code'],
		'invoice' => $atts['invoice'],
		'nohp' => $atts['nohp'],
		'affiliate' => $atts['affiliate'],
	);
	$archive_query_var = array();
	foreach ($archive_variable as $key => $value) {
		if ($value) {
			$view = 'archive';
			$archive_query_var[$key] = $value;
		}
	}

	// check for single attribute
	$single_query_var = array();
	if ($atts['single_id']) {
		$view = 'single';
		$single_query_var['single_id'] = $atts['single_id'];
	} elseif ($atts['tree_offset'] && $atts['lot_id']){
		$view = 'single';
		$single_query_var['tree_offset'] = $atts['tree_offset'];
		$single_query_var['lot_id'] = $atts['lot_id'];
	}

	// check for archive request
	$archive_request = array();
	foreach ($archive_variable as $key => $value) {
		if (isset($_REQUEST[$key])) {
			$view = 'archive-request';
			$archive_request[$key] = $_REQUEST[$key];
		}
	}

	// check for archive request
	$single_request = array();
	if (isset($_REQUEST['single_id'])) {
		$view = 'single-request';
		$single_request['single_id'] = $_REQUEST['single_id'];
	} elseif (isset($_REQUEST['tree_offset']) && isset($_REQUEST['lot_id'])){
		$view = 'single-request';
		$single_request['tree_offset'] = $_REQUEST['tree_offset'];
		$single_request['lot_id'] = $_REQUEST['lot_id'];
	}

	switch ($view) {
		case 'archive':
			$query_var = $archive_query_var;
			$template = 'archive';
			break;
		case 'single':
			$query_var = $single_query_var;
			$template = 'single';
			break;
		case 'archive-request':
			$query_var = $archive_request;
			$template = 'archive';
			break;
		case 'single-request':
			$query_var = $single_request;
			$template = 'single';
			break;
		default:
			$template = 'archive';
		// 	return 'Invalid Request';
		// 	break;
	}

	$template = 'template/tree-'.$template.'.php';

	// check for custom default template
	if (locate_template('trees-id-client/tree-'.$template.'.php') != '') {
		$template = get_stylesheet_directory().'/trees-id-client/'.$entity.'-detail.php';
	}

	// check for custom template
	if ($custom_template != '' && locate_template($custom_template) != '') {
		$template = get_stylesheet_directory().'/'.$custom_template;
	}

	if (isset($query_var)) {
		ob_start();
		include $template;
		$query_var = $query_var;
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	// custom parameter
	$search_by = $atts['search'];
	$meta_key = $atts['meta_key'];
	$lot_page = $atts['lot_page'];

	// echo $lot_page;

	// get lot page
	$lot_page = get_permalink(get_page_by_path($lot_page));
	$connector = '&';
	if (get_option('permalink_structure' )) {
		$connector = '?';
	}

	// get tree ids from meta key
	if ($meta_key != 0) {
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
	}

	if ($search_by) {

		if ($search_by == 'nohp') {

			// search tree by nohp
			if (isset($_GET['nohp'])){
				$nohp = $_GET['nohp'];
				$url =  'http://api.trees.id/?object=tree&nohp='.$nohp.'&json_ori=yes&per_page=200';

				$response = wp_remote_get($url, $args );
				$json_api = json_decode($response['body'], true);

				if ($json_api['success'] == 1){
					$tree_id_str = '';
					$tree_lot = [];
					$dataTree = $json_api['data'];
					$totalCountTree = $json_api['totalCount'];

					foreach ($dataTree as $key => $value) {
						$nama_donatur = $value['nama_donatur'];
						$tree_id_str .= $value['id_tree'].',';
						if (!in_array($value['tree_lot_id'], $tree_lot)) {
							$tree_lot[$value['tree_lot_id']] =  $value['nama_lot'];
						}
					}
					$tree_id_str = trim($tree_id_str, ",");
				}
			}
		}
	}

	ob_start();
	include 'template/tree-multiple.php';
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
add_shortcode( 'trees-id-view-tree', 'tid_view_tree_shortcode' );

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

?>