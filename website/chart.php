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


?>

<!DOCTYPE html>
<html lang="en">
<script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js">
    import 'chartjs-adapter-date-fns';
    import {enUS} from 'date-fns/locale';
</script>

<head>
    <title>Baby &#x1F476; Charts</title>
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
    .wrapper{
        width: 500px;
        text-align: center;
        display: inline-block;
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
echo "<P>Baby's stats for last <b>". $weeks ."weeks</b> since ". date("d M Y", strtotime('-'.$weeks.' weeks')) .".</P>";
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
