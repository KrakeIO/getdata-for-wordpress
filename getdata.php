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

      require( WP_PLUGIN_DIR . '/getdata/set_data_source.php' );
    }

    public function renderMappingsPage(){
      require( WP_PLUGIN_DIR . '/getdata/map_data_columns.php' );
    }

    public function saveSettings(){

    }

    public function getSettings(){

    }

    public function saveMappings(){

    } 

    public function getMapping(){

    }   

  }