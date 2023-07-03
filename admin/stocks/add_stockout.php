<?php 
require_once('../../config.php');

$item_id = isset($_GET['iid']) ? $_GET['iid'] : '';

// Retrieve min and max quantity from the item_list table
$qry = $conn->query("SELECT min_quantity as min_quantity, max_quantity as max_quantity FROM item_list WHERE id = '$item_id'");
$quantity_range = $qry->fetch_assoc();
$min_quantity = $quantity_range['min_quantity'];
$max_quantity = $quantity_range['max_quantity'];

$qry2 = $conn->query("SELECT i.*, (COALESCE((SELECT SUM(quantity) FROM `stockin_list` WHERE item_id = $item_id), 0) 
                        - COALESCE((SELECT SUM(quantity) FROM `stockout_list` WHERE item_id = $item_id), 0) 
                        - COALESCE((SELECT SUM(quantity) FROM `waste_list` WHERE item_id = $item_id), 0)) 
                        AS `total_quantity` FROM `item_list` i WHERE i.id = $item_id");
    $row = $qry2->fetch_assoc();
    $total_quantity = $row['total_quantity'];

?>
<div class="container-fluid">
    <form action="" id="stockout-form">
        <input type="hidden" name="item_id" value="<?= $item_id ?>">
        <div class="form-group">
            <label for="date" class="control-label">Date</label>
            <input type="date" name="date" id="date" class="form-control form-control-sm rounded-0" max="<?= date("Y-m-d") ?>" required>
        </div>
        <div class="form-group">
            <label for="quantity" class="control-label">Quantity</label>
            <input type="number" step="any" name="quantity" id="quantity" class="form-control form-control-sm rounded-0 text-right"  required>
        </div>

        <div class="form-group">
            <label for="remarks" class="control-label">Remarks</label>
            <textarea name="remarks" id="remarks" class="form-control form-control-sm rounded-0" required></textarea>
        </div>
    </form>
</div>
<script>

    $(function(){
        
        $('#stockout-form').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            if(_this[0].checkValidity() == false){
                _this[0].reportValidity();
                return false;
            }
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_stockout",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: function(err){
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    updateItemStatus(resp.item_id); // Call the function to update item status
                    location.reload();
                } else if (resp.status == 'failed' && !!resp.msg) {
                    var el = $('<div>');
                    el.addClass("alert alert-danger err-msg").text(resp.msg);
                    _this.prepend(el);
                    el.show('slow');
                    $("html, body, .modal").scrollTop(0);
                    end_loader();
                } else {
                    alert_toast("An error occurred", 'error');
                    end_loader();
                    console.log(resp);
                }
                
                // Update item status to "available" if total quantity is more than minimum quantity
                if (parseFloat(<?php echo $total_quantity; ?> - $('#quantity').val()) > parseFloat(<?php echo $min_quantity; ?>)) {
                    updateItemStatus(<?php echo $item_id; ?>, '1');
                }
                // Update item status to "unavailable" if total quantity is lower than minimum quantity
                if (parseFloat(<?php echo $total_quantity; ?> - $('#quantity').val()) < parseFloat(<?php echo $min_quantity; ?>)) {
                    updateItemStatus(<?php echo $item_id; ?>, '0');
                }
            }

        });

        function updateItemStatus(itemID, status) {
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=update_item_status",
                data: { item_id: itemID, status: status },
                method: 'POST',
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === 'success') {
                        console.log('Item status updated successfully.');
                    } else {
                        console.log('Failed to update item status.');
                    }
                },
                error: function(err) {
                    console.log('An error occurred while updating item status.');
                    console.log(err);
                }
            });
        }
    });
});

</script>
