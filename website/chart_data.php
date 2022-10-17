<?php
// SCOPE: Generate chart object to render using Chart.js
// Info: https://www.chartjs.org/
//
// INPUT: Number of weeks (default: 2)
// OUTPUT: chart_config usable directly in new Chart(ctx,chart_config);
//
// =========================================================

// Include sql data function
include_once 'sql_data.php';

// Read weeks parameters or default to 2 weeks
if(!isset($_POST['weeks'])){
	$weeks = 2;
}else{
	$weeks = intval($_POST['weeks']);
}

// ---------------------
// Daily entry with:
// Date | Pee Count | Poo Count | Milk Count | Milk Duration

// Get SQL data in JSON format
$sql_json_data = get_sql_data($weeks,"ASC");

// decode JSON to array
$sql_data = json_decode($sql_json_data, true);

/* print Chart data
echo "<pre>";
print_r($chart_data);
echo "</pre>";
*/

// ---------------------
// loop all the results from DB and save to individual array
foreach($sql_data as $event){
    // data_structure[
    //	day UNIX_TIMESTAMP(DATE),
    //	pee_count INT,
    //	poo_count INT,
    //  fed_count INT,
    //	fed_time TIME_TO_SEC]
    
    try {
        $x_labels[] = $event['day'];    // keep it in UNIX_TIMESTAMP format
        $data_pee_count[]  = $event['pee_count'];
        $data_poo_count[] = $event['poo_count'];
        $data_fed_count[] = $event['fed_count'];
        $data_fed_duration[] = $event['fed_duration']; // keep it in UNIX_TIMESTAMP format
    }
    catch (Exception $ex) {
        echo "<h1><center>Failed to create table</center></h1>";
        echo "<p><center>$ex</center></p>";
    }
}

/* --------------------------
// --- Chart config - start
// --------------------------
// Chart -> Config {
//      type: 'scatter',
//      data: {
//              labels:
//              dataset:
//                  [
//                      {type1, label1, data1},
//                      {type2, label2, data2},
//                      {...}
//                  ]
//            }
//      options:
//          scales { }
// }
// -------------------------- */
$chart_config = "{
    type: 'scatter',
    data: {
        labels:". json_encode($x_labels, JSON_PRETTY_PRINT) .",
        datasets: [".
            // Chart -> Config -> Data -> Dataset #1 -> Pee count
            "{
                type: 'line',
                label: 'Pee Count',
                yAxisID: 'y_count',
                backgroundColor: '#ffff66',
                borderColor: '#000000',
                borderWidth: '0.5',
                data:". json_encode($data_pee_count, JSON_PRETTY_PRINT) ."
            },".
            // Chart -> Config -> Data -> Dataset #2 -> Poo count
            "{
                type: 'line',
                label: 'Poo Count',
                yAxisID: 'y_count',
                backgroundColor: '#996600',
                borderColor: '#996600',
                data:". json_encode($data_poo_count, JSON_PRETTY_PRINT) ."
            },".
            // Chart -> Config -> Data -> Dataset #3 -> Milk count
            "{
                type: 'line',
                label: 'Milk Count',
                yAxisID: 'y_count',
                backgroundColor: '#399cbd',
                borderColor: '#399cbd',
                data:". json_encode($data_fed_count, JSON_PRETTY_PRINT) ."
            },".
            // Chart -> Config -> Data -> Dataset #4 -> Milk duration
            "{
                type: 'bar',
                label: 'Milk Duration',
                yAxisID: 'y_duration',
                backgroundColor: '#add8e6',
                borderColor: '#add8e6',
                data:". json_encode($data_fed_duration, JSON_PRETTY_PRINT) ."
            }
        ]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        bezierCurveTension: 0,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            x: {
                display: true,
                type: 'time',
            },
            y_count: {
                position: 'left',
                display: true,
                type: 'linear',                    
                beginAtZero: true
            },
            y_duration {
                position: 'right',
                display: true,
                type: 'time',                    
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
}";

// --------------------------
// --- Chart config - end
// --------------------------

// Return chart_config
echo($chart_config);
?>