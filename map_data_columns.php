<?php
  $UNIQUE_ID = "1_df30aaa36f078d501bae3f5ff10d78c7eses";

  include( WP_PLUGIN_DIR . '/getdata/krake_client.php' );
  $krake_client = new KrakeClient($UNIQUE_ID);  
  $data_schema = $krake_client->getColumns();
  $data_columns = $data_schema->columns

?>


<form method='post' action='admin.php?page=getdata/getdata.php/map-data-columns'>
  <input type='submit' value="save mappings">    
  <table>
    <tr>
      <th>Product attribute</th>
      <th>Data Source Column</th>
    </tr>

    <tr>
      <td>Product Name</td>
      <td>
        <select name='product_name'>
          <?php
            foreach( $data_columns  as $col_name) {
              ?>
                <option><?php echo $col_name; ?></option>
              <?php
            }
          ?>
        </select>
      </td>
    </tr>

    <tr>
      <td>Product Price</td>
      <td>
        <select name='product_price'>
          <?php
            foreach( $data_columns  as $col_name) {
              ?>
                <option><?php echo $col_name; ?></option>
              <?php
            }
          ?>
        </select>
      </td>
    </tr>

    <tr>
      <td>Product Image</td>
      <td>
        <select name='product_image'>
          <?php
            foreach( $data_columns  as $col_name) {
              ?>
                <option><?php echo $col_name; ?></option>
              <?php
            }
          ?>
        </select>
      </td>
    </tr>

  </table>
</form>