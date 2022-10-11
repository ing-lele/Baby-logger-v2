<?php
// Transform SQL data to match chart data
// Parameters: int $weeks
// =========================================================

// For debug, enable the following
// ini_set("display_error", "stderr");
// ini_set("display_startup_errors", 1);
// ini_set("log_errors", 1);
// ini_set("html_errors", 1);

function get_chart_data(mysqli_result $sql_data) {

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

    // loop all the results that were read from database
    while($sql_data->$event mysqli_fetch_assoc($sql_data)){
        // Query result structure  (
        //  	day DATE,
        //	    pee_count INT,
        //	    poo_count INT,
        //  	fed_count INT,
        //	    fed_time TIME)

        $event_count++;

        $x_labels[] = date("d M y", strtotime($event['day']));
        $data_pee_count[]  = $event['pee_count'];
        $data_poo_count[] = $event['poo_count'];
        $data_fed_count[] = $event['fed_count'];
        $data_fed_duration[] = $event['fed_duration'];

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
    // Chart -> Config -> Data = 
    // {
    //      labels:
    //      dataset: [
    //          {type1, label1, data1},
    //          {type2, label2, data2},
    //          {...}
    //      ]
    // }
    // --------------------------

    // --------------------------
    // Initialize dataset

    // Chart -> Config -> Data -> Dataset #1 -> Pee count
    $datasets_pee_count = array (
        'type' => "line",
        'label' => "Pee Count",
        'backgroundColor' => "#ffff66",
        'borderColor' => "#ffff66",
        'data' => $data_pee_count
    );

    // Chart -> Config -> Data -> Dataset #2 -> Poo count
    $datasets_poo_count = array(
        'type' => "line",
        'label' => "Poo Count",
        'backgroundColor' => "#996600",
        'borderColor' => "#996600",
        'data' => $data_poo_count
    );

    // Chart -> Config -> Data -> Dataset #3 -> Milk count
    $datasets_fed_count = array(
        'type' => "line",
        'label' => "Milk Count",
        'backgroundColor' => "#399cbd",
        'borderColor' => "#399cbd",
        'data' => $data_fed_count
    );

    // Chart -> Config -> Data -> Dataset #4 -> Milk duration
    $datasets_fed_duration = array(
        'type' => "bar",
        'label' => "Milk Duration",
        'backgroundColor' => "#add8e6",
        'borderColor' => "#add8e6",
        'data' => $data_fed_duration
    );

    echo "<br>Pee count dataset: ";
    print_r($datasets_pee_count);

    echo "<br>Poo count dataset: ";
    print_r($datasets_poo_count);

    echo "<br>Fed count dataset: ";
    print_r($datasets_fed_count);

    echo "<br>Fed duration dataset: ";
    print_r($datasets_fed_duration);

    // --------------------------
    // Initialize Chart Data
    //
    // Chart -> Config -> Data = 
    // {
    //      labels:
    //      dataset: [
    //          {type1, label1, data1},
    //          {type2, label2, data2},
    //          {...}
    //      ]
    // }
    $chart_data = array(
        'labels' => $x_labels,
        'datasets' => array(
            $datasets_pee_count,
            $datasets_poo_count,
            $datasets_fed_count,
            $datasets_fed_duration
        )
    );

    // Encode in JSON format and print
    return  json_encode($chart_data, JSON_PRETTY_PRINT);
}

// Test chart_data function
include_once 'sql_data.php';
$sql_data = get_sql_data($weeks,"ASC");
$chart_data = get_chart_data($sql_data);
ehco json_encode($chart_data, JSON_PRETTY_PRINT);

?>