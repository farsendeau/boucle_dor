import { Controller } from "@hotwired/stimulus"


function updateBaseDateRecap(dateTarget, recapDateTarget) {
    let content = '--/--/----';
    if (dateTarget.value) {
        const dateObj = new Date(dateTarget.value);
        content = dateObj.toLocaleDateString('fr-FR');
    }

    recapDateTarget.textContent = content;
}

function updateBasePersonRecap(personTarget, recapPersonTarget) {
    recapPersonTarget.textContent = personTarget.value ? personTarget.value : 0;
}

export default class extends Controller {
    static targets = [
        'dateArrival', 'recapArrival', 'dateDeparture', 'recapDeparture',
        'personAdult', 'personChild', 'recapPersonAdult', 'recapPersonChild',
        'recapNight', 'recapPrice'
    ];

    static values = { priceNight: Number }

    connect() {
        this.updateDateRecap();
        this.updatePersonRecap();
    };

    updateDateRecap() {
        // Date d'arrivée
        updateBaseDateRecap(this.dateArrivalTarget, this.recapArrivalTarget);
        // Date de départ
        updateBaseDateRecap(this.dateDepartureTarget, this.recapDepartureTarget);

        // Validation des dates croisées
        this.validateDates();

        // Mise à jour de la nuitée
        if (this.dateArrivalTarget.value && this.dateDepartureTarget.value) {
            const dateArrival = new Date(this.dateArrivalTarget.value);
            const dateDeparture = new Date(this.dateDepartureTarget.value);
            // Calcule de la différence en millisecondes
            const diffTime = dateDeparture - dateArrival;

            // Convertit en jours (1 jour = 24h * 60min * 60s * 1000ms)
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            // update le nombre de nuitées (positif) ou 0 si négatif
            const nbNight = Math.max(0, diffDays);
            this.recapNightTarget.textContent = nbNight;

            // update du prix
            this.recapPriceTarget.textContent = nbNight * this.priceNightValue + ' €**';
        } else {
            this.recapNightTarget.textContent = 0;
            this.recapPriceTarget.textContent = "--- €**";
        }
    }

    validateDates() {
        const arrivalValue = this.dateArrivalTarget.value;
        const departureValue = this.dateDepartureTarget.value;

        // Si dateArrival est sélectionnée, ajuste le min de dateDeparture
        if (arrivalValue) {
            const arrivalDate = new Date(arrivalValue);
            const nextDay = new Date(arrivalDate);
            nextDay.setDate(nextDay.getDate() + 1);
            const minDeparture = nextDay.toISOString().split('T')[0];
            
            this.dateDepartureTarget.min = minDeparture;
            
            // Si dateDeparture est déjà sélectionnée et invalide, la réinitialiser
            if (departureValue && departureValue <= arrivalValue) {
                this.dateDepartureTarget.value = '';
                this.dateDepartureTarget.classList.add('is-invalid');
                this.showDateError(this.dateDepartureTarget, 'La date de départ doit être postérieure à la date d\'arrivée');
            } else {
                this.dateDepartureTarget.classList.remove('is-invalid');
                this.removeDateError(this.dateDepartureTarget);
            }
        }

        // Si dateDeparture est sélectionnée, ajuste le max de dateArrival
        if (departureValue) {
            const departureDate = new Date(departureValue);
            const previousDay = new Date(departureDate);
            previousDay.setDate(previousDay.getDate() - 1);
            const maxArrival = previousDay.toISOString().split('T')[0];
            
            this.dateArrivalTarget.max = maxArrival;
            
            // Si dateArrival est déjà sélectionnée et invalide, la réinitialiser
            if (arrivalValue && arrivalValue >= departureValue) {
                this.dateArrivalTarget.value = '';
                this.dateArrivalTarget.classList.add('is-invalid');
                this.showDateError(this.dateArrivalTarget, 'La date d\'arrivée doit être antérieure à la date de départ');
            } else {
                this.dateArrivalTarget.classList.remove('is-invalid');
                this.removeDateError(this.dateArrivalTarget);
            }
        }
    }

    showDateError(target, message) {
        this.removeDateError(target);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = message;
        errorDiv.dataset.dateError = 'true';
        target.parentNode.appendChild(errorDiv);
    }

    removeDateError(target) {
        const existingError = target.parentNode.querySelector('[data-date-error="true"]');
        if (existingError) {
            existingError.remove();
        }
    }

    updatePersonRecap() {
        // Adults
        updateBasePersonRecap(this.personAdultTarget, this.recapPersonAdultTarget);
        // Enfants
        updateBasePersonRecap(this.personChildTarget, this.recapPersonChildTarget);
    }
}
