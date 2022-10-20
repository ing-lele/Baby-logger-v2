<!DOCTYPE html>
<html lang="en" style="width: 100%; height: 100%;">

<head>
    <title>Baby &#x1F476; Gantt</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="baby_logger.css">
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-bundle.min.js"></script>
    
    <script>
        anychart.onDocumentReady(function () {
            //anychart.data.loadJsonFile('gantt_data.json', function (data) {
            anychart.data.loadJsonFile(<?php require 'gantt_data.php' ?>;, function (data) {

                // create data tree on our data
                var treeData = anychart.data.tree(data, 'as-table');

                // create project gantt chart
                var chart = anychart.ganttResource();

                // set data for the chart
                chart.data(treeData);

                // set start splitter position settings
                chart.splitterPosition(280);

                // get chart data grid link to set column settings
                var dataGrid = chart.dataGrid();

                dataGrid.column(0).enabled(false);

                // set first column settings
                var firstColumn = dataGrid.column(1);
                
                // Set labels for first column
                firstColumn.labels().hAlign('left');
                firstColumn.title('Duration');
                firstColumn.width(150);
                firstColumn.labelsOverrider(function (label, item) {
                    return item.get('name');
                });

                // set first column settings
                var secondColumn = dataGrid.column(2);
                secondColumn.labels().hAlign('right');
                secondColumn.title('Free');
                secondColumn.width(60);
                secondColumn.labelsOverrider(function (label, item) {
                    return item.get('free') || '';
                });

                // set first column settings
                var thirdColumn = dataGrid.column(3);
                thirdColumn.labels().hAlign('right');
                thirdColumn.title('Milk');
                thirdColumn.width(60);
                thirdColumn.labelsOverrider(function (label, item) {
                    return item.get('milk') || '';
                });

                // set container id for the chart
                chart.container('BabyStatChart');

                chart.draw();

                chart.zoomTo(Date.UTC(2008, 0, 31, 1, 36), Date.UTC(2008, 1, 15, 10, 3));

            });
        });

        function labelTextSettingsOverrider(label, item) {
            switch (item.get('type')) {
                case 'free':
                    label.fontColor('black').fontWeight('bold');
                    break;
                case 'milk':
                    label.fontColor('blue').fontWeight('bold');
                    break;
            }
        }
    </script>
</head>

<body style="width: 90%; height: 90%; text-align: center;">

<?php
	// Include Navigation bar
	include_once 'navigation.php';
?>

<center>
    <form method='POST' action=<?php echo $_SERVER['PHP_SELF'];?>>
        <?php echo "<P>Baby's stats for last <b>$weeks weeks</b> since ". date("d M Y", strtotime('-'.$weeks.' weeks')) .".</P>"; ?>

		Show stats for past <select name='weeks' id='weeks'>
		<option value=2 <?php ($weeks==2) ? print('selected') : ''; ?>>2</option>
		<option value=4 <?php ($weeks==4) ? print('selected') : ''; ?>>4</option> 
		<option value=9 <?php ($weeks==9) ? print('selected') : ''; ?>>9</option>
		<option value=13 <?php ($weeks==13) ? print('selected') : ''; ?>>13</option>
		<option value=26 <?php ($weeks==26) ? print('selected') : ''; ?>>26</option>
		<option value=52 <?php ($weeks==52) ? print('selected') : ''; ?>>52</option>
		</select> weeks.
        <input type='submit' value='Update'>
    </form>
</center>

<!-- AnyChart.js to create chart
https://www.anychart.com/products/anygantt/overview/

Creating canvas -->
<div id="BabyStatChart"></div>

</body>

</html>