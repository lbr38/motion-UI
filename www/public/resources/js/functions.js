
/**
 *  Open websocket connection with server
 */
function websocket_client()
{
    const server = window.location.hostname;

    /**
     *  Get current origin (http://xxxx:port) then replace 'http' with 'ws'
     *  wss (secured) will automatically be used if the server uses https
     */
    const path = window.location.origin.replace('http', 'ws') + '/ws';
    const socket = new WebSocket(path);

    // Handle connection open
    socket.onopen = function (event) {
        console.log('Websocket connection opened at ' + path);

        // Send connection type to server
        message = JSON.stringify({
            'connection-type': 'browser-client'
        });

        sendMessage(message);
    };

    // Handle received message
    socket.onmessage = function (event) {
        // Parse message
        message = JSON.parse(event.data);

        // If message type is reload-container, then reload container
        if (message.type == 'reload-container') {
            mycontainer.reload(message.container);
        }
    };

    // Handle connection close
    socket.onclose = function (event) {
        console.log('Websocket connection closed with ' + server);

        // If the connection was closed cleanly
        if (event.wasClean) {
            console.log('Websocket connection with ' + server + ' closed (code=' + event.code + ' reason=' + event.reason + ')');

        // If the connection was closed unexpectedly
        // For example, the server process was killed or network problems occurred
        } else {
            console.log('Websocket connection with ' + server + ' closed unexpectedly');
        }
    };

    function sendMessage(message)
    {
        socket.send(message);
    }
}

/**
 *  Return GET parameters as object (array)
 */
function getGetParams()
{
    /**
     *  Get current URL and GET parameters
     */
    let url = new URL(window.location.href)
    let params = new URLSearchParams(url.search);
    let entries = params.entries();

    /**
     *  Parse and convert to object
     *  For each GET param, add key and value to the object
     */
    let array = {}
    for (let entry of entries) { // each 'entry' is a [key, value]
        let [key, val] = entry;

        /**
         *  If key ends with '[]' then it's an array
         */
        if (key.endsWith('[]')) {
            // clean up the key
            key = key.slice(0,-2);
            (array[key] || (array[key] = [])).push(val)
        /**
         *  Else it's a normal parameter
         */
        } else {
            array[key] = val;
        }
    }

    return array;
}

/**
 * Return true if the value is empty
 * @param {*} value
 * @returns
 */
function empty(value)
{
    // Check if the value is null or undefined
    if (value == null) {
        return true;
    }

    // Check if the value is a string and is empty
    if (typeof value === 'string' && value.trim() === '') {
        return true;
    }

    // Check if the value is an empty array
    if (Array.isArray(value) && value.length === 0) {
        return true;
    }

    // Check if the value is an empty object
    if (typeof value === 'object' && Object.keys(value).length === 0) {
        return true;
    }

    // Check if the value is a number and is NaN
    if (typeof value === 'number' && isNaN(value)) {
        return true;
    }

    // If none of the above conditions are met, the value is not empty
    return false;
}
