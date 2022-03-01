<?php

class Upload {
	private $_urlPath;
	private $_help;
	public function __construct($helper){
    $this->_urlPath = ABSPATH;
    $this->_help = $helper;
  }

	// Create New Field Report Post
	function addReport() {
	    $results = '';
	    $post_id = null;
	 		
	 		if($this->_help->checkUser() && isset($_POST['post_title']) && isset($_POST['post_caption']) && isset($_POST['post_tags']) && $_FILES['images']) :

		    $title = wp_strip_all_tags($_POST['post_title']);
		    $excerpt =  wp_strip_all_tags($_POST['post_caption']);
		    $author = $this->_help->currentUser->ID;
		    $tags = explode(',', preg_replace("/\s*,\s*/", ",", $_POST['post_tags']));
		 	//die("Test Javascript Stop Posting Dummy"); // This line is for testing only, this will stop user from posting dummy
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
	        $results = 'Field Report Added';
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
	        require_once($this->_urlPath . 'wp-admin/includes/file.php' );
	    }
	    $file_return = wp_handle_upload( $file, array('test_form' => false ) );
	    
	    if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
	        return $file_return['error'].' '.$file_return['upload_error_handler'];
	    } else {
	        $filename = $file_return['file'];
	        $file_return['url'] = (str_contains($file_return['url'], 'https://')) ? str_replace('https://'.$_SERVER['HTTP_HOST'].'/app/uploads/', '', $file_return['url']) : str_replace('http://'.$_SERVER['HTTP_HOST'].'/app/uploads/', '', $file_return['url']);
	        $attachment = array(
	            'post_mime_type' => $file_return['type'],
	            'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
	            'post_content' => '',
	            'post_status' => 'inherit',
	            'guid' => $file_return['url']
	        );
	        $attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );
	        require_once($this->_urlPath . 'wp-admin/includes/image.php');
	        $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
	        wp_update_attachment_metadata( $attachment_id, $attachment_data );
	        if( 0 < intval( $attachment_id ) ) {
	          return $attachment_id;
	        }
	    }
	    return false;
	}
}