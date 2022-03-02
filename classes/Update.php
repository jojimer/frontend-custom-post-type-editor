<?php

class Update {
  private $_urlPath;
  private $_help;
  private $_upload;
  public function __construct($helper,$upload){
    $this->_urlPath = ABSPATH;
    $this->_help = $helper;
    $this->_upload = $upload;
  }

  public function updateReport($id){
      $title = wp_strip_all_tags($_POST['post_title']);
      $excerpt =  wp_strip_all_tags($_POST['post_caption']);
      $deleteImages = explode(',',$_POST['delete_images']);
      $tags = explode(',', preg_replace("/\s*,\s*/", ",", $_POST['post_tags']));
      $nothing_change = $_POST['nothing_change'];

      //ADD IMAGES
      if(isset($_FILES['images']) && count($_FILES['images']) != 0){
        $this->_upload->addImagesToACFRow($_FILES['images'],$id);
      }

      // DELETE IMAGES
      if(count($deleteImages)){
        foreach($deleteImages as $index){
          delete_row('images', $index, $id);
        }
      }

      // EDIT FIELD REPORT TEXT INFO
      if($nothing_change){
        $postarr = array(
          "ID" => $id,
          "post_title" => $title,
          "post_excerpt" => $excerpt
        );      

        $result = wp_update_post($postarr,true);
        wp_set_object_terms( $id, $tags, 'tags' );
        if (is_wp_error($post_id)) {
            $errors = $post_id->get_error_messages();
            foreach ($errors as $error) {
                echo $error;
            }
        }

        echo $result;
      }else{
        echo 'Nothing Change';
      }
  }
}