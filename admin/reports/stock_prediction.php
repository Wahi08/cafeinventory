<?php

$labels = [];
$usedData = [];
$suggestedData = [];

$currentYear = date('Y');

$itemQuery = $conn->query("SELECT * FROM item_list");
while ($item = $itemQuery->fetch_assoc()) {
    $itemID = $item['id'];
    $stockReceivedQuery = $conn->query("SELECT MONTH(date) AS month, SUM(quantity) AS total_received FROM stockin_list WHERE item_id = '{$itemID}' AND YEAR(date) = '{$currentYear}' GROUP BY MONTH(date)");
    $stockReceivedData = [];
    while ($row = $stockReceivedQuery->fetch_assoc()) {
        $stockReceivedData[$row['month']] = $row['total_received'];
    }

    $stockOutQuery = $conn->query("SELECT MONTH(date) AS month, SUM(quantity) AS total_out FROM stockout_list WHERE item_id = '{$itemID}' AND YEAR(date) = '{$currentYear}' GROUP BY MONTH(date)");
    $stockOutData = [];
    while ($row = $stockOutQuery->fetch_assoc()) {
        $stockOutData[$row['month']] = $row['total_out'];
    }

    $wasteQuery = $conn->query("SELECT MONTH(date) AS month, SUM(quantity) AS total_waste FROM waste_list WHERE item_id = '{$itemID}' AND YEAR(date) = '{$currentYear}' GROUP BY MONTH(date)");
    $wasteData = [];
    while ($row = $wasteQuery->fetch_assoc()) {
        $wasteData[$row['month']] = $row['total_waste'];
    }

    $usedQuantityData = [];
    $suggestedQuantityData = [];
    $previousMonth = null;
    $previousUsedQuantity = null;
    for ($month = 1; $month <= 12; $month++) {
        $stockOut = isset($stockOutData[$month]) ? $stockOutData[$month] : 0;
        $waste = isset($wasteData[$month]) ? $wasteData[$month] : 0;
        $usedQuantity = isset($stockReceivedData[$month]) ? ($stockOut) : 0;
        $suggestedQuantity = $usedQuantity; // Default suggestion is the used quantity of the current month

        if ($previousMonth && $previousUsedQuantity) {
            $averageUsedQuantity = ($usedQuantity + $previousUsedQuantity) / 2; // Calculate the average of the current and previous month's used quantities
            $suggestedQuantity = $averageUsedQuantity * 1.2; // Adjust the factor according to your needs
        }

        $usedQuantityData[] = $usedQuantity;
        $suggestedQuantityData[] = $suggestedQuantity;
        $labels[] = date('F Y', mktime(0, 0, 0, $month, 1, $currentYear));

        $previousMonth = $month;
        $previousUsedQuantity = $usedQuantity;
    }

    $usedData[$item['name']] = $usedQuantityData;
    $suggestedData[$item['name']] = $suggestedQuantityData;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Stock Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="card card-outline bg-lightyellow rounded-0 shadow printout">
        <div class="card-header py-1">
            <div class="card-title">Suggested Stock-in Quantity by Month</div>
        </div>
        <div class="card-body">
            <div id="stock-chart-container">
                <div>
                    <label for="item-select">Select Item:</label>
                    <select id="item-select">
                        <?php
                        // Fetch items from the database and generate the options
                        $itemQuery = $conn->query("SELECT * FROM item_list");
                        while ($item = $itemQuery->fetch_assoc()) {
                            echo '<option value="' . $item['name'] . '">' . $item['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <canvas id="stock-chart"></canvas>
            </div>
        </div>
    </div>
    <script>
        // Retrieve the element where the chart will be rendered
        var ctx = document.getElementById('stock-chart').getContext('2d');

        // Chart data and options
        var chartData = {
            labels: [],
            datasets: []
        };

        var chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Month'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Quantity'
                    },
                    beginAtZero: true,
                    stepSize: 50
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ': ' + context.formattedValue;
                        }
                    }
                }
            }
        };

        // Create the line chart
        var chart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: chartOptions
        });

        // Function to update the chart based on the selected item
        function updateChart(item) {
            var selectedUsedData = <?php echo json_encode($usedData); ?>;
            var selectedSuggestedData = <?php echo json_encode($suggestedData); ?>;
            var selectedLabels = <?php echo json_encode($labels); ?>;

            var datasets = [
                {
                    label: 'Used Quantity',
                    data: selectedUsedData[item],
                    borderColor: 'blue',
                    fill: false
                },
                {
                    label: 'Suggested Quantity',
                    data: selectedSuggestedData[item],
                    borderColor: 'green',
                    fill: false
                }
            ];

            chart.data = {
                labels: selectedLabels,
                datasets: datasets
            };

            chart.update();
        }

        // Get the selected item and update the chart on change
        var itemSelect = document.getElementById('item-select');
        itemSelect.addEventListener('change', function () {
            var selectedItem = itemSelect.value;
            updateChart(selectedItem);
        });

        // Initialize the chart with the selected item (if any)
        var selectedItem = "<?php echo isset($_GET['item']) ? $_GET['item'] : ''; ?>";
        if (selectedItem) {
            itemSelect.value = selectedItem;
            updateChart(selectedItem);
        }
    </script>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <title>Stock Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body>
    <div class="card card-outline bg-lightyellow rounded-0 shadow printout">
        <div class="card-header py-1">
            <div class="card-title">Suggested Stock-in Quantity</div>
        </div>
        <div class="card-body">
            <div id="stock-chart-container">
                <div>
                    <label for="item-select">Select Item:</label>
                    <select id="item-select">
                        <?php
                        // Fetch items from the database and generate the options
                        $itemQuery = $conn->query("SELECT * FROM item_list");
                        while ($item = $itemQuery->fetch_assoc()) {
                            echo '<option value="' . $item['name'] . '">' . $item['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div id="stock-chart"></div>
            </div>
        </div>
    </div>
    <script>
        // Function to update the chart based on the selected item
        function updateChart(item) {
            var selectedUsedData = <?php echo json_encode($usedData); ?>;
            var selectedSuggestedData = <?php echo json_encode($suggestedData); ?>;
            var selectedLabels = <?php echo json_encode($labels); ?>;

            var chartOptions = {
                chart: {
                    type: 'line',
                    height: 400
                },
                series: [
                    {
                        name: 'Used Quantity',
                        data: selectedUsedData[item]
                    },
                    {
                        name: 'Suggested Quantity',
                        data: selectedSuggestedData[item]
                    }
                ],
                xaxis: {
                    categories: selectedLabels
                },
                yaxis: {
                    min: 0,
                    tickAmount: 10
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return value.toFixed(0);
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#stock-chart"), chartOptions);
            chart.render();
        }

        // Get the selected item and update the chart on change
        var itemSelect = document.getElementById('item-select');
        itemSelect.addEventListener('change', function () {
            var selectedItem = itemSelect.value;
            updateChart(selectedItem);
        });

        // Initialize the chart with the selected item (if any)
        var selectedItem = "<?php echo isset($_GET['item']) ? $_GET['item'] : ''; ?>";
        if (selectedItem) {
            itemSelect.value = selectedItem;
            updateChart(selectedItem);
        }
    </script>
</body>

</html>
