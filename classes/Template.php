<?php
include_once FFRCRUD_PATH.'classes/Helper.php';

class Template {
  private $_pluginPath;

  public function __construct(){
    $this->_pluginPath = FFRCRUD_PATH;
  }

  // Display Add New Field Report Post in frontend
  public function getFrontEndTemplate( $atts ){
    $help = new Helper;
    // Show Create Post Form
    if($help->checkUser()) {
      ?> 
      <button id="toggle-fr-form" class="btn btn-primary mt-5 d-block mb-4 ml-auto mr-4 px-5">
        <span class="fr-btn-icon"><i class="fas fa-paper-plane"></i></span>
        <span class="fr-btn-icon d-none"><i class="fas fa-times"></i></span>
        <span class="fr-btn-text"> Add Report</span>
      </button>
      <?php
      include_once($this->_pluginPath.'includes/template/create_report_form.php');
      $this->createModal('editReport','includes/template/edit_post.php','modal-xl');
      $this->createModal('deleteReport','includes/template/delete_post.php','modal-md');
    } 
    // Show Field Reports in Grid and List Tab
    include_once($this->_pluginPath.'includes/template/tab.php');
  }

  // Display Field Report Shortcodes in Admin settings
  public function viewShortcodesInAdmin(){ ?>
    <h1>Field Report Shortcodes</h1>
    <div class="wrap">
      <label for="New Field Report Post">Frontend form to create field report</label>
      <br><p><code>[new_field_report_post]</code></p>
    </div>
  <?php }

  // Create Modal
  public function createModal($modalID,$modalContent,$modalSize = ""){ ?>
    <div class="modal fade" id="<?php echo $modalID; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $modalID; ?>Label" aria-hidden="true">
    <div class="modal-dialog <?php echo $modalSize; ?>" role="document">
    <div class="modal-content">
    <?php include_once($this->_pluginPath.$modalContent); ?>
    </div></div></div>
<?php }

  // Report List Template
  public function reportListTemplate($query){
    $help = new Helper;
    $html = '';
    while ( $query->have_posts() ) : $query->the_post();
    $postID = get_the_ID();
    $title = get_the_title();
    $url = get_the_permalink();
    $images = get_field('images',$postID); 
    $image = wp_get_attachment_image_src($images[0]['image'],'medium')[0];

    $html .= '<div class="col-12 mb-4 report'.$postID.' report-item">
        <div class="card">
          <div class="card-body d-flex px-2">';
            if($help->checkUser()) {
            $html .= '<div class="fr-action-wrap">
              <span class="fr-edit"
                data-post-id="'.$postID.'"
                data-post-title="'.$title.'"
                title="Edit"
                data-toggle="modal"
                data-target="#editReport">
                <i class="fas fa-pencil-alt"></i>
              </span>
              <span class="fr-delete"
                data-post-id="'.$postID.'"
                data-post-title="'.$title.'"
                title="Delete" 
                data-toggle="modal" 
                data-target="#deleteReport">
                  <i class="fas fa-times"></i>
              </span>
            </div>';
            }
            $html .= '<div class="fr-thumbnail col-3">
              <div class="fr-d-grid">';
              $images = get_field('images',$postID);
              $imageCount = count($images);
              $images = ($imageCount > 8 ) ? array_slice($images, 0, 8) : $images;
              foreach($images as $image) : 
              $img = wp_get_attachment_image_src($image['image'],'medium');
              $html .= '<div class="img-thumb-wrap">
              <img class="img-thumbnail" src="'.$img[0].'" alt="'.$title.'"></div>';
              endforeach; 
                if($imageCount > 7){
                  $html .= '<div class="img-thumb-wrap fr-link"><a href="'.$url.'">'.$imageCount.'+</a></div>';
                }           
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="fr-content col-9">
              <h5 class="card-title fr-link"><a href="'.$url.'">'.$title.'</a></h5>';
            $html .= '<p class="card-text fr-link">'.wp_trim_words( get_the_excerpt(), $num_words = 120, '... <a class="mt-3 float-right" href="'.$url.'"> Read More ></a>' ).'</p>
            </div>
          </div>
        </div>
      </div>';
    endwhile;
    return $html;
  }

  // Report Grid Template
  public function reportGridTemplate($query){
    $help = new Helper;
    $html = '';
    while ( $query->have_posts() ) : $query->the_post(); 
      $postID = get_the_ID();
      $title = get_the_title();
      $url = get_the_permalink();
      
      $html .= '<div class="col-4 mb-4 report'. $postID.' report-item">
        <div class="card">
          <div class="card-body p-4 position-relative">';
           if($help->checkUser()) {
            $html .= '<div class="fr-action-wrap">
              <span class="fr-edit"
                data-post-id="'.$postID.'"
                data-post-title="'.$title.'"
                title="Edit"
                data-toggle="modal"
                data-target="#editReport">
                <i class="fas fa-pencil-alt"></i>
              </span>
              <span class="fr-delete"
                data-post-id="'.$postID.'"
                data-post-title="'.$title.'"
                title="Delete" 
                data-toggle="modal" 
                data-target="#deleteReport">
                  <i class="fas fa-times"></i>
              </span>
            </div>';
            }
            $html .= '<div class="fr-d-grid">';
              $images = get_field('images',$postID);
              $imageCount = count($images);
              $images = ($imageCount > 8 ) ? array_slice($images, 0, 8) : $images;
              foreach($images as $image) : 
              $img = wp_get_attachment_image_src($image['image'],'medium');
              $html .= '<div class="img-thumb-wrap">
              <img class="img-thumbnail" src="'.$img[0].'" alt="'.$title.'"></div>';
              endforeach;
              if($imageCount > 7){
                $html .= '<div class="img-thumb-wrap fr-link"><a href="'.$url.'">'.$imageCount.'+</a></div>';
              }            
            $html .= '</div>';              
            $html .= '<h5 class="card-title fr-link"><a href="'.$url.'">'.$title.'</a></h5>';
            $html .= '<p class="card-text fr-link">'.wp_trim_words( get_the_excerpt(), $num_words = 25, '... <br><a class="mt-3 float-right" href="'.$url.'"> Read More ></a>' ).'</p>
          </div>
        </div>
      </div>';
    endwhile;

    return $html;
  }

}