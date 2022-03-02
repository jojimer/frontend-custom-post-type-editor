<?php
include_once FFRCRUD_PATH.'classes/Upload.php';
include_once FFRCRUD_PATH.'classes/Update.php';
include_once FFRCRUD_PATH.'classes/Delete.php';
include_once FFRCRUD_PATH.'classes/Helper.php';

class Control {
  private $_upload;
  private $_delete;
  private $_helper;
  private $_update;

  public function processRequest(){
    $this->_helper = new Helper;
    $this->_upload = new Upload($this->_helper);
    $this->_update = new Update($this->_helper,$this->_upload);
    $this->_delete = new Delete($this->_helper);
    
    if($this->_helper->checkUser() && isset($_POST['action_type'])){
      if($_POST['action_type'] === 'get') 
        $this->_helper->fetchReport($_POST['postID']);
      if($_POST['action_type'] === 'post') 
        $this->_upload->addReport();
      if($_POST['action_type'] === 'update') 
        $this->_update->updateReport($_POST['postID']);
      if($_POST['action_type'] === 'delete') 
        $this->_delete->deleteReport($_POST['postID']);
    }
  }
}