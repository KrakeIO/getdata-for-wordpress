<?php

  wp_enqueue_style( 'GetDataMapDataColumnsStyle' );
  wp_enqueue_script( 'GetDataMapDataColumnsScript' );

  $UNIQUE_ID = get_option('getdata_unique_datasouce_id');
  if(!isset($UNIQUE_ID) || strlen($UNIQUE_ID) == 0) {
    ?>
      <h1>Data Source is not set</h1>
      <a href='admin.php?page=getdata/controllers/admin_controller.php/set-data-source'>Proceed here to do so</a>
    <?php
    return; 
  }


  // 1_df30aaa36f078d501bae3f5ff10d78c7eses
  $UNIQUE_ID = get_option('getdata_unique_datasouce_id');
  $krake_client   = new KrakeClient($UNIQUE_ID);
  $data_schema    = $krake_client->getColumns();
  $data_columns   = $data_schema->columns;
  $url_columns    = $data_schema->url_columns;
  $index_columns  = $data_schema->index_columns;


  $getdata_mapping = get_option('getdata_mapping');

  if($getdata_mapping) {
    $getdata_mapping = unserialize( $getdata_mapping );

  } else {

    $getdata_mapping = array(
      'post_content'  => false,      
      'post_title'    => false,
      'post_type'     => false,
      'post_excerpt'  => false      
    );
  }

  $SUPPORTED_POST_TYPE = array( 'listing_type', 'page', 'post', 'revision' );
  $SUPPORTED_POST_STATUS = array( 'draft', 'publish', 'pending', 'future', 'private' );

  $all_users = get_users( array() );

?>

<h1>DataSource Mappings</h1>
<form method='post' action='admin.php?page=getdata/controllers/admin_controller.php/map-data-columns'>

  <input type='submit' value="save mappings">  
  <h2 class='section-header'>User Settings</h2>
  <table>
    <tr>
      <td>Default User (Required)</td>
      <td>
        <select name='get_data[default_user_id]'>
          <?php foreach( $all_users  as $user) { ?>
            <option <?php if($getdata_mapping["default_user_id"] == $user->ID) echo "selected"; ?> value="<?php echo $user->ID; ?>">
              <?php echo $user->user_login; ?>
            </option>
          <?php } ?>
        </select>
      </td>
    </tr>
  </table>

  <h2 class='section-header'>Post Attributes Mapping</h2>
  <table>
    <tr>
      <td class='col-header'>Post attribute name</td>
      <td class='col-header'>Data Source Column</td>
    </tr>
    <tr>
      <td>Post Title (Unique)</td>
      <td>
        <select name='get_data[post_title]'>
          <?php foreach( $data_columns  as $col_name) { ?>
            <option <?php if($getdata_mapping["post_title"] == $col_name) echo "selected"; ?>>
              <?php echo $col_name; ?>
            </option>
          <?php } ?>
        </select>
      </td>
    </tr>

    <tr>
      <td>Post Content</td>
      <td>
        <select name='get_data[post_content]'>
          <?php foreach( $data_columns  as $col_name) { ?>
              <option <?php if($getdata_mapping["post_content"] == $col_name) echo "selected"; ?>>
                <?php echo $col_name; ?>
              </option>
          <?php } ?>
        </select>
      </td>
    </tr>

    <tr>
      <td>Post Excerpt</td>
      <td>
        <select name='get_data[post_excerpt]'>
          <?php foreach( $data_columns  as $col_name) { ?>
            <option <?php if($getdata_mapping["post_excerpt"] == $col_name) echo "selected"; ?>>
              <?php echo $col_name; ?>
            </option>
          <?php } ?>
        </select>
      </td>
    </tr>

    <tr>
      <td>Post Type</td>
      <td>
        <select name='get_data[post_type]'>
          <?php foreach( $SUPPORTED_POST_TYPE  as $col_name) { ?>
            <option <?php if($getdata_mapping["post_type"] == $col_name) echo "selected"; ?>>
              <?php echo $col_name; ?>
            </option>
          <?php } ?>
        </select>
      </td>
    </tr>

    <tr>
      <td>Default Post Status</td>
      <td>
        <select name='get_data[default_post_status]'>
          <?php foreach( $SUPPORTED_POST_STATUS  as $col_name) { ?>
            <option <?php if($getdata_mapping["default_post_status"] == $col_name) echo "selected"; ?>>
              <?php echo $col_name; ?>
            </option>
          <?php } ?>
        </select>
      </td>
    </tr>


    <tr>
      <td>Deleted entry Post Status</td>
      <td>
        <select name='get_data[deleted_post_status]'>
          <?php foreach( $SUPPORTED_POST_STATUS  as $col_name) { ?>
            <option <?php if($getdata_mapping["deleted_post_status"] == $col_name) echo "selected"; ?>>
              <?php echo $col_name; ?>
            </option>
          <?php } ?>
        </select>
      </td>
    </tr>
  </table>

  <h2 class='section-header'>Post Meta Mapping</h2>
  <table id='post_meta_mappings'>

    <thead>
      <tr>
        <td class='col-header'>Post Meta name</td>
        <td class='col-header'>Data Source Column</td>
      </tr>      
    </thead>

    <tbody>
      <tr class='postmeta_row'>
        <td>Price</td>
        <td>
          <select name='get_data[post_meta][price]'>
            <?php foreach( $data_columns  as $col_name) { ?>
                <option <?php if($getdata_mapping["post_content"] == $col_name) echo "selected"; ?>>
                  <?php echo $col_name; ?>
                </option>
            <?php } ?>
          </select>
        </td>
        <td>
          <a class='remove-butt'>Remove</a>
        </td>
      </tr>
    </tbody>

  </table>



</form>