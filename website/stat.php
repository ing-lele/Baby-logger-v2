<?php
// Statistic page
// =========================================================
// Daily entry with:
// Date | Pee Count | Poo Count | Milk Count | Milk Duration
//
// Drop down:
// * Current week
// * Current month
// * Last 3 months
// * Current semester


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
if(!isset($_POST['month'])){
	$month = 0.5;
}else{
	$month = intval($_POST['month']);
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
	WHERE ts_start>= CURRENT_DATE() - INTERVAL ".($month)." MONTH 
	GROUP BY DATE(ts_start)
	ORDER BY DATE(ts_start) DESC;";

$results = mysqli_query($connectdb, $sql);
?>

<html>
<head>
<title>Baby Logger &#x1F476; - Stats</title>
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

<form method='POST'>
<center>

<?php
if ($month > 1)
    print "Baby's stats for the last $month months.";
else
    print "Baby's stats for the last $month month.";
?>
<br><br>

Show stats for past <select name='month'>
<option value='0.5'>0.5</option>
<option value='1'>1</option>
<option value='3'>3</option>
<option value='6'>6</option>
<option value='12'>12</option>
</select> months.
<input type='submit' value='Update'>
</center>
</form>

<table border="1" cellpadding="1" cellspacing="1" align="center">
<tr>
<th>Day</th>
<th>&#128166; Count</th>
<th>&#128169; Count</th>
<th>&#x1f37c; Count</th>
<th>&#x1f37c; Duration</th>
<th>&#x1f37c; Duration [min]</th>
<tr>
<?php
$event_count = 0;
// loop all the results that were read from database and "draw" to web page
while($event = mysqli_fetch_assoc($results)){
	$event_count++;
	echo "<tr>";
	
	try {
		echo "<td>". date("d M y", strtotime($event['day'])) ."</td>";		
		echo "<td class='pee'>". $event['pee_count'] ."</td>";
		echo "<td class='poo'>". $event['poo_count'] ."</td>";
		echo "<td class='fed'>". $event['fed_count'] ."</td>";
		echo "<td class='fed'>". strtotime($event['fed_duration']) ."</td>";
		echo "<td class='fed'>". $event['fed_duration'] ."</td>";
		}
	catch (Exception $ex) {
		echo "<td><center>Failed to create table</center></td>";
		echo "<td><center>$er</center></td>";
	}
	
	echo "</tr>";
}
echo "</table>";
echo "<p align='center'>Event count: $event_count<br></p>";

?>
</body>
</html>

