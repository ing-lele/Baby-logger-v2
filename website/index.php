<?php
ini_set("display_error", "stderr");
ini_set("display_startup_errors", 1);
ini_set("log_errors", 1);
ini_set("html_errors", 1);
ini_set("error_log", "~/Baby-logger/website/php-error.log");
error_log( "Hello, errors!" );

// DB Connection settings
//$db_host
//$db_user
//$db_pass
//$db_name
include(mysql_variables.php);

print_r("DEBUG - " + get_defined_vars());

// Make connection to database
$connectdb = mysqli_connect($db_host, $db_user, $db_pass) or die ("ERROR - Cannot reach database");
mysqli_select_db($connectdb,$db_name) or die ("ERROR - Cannot select database");

print_r("DEBUG - " + get_defined_vars());

// TABLE buttondata(
//	id INT PRIMARY KEY auto_increment,
//	created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
//	category TEXT,
//	state TEXT); 

// show queries from the current date and the day before
if(!isset($_POST['days'])){
	$days = 2;
}else{
	$days = intval($_POST['days']);
}
if(isset($_POST['category']) && in_array($_POST['category'], ["pee", "poo", "fed"])){
	$type = $_POST['category'];
	$sql = "SELECT * FROM buttondata WHERE category = "$type" AND created >= NOW() - INTERVAL ".($days-1)." day ORDER BY created DESC;";
	print($sql);
}else{
	$sql = "SELECT * FROM buttondata WHERE created >= NOW() - INTERVAL ".($days-1)." day ORDER BY created DESC;";
	print(sql)
}

$results = mysqli_query($connectdb, $sql);
?>

<html>
<head>
<title>Baby Logger &#x1F476;</title>
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
<select name='category'>
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
<th width='50px'>ID</th>
<th width='300px'>Date & Time</th>
<th width='50px'>Category</th>
<th width='200px'>State</th>
<tr>
<?php
$event_count = 0;
// loop all the results that were read from database and "draw" to web page
while($event = mysqli_fetch_assoc($results)){
	$event_count++;
	echo "<tr>";
	if ($event['category'] == "fed"){
		echo "<td class='fed'>". $event['id'] ."</td>";
		echo "<td class='fed'>". date("M d y g:i a", strtotime($event['timestamp'])) ."</td>";
		echo "<td class='fed'>". $event['state'] ."</td>";
		echo "<td class='fed'><center>&#x1f37c;</center></td>"; //Show baby bottle emoji
	}else if ($event['category'] == "pee"){
		echo "<td class='pee'>". $event['id'] ."</td>";
		echo "<td class='pee'>". date("M d y g:i a", strtotime($event['timestamp'])) ."</td>";
		echo "<td class='pee'>". $event['state'] ."</td>";
		echo "<td class='pee'><center>&#128166;</center></td>";  //Show pee emoji
	}else if ($event['category'] == "poo"){
		echo "<td class='poo'>". $event['id'] ."</td>";
		echo "<td class='poo'>". date("M d y g:i a", strtotime($event['timestamp'])) ."</td>";
		echo "<td class='poo'>". $event['state'] ."</td>";
		echo "<td class='poo'><center>&#128169;</center></td>";  //Show poop emoji
	}else{
		echo "<td>". $event['id'] ."</td>";
		echo "<td>". date("M d y g:i a", strtotime($event['timestamp'])) ."</td>";
		echo "<td>". $event['state'] ."</td>";
		echo "<td style='background-color: red;'><center><b>Error</b></center></td>";
	}
	
	echo "<tr>\n";
}
echo "</table>\n";
echo "Event count: $event_count<br>";

?>
</body>
</html>
