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
        }, 30000);
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
}