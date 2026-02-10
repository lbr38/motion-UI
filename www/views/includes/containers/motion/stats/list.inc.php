<section class="main-container reloadable-container" container="motion/stats/list">
    <div>
        <h6><?= $_['h6']['select_period'] ?></h6>
        <select id="motion-stats-days-select" class="select-medium">
            <option value="1" selected><?= $_['select']['options']['1day'] ?></option>
            <option value="3"><?= $_['select']['options']['3days'] ?></option>
            <option value="7"><?= $_['select']['options']['7days'] ?></option>
            <option value="15"><?= $_['select']['options']['15days'] ?></option>
            <option value="30"><?= $_['select']['options']['30days'] ?></option>
        </select>
    </div>

    <div id="motion-stats-container" class="margin-top-30">
        <div class="div-generic-blue">
            <h6 class="margin-top-0"><?= $_['h6']['events_files_recorded'] ?></h6>

            <div class="echart-container">
                <div id="motion-event-chart-loading" class="echart-loading">
                    <img src="/assets/icons/loading.svg" class="icon-np" />
                </div>

                <div id="motion-event-chart" class="echart"></div>
            </div>
        </div>

        <div class="div-generic-blue">
            <h6 class="margin-top-0"><?= $_['h6']['motion_detection_activity'] ?></h6>
            <div class="echart-container">
                <div id="motion-status-chart-loading" class="echart-loading">
                    <img src="/assets/icons/loading.svg" class="icon-np" />
                </div>

                <div id="motion-status-chart" class="echart"></div>
            </div>
        </div>

        <div class="div-generic-blue">
            <h6 class="margin-top-0"><?= $_['h6']['cpu_usage'] ?></h6>
            <div class="echart-container">
                <div id="system-cpu-usage-chart-loading" class="echart-loading">
                    <img src="/assets/icons/loading.svg" class="icon-np" />
                </div>

                <div id="system-cpu-usage-chart" class="echart"></div>
            </div>
        </div>

        <div class="div-generic-blue">
            <h6 class="margin-top-0"><?= $_['h6']['memory_usage'] ?></h6>
            <div class="echart-container">
                <div id="system-memory-usage-chart-loading" class="echart-loading">
                    <img src="/assets/icons/loading.svg" class="icon-np" />
                </div>

                <div id="system-memory-usage-chart" class="echart"></div>
            </div>
        </div>

        <div class="div-generic-blue">
            <h6 class="margin-top-0"><?= $_['h6']['disk_usage'] ?></h6>
            <div class="echart-container">
                <div id="system-disk-usage-chart-loading" class="echart-loading">
                    <img src="/assets/icons/loading.svg" class="icon-np" />
                </div>

                <div id="system-disk-usage-chart" class="echart"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            new EChart('line', 'motion-event-chart');
            new EChart('line', 'motion-status-chart');
            new EChart('line', 'system-cpu-usage-chart');
            new EChart('line', 'system-memory-usage-chart');
            new EChart('line', 'system-disk-usage-chart');
        });
    </script>
</section>