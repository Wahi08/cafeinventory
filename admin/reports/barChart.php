<?php
$month = isset($_GET['month']) ? $_GET['month'] : date("Y-m");
$selectedYear = date("Y", strtotime($month . "-01"));

// Prepare array for all months
$labels = array(
    'January', 'February', 'March', 'April', 'May', 'June', 'July',
    'August', 'September', 'October', 'November', 'December'
);

// Fetch stock-in data for the selected year
$stockInData = $conn->query("SELECT DATE_FORMAT(date, '%m') AS month, SUM(quantity) AS total_quantity FROM stockin_list WHERE DATE_FORMAT(date, '%Y') = '{$selectedYear}' GROUP BY month ORDER BY month ASC");

//Fetch stock-out data for the selected year
$stockOutData = $conn->query("SELECT DATE_FORMAT(date, '%m') AS month, SUM(quantity) AS total_quantity FROM stockout_list WHERE DATE_FORMAT(date, '%Y') = '{$selectedYear}' GROUP BY month ORDER BY month ASC");

//Fetch stock-out data for the selected year
$wasteData = $conn->query("SELECT DATE_FORMAT(date, '%m') AS month, SUM(quantity) AS total_quantity FROM waste_list WHERE DATE_FORMAT(date, '%Y') = '{$selectedYear}' GROUP BY month ORDER BY month ASC");

// Prepare arrays for quantities with 0 values for all months
$stockInQuantities = array_fill(0, 12, 0);
$stockOutQuantities = array_fill(0, 12, 0);
$wasteQuantities = array_fill(0, 12, 0);

// Populate the arrays with data from the database
while ($row = $stockInData->fetch_assoc()) {
    $monthNumber = intval($row['month']);
    $stockInQuantities[$monthNumber - 1] = intval($row['total_quantity']);
}

// Populate the arrays with data from the second query
while ($row = $stockOutData->fetch_assoc()) {
    $monthNumber = intval($row['month']);
    $stockOutQuantities[$monthNumber - 1] = intval($row['total_quantity']);
}

// Populate the arrays with data from the third query
while ($row = $wasteData->fetch_assoc()) {
    $monthNumber = intval($row['month']);
    $wasteQuantities[$monthNumber - 1] = intval($row['total_quantity']);
}
?>

<div class="content py-5 px-3 bg-gradient-teal">
    <h2>Monthly Comparison</h2>
</div>

<div class="row flex-column mt-4 justify-content-center align-items-center mt-lg-n4 mt-md-3 mt-sm-0">
    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
        <div class="card rounded-0 mb-2 shadow">
            <div class="card-body">
                <fieldset>
                    <legend>Filter</legend>
                    <form action="" id="filter-form">
                        <div class="row align-items-end">
                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="year" class="control-label">Choose Year</label>
                                    <select class="form-control form-control-sm rounded-0" name="year" id="year">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($i = $currentYear; $i >= 2023; $i--) {
                                            $selected = ($i == $selectedYear) ? 'selected' : '';
                                            echo "<option value=\"$i\" $selected>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <button class="btn btn-sm btn-flat btn-primary bg-gradient-primary"><i class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </fieldset>
            </div>
        </div>
    </div>

    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
        <div class="card rounded-0 mb-2 shadow">
            <div class="card-header py-1">
            </div>
            <div class="card-body">
                <div class="container-fluid" id="printout">
                    <div id="chart-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<noscript id="print-header">
    <!-- Print header content -->
</noscript>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
<script>
    // Function to reload the page with the selected year
    function filterByYear(year) {
        var currentURL = window.location.href;
        var separator = currentURL.includes('?') ? '&' : '?';
        var newURL = currentURL + separator + 'year=' + year;
        window.location.href = newURL;
    }

    $(function () {
        // Initialize the select input with the selected year
        $('#year').change(function () {
            var selectedYear = $(this).val();
            filterByYear(selectedYear);
        });

        // Create bar chart
        var chart = new ApexCharts(document.querySelector('#chart-container'), {
            chart: {
                type: 'bar',
                height: 400
            },
            series: [
                {
                    name: 'Stock-In Quantity',
                    data: <?= json_encode($stockInQuantities) ?>
                },
                {
                    name: 'Stock-Out Quantity',
                    data: <?= json_encode($stockOutQuantities) ?>
                },
                {
                    name: 'Waste Quantity',
                    data: <?= json_encode($wasteQuantities) ?>
                }
            ],
            xaxis: {
                categories: <?= json_encode($labels) ?>
            },
            yaxis: {
                title: {
                    text: 'Quantity'
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val.toLocaleString();
                    }
                }
            },
            legend: {
                position: 'top'
            }
        });

        chart.render();
    });
</script>
