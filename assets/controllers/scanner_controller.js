import { Controller } from '@hotwired/stimulus';
import { Html5QrcodeScanner } from 'html5-qrcode';
import axios from 'axios';

export default class extends Controller {
    static values = {
        eventId: Number,
        url: String
    }

    connect() {
        let reader = new Html5QrcodeScanner('reader', { fps: 10, qrbox: {width: 250, height: 250} }, false)
        reader.render(this.onSuccess, this.onFailure);
        window.checkingUrlForApp = this.urlValue;
        window.eventIdForApp = this.eventIdValue;
    }

    onSuccess(decodedText, decodedResult) {
        if (window.sessionStorage.getItem('lastQrcodeScanned') !== decodedText) {
            window.sessionStorage.setItem('lastQrcodeScanned', decodedText);
            axios.post(window.checkingUrlForApp, {
                eventId: window.eventIdForApp,
                ticketNumber: decodedText
            }).then(function(response) {
                document.getElementById("response-area").innerHTML = response.data.html;
            })
        }
    }

    onFailure() {
    }
}
