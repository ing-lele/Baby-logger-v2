<?php
// Settings
// host, user, and password settings
$db_host = "mysql.webserver.com";
$db_user = "logger";
$db_pass = "password";
$db_name = "babylogger";

// Make connection to database
$connectdb = mysqli_connect($db_host, $db_user, $db_pass) or die ("Cannot reach database");
mysqli_select_db($connectdb,$db_name) or die ("Cannot select database");

// show queries from the current date and the day before
if(!isset($_POST['days'])){
	$days = 2;
}else{
	$days = intval($_POST['days']);
}
if(isset($_POST['type']) && in_array($_POST['type'], ["pee", "poo", "fed"])){
	$type = $_POST['type'];
	$sql = "SELECT * FROM babylogger WHERE type = '$type' AND tdate >= CURDATE() - INTERVAL ".($days-1)." day ORDER BY tdate DESC, ttime DESC;";
}else{
	$sql = "SELECT * FROM babylogger WHERE tdate >= CURDATE() - INTERVAL ".($days-1)." day ORDER BY tdate DESC, ttime DESC;";
}

$results = mysqli_query($connectdb, $sql);
?>

<html>
<head>
<title>Baby Logger &#x1F476;</title>
<style>
body{
	background-color: #e6f2ff;
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
.pee{
	background-color: #ffff66;
}

.poo{
	background-color: #996600;
}

.fed{
	background-color: #ffffff;
}
</style>
</head>

<body>
<form method='POST'>
<center>
<?php
if ($days > 1)
    print "Baby's vital functions for the last $days days.";
else
    print "Baby's vital functions for the last $days day.";
?>
<hr width='200' size=1>
Show 
<select name='type'>
<option value='all'>All</option>
<option value='pee'>Pee</option>
<option value='poo'>Poop</option>
<option value='fed'>Feeding</option>
</select>
events for past <select name='days'>
<option value='1'>1</option>
<option value='2'>2</option>
<option value='3'>3</option>
<option value='4'>4</option>
<option value='5'>5</option>
<option value='6'>6</option>
<option value='7'>7</option>
<option value='14'>14</option>
<option value='21'>21</option>
<option value='31'>31</option>
<option value='365'>365</option>
</select> days.
<input type='submit' value='Update'>
</center>
</form>

<table width="600" border="1" cellpadding="1" cellspacing="1" align="center">
<tr>
<th width='200px'>Date</th>
<th width='200px'>Time</th>
<th width='200px'>Event</th>
<tr>
<?php
$event_count = 0;
// loop all the results that were read from database and "draw" to web page
while($event = mysqli_fetch_assoc($results)){
	$event_count++;
	echo "<tr>";
	if ($event['type'] == "fed"){
		echo "<td class='fed'>". date("M d y", strtotime($event['tdate'])) ."</td>";
		echo "<td class='fed'>". date("g:i a", strtotime($event['ttime'])) ."</td>";
		echo "<td class='fed'><center>&#x1f37c;</center></td>";
	}else if ($event['type'] == "pee"){
		echo "<td class='pee'>". date("M d y", strtotime($event['tdate'])) ."</td>";
		echo "<td class='pee'>". date("g:i a", strtotime($event['ttime'])) ."</td>";
		echo "<td class='pee'><center>&#128166;</center></td>";
	}else if ($event['type'] == "poo"){
		echo "<td class='poo'>". date("M d y", strtotime($event['tdate'])) ."</td>";
		echo "<td class='poo'>". date("g:i a", strtotime($event['ttime'])) ."</td>";
		echo "<td class='poo'><center>&#128169;</center></td>";
	}else{
		echo "<td>". date("M d y", strtotime($event['tdate'])) ."</td>";
		echo "<td>". date("g:i a", strtotime($event['ttime'])) ."</td>";
		echo "<td style='background-color: red;'><center><b>Error</b></center></td>";
	}
	
	echo "<tr>\n";
}
echo "</table>\n";
echo "Event count: $event_count<br>";

?>
</body>
</html>
