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
// Using chart.js to create chart
// https://www.chartjs.org/docs/latest/getting-started/

?>

<div><canvas id='myChart'></canvas></div>
<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>

<script>
  const labels = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
  ];

  const data = {
    labels: labels,
    datasets: [{
      label: 'My First dataset',
      backgroundColor: 'rgb(255, 99, 132)',
      borderColor: 'rgb(255, 99, 132)',
      data: [0, 10, 5, 2, 20, 30, 45],
    }]
  };

  const config = {
    type: 'line',
    data: data,
    options: {}
  };
</script>

</body>
</html>
