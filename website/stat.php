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
include 'sql_data.php';

// default to 2 weeks
$weeks = 2;

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Baby &#x1F476; Statistics</title>
	<link rel="icon" type="image/x-icon" href="favicon.ico">
	<link rel="stylesheet" href="baby_logger.css">
</head>

<body>

<?php
	// Include Navigation bar
	include_once 'navigation.php';
?>

<center>
	<form method='POST' action=<?php echo $_SERVER['PHP_SELF'];?>>
		<?php echo "<P>Baby's stats for last <b>$weeks weeks</b> since ". date("d M Y", strtotime('-'.$weeks.' weeks')) .".</P>"; ?>

		Show stats for past <select name='weeks' id='weeks'>
		<option value=2 <?php ($weeks==2) ? 'selected' : ''; ?>>2</option>
		<option value=4 <?php ($weeks==4) ? 'selected' : ''; ?>>4</option> 
		<option value=9 <?php ($weeks==9) ? 'selected' : ''; ?>>9</option>
		<option value=13 <?php ($weeks==13) ? 'selected' : ''; ?>>13</option>
		<option value=26 <?php ($weeks==26) ? 'selected' : ''; ?>>26</option>
		<option value=52 <?php ($weeks==52) ? 'selected' : ''; ?>>52</option>
		</select> weeks.
		<input type='submit' value='Update'>
	</form>
</center>

<?php
echo "<p align='center'>Weeks variable: $weeks </p>";
echo "<p align='center'>Weeks POST data:" + intval($_POST['weeks']) + "</p>";

// Read weeks from POST Form
//if ($_SERVER["REQUEST_METHOD"] == "POST") {
if(!isset($_POST['weeks'])) {
	// Default to 2 weeks
	$weeks = 2;
}
else {
	// Read from POST
	$weeks = intval($_POST['weeks']);
	// Display info 			
	echo "<p align='center'>Weeks after reading POST: $weeks </p>";
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

// Table header
echo "<br>";

echo "<table border='1' cellpadding='1' cellspacing='1' align='center'>";
echo "<tr>";
echo "<th>Day</th>";
echo "<th class='pee'>&#128166; Count</th>";
echo "<th class='poo'>&#128169; Count</th>";
echo "<th class='fed'>&#x1f37c; Count</th>";
echo "<th class='fed'>&#x1f37c; Duration</th>";
echo "<tr>";

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
		echo "<td><center>$ex</center></td>";
	}
	
	echo "</tr>";
}
echo "</table>";
echo "<p align='center'>Event count: $event_count<br></p>";

echo "<p align='center'>Weeks variable $weeks </p>";
echo "<p align='center'>Weeks POST data" + intval($_POST['weeks']) + "</p>";

?>
</body>
</html>

