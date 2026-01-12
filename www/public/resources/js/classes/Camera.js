class Camera
{
    constructor(id)
    {
        this.id = id;
        this.retry = 0;
        this.mse = null; // MSE instance
        this.webrtc = null; // WebRTC instance
        
        // Store references to event listeners to be able to remove them later
        this.eventListeners = {
            onError: null,
            onWsError: null,
            onWsClose: null,
            onStreamReady: null,
            onStreamError: null
        };
    }

    /**
     * Start the camera ping for keep-alive
     * This is useful for Android devices that close the connection after 60 seconds
     * The client sends a ping every 30 seconds to keep the connection alive
     * @param {*} ws
     */
    ping(ws)
    {
        // Send a ping every 30 seconds
        this.pingInterval = setInterval(() => {
            if (ws.readyState === WebSocket.OPEN) {
                // Send a message through the websocket connection
                ws.send(JSON.stringify({type: 'ping'}));
                // For debug purpose
                // console.info('WebSocket ping sent');
            }
        }, 2000);
    }

    /**
     * Stop the camera ping
     */
    stopPing()
    {
        if (this.pingInterval) {
            clearInterval(this.pingInterval);
            this.pingInterval = null;
            // For debug purpose
            // console.info('WebSocket ping stopped');
        }
    }

    /**
     * Initialize the SortableJS instance for the camera grid
     * @param {*} id
     */
    sort(id)
    {
        new Sortable(document.getElementById(id), {
            animation: 150,
            delay: 500,
            delayOnTouchOnly: true,
            ghostClass: 'sortable-ghost',
            // Class name of the element that should not trigger drag
            filter: '.do-not-drag',
            // Prevent drag from starting on filtered elements
            preventOnFilter: false,

            // On drag start
            onStart: function (evt) {
                // Set all cameras to low opacity
                $('.camera-container').css('opacity', '0.4');
            },

            // On drag end
            onEnd: function (evt) {
                // Reset all cameras to full opacity
                $('.camera-container').css('opacity', '1');

                // Get new order of cameras
                const order = Array.from(evt.from.children).map((el) => el.getAttribute('camera-id'));

                // For debug purpose
                // console.log('New order :', order);

                ajaxRequest(
                    // Controller:
                    'camera/stream',
                    // Action:
                    'sort',
                    // Data:
                    {
                        order: order
                    },
                    // Print success alert:
                    false,
                    // Print error alert:
                    true
                );
            }
        });
    }

    /**
     * Returns true if the camera stream is disabled (has the 'disabled' attribute)
     * @param {int} id
     */
    static isDisabled(id)
    {
        if ($('.video-container[camera-id="' + id + '"]').find('video[camera-id="' + id + '"]').attr('disabled')) {
            console.debug('Camera #' + id + ' stream is disabled');
            return true;
        }

        return false;
    }

    /**
     * Enable the video element for a camera
     * @param {int} id
     */
    static setEnabled(id)
    {
        $('video[camera-id="' + id + '"]').removeAttr('disabled');
    }

    /**
     * Show stream error message for a camera
     * @param {int} id 
     * @param {string} message 
     */
    static showStreamError(id, message = 'Stream error', copy = false) {
        Camera.hideStreamLoading(id);
        
        const errorDiv = document.querySelector('.camera-error[camera-id="' + id + '"]');
        if (errorDiv) {
            errorDiv.classList.remove('hide');
            errorDiv.classList.add('flex');
            
            const errorText = errorDiv.querySelector('.note');
            if (errorText) {
                errorText.textContent = message;

                if (copy) {
                    // Add class copy to enable text selection
                    errorText.classList.add('copy');
                } else {
                    // Remove class copy
                    errorText.classList.remove('copy');
                }
            }
        }

        // Remove the video element to free resources
        $('.video-container[camera-id="' + id + '"] video-stream').remove();
        $('.video-container[camera-id="' + id + '"] video').remove();
    }

    /**
     * Hide the error message for a camera
     * @param {int} id 
     */
    static hideStreamError(id) {
        const errorDiv = document.querySelector('.camera-error[camera-id="' + id + '"]');
        if (errorDiv) {
            errorDiv.classList.add('hide');
            errorDiv.classList.remove('flex');
        }
    }

    /**
     * Hide the loading spinner for a camera
     * @param {int} id 
     */
    static hideStreamLoading(id) {
        const loadingDiv = document.querySelector('.camera-loading[camera-id="' + id + '"]');
        if (loadingDiv) {
            loadingDiv.classList.add('hide');
            loadingDiv.classList.remove('flex');
        }
    }

    /**
     * Show the loading spinner for a camera
     * @param {int} id 
     * @param {string} message 
     */
    static showStreamLoading(id, message = null) {
        const loadingDiv = document.querySelector('.camera-loading[camera-id="' + id + '"]');

        if (loadingDiv) {
            loadingDiv.classList.remove('hide');
            loadingDiv.classList.add('flex');

            if (message != null) {
                const loadingText = loadingDiv.querySelector('p');
                if (loadingText) {
                    loadingText.textContent = message;
                }
            }
        }
    }

    /**
     * Show the stream disabled message for a camera
     * @param {int} id 
     */
    static showStreamDisabled(id) {
        $('.camera-disabled[camera-id="' + id + '"]').css('display', 'flex');
    }

    /**
     * Hide the stream disabled message for a camera
     * @param {int} id 
     */
    static hideStreamDisabled(id) {
        $('.camera-disabled[camera-id="' + id + '"]').hide();
        $('.camera-disabled[camera-id="' + id + '"]').removeClass('flex');
    }

    /**
     * Hide the stream for a camera
     * @param {int} id 
     */
    static hideStream(id) {
        const videoContainer = document.querySelector('.video-container[camera-id="' + id + '"]');
        if (videoContainer) {
            videoContainer.style.display = 'none';
        }
    }

    /**
     * Show the stream for a camera
     * @param {int} id 
     */
    static showStream(id) {
        const videoContainer = document.querySelector('.video-container[camera-id="' + id + '"]');
        if (videoContainer) {
            videoContainer.style.display = 'flex';
        }
    }

    /**
     * Delete the stream elements for a camera
     * @param {int} id 
     */
    static deleteStreamElement(id) {
        const video = document.querySelector('video[camera-id="' + id + '"]');
        if (video) {
            video.remove();
        }

        const videoStream = document.querySelector('video-stream[camera-id="' + id + '"]');
        if (videoStream) {
            videoStream.remove();
        }
    }

    /**
     * Create the video stream element for a camera
     * @param {int} id 
     * @returns 
     */
    static createVideoStreamElement(id) {
        // If the video element already exists, do nothing
        if ($('video[camera-id="' + id + '"]').length > 0) {
            return;
        }

        // Hide video container initially
        Camera.hideStream(id);

        /** @type {HTMLVideoElement} */
        const video = document.createElement('video');
        video.setAttribute('camera-id', id);
        video.setAttribute('autoplay', '');
        video.setAttribute('playsinline', '');
        video.setAttribute('muted', '');
        video.setAttribute('poster', '/assets/images/motionui-video-poster.png');

        const container = document.querySelector('.video-container[camera-id="' + id + '"]');
        container.appendChild(video);
    }

    /**
     * Connect to the camera stream using MSE or WebRTC
     */
    async connect()
    {
        // Get camera stream technology
        const streamTechnology = $('.camera-container[camera-id="' + this.id + '"]').find('div.camera-image').attr('stream-technology');

        // Get camera width
        const cameraWidth = $('.camera-container[camera-id="' + this.id + '"]').find('div.camera-image').attr('width');

        // Show loading message
        Camera.showStreamLoading(this.id);

        // Create the <video> element if not exists (might have been removed from a previous error)
        Camera.createVideoStreamElement(this.id);

        /**
         *  Connect to the camera using MSE or MJPEG
         *  See js/stream/video-stream.js
         */
        if (streamTechnology == 'mse' || streamTechnology == 'mjpeg') {          
            // Create a new MSEConnect instance if it does not exist
            this.mse = new MSEConnect(this.id, streamTechnology, cameraWidth);
            this.mse.connect();
        }

        /**
         *  Connect to the camera using WebRTC
         *  See js/stream/webrtc.js
         */
        if (streamTechnology == 'webrtc') {
            // Create a new WebRTCConnect instance if it does not exist
            this.webrtc = new WebRTCConnect();
            this.webrtc.connect(this.id);
        }
    }

    /**
     * Disconnect a camera instance safely by checking if it exists first
     * @param {Camera|null|undefined} cameraInstance
     * @param {string|number} [cameraId]
     */
    static safeDisconnect(cameraInstance, cameraId = null) {
        try {
            if (cameraInstance && typeof cameraInstance.disconnect === 'function') {
                cameraInstance.disconnect();
            } else {
                const id = cameraId || (cameraInstance?.id) || 'unknown';
                console.warn(`Cannot disconnect camera ${id}: instance is null or invalid`);

            }
        } catch (error) {
            const id = cameraId || (cameraInstance?.id) || 'unknown';
            console.error(`Error disconnecting camera ${id}:`, error);
        }

        // Delete the camera instance from the registry after disconnection
        if (window.cameraInstances && window.cameraInstances[cameraId]) {
            Camera.hideStreamError(cameraId);
            Camera.hideStreamLoading(cameraId);
            Camera.deleteStreamElement(cameraId);

            delete window.cameraInstances[cameraId];

            console.debug('Camera instance for camera ' + cameraId + ' deleted from registry.');
        }
    }

    /**
     * Disconnects the camera stream and cleans up all resources
     */
    disconnect() {
        if (this.mse) {
            // Clean up event listeners
            this.mse.cleanupEventListeners();
            
            // Stop the ping if active
            this.stopPing();
            
            // Delete the video element (case of MSE/MJPEG)
            if (this.mse.videoElement) {
                this.mse.videoElement.remove();
                this.mse.videoElement = null;
            }

            // Reset the retry counter
            this.mse.retry = 0;
        }

        // If WebRTC is used, close the connection
        if (this.webrtc && typeof this.webrtc.closeConnection === 'function') {
            // Mark the connection as closed to avoid reconnection attempts
            if (this.webrtc.cameraConnection) {
                this.webrtc.cameraConnection.connected = false;
            }
            
            this.webrtc.closeConnection();
            this.webrtc = null;
        } else if (this.webrtc) {
            // Case where this.webrtc exists but has no closeConnection method
            console.warn('WebRTC instance found but no closeConnection method available for camera ' + this.id);
            this.webrtc = null;
        }
        
        console.info('Camera ' + this.id + ' disconnected and cleaned up');
    }
}
