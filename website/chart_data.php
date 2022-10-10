<?php
// Generate JS array for count and duration to use in chart.php
// =========================================================

// For debug, enable the following
// ini_set("display_error", "stderr");
// ini_set("display_startup_errors", 1);
// ini_set("log_errors", 1);
// ini_set("html_errors", 1);

// DB Connection settings - mysql_variables.php
//$db_host
//$db_user
//$db_pass
//$db_name
//$db_table
include_once 'mysql_variables.php';

// Make connection to database
$connectdb = mysqli_connect($db_host, $db_user, $db_pass) or die ("ERROR - Cannot reach database");
mysqli_select_db($connectdb,$db_name) or die ("ERROR - Cannot select database");

// Querie stat from the current date
if(!isset($_POST['weeks'])){
	$weeks = 2;
}else{
	$weeks = floatval($_POST['weeks']);
}

// Show count of pee, poo, fed, fed_duration by day
$sql = "SELECT 
	DATE(ts_start) AS day, 
	COUNT(CASE WHEN category = 'pee' THEN id END) AS pee_count,
	COUNT(CASE WHEN category = 'poo' THEN id END) AS poo_count,
	COUNT(CASE WHEN category = 'fed' THEN id END) AS fed_count,
	SEC_TO_TIME(SUM(CASE WHEN category = 'fed' THEN TIME_TO_SEC(TIMEDIFF(ts_end,ts_start)) END)) AS fed_duration 
	FROM switchdata
	WHERE ts_start>= CURRENT_DATE() - INTERVAL ".($weeks)." WEEK 
	GROUP BY DATE(ts_start)
	ORDER BY DATE(ts_start) DESC;";

$results = mysqli_query($connectdb, $sql) or die(mysql_error());

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

// Initialize variables
$event_count = 0;
$x_labels = array();
$data_pee_count = array();
$data_poo_count = array();
$data_fed_count = array();
$data_fed_duration = array();

// loop all the results that were read from database
while($event = mysqli_fetch_assoc($results)){
    // Query result structure  (
    //  	day DATE,
    //	    pee_count INT,
    //	    poo_count INT,
    //  	fed_count INT,
    //	    fed_time TIME)

	$event_count++;

    $x_labels[] = date("d M y", strtotime($event['day']));
    $data_pee_count[]  = $event['pee_count'];
    $data_poo_count[] = $event['poo_count'];
    $data_fed_count[] = $event['fed_count'];
    $data_fed_duration[] = $event['fed_duration'];

}

// Print to check arrays values
echo "<br>Lable array: ";
print_r($x_labels);

echo "<br>Pee array: ";
print_r($data_pee_count);

echo "<br>Poo array: ";
print_r($data_poo_count);

echo "<br>Fed array: ";
print_r($data_fed_count);

echo "<br>Duration array: ";
print_r($data_fed_duration);


// --------------------------
// Initialize dataset

// Chart -> Config -> Data -> Dataset #1 -> Pee count
$datasets_pee_count = array (
    'type' => "line",
    'label' => "Pee Count",
    'backgroundColor' => "#ffff66",
    'borderColor' => "#ffff66",
    'data' => $data_pee_count
);

// Chart -> Config -> Data -> Dataset #2 -> Poo count
$datasets_poo_count = array(
    'type' => "line",
    'label' => "Poo Count",
    'backgroundColor' => "#996600",
    'borderColor' => "#996600",
    'data' => $data_poo_count
);

// Chart -> Config -> Data -> Dataset #3 -> Milk count
$datasets_fed_count = array(
    'type' => "line",
    'label' => "Milk Count",
    'backgroundColor' => "#399cbd",
    'borderColor' => "#399cbd",
    'data' => $data_fed_count
);

// Chart -> Config -> Data -> Dataset #4 -> Milk duration
$datasets_fed_duration = array(
    'type' => "bar",
    'label' => "Milk Duration",
    'backgroundColor' => "#add8e6",
    'borderColor' => "#add8e6",
    'data' => $data_fed_duration
);

echo "<br>Pee count dataset: ";
print_r($datasets_pee_count);

echo "<br>Poo count dataset: ";
print_r($datasets_poo_count);

echo "<br>Fed count dataset: ";
print_r($datasets_fed_count);

echo "<br>Fed duration dataset: ";
print_r($datasets_fed_duration);

// --------------------------
// Initialize Chart Data
//
// Chart -> Config -> Data = 
// {
//      labels:
//      dataset: [
//          {type1, label1, data1},
//          {type2, label2, data2},
//          {...}
//      ]
// }
$chart_data = array(
    array(
        'labels' => $x_labels,
        'datasets' => array(
            $datasets_pee_count,
            $datasets_poo_count,
            $datasets_fed_count,
            $datasets_fed_duration
        )
    )
);

echo "<br>Chart data: ";
print_r ($chart_data);

// Encond in JSON format
print(json_encode($chart_data));

?>