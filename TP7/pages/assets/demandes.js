'use strict';

/*
 * Liste des demandes.
 * Tout le contenu vient de l'API et est inséré avec textContent :
 * une balise HTML saisie par un utilisateur reste du texte.
 */

const utilisateur = exigerConnexion();
const lignes = document.getElementById('lignes');
const listeVide = document.getElementById('liste-vide');
const zoneErreur = document.getElementById('erreur');
const lienDepot = document.getElementById('lien-depot');

if (utilisateur) {
    afficherEntete(utilisateur);
    lienDepot.hidden = utilisateur.role !== 'ETUDIANT';
}

document.getElementById('changer-compte').addEventListener('click', oublierUtilisateur);

/** Construit une ligne de tableau sans jamais interpréter de HTML. */
function ajouterLigne(demande) {
    const ligne = document.createElement('tr');

    ligne.append(cellule(demande.intitule));
    ligne.append(cellule(libelleEntreprise(demande.entreprise_id)));
    ligne.append(cellule(`${formaterDate(demande.date_debut)} → ${formaterDate(demande.date_fin)}`));

    const caseStatut = document.createElement('td');
    const pastille = document.createElement('span');
    pastille.className = `statut statut-${String(demande.statut).toLowerCase()}`;
    pastille.textContent = libelleStatut(demande.statut);
    caseStatut.append(pastille);
    ligne.append(caseStatut);

    const caseAction = document.createElement('td');
    const lien = document.createElement('a');
    lien.href = `detail.html?id=${encodeURIComponent(demande.id)}`;
    lien.textContent = 'Voir';
    caseAction.append(lien);
    ligne.append(caseAction);

    lignes.append(ligne);
}

async function chargerDemandes() {
    zoneErreur.hidden = true;
    lignes.replaceChildren();

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

    demandes.forEach(ajouterLigne);
    listeVide.hidden = demandes.length > 0;
}

document.getElementById('actualiser').addEventListener('click', chargerDemandes);

chargerDemandes();
