class System {
    /**
     *  Get the CPU usage and update the display
     */
    getCpuUsage() {
        // Use a Web Worker for CPU usage to avoid blocking the main thread and so the browser
        this.cpuWorker = new Worker('/resources/js/workers/cpu-usage.js');

        // When the worker sends data
        this.cpuWorker.onmessage = (event) => {
            const cpuUsage = event.data.cpuUsage;

            // If the CPU usage is null, display an error
            if (cpuUsage === null) {
                $('#cpu-usage').text('Error');
                $('#cpu-usage-loading').remove();
                $('#cpu-usage-icon').remove();
                $('#cpu-usage-container').append('<span id="cpu-usage-icon" class="round-item bkg-red"></span>');
            } else {
                let color;
                if (cpuUsage >= 0 && cpuUsage <= 30) {
                    color = 'green';
                } else if (cpuUsage > 30 && cpuUsage <= 70) {
                    color = 'yellow';
                } else {
                    color = 'red';
                }

                // Update the CPU usage display
                $('#cpu-usage').text(cpuUsage + '%');
                $('#cpu-usage-loading').remove();
                $('#cpu-usage-icon').remove();
                $('#cpu-usage-container').append('<span id="cpu-usage-icon" class="round-item bkg-' + color + '"></span>');
            }
        };
    }
}
