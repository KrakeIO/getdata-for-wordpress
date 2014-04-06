<?php

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

  $post_type = array( "listing_type", "page", "post", "revision" );

?>

<h1>DataSource Mappings</h1>
<form method='post' action='admin.php?page=getdata/controllers/admin_controller.php/map-data-columns'>
  <table>
    <tr>
      <th>Product attribute</th>
      <th>Data Source Column</th>
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
          <?php foreach( $post_type  as $col_name) { ?>
            <option <?php if($getdata_mapping["post_excerpt"] == $col_name) echo "selected"; ?>>
              <?php echo $col_name; ?>
            </option>
          <?php } ?>
        </select>
      </td>
    </tr>    
  </table>
  <input type='submit' value="save mappings">  
</form>