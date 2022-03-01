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
}