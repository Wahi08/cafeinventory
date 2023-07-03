<style>
  #system-cover{
    width:100%;
    height:45em;
    object-fit:cover;
    object-position:center center;
  }
</style>
<h1 class="">Welcome, <?php echo $_settings->userdata('username') ?> !</h1>
<hr>

<div class="card card-outline rounded-0 shadow printout">
  <div class="card-header py-1">
     <div class="card-title">Summary</div>
  </div>
  <div class="card-body">
      <div class="row">
      <div class="col-12 col-sm-4 col-md-4">
        <div class="info-box bg-gradient-light">
          <span class="info-box-icon bg-gradient-teal elevation-1"><i class="fas fa-th-list"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Available Categories</span>
            <span class="info-box-number text-right h5">
              <?php 
                $category = $conn->query("SELECT id FROM category_list where delete_flag = 0 and `status` = 1")->num_rows;
                echo format_num($category);
              ?>
              <?php ?>
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-12 col-sm-4 col-md-4">
        <div class="info-box bg-gradient-light">
          <span class="info-box-icon bg-gradient-teal elevation-1"><i class="fas fa-seedling"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Available Items</span>
            <span class="info-box-number text-right h5">
              <?php 
                $items = $conn->query("SELECT id FROM item_list where delete_flag = 0 and `status` = 1")->num_rows;
                echo format_num($items);
              ?>
              <?php ?>
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-12 col-sm-4 col-md-4">
        <div class="info-box bg-gradient-light">
          <span class="info-box-icon bg-gradient-teal elevation-1"><i class="fas fa-user-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total User</span>
            <span class="info-box-number text-right h5">
              <?php 
                $items = $conn->query("SELECT id FROM users")->num_rows;
                echo format_num($items);
              ?>
              <?php ?>
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
    </div>
  </div>
</div>

<div class="card card-outline bg-lightyellow rounded-0 shadow printout">
  <div class="card-header py-1">
     <div class="card-title">Low Stock Items</div>
  </div>
  <div class="card-body">
    <table class="table table-bordered table-stripped" id="stockin-tbl">
      <thead>
        <tr>
          <th class="p-1 text-center">Last Stock-out Date</th>
          <th class="p-1 text-center">Item Name</th>
          <th class="p-1 text-center">Quantity Available</th>
          <th class="p-1 text-center">Min Quantity Needed</th>
        </tr>
      </thead>
      <tbody>
        <?php 
            $stockin = $conn->query("SELECT * FROM `item_list` WHERE `status` = '0'");
            while($row = $stockin->fetch_assoc()):
            
              // Fetch item name from stockin_list table using the item_id
              $item_id = $row['id'];
              $item_qry = $conn->query("SELECT * FROM `stockout_list` WHERE item_id = '{$item_id}'");
              $stockout_row = ($item_qry->num_rows > 0) ? $item_qry->fetch_assoc() : null;
              $stockout_date = ($stockout_row !== null) ? $stockout_row['date'] : 'N/A';
              $stockout_remarks = ($stockout_row !== null) ? $stockout_row['remarks'] : 'N/A';
        
              $qry = $conn->query("SELECT i.*, c.name as `category`, (COALESCE((SELECT SUM(quantity) FROM `stockin_list` WHERE item_id = $item_id),0) - COALESCE((SELECT SUM(quantity) FROM `stockout_list` WHERE item_id = $item_id),0) - COALESCE((SELECT SUM(quantity) FROM `waste_list` WHERE item_id = $item_id),0)) as `available` FROM `item_list` i INNER JOIN `category_list` c ON i.category_id = c.id WHERE i.id = '{$item_id}' AND i.delete_flag = 0");
              if($qry->num_rows > 0):
                $item = $qry->fetch_assoc();
        ?>
        <tr>
          <td class="p-1 align-middle"><?= $stockout_date ?></td>
          <td class="p-1 align-middle"><?= $item['name'] ?></td>
          <td class="p-1 align-middle"><?= format_num($item['available']) ?></td>
          <td class="p-1 align-middle"><?= $item['min_quantity'] ?></td>
        </tr>
        <?php 
              endif;
            endwhile;
        ?>
      </tbody>
    </table>
  </div>
</div>




<div class="card card-outline bg-lightyellow rounded-0 shadow printout">
  <div class="card-header py-1">
    <div class="card-title">Overstock Items</div>
  </div>
  <div class="card-body">
    <table class="table table-bordered table-stripped" id="stockin-tbl">
      <thead>
        <tr>
          <th class="p-1 text-center">Last Stock-in Date</th>
          <th class="p-1 text-center">Item Name</th>
          <th class="p-1 text-center">Quantity Available</th>
          <th class="p-1 text-center">Max Quantity</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $stockin = $conn->query("SELECT * FROM `item_list` WHERE `status` = '1'");
        while ($row = $stockin->fetch_assoc()) :

          // Fetch item name from stockin_list table using the item_id
          $item_id = $row['id'];
          $item_qry = $conn->query("SELECT * FROM `stockin_list` WHERE item_id = '{$item_id}'");
          $stockin_row = ($item_qry->num_rows > 0) ? $item_qry->fetch_assoc() : null;
          $stockin_date = ($stockin_row !== null) ? $stockin_row['date'] : 'N/A';
          $qry = $conn->query("SELECT i.id, i.min_quantity, i.max_quantity FROM item_list i INNER JOIN stockin_list s ON i.id = s.item_id WHERE s.id = '$item_id'");
          $quantity_range = $qry->fetch_assoc();
          $min_quantity = $quantity_range['min_quantity'] ?? '';
          $max_quantity = $quantity_range['max_quantity'] ?? '';

          $qry = $conn->query("SELECT i.*, c.name as `category`, (COALESCE((SELECT SUM(quantity) FROM `stockin_list` WHERE item_id = $item_id),0) - COALESCE((SELECT SUM(quantity) FROM `stockout_list` WHERE item_id = $item_id),0) - COALESCE((SELECT SUM(quantity) FROM `waste_list` WHERE item_id = $item_id),0)) as `available` FROM `item_list` i INNER JOIN `category_list` c ON i.category_id = c.id WHERE i.id = '{$item_id}' AND i.delete_flag = 0");
          if ($qry->num_rows > 0) :
            $item = $qry->fetch_assoc();
            if ($item['available'] > $item['max_quantity']):
        ?>
              <tr>
                <td class="p-1 align-middle"><?= $stockin_date ?></td>
                <td class="p-1 align-middle"><?= $item['name'] ?></td>
                <td class="p-1 align-middle"><?= format_num($item['available']) ?></td>
                <td class="p-1 align-middle"><?= $item['max_quantity'] ?></td>
              </tr>
        <?php
            endif;
          endif;
        endwhile;
        ?>
      </tbody>
    </table>
  </div>
</div>


