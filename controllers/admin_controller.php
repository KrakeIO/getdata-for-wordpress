<?php
/**
 * Manages the different views in the admin panel
 */

class AdminController {

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

      $this->registerStyleSheets();
      $this->registerJavascripts();
  }

  public function registerStyleSheets(){
    wp_register_style( 'GetDataMapDataColumnsStyle', plugins_url('/getdata/assets/stylesheets/map_data_columns.css') );
  }

  public function registerJavascripts(){
    wp_register_script( 'GetDataMapDataColumnsScript', plugins_url('/getdata/assets/javascripts/map_data_columns.js') );
  }

  public function readmePage() {
    require( WP_PLUGIN_DIR . '/getdata/views/readme.php' );      
  }

  public function renderSettingsPage(){
    $this->saveSettings();
    require( WP_PLUGIN_DIR . '/getdata/views/set_data_source.php' );
  }

  public function renderMappingsPage(){
    $this->saveMappings();    
    require( WP_PLUGIN_DIR . '/getdata/views/map_data_columns.php' );
  }

  public function saveSettings(){
    if(isset($_REQUEST['getdata_unique_datasource_id'])) {
      update_option('getdata_unique_datasouce_id', $_REQUEST['getdata_unique_datasource_id']);
    }
  }

  public function saveMappings(){
    if(isset($_POST["get_data"])) {
        $data = $_POST["get_data"];
        update_option('getdata_mapping', serialize($data));
    }
  } 

}

?>