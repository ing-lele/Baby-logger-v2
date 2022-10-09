<?php
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

// TABLE switchdata(
//  id INT PRIMARY KEY auto_increment NOT NULL,
//  ts_start TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
//  ts_end TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
//  category TEXT

// show queries from the current date and the day before
if(!isset($_POST['days'])){
	$days = 2;
}else{
	$days = intval($_POST['days']);
}
if(isset($_POST['category']) && in_array($_POST['category'], ["pee", "poo", "fed"])){
	$category = $_POST['category'];
	$sql = "SELECT ts_start, category, TIMEDIFF(ts_end,ts_start) AS duration FROM $db_table WHERE category = '$category' AND ts_start >= CURRENT_DATE() - INTERVAL ".($days-1)." day ORDER BY id DESC;";
}else{
	$sql = "SELECT ts_start, category, TIMEDIFF(ts_end,ts_start) AS duration FROM $db_table WHERE ts_start >= CURRENT_DATE() - INTERVAL ".($days-1)." day ORDER BY id DESC;";
}

$results = mysqli_query($connectdb, $sql);
?>

<html>
<head>
<title>Baby &#x1F476; Charts</title>
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

<?php
$event_count = 0;
// loop all the results that were read from database and "draw" to web page
while($event = mysqli_fetch_assoc($results)){
	$event_count++;
	echo "<tr>";
	if ($event['category'] == "fed"){
		echo "<td class='fed'>". date("d M y G:i", strtotime($event['ts_start'])) ."</td>";
		echo "<td class='fed'>&#x1f37c;</td>"; //Show baby bottle emoji
		echo "<td class='fed'>". date("G:i:s", strtotime($event['duration'])) ."</td>";

	}else if ($event['category'] == "pee"){
		echo "<td class='pee'>". date("d M y G:i", strtotime($event['ts_start'])) ."</td>";
		echo "<td class='pee'>&#128166;</td>";  //Show pee emoji
		echo "<td class='pee'>". date("G:i:s", strtotime($event['duration'])) ."</td>";

	}else if ($event['category'] == "poo"){
		echo "<td class='poo'>". date("d M y G:i", strtotime($event['ts_start'])) ."</td>";
		echo "<td class='poo'>&#128169;</td>";  //Show poop emoji
		echo "<td class='pee'>". date("G:i:s", strtotime($event['duration'])) ."</td>";

	}else{
		echo "<td>". date("d M y G:i", strtotime($event['ts_start'])) ."</td>";
		echo "<td>&#10060;</td>"; //Show cross mark emoji
		echo "<td style='background-color: red;'><center><b>Error</b></center></td>";		
	}
	
	echo "<tr>\n";
}
echo "</table>\n";
echo "<p align='center'>Event count: $event_count<br></p>";

?>

</body>
</html>
