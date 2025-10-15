<section class="main-container reloadable-container" container="motion/stats/list">
    <div id="motion-stats-container" class="margin-top-30">
        <div class="div-generic-blue relative">
            <div id="motion-event-chart-loading" class="flex justify-center align-item-center height-100">
                <img src="/assets/icons/loading.svg" class="icon-np">
            </div>

            <div id="motion-event-chart"></div>
        </div>

        <div class="div-generic-blue relative">
            <div id="motion-status-chart-loading" class="flex justify-center align-item-center height-100">
                <img src="/assets/icons/loading.svg" class="icon-np">
            </div>

            <div id="motion-status-chart"></div>
        </div>

        <div class="div-generic-blue relative">
            <div id="system-cpu-usage-chart-loading" class="flex justify-center align-item-center height-100">
                <img src="/assets/icons/loading.svg" class="icon-np">
            </div>

            <div id="system-cpu-usage-chart"></div>
        </div>

        <div class="div-generic-blue">
            <div id="system-memory-usage-chart-loading" class="flex justify-center align-item-center height-100">
                <img src="/assets/icons/loading.svg" class="icon-np">
            </div>

            <div id="system-memory-usage-chart"></div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            new ApexChart('line', 'motion-event-chart');
            new ApexChart('line', 'motion-status-chart');
            new ApexChart('line', 'system-cpu-usage-chart');
            new ApexChart('line', 'system-memory-usage-chart');
        });    
    </script>
</section>