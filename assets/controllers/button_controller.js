import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['spinner', 'label'];

    initialize() {
        this.spinnerTarget.classList.remove('block');
        this.spinnerTarget.classList.add('hidden');
    }

    click() {
        this.spinnerTarget.classList.remove('hidden');
        this.spinnerTarget.classList.add('block');
        this.labelTarget.innerText = 'Chargement...'
    }
}
