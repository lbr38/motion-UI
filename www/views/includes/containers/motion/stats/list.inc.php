<section class="main-container reloadable-container" container="motion/stats/list">
    <div>
        <h6>SELECT PERIOD</h6>
        <div class="flex column-gap-10">
            <input type="date" name="dateStart" class="input-medium stats-date-input" value="<?= $statsDateStart ?>" />
            <input type="date" name="dateEnd" class="input-medium stats-date-input" value="<?= $statsDateEnd ?>" />
        </div>
    </div>

    <div id="motion-stats-container" class="margin-top-30">
        <div class="div-generic-blue relative">
            <div id="motion-event-chart-loading" class="flex justify-center align-item-center">
                <img src="/assets/icons/loading.svg" class="icon-np">
            </div>

            <canvas id="motion-event-chart"></canvas>
        </div>

        <div class="div-generic-blue relative">
            <div id="motion-status-chart-loading" class="flex justify-center align-item-center">
                <img src="/assets/icons/loading.svg" class="icon-np">
            </div>

            <canvas id="motion-status-chart"></canvas>
        </div>

        <div class="div-generic-blue relative">
            <div id="system-cpu-usage-chart-loading" class="flex justify-center align-item-center">
                <img src="/assets/icons/loading.svg" class="icon-np">
            </div>

            <canvas id="system-cpu-usage-chart"></canvas>
        </div>

        <div class="div-generic-blue relative">
            <div id="system-memory-usage-chart-loading" class="flex justify-center align-item-center">
                <img src="/assets/icons/loading.svg" class="icon-np">
            </div>

            <canvas id="system-memory-usage-chart"></canvas>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            new AsyncChart('line', 'motion-event-chart');
            new AsyncChart('line', 'motion-status-chart');
            new AsyncChart('line', 'system-memory-usage-chart');
            new AsyncChart('line', 'system-cpu-usage-chart');
        });    
    </script>
</section>