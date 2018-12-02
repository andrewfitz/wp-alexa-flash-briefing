<?php

/*

* Plugin Name: WP Alexa Flash Briefing
* Plugin URI: https://github.com/andrewfitz/wp-alexa-flash-briefing
* Description: Creates briefing post types and JSON feed endpoint for Alexa flash briefing skill
* Version: 1.5
* Tested up to: 4.9.8
* Requires at least: 4.7
* Author: Andrew Fitzgerald
* Author URI: https://github.com/andrewfitz
* Donate link: https://www.paypal.me/andrewfitz
* Contributors: andrewfitz
* License: GPL-2.0+
* License URI: http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain: alexa-fb

*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ALEXA_FB_VERSION', '1.5' );

// Register Custom Post Type
function briefing_post_type() {

	$labels = array(
		'name'                  => _x( 'Briefings', 'Post Type General Name', 'alexa-fb' ),
		'singular_name'         => _x( 'Briefing', 'Post Type Singular Name', 'alexa-fb' ),
		'menu_name'             => __( 'Briefings', 'alexa-fb' ),
		'name_admin_bar'        => __( 'Briefing', 'alexa-fb' ),
		'archives'              => __( 'Briefing Archives', 'alexa-fb' ),
		'attributes'            => __( 'Briefing Attributes', 'alexa-fb' ),
		'parent_item_colon'     => __( 'Parent Briefing:', 'alexa-fb' ),
		'all_items'             => __( 'All Briefings', 'alexa-fb' ),
		'add_new_item'          => __( 'Add New Briefing', 'alexa-fb' ),
		'add_new'               => __( 'Add New', 'alexa-fb' ),
		'new_item'              => __( 'New Briefing', 'alexa-fb' ),
		'edit_item'             => __( 'Edit Briefing', 'alexa-fb' ),
		'update_item'           => __( 'Update Briefing', 'alexa-fb' ),
		'view_item'             => __( 'View Briefing', 'alexa-fb' ),
		'view_items'            => __( 'View Briefings', 'alexa-fb' ),
		'search_items'          => __( 'Search Briefings', 'alexa-fb' ),
		'not_found'             => __( 'Not found', 'alexa-fb' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'alexa-fb' ),
		'featured_image'        => __( 'Featured Image', 'alexa-fb' ),
		'set_featured_image'    => __( 'Set featured image', 'alexa-fb' ),
		'remove_featured_image' => __( 'Remove featured image', 'alexa-fb' ),
		'use_featured_image'    => __( 'Use as featured image', 'alexa-fb' ),
		'insert_into_item'      => __( 'Insert into briefing', 'alexa-fb' ),
		'uploaded_to_this_item' => __( 'Uploaded to this briefing', 'alexa-fb' ),
		'items_list'            => __( 'Briefings list', 'alexa-fb' ),
		'items_list_navigation' => __( 'Briefings list navigation', 'alexa-fb' ),
		'filter_items_list'     => __( 'Filter briefings list', 'alexa-fb' ),
	);
	$rewrite = array(
		'slug'                  => 'briefing',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'Briefing', 'alexa-fb' ),
		'description'           => __( 'Alexa flash briefing', 'alexa-fb' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'comments', 'thumbnail', 'excerpt' ),
		'taxonomies'            => array( 'category'),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-microphone',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'rewrite'               => $rewrite,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
		'rest_base'             => 'briefing',
	);
	register_post_type( 'briefing', $args );
}


add_action( 'init', 'briefing_post_type', 0 );

register_activation_hook( __FILE__, 'active_hook' );

function active_hook() {
  
    flush_rewrite_rules();
}


//get the briefing posts and format for JSON and Amazon feed for API 
function init_api1( $data ) {
	//GET variables
	$prm = $data->get_params();
	$b_cat = $prm['category'];
	$numc = $prm['limit'];

	// Check for transient. If none, then execute WP_Query
	if ( false === ( $gg = get_transient( 'afb_cached_' . (empty($b_cat) ? 'all' : $b_cat) . (empty($numc) ? 1 : $numc) ) ) ) {

		$argss = array(
			'no_found_rows' => true,
			'post_status' => 'publish',
			'numberposts' => (empty($numc) ? 1 : $numc),
			'post_type'   => 'briefing'
		);

		if ( ! empty( $b_cat ) ) {
			$argss['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field' => 'term_id',
					'terms' => $b_cat,
				),
			);
		}

		$posts = get_posts( $argss );

		if ( empty( $posts ) ) {
			return null;
		}

		$gg = [];

		foreach($posts as $post){
			$response = array(
				'uid' => 'urn:uuid:' . wp_generate_uuid4( get_permalink( $post ) ),
				'updateDate' => get_post_modified_time( 'Y-m-d\TH:i:s.\0\Z', true, $post ),
				'titleText' => $post->post_title,
				'mainText' => '',
				'redirectionUrl' => get_permalink( $post ),
			);

			$cntnt = $post->post_content;

			preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $cntnt , $match);

			if(empty($match[0])){
				$response['mainText'] = wp_strip_all_tags( strip_shortcodes($post->post_content));
			} else {
				$response['streamUrl'] = esc_url_raw($match[0][0]);
			}

			array_push($gg, $response);
		};

		// if only one briefing, do not put out a multi item array
		if ( count( $gg ) === 1 ) {
			$gg = $gg[0];
		}
		// Put the results in a transient. Expire after 12 hours.
		set_transient( 'afb_cached_' . (empty($b_cat) ? 'all' : $b_cat), $gg, 1 * HOUR_IN_SECONDS );
	}
	return $gg;
}

//register api
add_action( 'rest_api_init', function () {
	register_rest_route( 'alexa-fb/v1', '/briefings/', array(
	'methods' => 'GET',
	'callback' => 'init_api1',
	));
	
} );


?>
