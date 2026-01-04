class MSEConnect
{
    constructor(id, streamTechnology, cameraWidth)
    {
        // Camera ID
        this.id = id;

        // Stream technology: 'mse' or 'mjpeg'
        this.streamTechnology = streamTechnology;

        // Width of the camera video element
        this.cameraWidth = cameraWidth;

        // Retry counter for connection attempts
        this.retry = 0;

        // The video element that will display the stream
        this.videoElement = null;

        // Store event listener references to allow removal later
        this.eventListeners = {
            onError: null,
            onWsError: null,
            onWsClose: null,
            onStreamReady: null,
            onStreamError: null
        };
    }

    /**
     * Connect to a camera via WebRTC
     * Connection is monitored to ensure frames are being received
     * @returns 
     */
    async connect()
    {
        // Save reference to 'this' camera instance, to use in event listeners
        const self = this;

        // videoElement must be the <video> with the camera-id attribute
        const video = document.querySelector('video[camera-id="' + self.id + '"]');

        /** @type {VideoStream} */
        this.videoElement = document.createElement('video-stream');
        this.videoElement.mode = this.streamTechnology;
        this.videoElement.style.flex = '1 0 ' + this.cameraWidth;
        this.videoElement.src = new URL('api/ws?src=camera_' + self.id, location.href);
        this.videoElement.cameraId = self.id;

        // When video is loaded, hide the loading div, replace existing video with this.videoElement
        video.replaceWith(this.videoElement);

        // Définir les fonctions d'événements et les stocker pour pouvoir les supprimer
        this.eventListeners.onError = function(event) {
            console.error('Video stream error for camera ' + self.id + ':', event);
            Camera.showStreamError(self.id, 'Stream connection failed');
        };

        this.eventListeners.onWsError = function(event) {
            console.error('WebSocket error for camera ' + self.id + ':', event.detail);
            Camera.showStreamError(self.id, event.detail.message || 'Connection error', true);
        };

        this.eventListeners.onWsClose = function(event) {
            console.warn('WebSocket closed for camera ' + self.id + ':', event.detail);

            // Try to reconnect up to 3 times
            if (self.retry < 3) {
                self.retry++;

                Camera.hideStreamError(self.id);
                Camera.showStreamLoading(self.id, 'Reconnecting (' + self.retry + ' of 3)...');
                
                setTimeout(() => {
                    self.connect();
                }, 1000);
            } else {
                console.error('Max retry attempts reached for camera ' + self.id + ' (' + self.retry + '). Giving up.');
                Camera.showStreamError(self.id, 'Max reconnection attempts reached');
            }
        };

        this.eventListeners.onStreamReady = function(event) {
            console.log('Stream ready for camera ' + self.id);
            Camera.hideStreamError(self.id);
            Camera.hideStreamLoading(self.id);
            Camera.showStream(self.id);

            // Reset retry counter
            self.retry = 0;
        };

        this.eventListeners.onStreamError = function(event) {
            clearTimeout(connectionTimeout);
            Camera.showStreamError(self.id, event.detail.message);
        };

        // Ajouter les event listeners
        this.videoElement.addEventListener('error', this.eventListeners.onError);
        this.videoElement.addEventListener('ws-error', this.eventListeners.onWsError);
        this.videoElement.addEventListener('ws-close', this.eventListeners.onWsClose);
        this.videoElement.addEventListener('stream-ready', this.eventListeners.onStreamReady);
        this.videoElement.addEventListener('stream-error', this.eventListeners.onStreamError);
    }

    /**
     * Supprime un event listener spécifique
     * @param {string} eventType - Type d'événement ('error', 'ws-error', 'ws-close', 'stream-ready', 'stream-error')
     */
    removeEventListener(eventType) {
        if (!this.videoElement || !this.eventListeners) return;

        const eventMap = {
            'error': 'onError',
            'ws-error': 'onWsError', 
            'ws-close': 'onWsClose',
            'stream-ready': 'onStreamReady',
            'stream-error': 'onStreamError'
        };

        const listenerKey = eventMap[eventType];
        if (listenerKey && this.eventListeners[listenerKey]) {
            this.videoElement.removeEventListener(eventType, this.eventListeners[listenerKey]);
            this.eventListeners[listenerKey] = null;
            console.log(`Event listener '${eventType}' removed for camera ${this.id}`);
        }
    }

    /**
     * Nettoie tous les event listeners du stream vidéo
     * Utile pour éviter les fuites mémoire et permettre une déconnexion propre
     */
    cleanupEventListeners() {
        if (this.videoElement && this.eventListeners) {
            // Supprimer tous les event listeners
            if (this.eventListeners.onError) {
                this.videoElement.removeEventListener('error', this.eventListeners.onError);
            }
            if (this.eventListeners.onWsError) {
                this.videoElement.removeEventListener('ws-error', this.eventListeners.onWsError);
            }
            if (this.eventListeners.onWsClose) {
                this.videoElement.removeEventListener('ws-close', this.eventListeners.onWsClose);
            }
            if (this.eventListeners.onStreamReady) {
                this.videoElement.removeEventListener('stream-ready', this.eventListeners.onStreamReady);
            }
            if (this.eventListeners.onStreamError) {
                this.videoElement.removeEventListener('stream-error', this.eventListeners.onStreamError);
            }

            // Réinitialiser les références
            this.eventListeners = {
                onError: null,
                onWsError: null,
                onWsClose: null,
                onStreamReady: null,
                onStreamError: null
            };

            console.log('All event listeners cleaned up for camera ' + this.id);
        }
    }
}
