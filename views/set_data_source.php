<?php
  // 1_df30aaa36f078d501bae3f5ff10d78c7eses
  $UNIQUE_ID = get_option('getdata_unique_datasouce_id');
?>

<h1>DataSource Settings</h1>
<form method='post' action='admin.php?page=getdata/controllers/admin_controller.php/set-data-source'>
  Unique ID of Data Repository : 
  <input name='getdata_unique_datasource_id' value="<?php echo $UNIQUE_ID; ?>">
  <br>
  <input type='submit' value="save settings">  
</form>



<?php 
  if(!isset($UNIQUE_ID) || strlen($UNIQUE_ID) == 0) return;
  $krake_client    = new KrakeClient($UNIQUE_ID);  
  $data_schema    = $krake_client->getColumns();
  $data_columns   = $data_schema->columns;
  $url_columns    = $data_schema->url_columns;
  $index_columns  = $data_schema->index_columns;  
?>

<table>

  <tr>
    <td><h2>Columns</h2></td>
  </tr>
  <?php foreach( $data_columns  as $col_name) { ?>
    <tr>
      <td><?php echo $col_name; ?></td>
    </tr>
  <?php } ?>

  <tr>
    <td><h2>URL Columns</h2></td>
  </tr>
  <?php foreach( $url_columns  as $col_name) { ?>
    <tr>
      <td><?php echo $col_name; ?></td>
    </tr>
  <?php } ?>  

  <tr>
    <td><h2>Index Columns</h2></td>
  </tr>
  <?php foreach( $index_columns  as $col_name) { ?>
    <tr>
      <td><?php echo $col_name; ?></td>
    </tr>
  <?php } ?>  

</table>