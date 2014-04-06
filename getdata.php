<?php
  /**
   * Plugin Name: GetData
   * Plugin URI: https://github.com/KrakeIO/getdata-for-wordpress
   * Description: Synchronize Wordpress with a data repository on GetData.IO .
   * Version: 1.0
   * Author: Gary Teh
   * Author URI: http://GetData.IO
   * License: GPL2
   */


  $object = new GetData();
  add_action('admin_menu',  array($object, 'addMenuItems'));

  class GetData {

    public function addMenuItems(){
        add_menu_page(
          'GetData', 
          'GetData', 
          'administrator', 
          __FILE__,
          array($this, 'readmePage')          
        );

        add_submenu_page( 
          __FILE__, 
          'Set DataSource', 
          'Set DataSource', 
          'administrator', 
          __FILE__ . '/set-data-source', 
          array($this, 'renderSettingsPage')
        );

        add_submenu_page( 
          __FILE__, 
          'Map Columns', 
          'Map Columns', 
          'administrator', 
          __FILE__ . '/map-data-columns', 
          array($this, 'renderMappingsPage')
        );
    }

    public function readmePage() {
      require( WP_PLUGIN_DIR . '/getdata/readme.php' );      
    }

    public function renderSettingsPage(){
      $this->saveSettings();
      require( WP_PLUGIN_DIR . '/getdata/set_data_source.php' );
    }

    public function renderMappingsPage(){
      $this->saveMappings();
      require( WP_PLUGIN_DIR . '/getdata/map_data_columns.php' );
    }

    public function saveSettings(){
      if(isset($_REQUEST['getdata_unique_datasource_id'])) {
        update_option('getdata_unique_datasouce_id', $_REQUEST['getdata_unique_datasource_id']);
      }
    }

    public function saveMappings(){
      if(
        isset($_REQUEST['product_name']) &&
        isset($_REQUEST['product_price']) &&
        isset($_REQUEST['product_image'])
        ) {      
          $data = array(
            'product_name'  => $_REQUEST['product_name'],
            'product_price' => $_REQUEST['product_price'],
            'product_image' => $_REQUEST['product_image']
          );
          update_option('getdata_mapping', serialize($data));
      }
    } 
   

  }