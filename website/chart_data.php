<?php
// Generate JS array for count and duration to use in chart.php
// =========================================================

// For debug, enable the following
// ini_set("display_error", "stderr");
// ini_set("display_startup_errors", 1);
// ini_set("log_errors", 1);
// ini_set("html_errors", 1);


$i=0; $q=mysql_query('select ..');

while($row=mysql_fetch_array($q)){               

    echo "myarray[".$i."]='".$row['data']."';";

    $i++;  
}


?>