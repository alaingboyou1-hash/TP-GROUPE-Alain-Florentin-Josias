'use strict';

/*
 * Outils partagés par les pages : appels à l'API, messages d'erreur,
 * mémoire de la session côté navigateur et petits formats d'affichage.
 */

// Les pages et l'API sont servies par le même serveur : pas de domaine à préciser.
const API = '';

// Noms du jeu d'essai (config/seed.sql de l'API) : l'API ne renvoie que l'identifiant.
const ENTREPRISES = {
    1: 'Atelier Numérique — Cotonou',
    2: 'Bénin Data — Porto-Novo',
};

/*
 * Page ouverte en double-cliquant le fichier (file://) : aucune API ne peut
 * répondre. On l'explique au lieu de laisser un « serveur injoignable » opaque.
 */
if (location.protocol === 'file:') {
    document.addEventListener('DOMContentLoaded', () => {
        const avis = document.createElement('p');
        avis.className = 'erreur';
        avis.setAttribute('role', 'alert');
        avis.textContent = "Cette page est ouverte depuis le disque. Lancez le serveur, puis ouvrez http://localhost:8000 — sinon l'API ne peut pas répondre.";
        document.querySelector('main')?.prepend(avis);
    });
}

const LIBELLES_STATUT = {
    BROUILLON: 'Brouillon',
    SOUMISE: 'Soumise',
    VALIDEE: 'Validée',
    REFUSEE: 'Refusée',
};

/** Appelle l'API et renvoie le code HTTP avec le corps JSON éventuel. */
async function appelApi(chemin, options = {}) {
    const reponse = await fetch(API + chemin, {
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        ...options,
    });

    let donnees = null;
    try {
        donnees = await reponse.json();
    } catch (erreur) {
        // Réponse vide ou non JSON : on garde donnees à null.
    }

    return { statut: reponse.status, donnees };
}

/** Transforme un code HTTP en message compréhensible par l'utilisateur. */
function messageErreur(statut, donnees) {
    if (statut === 400) {
        return donnees?.erreur?.message ?? 'Les données saisies sont invalides.';
    }
    if (statut === 401) {
        return 'Votre session a expiré. Reconnectez-vous.';
    }
    if (statut === 403) {
        return "Vous n'avez pas les droits pour cette action.";
    }
    if (statut === 404) {
        return 'Cette demande est introuvable.';
    }
    if (statut === 409) {
        return donnees?.erreur?.message ?? "Cette action n'est plus possible.";
    }
    // 500 et cas imprévus : message neutre, jamais de détail technique.
    return 'Une erreur est survenue. Réessayez dans un instant.';
}

function afficherErreur(element, message) {
    element.textContent = message;
    element.hidden = message === '';
}

function viderErreurs() {
    document.querySelectorAll('.erreur').forEach((element) => {
        element.textContent = '';
        element.hidden = true;
    });
}

function memoriserUtilisateur(utilisateur) {
    sessionStorage.setItem('utilisateur', JSON.stringify(utilisateur));
}

function utilisateurCourant() {
    try {
        return JSON.parse(sessionStorage.getItem('utilisateur'));
    } catch (erreur) {
        return null;
    }
}

function oublierUtilisateur() {
    sessionStorage.removeItem('utilisateur');
}

/**
 * Renvoie l'utilisateur mémorisé, ou redirige vers la connexion.
 * Le serveur reste seul juge : une session bricolée côté navigateur
 * reçoit 403 dès le premier appel.
 */
function exigerConnexion() {
    const utilisateur = utilisateurCourant();

    if (!utilisateur) {
        location.replace('connexion.html?erreur=session');
        return null;
    }

    return utilisateur;
}

/** Remplit l'en-tête avec le nom, le rôle et le lien de changement de compte. */
function afficherEntete(utilisateur) {
    const zone = document.getElementById('utilisateur');

    if (!zone) {
        return;
    }

    zone.textContent = `${utilisateur.prenom} ${utilisateur.nom} — ${libelleRole(utilisateur.role)}`;
}

function libelleRole(role) {
    return { ETUDIANT: 'Étudiant', RESPONSABLE: 'Responsable', SECRETARIAT: 'Secrétariat' }[role] ?? role;
}

function libelleStatut(statut) {
    return LIBELLES_STATUT[statut] ?? statut;
}

function libelleEntreprise(id) {
    return ENTREPRISES[id] ?? `Entreprise ${id}`;
}

/** 2027-02-01 devient 01/02/2027. */
function formaterDate(dateIso) {
    const [annee, mois, jour] = String(dateIso).split('-');

    return `${jour}/${mois}/${annee}`;
}

/** Crée une cellule de tableau en texte brut, jamais en HTML. */
function cellule(contenu, entete = false) {
    const element = document.createElement(entete ? 'th' : 'td');
    element.textContent = contenu;

    return element;
}
