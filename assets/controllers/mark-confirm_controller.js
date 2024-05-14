import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String
    }

    confirm() {
        document.getElementById("confirm-paid-form").action = this.urlValue
    }
}
