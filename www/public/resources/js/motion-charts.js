/**
 *  Create empty motion status and events charts
 */
var ctx = document.getElementById('motion-event-chart').getContext('2d');
var myEventChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [
        {
            data: [],
            label: "Total events per day",
            borderColor: '#3e95cd',
            fill: false
        },
        {
            data: [],
            label: "Total files recorded per day",
            borderColor: '#ea974d',
            fill: false
        }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        tension: 0.2,
        scales: {
            x: {
                display: true,
            },
            y: {
                beginAtZero: true,
                display: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
    }
});
var yLabels = {
    0 : 'inactive',
    1 : 'active'
}
var ctx = document.getElementById('motion-status-chart').getContext('2d');
var myMotionStatusChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            data: [],
            label: "Motion service activity (24h)",
            borderColor: '#d8524e',
            fill: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        tension: 0.2,
        scales: {
            x: {
                display: true,
            },
            y: {
                beginAtZero: true,
                display: true,
                ticks: {
                    stepSize: 1,
                    callback: function (value, index, values) {
                        return yLabels[value];
                    }
                }
            }
        },
    }
});

/**
 *  Inject charts labels and data
 */
loadAllStatsCharts();

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
    reloadContentById('motion-stats-labels-data');

    /**
     *  Wait for the div reload, then reload charts
     */
    setTimeout(function () {
        loadAllStatsCharts();
    }, 500);
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
    reloadContentById('events-captures-div');
}