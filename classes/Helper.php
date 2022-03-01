<?php

class Helper {  
  public $currentUser;
  public $profileUser;

  function __construct(){
    $this->currentUser = wp_get_current_user();
    $this->profileUser = uwp_get_displayed_user();
  }

  // Check if user is logged-in and a VIP member
  public function checkUser() {
    if(is_user_logged_in() && (in_array( 'vip-member', (array) $this->currentUser->roles ) || in_array( 'administrator', (array) $this->currentUser->roles )) && $this->profileUser->data->ID == $this->currentUser->ID) return true;

    return false;
  }

  // Fetch Report
  public function fetchReport($id) {
    if($this->checkUser()){
      // Create WP Query to fetch single fiel report for edit
      $post = get_post($id);
      $tags = wp_get_object_terms( $id, 'tags', array('fields' => 'names') );
      $images = get_field('images',$id);
      $imageArray = array();

      foreach($images as $key => $image){
        $imageArray[$key]['id'] = $image['image'];
        $attachment = wp_get_attachment_image_src($image['image'],'thumbnail');
        if(is_array($attachment)) 
          $imageArray[$key]['thumbnail'] = $attachment[0];
      }

      $data = array(
        "ID" => $id,
        "title" => $post->post_title,
        "excerpt" => $post->post_excerpt,
        "tags" => $tags,
        "images" => $imageArray
      );
      echo json_encode($data);
    }
  }
}