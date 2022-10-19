<!DOCTYPE html>
<html lang="en">

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
            anychart.data.loadJsonFile('https://cdn.anychart.com/samples-data/gantt-charts/server-status-list/data.json', function (data) {
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
                firstColumn.title('Server')
                    .width(140)
                    .cellTextSettingsOverrider(labelTextSettingsOverrider)
                    .format(function (item) {
                        return item.get('name');
                    });

                // set first column settings
                var secondColumn = dataGrid.column(2);
                secondColumn.cellTextSettings().hAlign('right');
                secondColumn.title('Online')
                    .width(60)
                    .cellTextSettingsOverrider(labelTextSettingsOverrider)
                    .format(function (item) {
                        return item.get('online') || '';
                    });

                // set first column settings
                var thirdColumn = dataGrid.column(3);
                thirdColumn.cellTextSettings().hAlign('right');
                thirdColumn.title('Maintenance')
                    .width(60)
                    .cellTextSettingsOverrider(labelTextSettingsOverrider)
                    .format(function (item) {
                        return item.get('maintenance') || '';
                    });

                // set first column settings
                var fourthColumn = dataGrid.column(4);
                fourthColumn.cellTextSettings().hAlign('right');
                fourthColumn.title('Offline')
                    .width(60)
                    .cellTextSettingsOverrider(labelTextSettingsOverrider)
                    .format(function (item) {
                        return item.get('offline') || '';
                    });

                // set container id for the chart
                chart.container('wrapper');

                chart.draw();

                chart.zoomTo(Date.UTC(2008, 0, 31, 1, 36), Date.UTC(2008, 1, 15, 10, 3));

            });
        });

        function labelTextSettingsOverrider(label, item) {
            switch (item.get('type')) {
                case 'online':
                    label.fontColor('green').fontWeight('bold');
                    break;
                case 'offline':
                    label.fontColor('red').fontWeight('bold');
                    break;
                case 'maintenance':
                    label.fontColor('orange').fontWeight('bold');
                    break;
            }
        }
    </script>
</head>

<body>
    <div id="wrapper"></div>
</body>

</html>