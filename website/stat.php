<?php
// --- Show statistics about metrics
// =========================================================

/* For debug, enable the following
ini_set("display_error", "stderr");
ini_set("display_startup_errors", 1);
ini_set("log_errors", 1);
ini_set("html_errors", 1);
*/

// Daily entry with:
// Date | Pee Count | Poo Count | Milk Count | Milk Duration
//
// Drop down:
// * Current week
// * Current month
// * Last 3 months
// * Current semester

// Include sql data function
include_once 'sql_data.php';

// Check page parameters
if(!isset($_POST['weeks'])) {
	// default to 2 weeks
	$weeks = 2;
}
else {
	// Get from FORM
	$weeks = int($_POST['weeks']);
}

// ---------------------
// Daily entry with:
// Date | Pee Count | Poo Count | Milk Count | Milk Duration

// Get SQL data in JSON format
$sql_json_data = get_sql_data($weeks,"DESC");

// decode JSON to array
$sql_data = json_decode($sql_json_data, true);

/* Print SQL data
echo ("<pre>");
print_r($sql_data);
echo ("</pre>");
*/

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Baby &#x1F476; Statistics</title>
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

<?php
	// Include Navigation bar
	include_once 'navigation.php';
?>

<form method='POST' name="weeks_form">
<center>

<?php
echo "<P>Baby's stats for last <b>$weeks weeks</b> since ". date("d M Y", strtotime('-'.$weeks.' weeks')) .".</P>";
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

<script>
  $('#weeks_form').submit(function(){
    var post_params = $('#weeks_form').serialize();
    $('#waiting').show();
    $.post('stat.php', post_params, function(data) {
      $('#waiting').hide();
      return false;
    })
  });
</script>

<br>

<table border="1" cellpadding="1" cellspacing="1" align="center">
<tr>
<th>Day</th>
<th class='pee'>&#128166; Count</th>
<th class='poo'>&#128169; Count</th>
<th class='fed'>&#x1f37c; Count</th>
<th class='fed'>&#x1f37c; Duration</th>
<tr>

<?php

// read JSON data
$event_count = 0;
// loop all the results from DB and create table
foreach($sql_data as $event){
	$event_count++;
	echo "<tr>";

	// data_structure[
	//	day UNIX_TIMESTAMP(DATE),
	//	pee_count INT,
	//	poo_count INT,
	//  fed_count INT,
	//	fed_time TIME]

	try {
		echo "<td>". date("d M Y", $event['day']) ."</td>";		
		echo "<td class='pee'>". $event['pee_count'] ."</td>";
		echo "<td class='poo'>". $event['poo_count'] ."</td>";
		echo "<td class='fed'>". $event['fed_count'] ."</td>";
		echo "<td class='fed'>". gmdate("H:i:s", $event['fed_duration']) ."</td>";
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

