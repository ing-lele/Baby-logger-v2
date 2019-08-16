<?php
// Settings
// host, user and password settings
$host = "localhost";
$user = "logger";
$password = "password";
$database = "buttons";


// make connection to database
$connectdb = mysqli_connect($host,$user,$password)
or die ("Cannot reach database");

// select db
mysqli_select_db($connectdb,$database)
or die ("Cannot select database");


// show queries from the current date and the day before
$sql="SELECT * FROM buttondata WHERE tdate >= CURDATE() - INTERVAL 1 day;";

// set query to variable
$buttons = mysqli_query($connectdb,$sql);

// create content to web page
?>
<html>
<head>
<title>Button log</title>
</head>

<body>
</body>
<center>Baby's vital functions for the last 2 days</center>
<br><br>
<table width="800" border="1" cellpadding="1" cellspacing="1" align="center">
<tr>
<th>Date</th>
<th>Time</th>
<th>Button</th>
<tr>
<?php
// loop all the results that were read from database and "draw" to web page
while($button=mysqli_fetch_assoc($buttons)){
echo "<tr>";
echo "<td>".$button['tdate']."</td>";
echo "<td>".$button['ttime']."</td>";
echo "<td>".$button['type']."</td>";
echo "<tr>";
}
?>
</table>
</html>

