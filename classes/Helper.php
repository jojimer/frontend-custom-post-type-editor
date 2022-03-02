<?php
include_once FFRCRUD_PATH.'classes/Template.php';

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

  // Fetch Single Report
  public function fetchSingleReport($id) {
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

  // Fetch Paginated Report
  public function fetchPaginatedReport($paged = 1, $profileUser = 0){
    $profileUser = ($profileUser === 0) ? $this->profileUser->data->ID : $profileUser;
    $query = $this->prepareReport($paged,$profileUser);
    $template = new Template;
    $pagination = $this->report_pagination($query,$paged);
    $pagination = str_replace('<a','<span',$pagination);
    $pagination = str_replace('a>','span>',$pagination);
    $result = array(
      "list" => $template->reportListTemplate($query),
      "grid" => $template->reportGridTemplate($query),
      "pagination" => $pagination
    );

    echo json_encode($result);
  }

  // Prepare Report Argument and Paginated Post
  public function prepareReport
  ($paged = 1, $profileUser = 0){
    $profileUser = ($profileUser === 0) ? $this->profileUser->data->ID : $profileUser;
    $args = [
      'paged' => $paged,
      'author' => $profileUser,
      'post_type' => 'field-report',
      'order' => 'DESC',
      'posts_per_page' => 8
    ];

    return new WP_Query($args);
  }

  // Create Pagination with number
  public function report_pagination($query,$current_page = 1) {
    $big = 9999999; // need an unlikely integer
    return paginate_links( array(
     'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
     'format' => '?paged=%#%',
     'current' => max( 1, $current_page),
     'total' => $query->max_num_pages) );
  }
}