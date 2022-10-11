<?php
// Generate chart using Chart.js
// Info: https://www.chartjs.org/
// =========================================================

/* For debug, enable the following
 ini_set("display_error", "stderr");
 ini_set("display_startup_errors", 1);
 ini_set("log_errors", 1);
 ini_set("html_errors", 1);
*/

// Include sql data function
include_once 'sql_data.php';

?>

<!DOCTYPE html>
<html lang="en">

<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>

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
    .wrapper{
        width: 500px;
        text-align: center;
        display: inline-block;
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

<!-- chart.js to create chart
https://www.chartjs.org/docs/latest/getting-started/

Creating canvas -->
<div style="text-align: center">
    <div class="wrapper">
        <canvas id='BabyStatChart'></canvas>
    </div>
</div>

<?php
// ---------------------
// Query stat from the current date
if(!isset($_POST['weeks'])){
	$weeks = 2;
}else{
	$weeks = floatval($_POST['weeks']);
}

// ---------------------
// Daily entry with:
// Date | Pee Count | Poo Count | Milk Count | Milk Duration

// Get SQL data in JSON format
$sql_json_data = get_sql_data($weeks,"ASC");

// decode JSON to array
$sql_data = json_decode($sql_json_data, true);

/* print Chart data
echo "<pre>";
print_r($chart_data);
echo "</pre>";
*/

// Initialize variables
$event_count = 0;

// loop all the results from DB and save to individual array
foreach($sql_data as $event){
    $event_count++;

    // data_structure[
    //	day DATE,
    //	pee_count INT,
    //	poo_count INT,
    //  fed_count INT,
    //	fed_time TIME]

    try {
        $x_labels[] = date("d M y", strtotime($event['day']));
        $data_pee_count[]  = $event['pee_count'];
        $data_poo_count[] = $event['poo_count'];
        $data_fed_count[] = $event['fed_count'];
        $data_fed_duration[] = $event['fed_duration'];
    }
    catch (Exception $ex) {
        echo "<td><center>Failed to create table</center></td>";
        echo "<td><center>$er</center></td>";
    }
}

?>

<script>

// Set data variable from PHP via JSON format
const x_labels = (<?php echo json_encode($x_labels); ?>);
const data_pee_count = (<?php echo json_encode($data_pee_count); ?>);
const data_poo_count = (<?php echo json_encode($data_poo_count); ?>);
const data_fed_count = (<?php echo json_encode($data_fed_count); ?>);
const data_fed_duration = (<?php echo json_encode($data_fed_duration); ?>);

/* Print array
console.log(x_labels);
console.log(data_pee_count);
console.log(data_poo_count);
console.log(data_fed_count);
console.log(data_fed_duration);
*/

// --------------------------
// --- Chart config - start
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
        // Chart -> Config -> Data -> Dataset #1 -> Pee count
        {
            type: 'line',
            label: 'Pee Count',
            backgroundColor: '#ffff66',
            borderColor: '#ffff66',
            data: data_pee_count
        },
        // Chart -> Config -> Data -> Dataset #2 -> Poo count
        {
            type: 'line',
            label: 'Poo Count',
            backgroundColor: '#996600',
            borderColor: '#996600',
            data: data_poo_count
        },
        // Chart -> Config -> Data -> Dataset #3 -> Milk count
        {
            type: 'line',
            label: 'Milk Count',
            backgroundColor: '#399cbd',
            borderColor: '#399cbd',
            data: data_fed_count
        },
        // Chart -> Config -> Data -> Dataset #4 -> Milk duration
        {
            type: 'bar',
            label: 'Milk Duration',
            backgroundColor: '#add8e6',
            borderColor: '#add8e6',
            data: data_fed_duration
        }
    ]
};

// --------------------------
// Chart -> Config -> Options
const chart_option = {
    responsive:true,
    scales: {
        y:{
            beginAtZero: true
        }
    }
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

// Get context
ctx = document.getElementById("BabyStatChart").getContext("2d");

// Draw chart
var MyBabyStatChart = new Chart(
    ctx,
    chart_config
);

</script>

</body>
</html>
