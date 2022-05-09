import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['message'];

    hide() {
        this.messageTarget.classList.add('hidden');
    }
}
