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

// Stats (
//	day DATE,
//	pee_count INT,
//	poo_count INT,
//	fed_count INT,
//	fed_time TIME)

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

$results = mysqli_query($connectdb, $sql);

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

$arrLabels = array("January","February","March","April","May","June","July");
$arrDatasets = array(
    'label' => "My First dataset",
    'fillColor' => "rgba(220,220,220,0.2)", 
    'strokeColor' => "rgba(220,220,220,1)", 
    'pointColor' => "rgba(220,220,220,1)", 
    'pointStrokeColor' => "#fff", 
    'pointHighlightFill' => "#fff", 
    'pointHighlightStroke' => "rgba(220,220,220,1)", 
    'data' => array('28', '48', '40', '19', '86', '27', '90'));

$arrReturn = array(
    array(
        'labels' => $arrLabels,
        'datasets' => $arrDatasets));

print (json_encode($arrReturn));



$i=0; $q=mysql_query('select ..');

while($row=mysql_fetch_array($q)){               

    echo "myarray[".$i."]='".$row['data']."';";

    $i++;  
}


?>