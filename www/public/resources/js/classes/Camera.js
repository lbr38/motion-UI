class Camera
{
    constructor(id)
    {
        this.id = id;
        this.retry = 0;
        this.streamTechnology = null;
        this.cameraWidth = null;
        this.webrtc = null; // Instance WebRTC
        
        // Stockage des références des event listeners pour pouvoir les supprimer
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
     * @param {*} id
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

        $('.video-container[camera-id="' + id + '"] video-stream').remove();
        $('.video-container[camera-id="' + id + '"] video').remove();

        // this.videoElement.remove();
    }

    /**
     * Cache le message d'erreur pour une caméra
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
     * Cache le loading pour une caméra
     * @param {int} id 
     */
    static hideStreamLoading(id) {
        const loadingDiv = document.querySelector('.camera-loading[camera-id="' + id + '"]');
        if (loadingDiv) {
            loadingDiv.classList.add('hide');
            loadingDiv.classList.remove('flex');
        }
    }

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

    static hideStreamDisabled(id) {
        $('.camera-disabled[camera-id="' + id + '"]').hide();
        $('.camera-disabled[camera-id="' + id + '"]').removeClass('flex');
    }

    static hideStream(id) {
        const videoContainer = document.querySelector('.video-container[camera-id="' + id + '"]');
        if (videoContainer) {
            videoContainer.style.display = 'none';
        }
    }

    static showStream(id) {
        const videoContainer = document.querySelector('.video-container[camera-id="' + id + '"]');
        if (videoContainer) {
            videoContainer.style.display = 'flex';
        }
    }

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

    async connect()
    {
        // Get camera stream technology
        this.streamTechnology = $('.camera-container[camera-id="' + this.id + '"]').find('div.camera-image').attr('stream-technology');

        // Get camera width
        this.cameraWidth = $('.camera-container[camera-id="' + this.id + '"]').find('div.camera-image').attr('width');

        // Show loading message
        Camera.showStreamLoading(this.id);

        // Create the <video> element if not exists (might have been removed from a previous error)
        Camera.createVideoStreamElement(this.id);

        /**
         *  Connect to the camera using MSE or MJPEG
         *  See js/stream/video-stream.js
         */
        if (this.streamTechnology == 'mse' || this.streamTechnology == 'mjpeg') {
            // const params = new URLSearchParams(location.search);

            // support multiple streams and multiple modes
            const width = '1 0 ' + this.cameraWidth;
            
            // Capturer l'ID de la caméra pour l'utiliser dans les event listeners
            const id = this.id;

            // videoElement must be the <video> with the camera-id attribute
            const video = document.querySelector('video[camera-id="' + this.id + '"]');

            /** @type {VideoStream} */
            this.videoElement = document.createElement('video-stream');
            this.videoElement.mode = this.streamTechnology;
            this.videoElement.style.flex = width;
            this.videoElement.src = new URL('api/ws?src=camera_' + this.id, location.href);
            this.videoElement.cameraId = this.id;

            // When video is loaded, hide the loading div, replace existing video with this.videoElement
            video.replaceWith(this.videoElement);

            // Save reference to 'this' camera instance, to use in event listeners
            const self = this;

            // Timeout pour détecter les échecs de connexion
            // const connectionTimeout = setTimeout(() => {
            //     self.showStreamError(id, 'Connection timeout');
            // }, 15000); // 15 secondes

            // Définir les fonctions d'événements et les stocker pour pouvoir les supprimer
            this.eventListeners.onError = function(event) {
                console.error('Video stream error for camera ' + id + ':', event);
                Camera.showStreamError(id, 'Stream connection failed');
            };

            this.eventListeners.onWsError = function(event) {
                console.error('WebSocket error for camera ' + id + ':', event.detail);
                Camera.showStreamError(id, event.detail.message || 'Connection error', true);
            };

            this.eventListeners.onWsClose = function(event) {
                console.warn('WebSocket closed for camera ' + id + ':', event.detail);

                // Try to reconnect up to 3 times
                if (self.retry < 3) {
                    self.retry++;

                    Camera.hideStreamError(id);
                    Camera.showStreamLoading(id, 'Reconnecting (' + self.retry + ' of 3)...');
                    
                    setTimeout(() => {
                        self.connect();
                    }, 1000);
                } else {
                    console.error('Max retry attempts reached for camera ' + id + ' (' + self.retry + '). Giving up.');
                    Camera.showStreamError(id, 'Max reconnection attempts reached');
                }
            };

            this.eventListeners.onStreamReady = function(event) {
                console.log('Stream ready for camera ' + id);
                Camera.hideStreamError(id);
                Camera.hideStreamLoading(id);
                Camera.showStream(id);

                // Reset retry counter
                self.retry = 0;

                // clearTimeout(connectionTimeout);
            };

            this.eventListeners.onStreamError = function(event) {
                clearTimeout(connectionTimeout);
                Camera.showStreamError(id, event.detail.message);
            };

            // Ajouter les event listeners
            this.videoElement.addEventListener('error', this.eventListeners.onError);
            this.videoElement.addEventListener('ws-error', this.eventListeners.onWsError);
            this.videoElement.addEventListener('ws-close', this.eventListeners.onWsClose);
            this.videoElement.addEventListener('stream-ready', this.eventListeners.onStreamReady);
            this.videoElement.addEventListener('stream-error', this.eventListeners.onStreamError);
        }

        /**
         *  Connect to the camera using WebRTC
         *  See js/stream/webrtc.js
         */
        if (this.streamTechnology == 'webrtc') {
            // Create a new WebrtcConnect instance if it does not exist
            this.webrtc = new WebrtcConnect();
            this.webrtc.connect(this.id);
        }
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

    /**
     * Déconnecte une instance de caméra en vérifiant d'abord qu'elle existe
     * @param {Camera|null|undefined} cameraInstance - Instance de Camera à déconnecter
     * @param {string|number} [cameraId] - ID de la caméra pour les logs (optionnel)
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
     * Déconnecte complètement le stream et nettoie toutes les ressources
     */
    disconnect() {
        // Nettoyer les event listeners
        this.cleanupEventListeners();
        
        // Arrêter le ping si actif
        this.stopPing();
        
        // Supprimer l'élément vidéo (case du MSE/MJPEG)
        if (this.videoElement) {
            this.videoElement.remove();
            this.videoElement = null;
        }

        // Si WebRTC est utilisé, fermer la connexion
        if (this.webrtc && typeof this.webrtc.closeConnection === 'function') {
            // Marquer la connexion comme fermée pour éviter les tentatives de reconnexion
            if (this.webrtc.cameraConnection) {
                this.webrtc.cameraConnection.connected = false;
            }
            
            this.webrtc.closeConnection();
            this.webrtc = null;
        } else if (this.webrtc) {
            // Cas où this.webrtc existe mais n'a pas de méthode closeConnection
            console.warn('WebRTC instance found but no closeConnection method available for camera ' + this.id);
            this.webrtc = null;
        }
        
        // Réinitialiser le compteur de retry
        this.retry = 0;
        
        console.log('Camera ' + this.id + ' disconnected and cleaned up');
    }
}
