<!DOCTYPE html>
<html lang="en" style="width: 100%; height: 100%;">

<head>
    <title>Baby &#x1F476; Gantt</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="baby_logger.css">
    <script src="https://cdn.anychart.com/releases/8.0.0/js/anychart-base.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.0.0/js/anychart-ui.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.0.0/js/anychart-exports.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.0.0/js/anychart-gantt.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.0.0/js/anychart-data-adapter.min.js"></script>

    <script>
        anychart.onDocumentReady(function () {
            // The data used in this sample can be obtained from the CDN
            // https://cdn.anychart.com/samples-data/gantt-charts/server-status-list/data.json
            anychart.data.loadJsonFile('gantt_data.json', function (data) {
                // create data tree on our data
                var treeData = anychart.data.tree(data, 'as-table');

                // create project gantt chart
                var chart = anychart.ganttResource();

                // set data for the chart
                chart.data(treeData);

                // set start splitter position settings
                chart.splitterPosition(320);

                // get chart data grid link to set column settings
                var dataGrid = chart.dataGrid();

                dataGrid.column(0).enabled(false);

                // set first column settings
                var firstColumn = dataGrid.column(1);
                firstColumn.cellTextSettings().hAlign('left');
                firstColumn.title('Duration')
                    .width(100)
                    .cellTextSettingsOverrider(labelTextSettingsOverrider)
                    .format(function (item) {
                        return item.get('name');
                    });

                // set first column settings
                var secondColumn = dataGrid.column(2);
                secondColumn.cellTextSettings().hAlign('right');
                secondColumn.title('Free')
                    .width(60)
                    .cellTextSettingsOverrider(labelTextSettingsOverrider)
                    .format(function (item) {
                        return item.get('free') || '';
                    });

                // set first column settings
                var thirdColumn = dataGrid.column(3);
                thirdColumn.cellTextSettings().hAlign('right');
                thirdColumn.title('Milk')
                    .width(60)
                    .cellTextSettingsOverrider(labelTextSettingsOverrider)
                    .format(function (item) {
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
    <div id="BabyStatChart"></div>
</body>

</html>