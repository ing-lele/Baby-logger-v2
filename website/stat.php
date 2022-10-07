<?php
// Statistic page
// =========================================================
// Daily entry with:
// Date | Pee Count | Poo Count | Milk Count | Milk Duration
//
// Drop down:
// * Current week
// * Current month
// * Current last 3 months
// * Current semester
//
?>

<html>
<head>
<title>Baby Logger &#x1F476; - Stats</title>
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
<hr size=1>

<table border="0" cellpadding="1" cellspacing="1" align="center">
	<!-- add export link -->
	<tr>
		<th><a href='export_data.php'>Export all data from DB</a></th>
		<th><a href='off.php' target='_blank'>Turn off Raspberry PI</a></th>
	</tr>

</table>

<hr width='500px' size=1>

<form method='POST'>
<center>

<?php
if ($days > 1)
    print "Baby's vital functions for the last $days days.";
else
    print "Baby's vital functions for the last $days day.";
?>

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
echo "<p align='center'>Event count: $event_count<br></p>";

?>
</body>
</html>
