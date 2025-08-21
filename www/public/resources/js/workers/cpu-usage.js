// cpuWorker.js
async function fetchCpuUsage() {
    try {
        // Create a FormData object to send the action and controller via POST
        const formData = new FormData();
        formData.append('action', 'get-cpu-usage');
        formData.append('controller', 'system');

        // Send a POST request to the server to get CPU usage
        const response = await fetch('/ajax/controller.php', {
            body: formData,
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest', // Ensure the request is recognized as AJAX
            }
        });

        const data = await response.json();
        return data.message;
    } catch (error) {
        return null;
    }
}

// Fetch the CPU usage immediately, send it to the main thread
fetchCpuUsage().then(cpuUsage => {
    postMessage({ cpuUsage });
});

// Then every 5 seconds, fetch and send the CPU usage data to the main thread
setInterval(async () => {
    const cpuUsage = await fetchCpuUsage();
    postMessage({ cpuUsage });
}, 5000);
