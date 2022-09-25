<?php
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
include 'mysql_variables.php';

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
	$days = 2;
}else{
	$days = intval($_POST['days']);
}
if(isset($_POST['category']) && in_array($_POST['category'], ["pee", "poo", "fed"])){
	$category = $_POST['category'];
	$sql = "SELECT * FROM buttondata WHERE category = '$category' AND created >= NOW() - INTERVAL ".($days-1)." day ORDER BY id DESC;";
}else{
	$sql = "SELECT * FROM buttondata WHERE created >= NOW() - INTERVAL ".($days-1)." day ORDER BY id DESC;";
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
	background-color: #add8e6;
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
<hr width='500px' size=1>
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

<table width="500px" border="1" cellpadding="1" cellspacing="1" align="center">
<tr>
<th width='50px'>ID</th>
<th width='300px'>Date & Time</th>
<th width='100px'>Category</th>
<th width='50px'>State</th>
<tr>
<?php
$event_count = 0;
// loop all the results that were read from database and "draw" to web page
while($event = mysqli_fetch_assoc($results)){
	$event_count++;
	echo "<tr>";
	if ($event['category'] == "fed"){
		echo "<td class='fed'><center>". $event['id'] ."</center></td>";
		echo "<td class='fed'><center>". date("d M y G:i", strtotime($event['created'])) ."</center></td>";
		echo "<td class='fed'><center>&#x1f37c;</center></td>"; //Show baby bottle emoji
		echo "<td class='fed'><center>". $event['state'] ."</center></td>";
	}else if ($event['category'] == "pee"){
		echo "<td class='pee'><center>". $event['id'] ."</center></td>";
		echo "<td class='pee'><center>". date("d M y G:i", strtotime($event['created'])) ."</center></td>";
		echo "<td class='pee'><center>&#128166;</center></td>";  //Show pee emoji
		echo "<td class='pee'><center>". $event['state'] ."</center></td>";
	}else if ($event['category'] == "poo"){
		echo "<td class='poo'><center>". $event['id'] ."</center></td>";
		echo "<td class='poo'><center>". date("d M y G:i", strtotime($event['created'])) ."</center></td>";
		echo "<td class='poo'><center>&#128169;</center></td>";  //Show poop emoji
		echo "<td class='poo'><center>". $event['state'] ."</center></td>";
	}else{
		echo "<td><center>". $event['id'] ."</center></td>";
		echo "<td><center>". date("d M y G:i", strtotime($event['created'])) ."</center></td>";
		echo "<td style='background-color: red;'><center><b>Error</b></center></td>";
		echo "<td><center>". $event['state'] ."</center></td>";
	}
	
	echo "<tr>\n";
}
echo "</table>\n";
echo "<center>Event count: $event_count<br></center>";

?>
</body>
</html>
