/**
 *  Create charts with ApexChart
 *  - A <canvas> element with the same ID as the chart must exist in the HTML
 *  - The server must return the data with all the data needed to create the chart (.vars.inc.php file must exist)
 *  - (optional) A loading spinner with the ID <chartID>-loading must exist in the HTML
 */
class ApexChart
{
    constructor(type, id, autoUpdate = true, autoUpdateInterval = 10000)
    {
        this.id                 = id;
        this.type               = type;
        this.autoUpdate         = autoUpdate;
        this.autoUpdateInterval = autoUpdateInterval;
        this.datasets           = [];
        this.labels             = [];
        this.chartOptions       = [];
        this.animate            = '';

        // Maximum days data points to display on the chart, 1 day on mobile, 3 days on desktop
        this.days = window.innerWidth < 600 ? 1 : 3;

        // Default options (will be cloned before use)
        this.baseOptions = {
            chart: {
                type: undefined,
                height: '100%',
                width: '100%',
                fontFamily: 'Roboto',
                foreColor: '#8A99AA',
                // animations: {
                //     enabled: true,
                //     easing: 'easeinout',
                //     speed: 1
                // },
                zoom: {
                    enabled: true,
                    type: 'x',
                    autoScaleYaxis: true,
                    // Disable zooming with mouse wheel to prevent accidental zooms
                    allowMouseWheelZoom: false
                },
                toolbar: {
                    show: true,
                    offsetY: window.innerWidth < 600 ? 30 : 0,
                    tools: {
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    },
                    autoSelected: 'pan',
                    export: {
                        svg: {
                            filename: id,
                        }
                    }
                },
            },
            title: {
                text: undefined
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: "smooth",
                width: 2
            },
            xaxis: {
                type: 'datetime',
                labels: {
                    datetimeUTC: false
                },
                tooltip: {
                    enabled: false
                }
            },
            yaxis: {},
            grid: {
                show: false
            },
            theme: {
                // upto palette10
                palette: 'palette1'
            },
            tooltip: {
                theme: 'dark',
                shared: true,
                intersect: false,
                x: {
                    formatter: (val) => {
                        const d = new Date(val);
                        return d.toLocaleString(undefined, {
                        year: 'numeric', month: 'short', day: '2-digit',
                        hour: '2-digit', minute: '2-digit', second: '2-digit',
                        hour12: false
                        });
                    }
                }
            },
            // Fill the area under the line
            fill: {
                type: 'solid',
                opacity: 0.25,
            }
        }

        // To store the SetInterval Id for each chart
        this.setIntervalId = {};

        // Call the appropriate chart creation method based on the type
        if (typeof this[type] === 'function') {
            this[type](id);
        }

        // Start auto-update
        this.startAutoUpdate();
    }

    /**
     * Start auto-updating the chart
     */
    startAutoUpdate()
    {
        // If autoUpdate is enabled, set an interval to update the chart
        // Default: update every 10 seconds
        if (!this.autoUpdate) return;

        this.setIntervalId[this.id] = setInterval(async () => {
            // Get chart data
            await this.get(this.id);

            const chartElement = document.querySelector("#" + this.id);
            const chart = chartElement?._chartInstance;
            if (!chart) return;

            // Get current min/max (to preserve zoom/pan)
            const currentMin = chart.w.globals.minX;
            const currentMax = chart.w.globals.maxX;

            // Build timestamps array
            const timestamps = this.labels;

            // Convert datasets
            const series = this.buildSeries(timestamps);

            // Update series (false => no animation)
            chart.updateSeries(series, false);

            // Restore current min/max (to keep the zoom/pan)
            chart.updateOptions({
                xaxis: {
                    min: currentMin,
                    max: currentMax
                }
            }, false, false);

        }, this.autoUpdateInterval);
    }

    /**
     * Get chart data by ID
     * @param {*} id
     * @returns
     */
    get(id)
    {
        return new Promise((resolve, reject) => {
            try {
                ajaxRequest(
                    // Controller:
                    'chart',
                    // Action:
                    'get',
                    // Data:
                    {
                        id: id,
                        days: this.days,
                        sourceGetParameters: getGetParams()
                    },
                    // Print success alert:
                    false,
                    // Print error alert:
                    true
                ).then(() => {
                    // Parse the response and store it in the class properties
                    this.datasets     = jsonValue.message.datasets;
                    this.labels       = jsonValue.message.labels;
                    this.chartOptions = jsonValue.message.options;

                    // For debugging purposes only
                    // console.log("datasets: " + JSON.stringify(this.datasets));
                    // console.log("labels: " + JSON.stringify(this.labels));
                    // console.log("options: " + JSON.stringify(this.chartOptions));

                    // Resolve promise
                    resolve('Chart data retrieved');
                });

            } catch (error) {
                // Reject promise
                reject('Failed to get chart data: ' + error);
            }
        });
    }

