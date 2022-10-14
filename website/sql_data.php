<?php
// Collect SQL data for count and duration
// Parameters: 
//      int $weeks
//      string $sort = ["ASC,"DESC"]
// Return:
//      JSON data
// =========================================================

// For debug, enable the following
// ini_set("display_error", "stderr");
// ini_set("display_startup_errors", 1);
// ini_set("log_errors", 1);
// ini_set("html_errors", 1);

function get_sql_data(int $weeks, string $sort) {
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

    // Show count of pee, poo, fed, fed_duration by day
    $sql_query = "SELECT 
        UNIX_TIMESTAMP(DATE(ts_start)) AS day, 
        COUNT(CASE WHEN category = 'pee' THEN id END) AS pee_count,
        COUNT(CASE WHEN category = 'poo' THEN id END) AS poo_count,
        COUNT(CASE WHEN category = 'fed' THEN id END) AS fed_count,
        SUM(CASE WHEN category = 'fed' THEN TIME_TO_SEC(TIMEDIFF(ts_end,ts_start)) END) AS fed_duration 
        FROM switchdata
        WHERE ts_start>= CURRENT_DATE() - INTERVAL ".($weeks)." WEEK 
        GROUP BY DATE(ts_start)
        ORDER BY DATE(ts_start) ".($sort).";";
    
    // query SQL result
    $sql_results = mysqli_query($connectdb, $sql_query) or die(mysql_error());
    
    // convert to array
    $row_results = array();
    while($record = mysqli_fetch_assoc($sql_results)) {
        $row_results[] = $record;
    }

    // return JSON format
    return json_encode($row_results, JSON_PRETTY_PRINT);

}

?>