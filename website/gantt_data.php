<?php
// SCOPE: Generate chart data for gantt view
// Info: https://www.chartjs.org/
//
// INPUT: Number of weeks (default: 2)
// OUTPUT: chart_config usable directly in new Chart(ctx,chart_config);
//
// =========================================================

// Include sql data function
include_once 'sql_raw.php';

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
$sql_json_data = get_raw_data($weeks,"ASC");

// decode JSON to array
$sql_data = json_decode($sql_json_data, true);

/* print Chart data 
echo "<pre>";
print_r($sql_data);
echo "</pre>";
*/

$start_unix = date_format(date_create('2022-02-22T00:00:00.000Z'), 'U')*1000; // 1645488000 *1000
$end_unix = date_format(date_create('2022-02-23T00:00:00.000Z'), 'U')*1000;   // 1645574399 *1000

// ---------------------
// loop all the results from DB and save to individual array
foreach($sql_data as $event){
    // data_structure[
    //  day UNIX_TIMESTAMP(DATE),
    //	ts_start UNIX_TIMESTAMP(DATE+TIME),
    //	ts_end UNIX_TIMESTAMP(DATE+TIME),

    try {
        $x_days[] = $event['day']*1000;   // Keep UNIX_TIMESTAMP in milliseconds
        $data_start_end[] = [$event['ts_start']*1000 , $event['ts_end']*1000];
    }
    catch (Exception $ex) {
        echo "<h1><center>Failed to create table</center></h1>";
        echo "<p><center>$ex</center></p>";
    }
}

/* print Chart data */
//echo json_encode($x_labels, JSON_PRETTY_PRINT);
//echo json_encode($data_start_end, JSON_PRETTY_PRINT);


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
$gantt_config = "{
    type: 'bar',
    data: {
        labels:". json_encode($x_days, JSON_PRETTY_PRINT) .",
        datasets: [
            // Chart -> Config -> Data -> Dataset #1 -> [Milk start, Milk end]
            {
                type: 'bar',
                label: 'Milk Duration',
                backgroundColor: '#add8e6',
                borderColor: '##3a9fbf',
                data:". json_encode($data_start_end, JSON_PRETTY_PRINT) ."
            }
        ]
    },
    options: {
        responsive: true,
        //indexAxis: 'y',
        legend: {
            display: false
          },
        scales: {
            x: {
                display: true,
                type: 'time',
                time: {
                    displayFormats: {
                        day: 'd MMM',
                    },
                },
            },
            y: {
                position: 'left',
                display: true,
                type: 'linear',
                beginAtZero: false,
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    callback: value => {
                        return new Date(value).toISOString().match('T(.*).000Z')[1];
                    },
                },
            },
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y > 160000000) {
                            // Convert UNIX to hh:mm:ss
                            label += new Date(context.parsed.y).toISOString().match('T(.*).000Z')[1];
                        } else {
                            // Return count
                            label += context.parsed.y;
                        }
                        return label;
                    },
                },
            },
        },
    },
}";

// --------------------------
// --- Chart config - end
// --------------------------

// Return chart_config
echo($gantt_config);
?>