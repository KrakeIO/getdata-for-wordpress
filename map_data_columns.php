<?php

  $UNIQUE_ID = get_option('getdata_unique_datasouce_id');
  if(!isset($UNIQUE_ID) || strlen($UNIQUE_ID) == 0) {
    ?>
      <h1>Data Source is not set</h1>
      <a href='admin.php?page=getdata/getdata.php/set-data-source'>Proceed here to do so</a>
    <?php
    return; 
  }


  // 1_df30aaa36f078d501bae3f5ff10d78c7eses
  $UNIQUE_ID = get_option('getdata_unique_datasouce_id');
  include( WP_PLUGIN_DIR . '/getdata/krake_client.php' );
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
      'product_name'  => false,
      'product_price' => false,
      'product_image' => false
    );
  }

?>

<h1>DataSource Mappings</h1>
<form method='post' action='admin.php?page=getdata/getdata.php/map-data-columns'>
  <table>
    <tr>
      <th>Product attribute</th>
      <th>Data Source Column</th>
    </tr>

    <tr>
      <td>Product Name</td>
      <td>
        <select name='product_name'>
          <?php foreach( $data_columns  as $col_name) { ?>
            <option <?php if($getdata_mapping["product_name"] == $col_name) echo "selected"; ?>>
              <?php echo $col_name; ?>
            </option>
          <?php } ?>
        </select>
      </td>
    </tr>

    <tr>
      <td>Product Price</td>
      <td>
        <select name='product_price'>
          <?php foreach( $data_columns  as $col_name) { ?>
              <option <?php if($getdata_mapping["product_price"] == $col_name) echo "selected"; ?>>
                <?php echo $col_name; ?>
              </option>
          <?php } ?>
        </select>
      </td>
    </tr>

    <tr>
      <td>Product Image</td>
      <td>
        <select name='product_image'>
          <?php foreach( $data_columns  as $col_name) { ?>
            <option <?php if($getdata_mapping["product_image"] == $col_name) echo "selected"; ?>>
              <?php echo $col_name; ?>
            </option>
          <?php } ?>
        </select>
      </td>
    </tr>
  </table>
  <input type='submit' value="save mappings">  
</form>