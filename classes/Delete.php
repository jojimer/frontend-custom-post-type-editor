<?php

class Delete {
  private $_help;

  function __construct($helper){
    $this->_help = $helper;
  }

  public function deleteFieldReport($id) {
    return (wp_delete_post( $id )) ? $id : false;
  }
}