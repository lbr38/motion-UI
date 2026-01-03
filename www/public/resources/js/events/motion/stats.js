/**
 * Event: when the period (days) selection is changed
 */
$('#motion-stats-days-select').on('change', function() {
    // Get selected value
    const days = $(this).val();

    // Destroy and recreate charts with new days value
    if (EChart.destroyInstance('motion-event-chart')) {
        new EChart('line', 'motion-event-chart', true, 15000, days);
    }

    if (EChart.destroyInstance('motion-status-chart')) {
        new EChart('line', 'motion-status-chart', true, 15000, days);
    }

    if (EChart.destroyInstance('system-cpu-usage-chart')) {
        new EChart('line', 'system-cpu-usage-chart', true, 15000, days);
    }
    
    if (EChart.destroyInstance('system-memory-usage-chart')) {
        new EChart('line', 'system-memory-usage-chart', true, 15000, days);
    }

    if (EChart.destroyInstance('system-disk-usage-chart')) {
        new EChart('line', 'system-disk-usage-chart', true, 15000, days);
    }
});

