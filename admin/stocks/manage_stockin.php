<?php 
require_once('../../config.php');

$isEdit = false; // Flag to determine if it's an edit action
$stockin_id = isset($_GET['id']) ? $_GET['id'] : '';

if ($stockin_id > 0) {
    $qry = $conn->query("SELECT * FROM `stockin_list` WHERE id = '{$stockin_id}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
        $isEdit = true; // Set the flag to indicate it's an edit action
    }
}

$item_id = isset($_GET['iid']) ? $_GET['iid'] : '';
$qry = $conn->query("SELECT i.id, i.min_quantity, i.max_quantity FROM item_list i INNER JOIN stockin_list s ON i.id = s.item_id WHERE s.id = '$stockin_id'");
$quantity_range = $qry->fetch_assoc();
$min_quantity = $quantity_range['min_quantity'] ?? '';
$max_quantity = $quantity_range['max_quantity'] ?? '';

?>
<div class="container-fluid">
    <form action="" id="stockin-form">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
        <div class="form-group">
            <label for="date" class="control-label">Date</label>
            <input type="date" name="date" id="date" class="form-control form-control-sm rounded-0" value="<?= isset($date) ? $date : '' ?>" max="<?= date("Y-m-d") ?>" required>
        </div>
        <div class="form-group">
            <label for="quantity" class="control-label">Quantity</label>
            <input type="number" step="any" name="quantity" id="quantity" class="form-control form-control-sm rounded-0 text-right" value="<?= isset($quantity) ? $quantity : '' ?>" min="<?= $min_quantity ?>"  required>
        </div>
        <div class="form-group">
            <label for="remarks" class="control-label">Remarks</label>
            <textarea name="remarks" id="remarks" class="form-control form-control-sm rounded-0" required><?= isset($remarks) ? $remarks : '' ?></textarea>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#stockin-form').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            if(_this[0].checkValidity() == false){
                _this[0].reportValidity();
                return false;
            }
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_stockin",
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
                success: function(resp){
                    if(typeof resp == 'object' && resp.status == 'success'){
                        location.reload();
                    } else if(resp.status == 'failed' && !!resp.msg){
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
                }
            });
        });
    });
</script>
