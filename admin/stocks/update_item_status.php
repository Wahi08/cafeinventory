<?php
require_once('../../config.php');

if (isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];

    // Query to calculate total quantity
    $qry = $conn->query("SELECT i.*, (COALESCE((SELECT SUM(quantity) FROM `stockin_list` WHERE item_id = $item_id), 0) 
                        - COALESCE((SELECT SUM(quantity) FROM `stockout_list` WHERE item_id = $item_id), 0) 
                        - COALESCE((SELECT SUM(quantity) FROM `waste_list` WHERE item_id = $item_id), 0)) 
                        AS `total_quantity` FROM `item_list` i WHERE i.id = $item_id");
    $row = $qry->fetch_assoc();
    $total_quantity = $row['total_quantity'];

    // Retrieve min_quantity from item_list
    $qry = $conn->query("SELECT min_quantity FROM item_list WHERE id = '$item_id'");
    $row = $qry->fetch_assoc();
    $min_quantity = $row['min_quantity'];

    // Update item status based on total quantity
    $status = '';
    if ($total_quantity == 0) {
        $status = '1'; // Out of stock
    } elseif ($total_quantity < $min_quantity) {
        $status = '2'; // Low stock
    } else {
        $status = '0'; // In stock
    }

    // Update item status in the item_list table
    $conn->query("UPDATE item_list SET status = '$status' WHERE id = '$item_id'");

    echo json_encode(array('status' => 'success'));
    exit;
} else {
    echo json_encode(array('status' => 'failed', 'message' => 'Invalid item ID'));
    exit;
}
?>
