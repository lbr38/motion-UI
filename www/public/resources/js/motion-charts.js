/**
 *  Load and generate stats charts
 */
function loadAllStatsCharts()
{
    /**
     *  Get labels and data
     */
    var eventLabels = $('#motion-event-chart-labels-data').attr('labels').split(", ");
    var eventData = $('#motion-event-chart-labels-data').attr('event-data').split(", ");
    var filesData = $('#motion-event-chart-labels-data').attr('files-data').split(", ");

    var statusLabels = $('#motion-status-chart-labels-data').attr('labels').split(", ");
    var statusData = $('#motion-status-chart-labels-data').attr('status-data').split(", ");

    /**
     *  Inject/update labels and data into the charts
     */
    myEventChart.data.labels = eventLabels;
    myEventChart.data.datasets[0].data = eventData;
    myEventChart.data.datasets[1].data = filesData;
    myEventChart.update();

    myMotionStatusChart.data.labels = statusLabels;
    myMotionStatusChart.data.datasets[0].data = statusData;
    myMotionStatusChart.update();
}

/**
 * Function: print stats between selected dates
 * @param {*} dateStart
 * @param {*} dateEnd
 */
function statsDateSelect(dateStart, dateEnd)
{
    /**
     *  Add specified dates into cookies
     */
    document.cookie = "statsDateStart="+dateStart+";max-age=900;";
    document.cookie = "statsDateEnd="+dateEnd+";max-age=900;";

    /**
     *  Then reload stats div
     */
    reloadContainer('motion/stats/list');

    /**
     *  Wait for the div reload, then reload charts
     */
    setTimeout(function () {
        loadAllStatsCharts();
    }, 50);
}

/**
 * Function: print events between selected dates
 * @param {*} dateStart
 * @param {*} dateEnd
 */
function eventDateSelect(dateStart, dateEnd)
{
    /**
     *  Add specified dates into cookies
     */
    document.cookie = "eventDateStart="+dateStart+";max-age=900;";
    document.cookie = "eventDateEnd="+dateEnd+";max-age=900;";

    /**
     *  Then reload events div
     */
    reloadContainer('motion/events/list');
}