<?php
$month = isset($_GET['month']) ? $_GET['month'] : date("Y-m");

$categoryFilter = "";
if (isset($_GET['category']) && $_GET['category'] !== "") {
    $category = $conn->real_escape_string($_GET['category']);
    $categoryFilter = "AND i.category_id = '$category'";
}

$itemFilter = "";
if (isset($_GET['item']) && $_GET['item'] !== "") {
    $item = $conn->real_escape_string($_GET['item']);
    $itemFilter = "AND s.item_id = '$item'";
}

$query = "SELECT s.*, i.name as `item`, c.name as `category`, i.unit 
          FROM `stockin_list` s 
          INNER JOIN `item_list` i ON s.item_id = i.id 
          INNER JOIN category_list c ON i.category_id = c.id 
          WHERE DATE_FORMAT(s.date, '%Y-%m') = '{$month}' 
          {$categoryFilter}
          {$itemFilter}
          ORDER BY DATE(s.`date`) ASC";

$stock = $conn->query($query);
?>

<div class="content py-5 px-3 bg-gradient-teal">
    <h2>Monthly Stock-In Reports</h2>
</div>

<div class="row flex-column mt-4 justify-content-center align-items-center mt-lg-n4 mt-md-3 mt-sm-0">
    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
        <div class="card rounded-0 mb-2 shadow">
            <div class="card-body">
                <fieldset>
                    <legend>Filter</legend>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="filter-form">
                        <div class="row align-items-end">
                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="month" class="control-label">Choose Month</label>
                                    <input type="month" class="form-control form-control-sm rounded-0" name="month" id="month" value="<?= $month ?>" required="required">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="category" class="control-label">Choose Category</label>
                                    <select class="form-control form-control-sm rounded-0" name="category" id="category">
                                        <option value="">All Categories</option>
                                        <?php
                                        $categories = $conn->query("SELECT * FROM category_list");
                                        while ($cat = $categories->fetch_assoc()) {
                                            $selected = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '';
                                            echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="item" class="control-label">Choose Item</label>
                                    <select class="form-control form-control-sm rounded-0" name="item" id="item">
                                        <option value="">All Items</option>
                                        <?php
                                        $items = $conn->query("SELECT * FROM item_list");
                                        while ($item = $items->fetch_assoc()) {
                                            $selected = (isset($_GET['item']) && $_GET['item'] == $item['id']) ? 'selected' : '';
                                            echo "<option value='{$item['id']}' $selected>{$item['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-0">Filter</button>
                                    <button type="button" id="print-button" class="btn btn-primary btn-sm rounded-0" onclick="this.style.display='none';document.body.offsetHeight;window.print();this.style.display='inline';">Print</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </fieldset>

                <div class="table-responsive">
                    <table class="table table-striped" id="stockin-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Item</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalQuantity = 0; // Add this line

                            if ($stock->num_rows > 0) {
                                while ($row = $stock->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . date("F d, Y", strtotime($row['date'])) . "</td>";
                                    echo "<td>" . $row['category'] . "</td>";
                                    echo "<td>" . $row['item'] . "</td>";
                                    echo "<td>" . $row['unit'] . "</td>";
                                    echo "<td>" . $row['quantity'] . "</td>";
                                    echo "</tr>";
                                    $totalQuantity += $row['quantity']; // Add this line
                                }
                            } else {
                                echo "<tr><td colspan='5'>No records found.</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot> <!-- Total Quantity -->
                            <tr>
                                <th colspan="3"></th>
                                <th>Total Quantity:</th>
                                <th><?php echo $totalQuantity; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<noscript id="print-header">
    <div>
        <style>
            html {
                min-height: unset !important;
            }
        </style>
        <div class="d-flex w-100 align-items-center">
            <div class="col-2 text-center">
                <img src="<?= validate_image($_settings->info('logo')) ?>" alt="" class="rounded-circle border" style="width: 5em;height: 5em;object-fit:cover;object-position:center center">
            </div>
            <div class="col-8">
                <div style="line-height:1em">
                    <div class="text-center font-weight-bold h5 mb-0"><large><?= $_settings->info('name') ?></large></div>
                    <div class="text-center font-weight-bold h5 mb-0"><large>Monthly Stock-In Report</large></div>
                    <div class="text-center font-weight-bold h5 mb-0">as of <?= date("F Y", strtotime($month . "-01")) ?></div>
                </div>
            </div>
        </div>
        <hr>
    </div>
</noscript>

<script>
    function print_r() {
        var html = $('html').clone();
        html.find('#print-button').remove();
        html.find('#filter-form').remove();
        html.find('#category').prop('disabled', true);
        html.find('#item').prop('disabled', true);
        html.find('#month').prop('disabled', true);
        
        var printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.open();
        printWindow.document.write('<html><head><title>Print</title></head><body>');
        printWindow.document.write(html.html());
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.onload = function() {
            printWindow.print();
            printWindow.close();
        };
    }


    $(function () {
    // Function to update the total quantity
    function updateTotalQuantity() {
        var currentTotal = 0;
        $('#stockin-table tbody tr').each(function () {
            var quantity = parseInt($(this).find('td:last').text());
            if (!isNaN(quantity)) {
                currentTotal += quantity;
            }
        });
        $('#stockin-table tfoot tr:last-child th:last-child').text(currentTotal);
    }

    // Update the total quantity on page load
    updateTotalQuantity();

    // Perform an AJAX request to fetch the filtered results
    $('#filter-form').submit(function (e) {
        e.preventDefault();

        // Serialize the form data
        var formData = $(this).serialize();

        // Reload the page with the filtered URL
        window.location = './?page=reports/stockin&' + formData;
    });

    // ...

    // Auto-filter out the item when the category is chosen
    $('#category').change(function () {
        var selectedCategory = $(this).val();
        if (selectedCategory !== "") {
            $('#item').val("");
        }
        // Update the total quantity when the category is changed
        updateTotalQuantity();
    });

    // Update the total quantity when the item is changed
    $('#item').change(function () {
        // Perform an AJAX request to fetch the filtered results
        $.ajax({
            url: './?page=reports/stockin',
            method: 'GET',
            data: $('#filter-form').serialize(),
            success: function (response) {
                // Extract the table body content from the response
                var tableBody = $(response).find('#stockin-table tbody').html();

                // Update the table body with the filtered results
                $('#stockin-table tbody').html(tableBody);

                // Update the total quantity
                updateTotalQuantity();
            },
            error: function (xhr, status, error) {
                // Handle the error if necessary
            }
        });
    });
});

</script>
