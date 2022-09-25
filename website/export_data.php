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
include_once 'mysql_variables.php';

// Make connection to database
$connectdb = mysqli_connect($db_host, $db_user, $db_pass) or die ("ERROR - Cannot reach database");
mysqli_select_db($connectdb,$db_name) or die ("ERROR - Cannot select database");

// TABLE buttondata(
//      id INT PRIMARY KEY auto_increment,
//  	created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
//	    category TEXT,
//  	state TEXT); 
 
// Fetch all records from database 
$sql = "SELECT * FROM buttondata ORDER BY id DESC"; 
$results = mysqli_query($connectdb, $sql);

if($results->num_rows > 0){ 
    $delimiter = ","; 
    $filename = "button-data_" . date('Y-m-d') . ".csv"; 
    
    // Create a file pointer 
    $f = fopen('php://memory', 'w'); 
     
    // Set column headers 
    $fields = array('ID', 'CREATED', 'CATEGORY', 'STATE');
    fputcsv($f, $fields, $delimiter); 
     
    // Output each row of the data, format line as csv and write to file pointer 
    while($row = $results->fetch_assoc()){ 
        $lineData = array($row['id'], $row['created'], $row['category'], $row['state']);
        fputcsv($f, $lineData, $delimiter);
    } 
     
    // Move back to beginning of file 
    fseek($f, 0); 
     
    // Set headers to download file rather than displayed 
    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename="' . $filename . '";'); 
     
    //output all remaining data on a file pointer 
    fpassthru($f); 
} 
exit; 
?>