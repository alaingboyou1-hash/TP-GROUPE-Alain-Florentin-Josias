'use strict';

/*
 * Page de connexion.
 * Affiche les erreurs 400 et 401 près des champs, et un message neutre pour un 500.
 */

const formulaire = document.getElementById('formulaire-connexion');
const champEmail = document.getElementById('email');
const champMotDePasse = document.getElementById('mot-de-passe');
const erreurEmail = document.getElementById('erreur-email');
const erreurMotDePasse = document.getElementById('erreur-mot-de-passe');
const erreurGenerale = document.getElementById('erreur-generale');
const information = document.getElementById('information');

// Arrivée après une session expirée ou une page protégée.
if (new URLSearchParams(location.search).get('erreur') === 'session') {
    information.textContent = 'Votre session a expiré. Reconnectez-vous pour continuer.';
    information.hidden = false;
}

formulaire.addEventListener('submit', async (evenement) => {
    evenement.preventDefault();
    viderErreurs();
    information.hidden = true;

    const email = champEmail.value.trim();
    const motDePasse = champMotDePasse.value;

    // Contrôle immédiat, le serveur revérifie de toute façon.
    if (email === '') {
        afficherErreur(erreurEmail, "L'adresse électronique est obligatoire.");
        champEmail.focus();
        return;
    }

    if (motDePasse === '') {
        afficherErreur(erreurMotDePasse, 'Le mot de passe est obligatoire.');
        champMotDePasse.focus();
        return;
    }

    let resultat;

    try {
        resultat = await appelApi('/api/connexion', {
            method: 'POST',
            body: JSON.stringify({ email, mot_de_passe: motDePasse }),
        });
    } catch (erreur) {
        afficherErreur(erreurGenerale, 'Le serveur est injoignable. Vérifiez votre connexion.');
        return;
    }

    if (resultat.statut === 200 && resultat.donnees?.utilisateur) {
        memoriserUtilisateur(resultat.donnees.utilisateur);
        location.assign('demandes.html');
        return;
    }

    if (resultat.statut === 400) {
        afficherErreur(erreurEmail, 'Adresse et mot de passe sont obligatoires.');
        return;
    }

    if (resultat.statut === 401) {
        afficherErreur(erreurGenerale, 'Adresse ou mot de passe incorrect.');
        return;
    }

    afficherErreur(erreurGenerale, messageErreur(resultat.statut, resultat.donnees));
});
