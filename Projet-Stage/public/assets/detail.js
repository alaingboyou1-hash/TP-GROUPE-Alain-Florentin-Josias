'use strict';

/*
 * Détail d'une demande.
 * L'API n'expose pas encore /api/demandes/{id} : la page lit la liste
 * autorisée et y retrouve la demande demandée.
 */

const utilisateur = exigerConnexion();
const identifiant = new URLSearchParams(location.search).get('id');

const fiche = document.getElementById('fiche');
const zoneErreur = document.getElementById('erreur');
const confirmation = document.getElementById('confirmation');
const actions = document.getElementById('actions');
const indisponible = document.getElementById('indisponible');
const formulaireRefus = document.getElementById('formulaire-refus');
const motifSaisi = document.getElementById('motif-saisi');
const erreurMotif = document.getElementById('erreur-motif');

let demandeCourante = null;

if (utilisateur) {
    afficherEntete(utilisateur);
}

document.getElementById('changer-compte').addEventListener('click', oublierUtilisateur);

if (new URLSearchParams(location.search).get('creee') === '1') {
    confirmation.textContent = 'Demande enregistrée. Elle apparaît maintenant dans la liste.';
    confirmation.hidden = false;
}

/** Remplit la fiche avec du texte brut (textContent), jamais du HTML. */
function afficherDemande(demande) {
    demandeCourante = demande;
    fiche.hidden = false;

    document.getElementById('intitule').textContent = demande.intitule;
    document.getElementById('entreprise').textContent = libelleEntreprise(demande.entreprise_id);
    document.getElementById('periode').textContent =
        `${formaterDate(demande.date_debut)} → ${formaterDate(demande.date_fin)}`;

    const statut = document.getElementById('statut');
    statut.replaceChildren();
    const pastille = document.createElement('span');
    pastille.className = `statut statut-${String(demande.statut).toLowerCase()}`;
    pastille.textContent = libelleStatut(demande.statut);
    statut.append(pastille);

    const motifVisible = demande.statut === 'REFUSEE' && demande.motif_refus;
    document.getElementById('etiquette-motif').hidden = !motifVisible;
    document.getElementById('motif').hidden = !motifVisible;
    document.getElementById('motif').textContent = motifVisible ? demande.motif_refus : '';

    construireActions(demande);
}

/** Boutons visibles uniquement pour les transitions autorisées par l'API. */
function construireActions(demande) {
    actions.replaceChildren();
    formulaireRefus.hidden = true;
    indisponible.hidden = true;

    if (utilisateur?.role !== 'RESPONSABLE') {
        indisponible.hidden = false;
        return;
    }

    if (demande.statut === 'BROUILLON') {
        ajouterBouton('Soumettre la demande', () => changerStatut('SOUMISE'));
    } else if (demande.statut === 'SOUMISE') {
        ajouterBouton('Valider la demande', () => changerStatut('VALIDEE'));
        ajouterBouton('Refuser la demande', () => {
            formulaireRefus.hidden = false;
            motifSaisi.focus();
        }, 'danger');
    } else if (demande.statut === 'REFUSEE') {
        ajouterBouton('Remettre en brouillon', () => changerStatut('BROUILLON'));
    } else {
        indisponible.hidden = false;
        indisponible.textContent = 'Cette demande est validée : plus aucune action possible.';
    }
}

function ajouterBouton(libelle, action, classe = '') {
    const bouton = document.createElement('button');
    bouton.type = 'button';
    bouton.textContent = libelle;

    if (classe !== '') {
        bouton.className = classe;
    }

    bouton.addEventListener('click', action);
    actions.append(bouton);
}

async function changerStatut(statut, motif = null) {
    zoneErreur.hidden = true;
    confirmation.hidden = true;
    viderErreurs();

    const corps = { statut };

    if (statut === 'REFUSEE') {
        corps.motif = motif;
    }

    let resultat;

    try {
        resultat = await appelApi(`/api/demandes/${encodeURIComponent(demandeCourante.id)}/statut`, {
            method: 'PATCH',
            body: JSON.stringify(corps),
        });
    } catch (erreur) {
        afficherErreur(zoneErreur, 'Le serveur est injoignable. Vérifiez votre connexion.');
        return;
    }

    if (resultat.statut === 200 && resultat.donnees?.demande) {
        afficherDemande(resultat.donnees.demande);
        confirmation.textContent = 'Statut mis à jour.';
        confirmation.hidden = false;
        return;
    }

    if (resultat.statut === 401) {
        oublierUtilisateur();
        location.replace('connexion.html?erreur=session');
        return;
    }

    if (resultat.statut === 400 && /motif/i.test(resultat.donnees?.erreur?.message ?? '')) {
        afficherErreur(erreurMotif, resultat.donnees.erreur.message);
        motifSaisi.focus();
        return;
    }

    afficherErreur(zoneErreur, messageErreur(resultat.statut, resultat.donnees));
}

formulaireRefus.addEventListener('submit', (evenement) => {
    evenement.preventDefault();

    if (motifSaisi.value.trim() === '') {
        afficherErreur(erreurMotif, 'Le motif de refus est obligatoire.');
        motifSaisi.focus();
        return;
    }

    changerStatut('REFUSEE', motifSaisi.value.trim());
});

document.getElementById('annuler-refus').addEventListener('click', () => {
    formulaireRefus.hidden = true;
    motifSaisi.value = '';
    viderErreurs();
});

async function charger() {
    if (!identifiant) {
        afficherErreur(zoneErreur, 'Aucune demande demandée.');
        return;
    }

    let resultat;

    try {
        resultat = await appelApi('/api/demandes');
    } catch (erreur) {
        afficherErreur(zoneErreur, 'Le serveur est injoignable. Vérifiez votre connexion.');
        return;
    }

    if (resultat.statut === 401) {
        oublierUtilisateur();
        location.replace('connexion.html?erreur=session');
        return;
    }

    if (resultat.statut !== 200) {
        afficherErreur(zoneErreur, messageErreur(resultat.statut, resultat.donnees));
        return;
    }

    const demandes = resultat.donnees?.demandes ?? [];
    const demande = demandes.find((element) => String(element.id) === String(identifiant));

    if (!demande) {
        afficherErreur(zoneErreur, "Cette demande n'existe pas ou ne vous appartient pas.");
        return;
    }

    afficherDemande(demande);
}

charger();
