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

    updatePersonRecap() {
        // Adults
        updateBasePersonRecap(this.personAdultTarget, this.recapPersonAdultTarget);
        // Enfants
        updateBasePersonRecap(this.personChildTarget, this.recapPersonChildTarget);
    }
}
