import {Controller} from '@hotwired/stimulus';
import { Modal } from "bootstrap"

export default class extends Controller {
    static targets = ['modal', 'currentImage', 'counter'];

    static values = {
        images: Array,
        currentIndex: Number,
    };

    connect() {
        this.modal = new Modal(this.modalTarget);
    };

    open(even) {
        this.currentIndexValue = parseInt(even.params.index);
        this.updateDisplay();
        this.modal.show();
    };

    // Ouvre la lightbox sur une image spécifique
    updateDisplay() {
        const currentImage = this.imagesValue[this.currentIndexValue];
        this.currentImageTarget.src = currentImage.src;
        this.currentImageTarget.alt = currentImage.alt;

        // Mettre à jour le compteur
        this.counterTarget.textContent = `${this.currentIndexValue + 1} / ${this.imagesValue.length}`
    };

    // Image suivante
    next() {
        if (this.currentIndexValue === this.imagesValue.length - 1) {
            this.currentIndexValue = 0;
        } else if (this.currentIndexValue < this.imagesValue.length - 1) {
            this.currentIndexValue++;
        }

        this.updateDisplay()
    }

    // Image précédente
    previous() {
        if (this.currentIndexValue === 0) {
            this.currentIndexValue = this.imagesValue.length - 1;
        } else if (this.currentIndexValue > 0) {
            this.currentIndexValue--
        }

        this.updateDisplay();
    }

    close() {
        console.log('close');
        this.modal.toggle();
    }
}
