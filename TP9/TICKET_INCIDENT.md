# TP9 — Ticket d'incident reproductible

## Titre
Dépôt d'une demande : double-clic sur « Envoyer » crée deux demandes.

## Contexte
- Version : TP6/TP7 (API + pages), branche principale.
- Environnement : `php -S localhost:8000`, base `tp6_stages`, compte étudiant.
- Type : maintenance corrective. Priorité : moyenne.

## Étapes pour reproduire
1. Se connecter comme étudiant (`etudiant@exemple.test` / `etudiant123`).
2. Ouvrir « Déposer une demande ».
3. Remplir le formulaire (entreprise, intitulé, dates valides).
4. Cliquer deux fois rapidement sur « Envoyer la demande ».

## Résultat observé
Deux requêtes `POST /api/demandes` sont envoyées ; deux demandes identiques
apparaissent dans la liste (deux lignes `BROUILLON` avec le même intitulé).

## Résultat attendu
Une seule demande créée ; le bouton est désactivé pendant la requête (ou la
seconde soumission est ignorée).

## Cause probable
Le gestionnaire `submit` de `depot.js` n'invalide pas le bouton pendant l'appel
`fetch` ; aucun garde-fou côté client contre la double soumission.

## Correctif proposé
Désactiver le bouton au début du `submit` et le réactiver en cas d'erreur ;
alternativement, générer une clé d'idempotence envoyée avec la requête.

## Vérification du correctif
Rejouer les étapes : une seule ligne créée ; en cas d'erreur réseau, le bouton
redevient actif.
