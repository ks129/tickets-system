import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String,
        type: String
    }

    confirm() {
        document.getElementById("delete-confirm-modal-form-" + this.typeValue).action = this.urlValue
    }
}
