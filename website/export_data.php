<?php
// Export DB and generate CSV file
// =========================================================

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

// TABLE switchdata(
//  id INT PRIMARY KEY auto_increment NOT NULL,
//  ts_start TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
//  ts_end TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
//  category TEXT

// Fetch all records from database 
$sql = "SELECT * FROM $db_table ORDER BY id DESC"; 
$results = mysqli_query($connectdb, $sql);

if($results->num_rows > 0){ 
    $delimiter = ","; 
    $filename = $db_table."_" . date('Y-m-d') . ".csv"; 
    
    // Create a file pointer 
    $f = fopen('php://memory', 'w'); 
     
    // Set column headers 
    $fields = array('ID', 'START', 'END', 'CATEGORY');
    fputcsv($f, $fields, $delimiter); 
     
    // Output each row of the data, format line as csv and write to file pointer 
    while($row = $results->fetch_assoc()){ 
        $lineData = array($row['id'], $row['ts_start'], $row['ts_end'], $row['category']);
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