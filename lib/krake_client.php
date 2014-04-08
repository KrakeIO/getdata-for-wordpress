<?php
  class KrakeClient {

    const DATA_EXPORT_SERVER  = 'http://data.getdata.io/';
    const DATA_FORMAT         = 'json';

    public function __construct($krake_data_unique_id) {
      $this->handle = $krake_data_unique_id;
    }

    // Gets the new records that were added to repository since previous batch
    // If no index_columns were indicated returns and empty array
    // $batch:String
    public function getBatchAdditions($batch) {
      if(!$this->usesIndex()) {
        return array();
      }

      $query_obj = array(
        '$where' => array(
          0 => array(
            'createdAt' => $batch
          )
        )
      );

      $query_string = json_encode($query_obj);
      $data_feed_location = $this->getDataFeedPath($query_string);
      $raw_response = $this->getData($data_feed_location);
      return json_decode($raw_response);
    }



    // That were modification since the previous batch
    // If no index_columns were indicated returns and empty array
    // $batch:String
    public function getBatchModifications($batch) {

      if(!$this->usesIndex()) {
        return array();
      }

      $previous_batch = $this->getPreviousBatch($batch);
      if(!$previous_batch) {
        return array();
      }      

      $query_obj = array(
        '$where' => array(
          0 => array(
            'pingedAt' => $batch,            
            'updatedAt' => $previous_batch
          )
        )
      );

      $query_string = json_encode($query_obj);
      $data_feed_location = $this->getDataFeedPath($query_string);
      $raw_response = $this->getData($data_feed_location);
      return json_decode($raw_response);
    }



    // Gets the priorly existing records in the previous batch that no longer exist in this one
    // $batch:String
    public function getBatchDeletions($batch) {

      if(!$this->usesIndex()) {
        return array();
      }

      $previous_batch = $this->getPreviousBatch($batch);
      if(!$previous_batch) {
        return array();
      }

      $query_obj = array(
        '$where' => array(
          0 => array(
            'pingedAt' => $previous_batch
          )
        )
      );

      $query_string = json_encode($query_obj);
      $data_feed_location = $this->getDataFeedPath($query_string);
      $raw_response = $this->getData($data_feed_location);
      return json_decode($raw_response);

    }



    // Gets the previous batch given a $batch value
    // return false if previous batch could not be found
    public function getPreviousBatch($batch) {
      $batches = $this->getBatches();
      for($x = 0; $x < count($batches); $x++) {
        $batches[$x]->pingedAt = preg_replace("/\+[0-9]{2}/", '', $batches[$x]->pingedAt); 
        if( $batches[$x]->pingedAt == $batch && isset($batches[$x + 1]) ) {
          return $batches[$x + 1]->pingedAt;
        }
      }
      return false;
    }



    // Checks if the data source uses index in its schema
    public function usesIndex() {
      $columns = $this->getColumns();
      if(count($columns->index_columns) == 0) {
        return false;

      } else {
        return true;

      }
    }



    // Gets all the records belonging to a batch
    // $batch:String -  example: 2014-02-26 03:43:16+00
    // $records_per_page:Integer - 10
    // $page_num:Integer - 0
    public function getBatchRecords($batch, $records_per_page = 10, $page_num = 0 ) {

      $page_num  = $page_num || 0;
      $offset    = $records_per_page * $page_num;
      $query_obj = array(
        '$where' => array(
          0 => array(
            'pingedAt' => $batch
          )
        ),
        '$limit' => $records_per_page,
        '$offset'  => $offset
      );

      $query_string = json_encode($query_obj);
      $data_feed_location = $this->getDataFeedPath($query_string);
      $raw_response = $this->getData($data_feed_location);
      return json_decode($raw_response);
    }    



    // Gets the batches of records available in this data feed
    // returns Array
    public function getBatches() {
      $query_obj = array(
        '$select' => array(
          0 => array(
            '$distinct' => 'pingedAt'
          )
        ),
        '$order' => array(
          0 => array (
            '$desc' => 'pingedAt'
          )
        ),
        '$fresh' => 'true'
      );
      $query_string = json_encode($query_obj);
      $data_feed_location = $this->getDataFeedPath($query_string);
      $raw_response = $this->getData($data_feed_location);
      return json_decode($raw_response);
    }



    // Gets the data feed end point to get data for the data repository specified
    public function getDataFeedPath($query_string) {
      return KrakeClient::DATA_EXPORT_SERVER . $this->handle . '/' . KrakeClient::DATA_FORMAT . '/?q=' . urlencode($query_string);
    }



    // Gets the columns that will be returned with each record
    public function getColumns() {
      $data_feed_location = KrakeClient::DATA_EXPORT_SERVER . $this->handle . '/schema';
      $raw_response = $this->getData($data_feed_location);
      return json_decode($raw_response);      
    }    



    // Gets the raw STRING response from the Data Export Server 
    public function getData($data_feed_url) {
      $ch = curl_init();
      $timeout = 5;
      curl_setopt($ch, CURLOPT_URL, $data_feed_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
      $data = curl_exec($ch);
      curl_close($ch);
      return $data;    
    }

  }


?>