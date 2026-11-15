<section class="main-container reloadable-container" container="motion/stats/list">
    <div>
        <h6><?= __('SELECT PERIOD') ?></h6>
        <div class="pointer input-daterangepicker input-large echart-range" chart-id="motion-event-chart motion-status-chart system-cpu-usage-chart system-memory-usage-chart system-disk-usage-chart" range="Last 24 Hours">
            <p>Last 24 Hours</p>
        </div>
    </div>

    <div id="motion-stats-container" class="margin-top-30">
        <div class="echart-container div-generic-blue">
            <div id="motion-event-chart-loading" class="echart-loading">
                <img src="/assets/icons/loading.svg" class="icon-np" />
            </div>

            <div id="motion-event-chart" class="echart"></div>
        </div>

        <div class="echart-container div-generic-blue">
            <div id="motion-status-chart-loading" class="echart-loading">
                <img src="/assets/icons/loading.svg" class="icon-np" />
            </div>

            <div id="motion-status-chart" class="echart"></div>
        </div>

        <div class="echart-container div-generic-blue">
            <div id="system-cpu-usage-chart-loading" class="echart-loading">
                <img src="/assets/icons/loading.svg" class="icon-np" />
            </div>

            <div id="system-cpu-usage-chart" class="echart"></div>
        </div>

        <div class="echart-container div-generic-blue">
            <div id="system-memory-usage-chart-loading" class="echart-loading">
                <img src="/assets/icons/loading.svg" class="icon-np" />
            </div>

            <div id="system-memory-usage-chart" class="echart"></div>
        </div>

        <div class="echart-container div-generic-blue">
            <div id="system-disk-usage-chart-loading" class="echart-loading">
                <img src="/assets/icons/loading.svg" class="icon-np" />
            </div>

            <div id="system-disk-usage-chart" class="echart"></div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            mydaterangepicker.convert('div.echart-range[chart-id~="motion-event-chart"]', 'Last 24 Hours');

            new EChart('line', 'motion-event-chart');
            new EChart('line', 'motion-status-chart');
            new EChart('line', 'system-cpu-usage-chart');
            new EChart('line', 'system-memory-usage-chart');
            new EChart('line', 'system-disk-usage-chart');
        });
    </script>
</section>
