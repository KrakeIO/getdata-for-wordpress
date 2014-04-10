<?php

class DataController {

  const POST_KEY = 'post_title';

  public function __construct($krake_data_unique_id, $batch) {
    $this->handle       = $krake_data_unique_id;
    $this->batch        = $batch;
    $this->krake_client = new KrakeClient($this->handle);
    $this->sychronize();
  }

  public function sychronize() {
    $this->additions      = array();
    $this->deletions      = array();
    $this->modifications  = array();
    $this->hasValidHandle();
    $this->hasMapping();
    $this->loginDefaultUser();
    $this->processDeletions();
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

  public function loginDefaultUser() {
    global $current_user;
    $user_id = $this->data_mapping["default_user_id"];
    if(!isset($user_id)) {
      $this->returnJson(
        'failed',
        'default user not indicated',
        'default user not indicated' 
      );
    }
    $current_user = get_user_by( 'id', $user_id );
  }  

  public function processAdditions() {
    $records_add = $this->krake_client->getBatchAdditions($this->batch);
    foreach($records_add as $record) {

      $post                 = $this->getPostArray($record);
      $record               = (array) $record;
      $post_title           = $record[ $this->data_mapping['post_title'] ];
      $existing_post_id     = $this->findPostId( $post_title );
      $post['post_status']  = $this->data_mapping['default_post_status'];

      if(isset($existing_post_id)) {
        $post['ID'] = $existing_post_id;
        $this->updatePost($post);

      } else {
        $post['ID'] = $this->insertPost($post);

      }
      
      $this->updatePostMeta($post['ID'], $record);      
    }

  }

  public function processModifications() {
    $records_mod = $this->krake_client->getBatchModifications($this->batch);
    foreach($records_mod as $record) {

      $post                 = $this->getPostArray($record);
      $record               = (array) $record;
      $post_title           = $record[ $this->data_mapping['post_title'] ];
      $existing_post_id     = $this->findPostId( $post_title );
      $post['post_status']  = $this->data_mapping['default_post_status'];

      if(isset($existing_post_id)) {
        $post['ID'] = $existing_post_id;
        $this->updatePost($post);

      } else {
        $post['ID'] = $this->insertPost($post);

      }

      $this->updatePostMeta($post['ID'], $record);

    }
  }  

  public function processDeletions() {
    $records_del = $this->krake_client->getBatchDeletions($this->batch);
    foreach($records_del as $record) {

      $post                 = $this->getPostArray($record);
      $record               = (array) $record;
      $post_title           = $record[ $this->data_mapping['post_title'] ];
      $existing_post_id     = $this->findPostId( $post_title );
      $post['post_status']  = $this->data_mapping['deleted_post_status'];
      if(isset($existing_post_id)) {
        $post['ID'] = $existing_post_id;
        $this->updatePost($post);
      }
    }
  }

  public function updatePostMeta($post_id, $record) {
    $meta_keys = array_keys($this->data_mapping['post_meta']);
    foreach($meta_keys as $meta_key) {
      update_post_meta($post_id, $meta_key, $record[$meta_key]); 
    }

  }

  public function getPostTitleColumn() {
    return $this->data_mapping[DataController::POST_KEY];
  }

  public function findPostId($title) {
    $post = get_page_by_title($title, OBJECT, $this->data_mapping['post_type']);
    if(!isset($post)) {
      return NULL;

    } else {
      return $post->ID;

    }
  }

  public function getPostArray($data_record_row) {
    $data_record_row = (array) $data_record_row;

    $post = array(
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

  public function insertPost($post) {
    return wp_insert_post($post, true);

  }

  public function updatePost($post) {
    if(in_array($post['post_status'], array('draft', 'pending', 'auto-draft'))) {
      $post['post_date_gmt'] = '0000-00-00 00:00:00';

    } else if ($post['post_status'] == 'publish' ) {
      $post['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', strtotime('now') );

    }

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