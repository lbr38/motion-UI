async function PeerConnection(cameraId) {
    const media = 'video+audio';

    const pc = new RTCPeerConnection({
        iceServers: [{urls: 'stun:stun.l.google.com:19302'}]
    });

    const localTracks = [];

    if (/camera|microphone/.test(media)) {
        const tracks = await getMediaTracks('user', {
            video: media.indexOf('camera') >= 0,
            audio: media.indexOf('microphone') >= 0,
        });
        tracks.forEach(track => {
            pc.addTransceiver(track, {direction: 'sendonly'});
            if (track.kind === 'video') localTracks.push(track);
        });
    }

    if (media.indexOf('display') >= 0) {
        const tracks = await getMediaTracks('display', {
            video: true,
            audio: media.indexOf('speaker') >= 0,
        });
        tracks.forEach(track => {
            pc.addTransceiver(track, {direction: 'sendonly'});
            if (track.kind === 'video') localTracks.push(track);
        });
    }

    if (/video|audio/.test(media)) {
        const tracks = ['video', 'audio']
            .filter(kind => media.indexOf(kind) >= 0)
            .map(kind => pc.addTransceiver(kind, {direction: 'recvonly'}).receiver.track);
        localTracks.push(...tracks);
    }

    /**
     *  Find the video element with the specified camera Id and set the source object
     */
    document.querySelectorAll('video[camera-id="' + cameraId + '"]').forEach(videoElement => {
        videoElement.srcObject = new MediaStream(localTracks);
    });
    return pc;
}

async function getMediaTracks(media, constraints) {
    try {
        const stream = media === 'user'
            ? await navigator.mediaDevices.getUserMedia(constraints)
            : await navigator.mediaDevices.getDisplayMedia(constraints);
        return stream.getTracks();
    } catch (e) {
        console.warn(e);
        return [];
    }
}

/**
 *  Connect to go2rtc server using WebSocket
 */
async function connect(cameraId) {
    const pc = await PeerConnection(cameraId);
    let pingInterval = null;

    /**
     *  Get current origin (http://xxxx:port) then replace 'http' with 'ws'
     *  wss (secured) will automatically be used if the server uses https
     */
    const wsUrl = window.location.origin.replace('http', 'ws') + '/api/ws?src=camera_' + cameraId + '&media=video+audio';

    // For debug purpose
    console.info('Connecting to WebSocket for WebRTC:', wsUrl);

    const ws = new WebSocket(wsUrl);

    ws.addEventListener('open', () => {
        console.info('WebRTC websocket connection opened at ' + wsUrl);

        // Start the ping interval for keep-alive
        mycamera.ping(ws);

        pc.addEventListener('icecandidate', ev => {
            if (!ev.candidate) return;
            const msg = {type: 'webrtc/candidate', value: ev.candidate.candidate};
            // For debug purpose
            console.info('Sending ICE candidate:', msg);
            ws.send(JSON.stringify(msg));
        });

        pc.createOffer().then(offer => {
            // For debug purpose
            console.info('Created offer:', offer);
            return pc.setLocalDescription(offer);
        }).then(() => {
            const msg = {type: 'webrtc/offer', value: pc.localDescription.sdp};
            // For debug purpose
            console.info('Sending offer:', msg);
            ws.send(JSON.stringify(msg));
        }).catch(error => {
            console.error('Error creating or sending offer:', error);
        });
    });

    ws.addEventListener('message', ev => {
        // Parse the received message
        const msg = JSON.parse(ev.data);

        // For debug purpose
        console.info('Received message:', msg);

        if (msg.type === 'webrtc/candidate') {
            // Try to add the ICE candidate, if an error is caught, close the WebSocket connection
            pc.addIceCandidate({candidate: msg.value, sdpMid: '0'}).catch(error => {
                console.error('Camera #' + cameraId + ': Error adding ICE candidate:', error);
                ws.close();
            });
        } else if (msg.type === 'webrtc/answer') {
            // Try to set the remote description, if an error is caught, close the WebSocket connection
            pc.setRemoteDescription({type: 'answer', sdp: msg.value}).catch(error => {
                console.error('Camera #' + cameraId + ': Error setting remote description:', error);
                ws.close();
            });
        // If the message type is 'error', close the WebSocket connection
        } else if (msg.type === 'error') {
            console.error('Camera #' + cameraId + ' Error:', msg.value);
            ws.close();
        }
    });

    // When an error occurs, log the error and close the WebSocket connection
    ws.addEventListener('error', error => {
        console.error('WebRTC websocket error:', error);
        ws.close();
    });

    // When the WebSocket connection is closed, set the camera as unavailable and close the PeerConnection
    ws.addEventListener('close', () => {
        console.error('WebRTC websocket connection closed for camera #' + cameraId);

        // Stop the ping interval
        mycamera.stopPing();

        // Set the camera as unavailable (unless the stream is disabled)
        mycamera.setUnavailable(cameraId, 'Stream error');

        // Close the PeerConnection
        pc.close();
    });
}