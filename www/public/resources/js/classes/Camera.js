class Camera
{
    /**
     * Returns true if the camera stream is disabled (has the 'disabled' attribute)
     * @param {*} id
     */
    isDisabled(id)
    {
        if ($('.video-container[camera-id="' + id + '"]').find('video[camera-id="' + id + '"]').attr('disabled')) {
            console.debug('Camera #' + id + ' stream is disabled');
            return true;
        }

        return false;
    }

    /**
     * Set camera as unavailable
     * @param {*} id
     */
    setUnavailable(id, message = null)
    {
        // If the stream is already disabled, do nothing
        if (this.isDisabled(id)) {
            return;
        }

        // If the stream is already unavailable, do nothing
        if ($('.camera-unavailable[camera-id=' + id + ']').css('display') == 'flex') {
            return;
        }

        $('.video-container[camera-id=' + id + ']').css('display', 'none');
        $('.camera-unavailable[camera-id=' + id + ']').css('display', 'flex');

        if (message != null) {
            $('.camera-unavailable[camera-id=' + id + ']').find('p.note').text(message);
        }
    }

    /**
     * Set camera as reconnecting
     * @param {*} id 
     */
    setReconnecting(id, message = 'Reconnecting...')
    {
        $('.camera-reconnecting[camera-id=' + id + ']').remove();
        $('.video-container[camera-id=' + id + ']').append('<div class="camera-reconnecting" camera-id="' + id + '"><p>' + message + '</p></div>');
    }

    /**
     * Set camera as available
     * @param { } id 
     * @returns 
     */
    setAvailable(id)
    {
        // If the stream is already disabled, do nothing
        if (this.isDisabled(id)) {
            return;
        }

        $('.camera-reconnecting[camera-id=' + id + ']').remove();
        $('.camera-unavailable[camera-id=' + id + ']').css('display', 'none');
        $('.video-container[camera-id=' + id + ']').css('display', 'flex');
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
}
