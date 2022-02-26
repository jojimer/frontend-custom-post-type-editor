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
    if($help->checkUser()) include_once($this->_pluginPath.'includes/template/create_post.php');
    // Show Field Reports in Grid and List Tab
    include_once($this->_pluginPath.'includes/template/tab.php');
  }

  // Display Field Report Shortcodes in Admin settings
  function viewShortcodesInAdmin(){ ?>
    <h1>Field Report Shortcodes</h1>
    <div class="wrap">
      <label for="New Field Report Post">Frontend form to create field report</label>
      <br><p><code>[new_field_report_post]</code></p>
    </div>
  <?php }
}