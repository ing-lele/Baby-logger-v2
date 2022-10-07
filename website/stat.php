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

// TABLE buttondata(
//	id INT PRIMARY KEY auto_increment,
//	created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
//	category TEXT,
//	state TEXT); 

// show queries from the current date and the day before
if(!isset($_POST['days'])){
	$days = 7;
}else{
	$days = intval($_POST['days']);
}
if(isset($_POST['category']) && in_array($_POST['category'], ["pee", "poo", "fed"])){
	$category = $_POST['category'];
	$sql = "SELECT * FROM buttondata WHERE category = '$category' AND created >= CURRENT_DATE() - INTERVAL ".($days-1)." day ORDER BY id DESC;";
}else{
	$sql = "SELECT * FROM buttondata WHERE created >= CURRENT_DATE() - INTERVAL ".($days-1)." day ORDER BY id DESC;";
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
    print "Baby's stats for the last $days days.";
?>

Show data for past <select name='days'>
<option value='7'>week</option>
<option value='30'>month</option>
<option value='90'>3 months</option>
<option value='180'>semester</option>
<option value='365'>year</option>
</select>
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
		echo "<td class='fed'><center>". $event['fee_time'] ."</center></td>";
		}
	catch:
		{
		echo "<td><center>error</center></td>";
	}
	
	echo "</tr>";
}
echo "</table>";
echo "<p align='center'>Event count: $event_count<br></p>";

?>
</body>
</html>
