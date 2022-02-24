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
    if(is_user_logged_in() && in_array( 'vip-member', (array) $this->currentUser->roles ) && $this->profileUser->data->ID == $this->currentUser->ID) return true;

    return false;
  }
}