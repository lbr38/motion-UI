class WebrtcConnect
{
    constructor()
    {
        this.cameraConnection = {
            connected: false,
            retries: 0
        };
        this.videoTimeout = null;
        this.statsInterval = null;
        this.lastFrameCount = 0;
    }

    /**
     * Connect to a camera via WebRTC
     * Connection is monitored to ensure frames are being received
     * @param {*} cameraId 
     * @returns 
     */
    async connect(cameraId)
    {
        const video = document.querySelector('video[camera-id="' + cameraId + '"]');
        
        if (!video) {
            mycamera.setUnavailable(cameraId, 'Camera #' + cameraId + ' video element not found');
            return;
        }

        // Close existing connection
        this.closeConnection();

        // Open connection to the camera
        this.connection = await connect(cameraId);
        
        // Start monitoring WebRTC stats
        this.startStatsMonitoring(cameraId);
    }

    /**
     * Start monitoring WebRTC stats to ensure frames are being received
     * @param {*} cameraId 
     * @returns 
     */
    startStatsMonitoring(cameraId)
    {
        if (!this.connection || !this.connection.peerConnection) {
            console.error('No peer connection available for stats monitoring');
            return;
        }

        // Stop existing stats monitoring
        if (this.statsInterval) {
            clearInterval(this.statsInterval);
        }

        let consecutiveFailures = 0;
        const maxFailures = 3;

        // Check every 2 seconds the stats
        this.statsInterval = setInterval(async () => {
            // If video has been disabled by the user then close the connection
            if (mycamera.isDisabled(cameraId)) {
                this.closeConnection();
                return;
            }

            try {
                const stats = await this.connection.peerConnection.getStats();
                let currentFrameCount = 0;

                // Look for video inbound RTP stats
                stats.forEach(report => {
                    if (report.type === 'inbound-rtp' && report.mediaType === 'video') {
                        currentFrameCount = report.framesReceived || 0;
                    }
                });

                // Check if new frames are being received
                if (currentFrameCount > this.lastFrameCount) {
                    // New frames detected
                    this.onFramesReceived(cameraId);
                    this.lastFrameCount = currentFrameCount;
                    consecutiveFailures = 0;
                } else {
                    // No new frames
                    consecutiveFailures++;
                    console.warn(`Camera #${cameraId}: No new frames (${consecutiveFailures}/${maxFailures})`);
                    
                    if (consecutiveFailures >= maxFailures) {
                        this.onNoFramesReceived(cameraId);
                    }
                }
            } catch (error) {
                console.error('Error getting WebRTC stats:', error);
                consecutiveFailures++;
                
                if (consecutiveFailures >= maxFailures) {
                    this.onNoFramesReceived(cameraId);
                }
            }
        }, 5000);
    }

    /**
     * Handle frames received successfully
     * @param {*} cameraId 
     */
    onFramesReceived(cameraId)
    {
        // Frames received successfully
        this.cameraConnection.connected = true;
        this.cameraConnection.retries = 0; // Reset retries on success
        mycamera.setAvailable(cameraId);
        
        // Reset timeout
        this.clearVideoTimeout();
    }

    /**
     * Handle no frames received
     * @param {*} cameraId 
     * @returns 
     */
    onNoFramesReceived(cameraId)
    {
        console.warn('Camera #' + cameraId + ' no frames received');

        // If we have less than 3 attempts, reconnect
        if (this.cameraConnection.retries < 3) {
            this.cameraConnection.retries++;
            console.info('Camera #' + cameraId + ' reconnecting... (attempt ' + this.cameraConnection.retries + ')');
            mycamera.setReconnecting(cameraId, 'Reconnecting... (attempt ' + this.cameraConnection.retries + '/3)');

            // Reconnect
            this.connect(cameraId);
            return;
        }

        // If we exhausted the attempts
        mycamera.setUnavailable(cameraId, 'No frames received');
        this.closeConnection();
    }

    /**
     * Close the WebRTC connection and stop monitoring
     */
    closeConnection()
    {
        // Stop stats monitoring
        if (this.statsInterval) {
            clearInterval(this.statsInterval);
            this.statsInterval = null;
        }

        // Close the WebRTC connection
        if (this.connection) {
            console.info('Closing WebRTC connection');
            this.connection.close();
            this.connection = null;
        }
        
        this.cameraConnection.connected = false;
        this.lastFrameCount = 0;
        this.clearVideoTimeout();
    }

    /**
     * Clear any existing video timeout
     */
    clearVideoTimeout()
    {
        if (this.videoTimeout) {
            clearTimeout(this.videoTimeout);
            this.videoTimeout = null;
        }
    }
}
