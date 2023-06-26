<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT i.*, c.name as `category`,( COALESCE((SELECT SUM(quantity) 
                        FROM `stockin_list` 
                        WHERE item_id = i.id),0) - COALESCE((SELECT SUM(quantity) FROM `stockout_list` 
                        WHERE item_id = i.id),0) - COALESCE((SELECT SUM(quantity) FROM `waste_list` where item_id = i.id),0) ) as `available` from `item_list` i 
                        inner join category_list c on i.category_id = c.id where i.id = '{$_GET['id']}' and i.delete_flag = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
		echo '<script>alert("item ID is not valid."); location.replace("./?page=items")</script>';
	}
}else{
	echo '<script>alert("item ID is Required."); location.replace("./?page=items")</script>';
}
?>
<div class="content bg-gradient-teal py-5 px-4">
    <h3 class="font-weight-bolder">Stock Details</h3>
</div>
<div class="row mt-n4 justify-content-center">
    <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
        <div class="card rounded-0 shadow">
            <div class="card-footer py-1 text-center">
                <button id="print" class="btn btn-success btn-flat bg-gradient-success btn-sm" type="button"><i class="fa fa-print"></i> Print</button>
                <a class="btn btn-light btn-flat bg-gradient-light border btn-sm text-dark" href="./?page=stocks"><i class="fa fa-angle-left"></i> Back to List</a>
            </div>
        </div>
        <div class="card card-outline card-teal rounded-0 shadow printout">
            <div class="card-header py-1">
                <div class="card-title"><b>Item Details</b></div>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <fieldset>
                        <div class="d-flex w-100">
                            <div class="col-4 bg-gradient-teal m-0 p-1 border">Category</div>
                            <div class="col-8 m-0 p-1 border"><?= isset($category) ? $category : '' ?></div>
                        </div>
                        <div class="d-flex w-100">
                            <div class="col-4 bg-gradient-teal m-0 p-1 border">Item Name</div>
                            <div class="col-8 m-0 p-1 border"><?= isset($name) ? $name : '' ?></div>
                        </div>
                        <div class="d-flex w-100">
                            <div class="col-4 bg-gradient-teal m-0 p-1 border">Unit</div>
                            <div class="col-8 m-0 p-1 border"><?= isset($unit) ? $unit : '' ?></div>
                        </div>
                        <div class="d-flex w-100">
                            <div class="col-4 bg-gradient-teal m-0 p-1 border">Available</div>
                            <div class="col-8 m-0 p-1 border font-weight-bolder"><?= isset($available) ? format_num($available) : '' ?></div>
                        </div>
                    </fieldset>
                </div>
            </div>
            
        </div>
        <div class="card card-outline card-teal rounded-0 shadow printout">
            <div class="card-header py-1">
                <div class="card-title">Stock-In History</div>
                <?php if($_settings->userdata('type') == 3): ?>
                <?php $id = isset($_GET['id']) ? $_GET['id'] : ''; ?>
                <div class="card-tools">
                    <div class="btn-group btn-group-xs">
                        <button class="btn btn-sm btn-flat btn-primary add_stockin" title="Add Data" type="button" data-id="<?= $id ?>" item-name="<?= $name ?>">
                            <i class="far fa-plus-square"></i> Add Stock In
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-stripped" id="stockin-tbl">
                    <thead>
                        <tr>
                            <th class="p-1 text-center">Date</th>
                            <th class="p-1 text-center">Quantity</th>
                            <th class="p-1 text-center">Remarks</th>
                            <?php if($_settings->userdata('type') == 3): ?>
                            <th class="p-1 text-center">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(isset($id)):
                        $stockins = $conn->query("SELECT * FROM `stockin_list` where item_id = '{$id}' order by date(`date`) asc");
                        while($row = $stockins->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="p-1 align-middle"><?= date("M d, Y", strtotime($row['date'])) ?></td>
                            <td class="p-1 align-middle text-right"><?= format_num($row['quantity']) ?></td>
                            <td class="p-1 align-middle"><?= $row['remarks'] ?></td>
                            <?php if($_settings->userdata('type') == 3): ?>
                            <td class="p-1 align-middle text-center">
                                <div class="btn-group btn-group-xs">
                                    <button class="btn btn-flat btn-primary btn-xs bg-gradient-primary edit_stockin" title="Edit Data" type="button" data-id = "<?= $row['id'] ?>" item-name="<?= $name ?>"><small><i class="fa fa-edit"></i></small></button>
                                    <button class="btn btn-flat btn-danger btn-xs bg-gradient-danger delete_stockin" title="Delete Data" type="button" data-id = "<?= $row['id'] ?>" item-name="<?= $name ?>"><small><i class="fa fa-trash"></i></small></button>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card card-outline card-teal rounded-0 shadow printout">
            <div class="card-header py-1">
                <div class="card-title">Stock-Out History</div>
                <?php if($_settings->userdata('type') == 3): ?>
                <div class="card-tools">
                    <button class="btn btn-sm btn-flat btn-danger" type="button" id="add_stockout" item-name="<?= $name ?>"><i class="far fa-plus-square"></i> Add Stock Out</button>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-stripped" id="stockout-tbl">
                    <thead>
                        <tr>
                            <th class="p-1 text-center">Date</th>
                            <th class="p-1 text-center">Quantity</th>
                            <th class="p-1 text-center">Remarks</th>
                            <?php if($_settings->userdata('type') == 3): ?>
                            <th class="p-1 text-center">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(isset($id)):
                        $stockouts = $conn->query("SELECT * FROM `stockout_list` where item_id = '{$id}' order by date(`date`) asc");
                        while($row = $stockouts->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="p-1 align-middle"><?= date("M d, Y", strtotime($row['date'])) ?></td>
                            <td class="p-1 align-middle text-right"><?= format_num($row['quantity']) ?></td>
                            <td class="p-1 align-middle"><?= $row['remarks'] ?></td>
                            <?php if($_settings->userdata('type') == 3): ?>
                            <td class="p-1 align-middle text-center">
                                <div class="btn-group btn-group-xs">
                                    <button class="btn btn-flat btn-primary btn-xs bg-gradient-primary edit_stockout" title="Edit Data" type="button" data-id = "<?= $row['id'] ?>" item-name="<?= $name ?>"><small><i class="fa fa-edit"></i></small></button>
                                    <button class="btn btn-flat btn-danger btn-xs bg-gradient-danger delete_stockout" title="Delete Data" type="button" data-id = "<?= $row['id'] ?>" item-name="<?= $name ?>"><small><i class="fa fa-trash"></i></small></button>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card card-outline card-teal rounded-0 shadow printout">
            <div class="card-header py-1">
                <div class="card-title">Waste History</div>
                <?php if($_settings->userdata('type') == 3): ?>
                <div class="card-tools">
                    <button class="btn btn-sm btn-flat btn-warning" type="button" id="add_waste" item-name="<?= $name ?>"><i class="far fa-plus-square"></i> Add Waste Data</button>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-stripped" id="waste-tbl">
                    <thead>
                        <tr>
                            <th class="p-1 text-center">Date</th>
                            <th class="p-1 text-center">Quantity</th>
                            <th class="p-1 text-center">Remarks</th>
                            <?php if($_settings->userdata('type') == 3): ?>
                            <th class="p-1 text-center">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(isset($id)):
                        $wastes = $conn->query("SELECT * FROM `waste_list` where item_id = '{$id}' order by date(`date`) asc");
                        while($row = $wastes->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="p-1 align-middle"><?= date("d M, Y", strtotime($row['date'])) ?></td>
                            <td class="p-1 align-middle text-right"><?= format_num($row['quantity']) ?></td>
                            <td class="p-1 align-middle"><?= $row['remarks'] ?></td>
                            <?php if($_settings->userdata('type') == 3): ?>
                            <td class="p-1 align-middle text-center">
                                <div class="btn-group btn-group-xs">
                                    <button class="btn btn-flat btn-primary btn-xs bg-gradient-primary edit_waste" title="Edit Data" type="button" data-id = "<?= $row['id'] ?>" item-name="<?= $name ?>"><small><i class="fa fa-edit"></i></small></button>
                                    <button class="btn btn-flat btn-danger btn-xs bg-gradient-danger delete_waste" title="Delete Data" type="button" data-id = "<?= $row['id'] ?>" item-name="<?= $name ?>"><small><i class="fa fa-trash"></i></small></button>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<noscript id="print-header">
    <div>
        <style>
            html, body{
                min-height:unset !important;
            }
        </style>
        <div class="d-flex w-100 align-items-center">
            <div class="col-2 text-center">
                <img src="<?= validate_image($_settings->info('logo')) ?>" alt="" class="rounded-circle border" style="width: 5em;height: 5em;object-fit:cover;object-position:center center">
            </div>
            <div class="col-8">
                <div style="line-height:1em">
                    <h3 class="text-center font-weight-bold mb-0"><large><?= $_settings->info('name') ?></large></h3>
                    <h3 class="text-center font-weight-bold mb-0"><large>Stock Details</large></h3>
                </div>
            </div>
        </div>
       
        <hr>
    </div>
</noscript>
<script>
    var tbl1,tbl2, tbl3;
     function print_t(){
         if(!!tbl1)
         tbl1.fnDestroy();
         if(!!tbl2)
         tbl2.fnDestroy();
         if(!!tbl3)
         tbl3.fnDestroy();
        var h = $('head').clone()
        var p = $('#printout').clone()
        var ph = $($('noscript#print-header').html()).clone()
        var el = "";

        $('.printout').each(function(){
            var card = $(this).clone()
            card.removeClass('shadow')
            card.find('.btn').remove()
            card.find('td:nth-child(4)').remove()
            card.find('th:nth-child(4)').remove()
            el += card[0].outerHTML
        })
        h.find('title').text("order Details - Print View")
        var nw = window.open("", "_blank", "width="+($(window).width() * .8)+",left="+($(window).width() * .1)+",height="+($(window).height() * .8)+",top="+($(window).height() * .1))
            nw.document.querySelector('head').innerHTML = h.html()
            nw.document.querySelector('body').innerHTML = ph[0].outerHTML
            nw.document.querySelector('body').innerHTML += el
            nw.document.close()
            start_loader()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                    nw.close()
                    end_loader()
                    tbl1 = $('#stockin-tbl').dataTable({
                        columnDefs: [
                                { orderable: false, targets: [3] }
                        ],
                        order:[0,'asc']
                    });
                    tbl2 = $('#stockout-tbl').dataTable({
                        columnDefs: [
                                { orderable: false, targets: [3] }
                        ],
                        order:[0,'asc']
                    });
                    tbl3 = $('#waste-tbl').dataTable({
                        columnDefs: [
                                { orderable: false, targets: [3] }
                        ],
                        order:[0,'asc']
                    });
                }, 200);
            }, 300);
    }
    
    $(function(){
       
        $('#print').click(function(){
            print_t()
        })

        // Stockin
        $('.add_stockin').click(function(){
            var id = $(this).attr('data-id');
            var itemName = $(this).attr('item-name');
            var minQuantity = $(this).closest('.product-item').find('.min-quantity').data('min-quantity');
            uni_modal("<i class='far fa-plus-square'></i> &nbsp"+  itemName +" - Add Stock-In ", 'stocks/manage_stockin.php?iid=' + id);
        });


        $('.edit_stockin').click(function(){
            var id = $(this).attr('data-id');
            var itemName = $(this).attr('item-name');
            uni_modal("<i class='fa fa-edit'></i> &nbsp"+  itemName +" - Edit Stock-In", 'stocks/edit_stockin.php?id=' + id);
        });


        $('.delete_stockin').click(function(){
			_conf("Are you sure to delete this stock-in data permanently?","delete_stockin",[$(this).attr('data-id')])
		})

        // Stockout
        $('#add_stockout').click(function(){
            var itemName = $(this).attr('item-name');
            uni_modal("<i class='far fa-plus-square'></i> &nbsp"+  itemName +" - Add Stock-out", 'stocks/manage_stockout.php?iid=<?= isset($id) ? $id : '' ?>')
        })
        $('.edit_stockout').click(function(){
            var itemName = $(this).attr('item-name');
            uni_modal("<i class='fa fa-edit'></i> &nbsp"+  itemName +" - Edit Stock-out", 'stocks/manage_stockout.php?iid=<?= isset($id) ? $id : '' ?>&id=' + $(this).attr('data-id'))
        })
        $('.delete_stockout').click(function(){
			_conf("Are you sure to delete this stock-out data permanently?","delete_stockout",[$(this).attr('data-id')])
		})

        // Waste
        $('#add_waste').click(function(){
            var itemName = $(this).attr('item-name');
            uni_modal("<i class='far fa-plus-square'></i> &nbsp"+  itemName +" - Add Waste", 'stocks/manage_waste.php?iid=<?= isset($id) ? $id : '' ?>')
        })
        $('.edit_waste').click(function(){
            var itemName = $(this).attr('item-name');
            uni_modal("<i class='fa fa-edit'></i> &nbsp"+  itemName +" - Edit Waste", 'stocks/manage_waste.php?iid=<?= isset($id) ? $id : '' ?>&id=' + $(this).attr('data-id'))
        })
        $('.delete_waste').click(function(){
			_conf("Are you sure to delete this Waste data permanently?","delete_waste",[$(this).attr('data-id')])
		})

        tbl1 = $('#stockin-tbl').dataTable({
			columnDefs: [
					{ orderable: false, targets: [3] }
			],
			order:[0,'asc']
		});
        tbl2 = $('#stockout-tbl').dataTable({
			columnDefs: [
					{ orderable: false, targets: [3] }
			],
			order:[0,'asc']
		});
        tbl3 = $('#waste-tbl').dataTable({
			columnDefs: [
					{ orderable: false, targets: [3] }
			],
			order:[0,'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
        $('.dataTables_paginate .pagination>li>a').addClass('p-1');
        $('.dataTables_filter input').addClass('rounded-0 form-control-sm py-1');
        
    })
    function delete_stockin($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_stockin",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
    
    function delete_stockout($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_stockout",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
    function delete_waste($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_waste",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>