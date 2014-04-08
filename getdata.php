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

  require( WP_PLUGIN_DIR . '/getdata/lib/krake_client.php' );
  require( WP_PLUGIN_DIR . '/getdata/controllers/admin_controller.php' );
  require( WP_PLUGIN_DIR . '/getdata/controllers/data_controller.php' );

  function processWebhookEvent() {

    if( isset($_POST) && 
      isset($_POST["krake_handle"]) && 
      isset($_POST["batch_time"]) &&
      isset($_POST["event_name"]) == "complete") {

        $object = new DataController($_POST["krake_handle"], $_POST["batch_time"]);

    }        
    
  }

  $object = new AdminController();
  add_action('admin_menu',  array($object, 'addMenuItems'));    
  add_action('init', 'processWebhookEvent');  