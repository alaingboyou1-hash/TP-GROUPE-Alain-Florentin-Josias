# TP8 — Fiche de revue de code

Contexte : revue croisée du dépôt `projet-stages` (TP6 API + TP7 pages). Faute de
groupe externe disponible, la revue a été faite par un membre de l'équipe qui n'avait
pas écrit le code relu ; les quatre remarques ci-dessous sont classées selon leur type
et leur priorité.

| # | Fichier / zone | Remarque | Type | Priorité | Action proposée |
|---|---|---|---|---|---|
| 1 | `src/Controllers/AuthController.php` — connexion | Aucune limitation du nombre de tentatives : un attaquant peut tester des mots de passe sans blocage. | Préventive | Haute | Ajouter un compteur d'échecs par adresse IP / compte et un délai exponentiel. |
| 2 | `src/Support/Http.php` / session | Pas de jeton CSRF dédié ; la protection repose uniquement sur `SameSite=Lax`. | Préventive | Haute | Mettre en place un jeton double-submit pour les requêtes mutantes. |
| 3 | `src/Controllers/DemandeController.php` — `index()` | `findAll()` charge toutes les demandes sans pagination ; risque de performance si le volume augmente. | Évolutive | Moyenne | Ajouter `LIMIT`/`OFFSET` et des paramètres `page`/`taille`. |
| 4 | `pages/assets/api.js` — `ENTREPRISES` | Les noms d'entreprises sont codés en dur côté client car l'API ne renvoie que `entreprise_id`. | Évolutive | Basse | Exposer `nom` et `ville` dans la réponse de l'API (jointure). |

## Points positifs relevés

- Requêtes SQL préparées (`PDO::prepare`) : pas d'injection SQL exploitable.
- Validation stricte des statuts et des transitions côté serveur (table `TRANSITIONS`).
- Motifs de refus obligatoires et longueur contrôlée (≤ 255).
- Transactions pour le changement de statut (statut + historique), avec rollback.
- Mots de passe hachés en bcrypt (coût 12), cookie `HttpOnly` + `SameSite=Lax`.
- Rendu côté client avec `textContent` : aucune interprétation de HTML saisi.

## Dette technique connue

- Pas de route `GET /api/demandes/{id}` : la page de détail relit la liste autorisée.
- Pas de route de déconnexion : une nouvelle connexion remplace la session courante.
- Pas de pagination ni de filtres sur la liste des demandes.
- Pas de journalisation des tentatives de connexion échouées.

## Synthèse

Les quatre remarques sont des améliorations, pas des défauts bloquants : aucune ne
contredit les exigences du cahier des charges. Les deux remarques de priorité haute
(CSRF, limitation des tentatives) sont à traiter avant une mise en production.
