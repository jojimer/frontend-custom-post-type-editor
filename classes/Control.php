<?php
include_once FFRCRUD_PATH.'classes/Upload.php';
include_once FFRCRUD_PATH.'classes/Delete.php';
include_once FFRCRUD_PATH.'classes/Helper.php';

class Control {
  private $_upload;
  private $_delete;
  private $_helper;

  public function processRequest(){
    $this->_helper = new Helper;
    $this->_upload = new Upload($this->_helper);
    $this->_delete = new Delete($this->_helper);
    
    if($this->_helper->checkUser() && isset($_POST['action_type'])){
      if($_POST['action_type'] === 'post') 
        $this->_upload->addpost();
      if($_POST['action_type'] === 'delete') 
        $this->_delete->deleteFieldReport($_POST['postID']);
    }
  }
}