import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['customerComment'];

    click(event) {
        event.stopPropagation();
        const commentContainer = document.getElementById('comment-container');
        const comment = document.getElementById('comment');
        commentContainer.classList.remove('hidden');
        comment.innerHTML = this.element.dataset.comment;

        document.addEventListener('click', () => {
            commentContainer.classList.add('hidden');
        })
    }
}
