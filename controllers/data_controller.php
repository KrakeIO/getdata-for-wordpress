<?php

class DataController {

  const POST_KEY = 'post_title';

  public function __construct($krake_data_unique_id, $batch) {
    $this->handle = $krake_data_unique_id;
    $this->batch = $batch;
    $this->krake_client = new KrakeClient($this->handle);
    $this->sychronize();
  }

  public function sychronize() {
    $this->hasValidHandle();
    $this->hasMapping();
    $this->processAdditions();
    $this->processModifications();
    $this->returnJson(
      "success", 
      array(
        "result" => "records synchronized",
        "batch"   => $this->batch
      ),
      "records synchronized : ".$this->batch);

    die();
  }

  public function hasValidHandle() {
    $UNIQUE_ID = get_option('getdata_unique_datasouce_id');
    if(!isset($UNIQUE_ID) || strlen($UNIQUE_ID) == 0) {
      $this->returnJson(
        'failed',
        'krake_data_unique_id is not set on server',
        'krake_data_unique_id is not set on server'
      );

    } else if($UNIQUE_ID != $this->handle) {
      $this->returnJson(
        'failed',
        'krake_data_unique_id not match',
        'krake_data_unique_id not match. WebHook handle: '.$this->handle.'. Saved Handle'.$UNIQUE_ID 
      );

    } else {
      return true;

    }
    
  }

  public function hasMapping() {
    $getdata_mapping = get_option('getdata_mapping');

    if($getdata_mapping) {
      $this->data_mapping = unserialize( $getdata_mapping );
      return true;

    } else {
      $this->returnJson(
        'failed',
        'columns not mapped yet',
        'columns not mapped yet' 
      );  
    }
  }

  public function processAdditions() {
    $records_add = $this->krake_client->getBatchAdditions($this->batch);
    foreach($records_add as $record) {
      $post = $this->getPostArray($record);  
    }

  }

  public function processModifications() {
    $records_mod = $this->krake_client->getBatchModifications($this->batch);
    foreach($records_mod as $record) {

    }
  }  

  public function processDeletions() {
    $records_del = $this->krake_client->getBatchDeletions($this->$batch);
  }

  public function getPostTitleColumn() {
    return $this->data_mapping[DataController::POST_KEY];
  }

  public function findPostId($title) {

  }

  public function getPostArray($data_record_row) {
    $data_record_row = (array) $data_record_row;

    $post = array(
      // 'ID'             => 37, // Are you updating an existing post?
      'post_content'   => $data_record_row[ $this->data_mapping['post_content'] ],
      'post_name'      => $data_record_row[ $this->data_mapping['post_title'] ],
      'post_title'     => $data_record_row[ $this->data_mapping['post_title'] ],
      // 'post_status'    => [ 'draft' | 'publish' | 'pending'| 'future' | 'private' | custom registered status ] // Default 'draft'.
      'post_type'      => $this->data_mapping['post_type'], // [ 'post' | 'page' | 'link' | 'nav_menu_item' | custom post type ] // Default 'post'.
      'post_author'    => 1, // The user ID number of the author. Default is the current user ID.
      'ping_status'    => 'open', // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
      'post_excerpt'   => $data_record_row[ $this->data_mapping['post_excerpt'] ] // For all your post excerpt needs.
    );

    return $post;      

  }

  public function insertPost($data_record_row) {
    $post = $this->getPostArray($data_record_row);  
    $insert_error = wp_insert_post($post, true);

  }

  public function updatePost($post_id, $data_record_row) {
    $post = $this->getPostArray($data_record_row);  
    $post["ID"] = $post_id;
    wp_update_post( $post );

  }

  public function returnJson($status, $res_msg, $err_msg) {
    error_log($err_msg);    
    header('Content-Type: application/json');    
    echo json_encode(array(
      'status' => $status, 
      'message' => $res_msg
    ));
    die();    
  }
}

?>