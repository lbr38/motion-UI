import {VideoRTC} from './video-rtc.js';

/**
 * This is example, how you can extend VideoRTC player for your app.
 * Also you can check this example: https://github.com/AlexxIT/WebRTC
 */
class VideoStream extends VideoRTC {
    set divMode(value) {
        // this.querySelector('.mode').innerText = value;
        this.querySelector('.status').innerText = '';
    }

    set divError(value) {
        const state = this.querySelector('.mode').innerText;
        if (state !== 'loading') return;
        this.querySelector('.mode').innerText = 'error';
        this.querySelector('.status').innerText = value;

        // Émettre un événement d'erreur personnalisé
        this.dispatchEvent(new CustomEvent('stream-error', {
            detail: { message: value, cameraId: this.cameraId }
        }));
    }

    /**
     * Custom GUI
     */
    oninit() {
        console.debug('stream.oninit');
        super.oninit();

        this.innerHTML = `
        <style>
        video-stream {
            position: relative;
        }
        .info {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            padding: 12px;
            color: white;
            display: flex;
            justify-content: space-between;
            pointer-events: none;
        }
        </style>
        <div class="info">
            <div class="status"></div>
            <div class="mode"></div>
        </div>
        `;

        const info = this.querySelector('.info');
        this.insertBefore(this.video, info);
    }

    onconnect() {
        console.debug('stream.onconnect');
        const result = super.onconnect();
        if (result) {
            this.divMode = 'loading';

            // Émettre un événement de connexion
            this.dispatchEvent(new CustomEvent('stream-connecting', {
                detail: { cameraId: this.cameraId }
            }));
        }

        return result;
    }

    ondisconnect() {
        console.debug('stream.ondisconnect');
        super.ondisconnect();

        // Émettre un événement de déconnexion
        this.dispatchEvent(new CustomEvent('stream-disconnected', {
            detail: { cameraId: this.cameraId }
        }));
    }

    onopen() {
        console.debug('stream.onopen');
        const result = super.onopen();

        this.onmessage['stream'] = msg => {
            console.debug('stream.onmessage', msg);
            switch (msg.type) {
                case 'error':
                    this.divError = msg.value;

                    // Émettre un événement d'erreur WebSocket
                    this.dispatchEvent(new CustomEvent('ws-error', {
                        detail: { message: msg.value, cameraId: this.cameraId }
                    }));
                    break;
                case 'mse':
                case 'hls':
                case 'mp4':
                case 'mjpeg':
                    this.divMode = msg.type.toUpperCase();

                    // Émettre un événement de stream prêt
                    this.dispatchEvent(new CustomEvent('stream-ready', {
                        detail: { mode: msg.type, cameraId: this.cameraId }
                    }));

                    break;
            }
        };

        return result;
    }

    onclose() {
        console.debug('stream.onclose');

        // Émettre un événement WebSocket fermé
        this.dispatchEvent(new CustomEvent('ws-close', {
            detail: { message: 'WebSocket connection closed', cameraId: this.cameraId }
        }));

        return super.onclose();
    }

    onpcvideo(ev) {
        console.debug('stream.onpcvideo');
        super.onpcvideo(ev);

        if (this.pcState !== WebSocket.CLOSED) {
            this.divMode = 'RTC';

            // Émettre un événement de stream prêt pour WebRTC
            this.dispatchEvent(new CustomEvent('stream-ready', {
                detail: { mode: 'webrtc', cameraId: this.cameraId }
            }));
        }
    }
}

customElements.define('video-stream', VideoStream);