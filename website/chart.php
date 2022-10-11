<?php
// Generate chart using Chart.js
// Info: https://www.chartjs.org/
// =========================================================

// For debug, enable the following
 ini_set("display_error", "stderr");
 ini_set("display_startup_errors", 1);
 ini_set("log_errors", 1);
 ini_set("html_errors", 1);

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
<div>
    <canvas id='BabyStatChart' width="500" height="500"></canvas>
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
$sql_json_data = get_sql_data($weeks,"DESC");

// decode JSON to array
$sql_data = json_decode($sql_json_data, true);

/* print Chart data
echo "<pre>";
print_r($chart_data);
echo "</pre>";
*/


// Initialize variables
$event_count = 0;
/*
$x_labels = array();
$data_pee_count = array();
$data_poo_count = array();
$data_fed_count = array();
$data_fed_duration = array();
*/

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

//Convert to JSON
$X_labels_json = json_encode($x_labels, JSON_PRETTY_PRINT);
$data_pee_count_json = json_encode($data_pee_count, JSON_PRETTY_PRINT);
$data_poo_count_json = json_encode($data_poo_count, JSON_PRETTY_PRINT);
$data_fed_count_json = json_encode($data_fed_count, JSON_PRETTY_PRINT);
$data_fed_duration_json = json_encode($data_fed_duration, JSON_PRETTY_PRINT);

?>

<script>

// Set data variable
const x_lables = JSON.parse(<?php print_r($x_labels_json);?>);
const data_pee_count = JSON.parse(<?php print_r($data_pee_count_json);?>);
const data_poo_count = JSON.parse(<?php print_r($data_poo_count_json);?>);
const data_fed_count = JSON.parse(<?php print_r($data_fed_count_json);?>);
const data_fed_duration = JSON.parse(<?php print_r($data_fed_duration_json);?>;

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
    // Data example: ['January', 'February', 'March', 'April', 'May','June'];
    labels: x_lables;
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
