<?php
// Generate chart using Chart.js
// Info: https://www.chartjs.org/
// =========================================================

// Data from MySQL
// include_once 'chart_data.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Baby &#x1F476; Charts</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <style>
    body{
        background-color: #fff3f5; /* pink background */
    }
    table{
        background-color: white;
        border: 1px solid black;
        border-spacing: 0px 0px;
    }
    th, td{
        font-family: arial;
    }
    td{
        text-align: center;
        font-size: 32px;
        padding: 2px;
    }
    </style>
</head>

<body>

<?php
	// Include Navigation bar
	include_once 'navigation.php';
?>

<form method='POST'>
<center>

<?php
	$updated_date = date_modify(new DateTime(), "-". $weeks ." week");
	echo "<P>Baby's stats for last <b>$weeks weeks</b> since ". date_format($updated_date, "d M y") .".</P>";
?>

Show stats for past <select name='weeks'>
<option value='2'>2</option>
<option value='4'>4</option> 
<option value='9'>9</option>
<option value='13'>13</option>
<option value='26'>26</option>
<option value='52'>52</option>
</select> weeks.
<input type='submit' value='Update'>
</center>
</form>

<!-- 
// Using chart.js to create chart
// https://www.chartjs.org/docs/latest/getting-started/

// Creating canvas -->
<div>
    <canvas id='BabyStatChart'></canvas>
</div>

<!-- // Load Chart.js -->
<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
<script>

// --------------------------
// --- Chart config - start
// --------------------------

// Chart -> Options
const chart_option = {
    scales: {
        y:{
            beginAtZero: true
        }
    }
};

// Chart -> Data -> Labels - used for all data in dataset
const x_labels = [
    'January', 'February', 'March', 'April', 'May','June'
];

// --------------------------
// Chart -> Config -> Data = 
// {
//      labels:
//      dataset: [
//          {type1, label1, data1},
//          {type2, label2, data2},
//          {...}
//      ]
// }
// --------------------------
const chart_data = {
    labels: x_labels,
    datasets: [
<?php // Chart -> Config -> Data -> Dataset #1 -> Pee count ?>
        {
            type: 'line',
            label: 'Pee Count',
            backgroundColor: '#ffff66',
            borderColor: '#ffff66',
            data:
                <?php
                // Gettin pee count data
                // Format example: [3, 13, 8, 5, 23, 33, 28]
                
                ?>
        },
<?php // Chart -> Config -> Data -> Dataset #2 -> Poo count ?>
        {
            type: 'line',
            label: 'Poo Count',
            backgroundColor: '#996600',
            borderColor: '#996600',
            data:
            <?php
                // Gettin poo count data
                // Format example: [1, 11, 6, 3, 21, 31, 26]
                
            ?>
        },
<?php // Chart -> Config -> Data -> Dataset #3 -> Milk count ?>
        {
            type: 'line',
            label: 'Milk Count',
            backgroundColor: '#399cbd',
            borderColor: '#399cbd',
            data:
            <?php
                // Gettin milk count data
                // Format example: [0, 10, 5, 2, 20, 30, 25]
                
            ?>
        },
<?php // Chart -> Config -> Data -> Dataset #4 -> Milk duration ?>
        {
            type: 'bar',
            label: 'Milk Duration',
            backgroundColor: '#add8e6',
            borderColor: '#add8e6',
            data: 
            <?php
                // Gettin milk duration data
                // Format example: [15, 15, 15, 10, 20, 15, 5]
                
            ?>
        }
    ]
};

// --------------------------
// Chart -> Config
// {
//      type: 'scatter',
//      data: chart_data,
//      options: chart_option
// }
// --------------------------
const chart_config = {
    type: 'scatter',
    data: chart_data,
    options: chart_option
};

// --------------------------
// --- Chart config - end
// --------------------------

// Create chart
const MyBabyStatChart = new Chart(
    document.getElementById('BabyStatChart').getContext("2d"),
    chart_config
);

</script>

</body>
</html>