    /**
     * Build datasets array formatted for Apex
     */
    buildSeries(timestamps)
    {
        return this.datasets.map(dataset => {
            const paired = dataset.data.map((v, i) => [timestamps[i], v]);
            return {
                name: dataset.name,
                data: paired,
                color: dataset.color
            };
        });
    }

    /**
     * Create or update a line chart (area in practice)
     * @param {*} id
     * @returns
     */
    async line(id)
    {
        await this.get(id);

        const chartElement = document.querySelector("#" + id);
        const existingChart = chartElement?._chartInstance;

        // Build timestamps array
        const timestamps = this.labels;

        // Convert datasets
        const series = this.buildSeries(timestamps);

        if (existingChart) {
            return this.updateChart(existingChart, series);
        }

        return this.createChart(id, series);
    }

    /**
     * Create the chart for the first time
     */
    createChart(id, series)
    {
        // Get the container element and bail out if missing
        const chartElement = document.querySelector("#" + id);
        if (!chartElement) {
            console.warn('ApexChart.createChart: container not found for id=', id);
            return;
        }

        // Clone base options
        const options = JSON.parse(JSON.stringify(this.baseOptions));

        // Customize tooltip date formatter
        options.tooltip = options.tooltip || {};
        options.tooltip.x = options.tooltip.x || {};
        options.tooltip.x.formatter = this._formatDate.bind(this);

        // Set chart type
        options.chart.type = 'area';

        // Window size (default 15)
        const visibleCount = this.chartOptions?.['init-zoom'] ?? 15;

        // timestamp min/max
        let minTimestamp, maxTimestamp;
        const total = this.labels.length;
        if (total > 0) {
            const startIndex = Math.max(0, total - visibleCount);
            minTimestamp = this.labels[startIndex];
            maxTimestamp = this.labels[total - 1];
        }

        // Set datasets
        options.series = series;

        // Set initial zoom window
        if (minTimestamp !== undefined && maxTimestamp !== undefined) {
            options.xaxis.min = minTimestamp;
            options.xaxis.max = maxTimestamp;
        }

        // Set title
        if (this.chartOptions.title?.text) {
            options.title = {
                text: this.chartOptions.title.text,
                align: this.chartOptions.title.align || 'left'
            };
        }

        // Y-axis features
        if (this.chartOptions.yaxis) {
            options.yaxis.min          = this.chartOptions.yaxis.min ?? undefined;
            options.yaxis.max          = this.chartOptions.yaxis.max ?? undefined;
            options.yaxis.tickAmount   = this.chartOptions.yaxis.tickAmount ?? undefined;

            // If server sends a formatterName, use table lookup (instead of eval)
            if (this.chartOptions.yaxis.labels?.formatterName) {
                const formatterFn = ApexChart.formatters[this.chartOptions.yaxis.labels.formatterName];
                if (formatterFn) {
                    options.yaxis.labels = { formatter: formatterFn };
                }
            }
        }

        // Toolbar show / hide
        if (this.chartOptions.toolbar?.show !== undefined) {
            options.chart.toolbar.show = this.chartOptions.toolbar.show;
        }

        // Create and render
        const chart = new ApexCharts(document.querySelector("#" + id), options);
        chart.render().then(() => {

            // Initial zoom
            if (minTimestamp !== undefined && maxTimestamp !== undefined) {
                chart.zoomX(minTimestamp, maxTimestamp);
            }

            // Remove spinner
            $('#' + id + '-loading').remove();
        });

        chartElement._chartInstance = chart;
    }

    // helper local formatter (use user's locale and local timezone)
    _formatDate(val, withDate = false) {
        const d = new Date(Number(val));

        if (withDate) {
            return d.toLocaleString(undefined, {
                year: 'numeric', month: 'short', day: '2-digit',
                hour: '2-digit', minute: '2-digit', second: '2-digit',
                hour12: false
            });
        }

        return d.toLocaleString(undefined, {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
    }

    /**
     * Update an existing chart without resetting zoom/pan
     */
    updateChart(chart, series)
    {
        // Get current min/max (to preserve zoom/pan)
        const currentMin = chart.w.globals.minX;
        const currentMax = chart.w.globals.maxX;

        // Update series (fast)
        chart.updateSeries(series, false);

        // Restore zoom
        chart.updateOptions({
            xaxis: {
                min: currentMin,
                max: currentMax
            }
        }, false, false);

        $('#' + this.id + '-loading').remove();
    }
}

/**
 * Register available formatters (to avoid eval)
 */
ApexChart.formatters = {
    // Example usage: formatterName: "activeState"
    activeState: (val) => val === 1 ? "active" : "inactive",
};
