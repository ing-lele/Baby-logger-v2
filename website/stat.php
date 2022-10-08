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
include_once 'mysql_variables.php';

// Make connection to database
$connectdb = mysqli_connect($db_host, $db_user, $db_pass) or die ("ERROR - Cannot reach database");
mysqli_select_db($connectdb,$db_name) or die ("ERROR - Cannot select database");

//
// https://www.techrepublic.com/article/10-tips-for-sorting-grouping-and-summarizing-sql-data
// 
// 

// 
// Summary Table (
//	created TIMESTAMP,
//	pee_count INT,
//	poo_count INT,
//	fee_count INT,
//	fee_time TIMESTAMP)

// Querie stat from the current date
if(!isset($_POST['month'])){
	$month = 0.5;
}else{
	$month = intval($_POST['month']);
}

if(!isset($_POST['category'])){
	$category = 'pee';
}else{
	$category = ($_POST['category']);
}

if($category == "pee" or $category == "poo"){
	// Poo + Pee stats
	$sql = "SELECT DATE(created) as day, COUNT(*) as ".$category."_count FROM buttondata WHERE category = '$category' AND state='start' AND created >= CURRENT_DATE() - INTERVAL '$month' MONTH GROUP BY day DESC;"
}elseif($category == "fed"){
	// Fee stats
	$sql = "SELECT DATE(created) as day, COUNT(*) as ".$category."_count FROM buttondata WHERE category = '$category' AND state='start' AND created >= CURRENT_DATE() - INTERVAL '$month' MONTH GROUP BY day DESC;"
}else{
	// All stats
	$sql = "SELECT * FROM buttondata WHERE created >= CURRENT_DATE() - INTERVAL ".($month)." MONTH ORDER BY id DESC;";
}

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
	text-align: right;
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

Show 
<select name='category'>
<option value='pee'>Pee</option>
<option value='poo'>Poop</option>
<option value='fed' >Feeding</option>
<!--<option value='all'>All</option>-->
</select>
stats for past <select name='month'>
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
<th>Date & Time</th>
<th>&#128166; Count</th>
<th>&#128169; Count</th>
<th>&#x1f37c; Count</th>
<th>&#x1f37c; Duration</th>
<tr>
<?php
$event_count = 0;
// loop all the results that were read from database and "draw" to web page
while($event = mysqli_fetch_assoc($results)){
	$event_count++;
	echo "<tr>";
	
	try {
		echo "<td><center>". date("d M y G:i", strtotime($event['created'])) ."</center></td>";
		echo "<td class='pee'><center>". $event['pee_count'] ."</center></td>";
		echo "<td class='poo'><center>". $event['poo_count'] ."</center></td>";
		echo "<td class='fed'><center>". $event['fee_count'] ."</center></td>";
		echo "<td class='fed'><center>". date("G:i", strtotime($event['fee_time'])) ."</center></td>";
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
