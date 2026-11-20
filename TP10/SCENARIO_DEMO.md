# TP10 — Scénario de démonstration (8 minutes)

Données de démonstration prêtes (base rechargée). Solution de repli : captures du
TP7 (`preuves/01` à `10`) et matrice du TP8 si le réseau tombe.

| Minute | Séquence | Preuve montrée |
|---|---|---|
| 0-1 | Contexte et problème : gestion manuelle des demandes de stage, besoin de suivi et d'audit. | Indicateurs du cadrage (TP1) |
| 1-2 | Connexion étudiant (`etudiant@exemple.test`). | `preuves/01_connexion.jpg` (TP7) |
| 2-3 | Dépôt d'une demande (entreprise, intitulé, dates). Ligne créée en base. | `preuves/02_formulaire_depot.jpg`, `preuves/03_demande_creee.jpg` |
| 3-4 | La demande apparaît dans la liste de l'étudiant. | `preuves/04_liste_etudiante.jpg` |
| 4-5 | Connexion responsable, soumission puis validation de la demande. | `preuves/05_validation.jpg` |
| 5-6 | Rafraîchissement : le statut persiste (donnée en base, pas en mémoire). | `preuves/06_persistance.jpg` |
| 6-7 | Contrôle d'accès : étudiant B ne voit pas les demandes de A ; mutation refusée → 403. | `preuves/11_patch_par_etudiant.txt` (TP8) |
| 7-8 | Tests et bilan : matrice de tests (16 cas), revue de code, sauvegarde/restauration. | `TP8/MATRICE_TESTS.md`, `TP9/preuves/03_sauvegarde_restoration.txt` |

## Chronométrage
- Durée cible : 8 minutes. Répéter au moins deux fois avec chronomètre.
- Si le réseau tombe : basculer sur les captures et la matrice (solution de repli).

## Ordre des preuves
Connexion → dépôt → création → liste → validation → persistance → accès 403 → tests.
