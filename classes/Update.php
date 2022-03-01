<?php

class Update {
  private $_urlPath;
  private $_help;
  public function __construct($helper){
    $this->_urlPath = ABSPATH;
    $this->_help = $helper;
  }

  public function updateReport($id){
      $title = wp_strip_all_tags($_POST['post_title']);
      $excerpt =  wp_strip_all_tags($_POST['post_caption']);
      $tags = explode(',', preg_replace("/\s*,\s*/", ",", $_POST['post_tags']));

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
  }
}