'use strict';

/*
 * Formulaire de dépôt.
 * Les contrôles du navigateur aident, mais le serveur reste seul juge :
 * les messages 400 de l'API sont affichés sous le champ concerné.
 */

const utilisateur = exigerConnexion();

if (utilisateur) {
    afficherEntete(utilisateur);

    if (utilisateur.role !== 'ETUDIANT') {
        document.getElementById('formulaire-depot').hidden = true;
        afficherErreur(
            document.getElementById('erreur-generale'),
            'Seul un étudiant peut déposer une demande.'
        );
    }
}

document.getElementById('changer-compte').addEventListener('click', oublierUtilisateur);

const formulaire = document.getElementById('formulaire-depot');
const entreprise = document.getElementById('entreprise');
const intitule = document.getElementById('intitule');
const dateDebut = document.getElementById('date-debut');
const dateFin = document.getElementById('date-fin');

const champsErreur = {
    entreprise: document.getElementById('erreur-entreprise'),
    intitule: document.getElementById('erreur-intitule'),
    dates: document.getElementById('erreur-dates'),
    generale: document.getElementById('erreur-generale'),
};

/** Place le message du serveur sous le champ qu'il concerne. */
function afficherErreurServeur(message) {
    if (/entreprise/i.test(message)) {
        afficherErreur(champsErreur.entreprise, message);
    } else if (/intitul/i.test(message)) {
        afficherErreur(champsErreur.intitule, message);
    } else if (/date/i.test(message)) {
        afficherErreur(champsErreur.dates, message);
    } else {
        afficherErreur(champsErreur.generale, message);
    }
}

formulaire.addEventListener('submit', async (evenement) => {
    evenement.preventDefault();
    viderErreurs();

    // Contrôles immédiats.
    if (entreprise.value === '') {
        afficherErreur(champsErreur.entreprise, "Choisissez l'entreprise d'accueil.");
        entreprise.focus();
        return;
    }

    if (intitule.value.trim().length < 3) {
        afficherErreur(champsErreur.intitule, "L'intitulé doit contenir au moins 3 caractères.");
        intitule.focus();
        return;
    }

    if (dateDebut.value === '') {
        afficherErreur(champsErreur.dates, 'La date de début est obligatoire.');
        dateDebut.focus();
        return;
    }

    if (dateFin.value === '') {
        afficherErreur(champsErreur.dates, 'La date de fin est obligatoire.');
        dateFin.focus();
        return;
    }

    // L'ordre des dates est vérifié par le serveur : son message 400
    // s'affichera sous les dates, ce qui prouve que le client ne suffit pas.

    let resultat;

    try {
        resultat = await appelApi('/api/demandes', {
            method: 'POST',
            body: JSON.stringify({
                entreprise_id: Number(entreprise.value),
                intitule: intitule.value.trim(),
                date_debut: dateDebut.value,
                date_fin: dateFin.value,
            }),
        });
    } catch (erreur) {
        afficherErreur(champsErreur.generale, 'Le serveur est injoignable. Vérifiez votre connexion.');
        return;
    }

    if (resultat.statut === 201 && resultat.donnees?.demande) {
        // On ouvre la demande créée : elle est ainsi « retrouvée » tout de suite.
        location.assign(`detail.html?id=${encodeURIComponent(resultat.donnees.demande.id)}&creee=1`);
        return;
    }

    if (resultat.statut === 401) {
        oublierUtilisateur();
        location.replace('connexion.html?erreur=session');
        return;
    }

    if (resultat.statut === 403) {
        afficherErreur(champsErreur.generale, 'Seul un étudiant peut déposer une demande.');
        return;
    }

    afficherErreurServeur(messageErreur(resultat.statut, resultat.donnees));
});
