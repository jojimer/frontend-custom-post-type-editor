<?php

/* 
	Plugin Name: Field Report CRUD
	Description: Frontend Field Report CRUD
	Version: 1.0.0
	Author: Jojimer Castino
	Author URI: jojimercastino.com
*/

if( ! defined('ABSPATH')) exit(); // No direct Access Allowed

/**
 * Define Plugins Constant
 * 
*/
define('FFRCRUD_PATH',trailingslashit(plugin_dir_path(__FILE__)));
define('FFRCRUD_URL',trailingslashit(plugins_url('/',__FILE__)));

class FieldReportShortcodes {
	private $_user;

	function __construct(){
		add_action('admin_menu', [$this, 'pluginSettings']);
		add_action( 'wp_enqueue_scripts', [$this, 'load_scripts'] );
		add_shortcode( 'new_field_report_post', [$this, 'getFrontEndTemplate'] );
		// creating Ajax call for WordPress
		add_action( 'wp_ajax_nopriv_fr_addpost', [$this, 'fr_addpost'] );
		add_action( 'wp_ajax_fr_addpost', [$this, 'fr_addpost'] );
	}

	// Add Plugin Page on settings menu
	function pluginSettings() {
		add_options_page('Frontend Field Report Shortcodes','Field Report Shortcodes','manage_options','frontend-field-report-shortcodes',[$this,'returnShortcodes']);
	}

	// Check if user is logged-in and a VIP member
	function checkUser() {
		$this->_user = wp_get_current_user();
		if(is_user_logged_in() && in_array( 'vip-member', (array) $this->_user->roles )) return true;
	}

	// Load JS script from React	
	function load_scripts() {
	    wp_enqueue_script( 'frontend-fr-crud', FFRCRUD_URL . 'includes/index.js', [ 'jquery' ], null, true );
	    wp_enqueue_style('main_css',FFRCRUD_URL . 'includes/main.css',false,null);
	    $ajax_params = array(
	        'ajax_url' => admin_url('admin-ajax.php'),
	        'ajax_nonce' => wp_create_nonce('my_nonce'),
	    );
	    wp_localize_script( 'frontend-fr-crud', 'fr_crudajax', $ajax_params );
	}

	// Create New Field Report Post
	function fr_addpost() {
	    $results = '';
	    $post_id = null;
	 		
	 		if($this->checkUser() && isset($_POST['post_title']) && isset($_POST['post_caption']) && isset($_POST['post_tags']) && $_FILES['images']) :

		    $title = wp_strip_all_tags($_POST['post_title']);
		    $excerpt =  wp_strip_all_tags($_POST['post_caption']);
		    $author = $this->_user->ID;
		    $tags = explode(',', preg_replace("/\s*,\s*/", ",", $_POST['post_tags']));
		 		die("Testing Javascript");
		    $post_id = wp_insert_post( array(
		        'post_title'        => $title,
		        'post_type'			=> 'field-report',
		        'post_excerpt'      => $excerpt,
		        'post_status'       => 'publish',
		        'post_author'       => $author
		    ) );

		    // Upload Images	
		    $files = $_FILES['images'];	    
		    foreach( $files['name'] as $key => $val )
		    {
		    			$file = array(
		    				"name" => $files["name"][$key],
		    				"type" => $files["type"][$key],
		    				"tmp_name" => $files["tmp_name"][$key],		    				
		    				"size" => $files["size"][$key],
		    				"error" => $files["error"][$key],
		    			);
		          if( is_array( $file ) ) {
		                $attach_id = $this->upload_user_file($file);
		                $row = array(
										    'field_61649f14fe95e' => $attach_id,
										    'field_616595a8df104'   => ''
										);

										add_row('field_61649f05fe95d', $row, $post_id);
		          }
		    }
		  endif;
	 
	    if ( $post_id != 0 && $post_id != null)
	    {
	    		wp_set_object_terms( $post_id, $tags, 'tags' );
	        $results = 'Field Report Added.';
	    }
	    else {
	        $results = 'Error occurred while adding the post!';
	    }
	    // Return the String
	    die($results);
	}

	//File Upload
	function upload_user_file( $file = array() ) {
			if ( ! function_exists( 'wp_handle_upload' ) ) {
	        require_once( ABSPATH . 'wp-admin/includes/file.php' );
	    }
	    $file_return = wp_handle_upload( $file, array('test_form' => false ) );
	    
	    if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
	        return $file_return['error'].' '.$file_return['upload_error_handler'];
	    } else {
	        $filename = $file_return['file'];
	        $file_return['url'] = str_replace($_SERVER['HTTP_HOST'].'/app/uploads/', '', $file_return['url']);
	        $attachment = array(
	            'post_mime_type' => $file_return['type'],
	            'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
	            'post_content' => '',
	            'post_status' => 'inherit',
	            'guid' => $file_return['url']
	        );
	        $attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );
	        require_once(ABSPATH . 'wp-admin/includes/image.php');
	        $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
	        wp_update_attachment_metadata( $attachment_id, $attachment_data );
	        if( 0 < intval( $attachment_id ) ) {
	          return $attachment_id;
	        }
	    }
	    return false;
	}

	// Shortcodes
	// Display Add New Field Report Post in frontend
	function getFrontEndTemplate( $atts ){
		// Show Create Post Form
		if($this->checkUser()) include_once(FFRCRUD_PATH.'includes/create_post.php');
		// Show Field Reports in Grid and List Tab
		include_once(FFRCRUD_PATH.'includes/tab.php');
	}

	// Display Field Report Shortcodes in Admin settings
	function returnShortcodes(){ ?>
		<h1>Field Report Shortcodes</h1>
		<div class="wrap">
			<label for="New Field Report Post">Frontend form to create field report</label>
			<br><p><code>[new_field_report_post]</code></p>
		</div>
	<?php }
}

$fieldReportsShortcode = new FieldReportShortcodes;