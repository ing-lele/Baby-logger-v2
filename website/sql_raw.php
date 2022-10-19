<?php
// Collect SQL data for count and duration
// Parameters: 
//      int $weeks
//      string $sort = ["ASC,"DESC"]
// Return:
//      JSON data
// =========================================================

function get_raw_data(int $weeks, string $sort) {
    // DB Connection settings - mysql_variables.php
    //$db_host
    //$db_user
    //$db_pass
    //$db_name
    //$db_table
    include 'mysql_variables.php';

    // Make connection to database
    $connectdb = mysqli_connect($db_host, $db_user, $db_pass) or die ("ERROR - Cannot reach database");
    mysqli_select_db($connectdb,$db_name) or die ("ERROR - Cannot select database");

    // Validate week
    if(!isset($weeks) && is_int($weeks)){
        $weeks = 2;
    }

    // Validate sort
    if(!isset($sort) && in_array($sort, ["ASC","DESC"])){
        $sort = "DESC";
    }

    // Return Start / End for FED entries
    $sql_query = "SELECT 
        UNIX_TIMESTAMP(DATE(ts_start)) AS ts_start,
        UNIX_TIMESTAMP(DATE(ts_end)) AS ts_end
        FROM switchdata
        WHERE category = 'fed' AND ts_start>= CURRENT_DATE() - INTERVAL ".($weeks)." WEEK 
        ORDER BY DATE(ts_start) ".($sort).";";
    
    // query SQL result
    $sql_results = mysqli_query($connectdb, $sql_query) or die();
    
    // convert to array
    $row_results = array();
    while($record = mysqli_fetch_assoc($sql_results)) {
       $row_results[] = $record;
    }

    // return JSON format
    return json_encode($row_results, JSON_PRETTY_PRINT);

}

?>