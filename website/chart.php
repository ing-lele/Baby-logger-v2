<?php
// Generate chart using Chart.js
// Info: https://www.chartjs.org/
// =========================================================

/* For debug, enable the following
 ini_set("display_error", "stderr");
 ini_set("display_startup_errors", 1);
 ini_set("log_errors", 1);
 ini_set("html_errors", 1);
*/

// Read weeks from POST Form
if(!isset($_POST['weeks'])) {
    // Default to 2 weeks
    $weeks = 2;
    //echo "<p align='center'>Set default weeks: $weeks </p>\n";
}
else {
    // Read from POST
    $weeks = intval($_POST['weeks']);
    //echo "<p align='center'>Weeks after POST: $weeks , is integer? ". is_int($weeks) ."</p>\n";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Baby &#x1F476; Charts</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="baby_logger.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/date-fns/index.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js">
        import 'chartjs-adapter-date-fns';
        // import date-fns locale:
        import {enUS} from 'date-fns/locale';
        import {format, fromUnixTime} from 'date-fns';
    </script>
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
		<option value=2 <?php ($weeks==2) ? print('selected') : ''; ?>>2</option>
		<option value=4 <?php ($weeks==4) ? print('selected') : ''; ?>>4</option> 
		<option value=9 <?php ($weeks==9) ? print('selected') : ''; ?>>9</option>
		<option value=13 <?php ($weeks==13) ? print('selected') : ''; ?>>13</option>
		<option value=26 <?php ($weeks==26) ? print('selected') : ''; ?>>26</option>
		<option value=52 <?php ($weeks==52) ? print('selected') : ''; ?>>52</option>
		</select> weeks.
        <input type='submit' value='Update'>
    </form>
</center>

<!-- chart.js to create chart
https://www.chartjs.org/docs/latest/getting-started/

Creating canvas -->
<div style="text-align: center">
    <div class="wrapper">
        <canvas id='BabyStatChart'></canvas>
    </div>
</div>

<script>

// Get Data
chart_config = <?php require 'chart_data.php' ?>;

// Get context
ctx = document.getElementById("BabyStatChart").getContext("2d");

// Draw chart
var MyBabyStatChart = new Chart(
    ctx,
    chart_config
);

</script>

</body>
</html>
