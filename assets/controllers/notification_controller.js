import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button'];

    initialize() {
        const state = this.element.dataset.state;

        if (state === "0") {
            this._setButtonOffClass();
            return;
        }

        this._setButtonOnClass();
    }

    toggle() {
        const state = this.element.dataset.state;
        this._toggleButtonStateClass();
        this.element.dataset.state = state === "0" ? 1 : 0;

        if (state === "0") {
            this._addUserEventNotification();
            return;
        }

        this._removeUserEventNotification()
    }

    _setButtonOnClass() {
        this.buttonTarget.classList.add('ml-auto');
        this.buttonTarget.classList.add('bg-prunoise');
    }

    _setButtonOffClass() {
        this.buttonTarget.classList.add('bg-gray-200');
    }

    _toggleButtonStateClass() {
        this.buttonTarget.classList.toggle('ml-auto');
        this.buttonTarget.classList.toggle('bg-prunoise');
        this.buttonTarget.classList.toggle('bg-gray-200');
    }

    _addUserEventNotification() {
        const route = this.element.dataset.addroute;
        fetch(route, {
            method: 'POST',
            mode: 'same-origin',
            cache: 'no-cache'
        });
    }

    _removeUserEventNotification() {
        const route = this.element.dataset.removeroute;
        fetch(route, {
            method: 'DELETE',
            mode: 'same-origin',
            cache: 'no-cache'
        });
    }
}
