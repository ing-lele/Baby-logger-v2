<?php
// Transform SQL data to match chart data
// Parameters: int $weeks
// =========================================================

// For debug, enable the following
// ini_set("display_error", "stderr");
// ini_set("display_startup_errors", 1);
// ini_set("log_errors", 1);
// ini_set("html_errors", 1);

function get_chart_data($sql_json_data) {
    
    // decode JSON to array
    $sql_data = json_decode($sql_json_data, true);

    // Check if valid
    if (!isset($sql_data)){
        die("No SQL data provided");
    }

    // Initialize variables
    $event_count = 0;
    $x_labels = array();
    $data_pee_count = array();
    $data_poo_count = array();
    $data_fed_count = array();
    $data_fed_duration = array();

    // loop all the results from DB and save to individual array
    foreach($sql_data as $event){
        $event_count++;

        // data_structure[
        //	day DATE,
        //	pee_count INT,
        //	poo_count INT,
        //  fed_count INT,
        //	fed_time TIME]

        try {
            $x_labels[] = date("d M y", strtotime($event['day']));
            $data_pee_count[]  = $event['pee_count'];
            $data_poo_count[] = $event['poo_count'];
            $data_fed_count[] = $event['fed_count'];
            $data_fed_duration[] = $event['fed_duration'];
        }
        catch (Exception $ex) {
            echo "<td><center>Failed to create table</center></td>";
            echo "<td><center>$er</center></td>";
        }
    }
    
    // Print to check arrays values
    echo "<br>Label array: ";
    print_r($x_labels);

    echo "<br>Pee array: ";
    print_r($data_pee_count);

    echo "<br>Poo array: ";
    print_r($data_poo_count);

    echo "<br>Fed array: ";
    print_r($data_fed_count);

    echo "<br>Duration array: ";
    print_r($data_fed_duration);
    

    // --------------------------
    // Return Chart Data
    //
    // Chart -> Config -> Data = 
    // {
    //      labels:
    //      dataset: [
    //          data1[value11,value12,...],
    //          data2[value21,value22,...],
    //          dataN[valueN1,valueN1,...]
    //      ]
    // }
    $chart_data = array(
        'labels' => $x_labels,
        'data' => array(
            $data_pee_count,
            $data_poo_count,
            $data_fed_count,
            $data_fed_duration
        )
    );

    // Encode in JSON format and print
    return  json_encode($chart_data, JSON_PRETTY_PRINT);
}

?>