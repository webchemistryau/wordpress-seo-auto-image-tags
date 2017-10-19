<?php
/*
* Plugin Name: SEO Friendly Clean ALT Tags
* Plugin URI: http://www.menkom.com.au/
* Description: Auto generate clean ALT tags & Title tags for your images as they are uploaded. Automatically removes hyphens, periods and other characters to generate clean alt tag names.
* Version: 1.0
* Author: Menkom
* Author URI: http://www.menkom.com.au/
* Text Domain: seo-friendly-clean-alt-tags
* License: GPL2
*/

defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

include_once('admin/class-seo-image-settings.php');

/**
* Register and enqueue jQuery files to run on frontend, enqueue on admin_init
*/
add_action( 'init', 'register_siat_scripts' );


function register_siat_scripts(){
	/*wp_register_script( 'siat_scripts', plugins_url('inc/siat-scripts.js', __FILE__), array('jquery'));
	wp_register_style( 'siat_styles', plugins_url('inc/siat-styles.css', __FILE__));
	wp_enqueue_script( 'siat_scripts' );
	wp_enqueue_style( 'siat_styles' );	Removed to test if it actually provides any value. */
}

//add_action( 'admin_notices', 'display_activation_notice' );

function display_activation_notice(){
	//if (is_plugin_active('seo-image-alt-tags/seo-image-alt-tags.php')){
		echo '<div id="error" class="error notice is-dismissible"><p><b>SEO Friendly Clean ALT Tags</b> may not be completely up to date. <a href="tools.php?page=seo-friendly-clean-alt-tags">Click here</a> to configure settings and update database.</div>';
	//}
}

add_filter( 'plugin_action_links', 'seo_image_settings_link', 10, 5 );

function seo_image_settings_link( $actions, $plugin_file ){
	static $plugin;
	if (!isset($plugin)) $plugin = plugin_basename(__FILE__);

	if ($plugin == $plugin_file){
		$settings = array('settings' => '<a href="tools.php?page=seo-friendly-clean-alt-tags">' . __('Settings', 'General') . '</a>');
		$actions = array_merge($settings, $actions);
	}
	return $actions;
}

/**
* Copy image title and save to Alt text field when image is uploaded. Runs anytime
* an image is uploaded, automatically.
*/
add_filter('add_attachment', 'insert_image_alt_tag', 10, 2);

function insert_image_alt_tag($post_ID){
	$clean_title = pathinfo(get_attached_file($post_ID),PATHINFO_FILENAME);
	//Make alphanumeric (removes all other characters)
	$clean_title = trim(preg_replace("/[^[:alnum:]]+/"," ", $clean_title));
	//Convert to title case
	$clean_title = ucwords(strtolower($clean_title));
	
	//Replace the Title with the new clean title we created
	$my_post = array(
		'ID'			=>  $post_ID,
		'post_title'	=>  $clean_title,
	);
	wp_update_post($my_post);
		//Updates the alt tag to be the same as the title
	if ( ! add_post_meta( $post_ID, '_wp_attachment_image_alt', $clean_title, true ) ){
		update_post_meta ( $post_ID, '_wp_attachment_image_alt', $clean_title );
	} 
}

/**
* Getting all posts that are attachments (images included) and adds the the
* alt text meta data to the image based on the title of the post
*/
function batch_update_image_tags($is_update){
	$total = 0;
	$created = 0;
	$updated = 0;
	$deleted = 0;

	$args = array(
		'post_type' => 'attachment',
		'numberposts' => -1,
		'post_status' => null,
		'post_parent' => null, // any parent
		);

	//Get all attachment posts
		$attachments = get_posts($args);

	//if there are posts
		if ($attachments){
			$image_mime = 'image';
			//Loop thru each attachment
			foreach ($attachments as $post){
			//get post data ready,set title var to post title
				setup_postdata($post);
				$title = get_the_title($post->ID);
				$type = get_post_mime_type($post->ID);
				$tag = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );
				$tag_str = strval($tag);
				$tag_len = strlen($tag_str);
				//echo $type;
				if (strpos($type, $image_mime) !== false){

					if ( $is_update == True ){
						//if has post meta for alt tag, update it else add it.
						if (! add_post_meta( $post->ID, '_wp_attachment_image_alt', $title, true )){
							if ((empty($tag) || (($tag_len <= 2 ) && ($tag_str !== $title)))){
								insert_image_alt_tag($post->ID);
									$updated++;
							}
						} else {
							insert_image_alt_tag($post->ID);
							$created++; //update counter
						}
					} else {
						//if has post meta for alt tag, update it else add it.
						if (! empty($tag) ){
							delete_post_meta($post->ID, '_wp_attachment_image_alt', $title);
							$deleted++; //update counter
						} //end add_post_meta
					}
					$total++;
				} //end of image_mime

		} //end foreach

	} //end attachments

	$count = array(
		'total' => $total,
		'created' => $created,
		'updated' => $updated,
		'deleted' => $deleted
	);

	//count of files updated
	return $count;
}
