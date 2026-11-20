# TP10 — Dossier de livraison

## Liste de contrôle (8 éléments)

| # | Élément | Où dans le dépôt | Statut |
|---|---|---|---|
| 1 | Dépôt Git (historique des commits) | `Documents/TP` (racine du dépôt) | ✅ |
| 2 | Fiche de cadrage | `TP1/Fiche de cadrage.pdf` | ✅ |
| 3 | Exigences + matrice de traçabilité | `TP2/` | ✅ |
| 4 | Diagrammes UML | `TP3/` | ✅ |
| 5 | Schéma SQL et migrations | `TP3/`, `TP6/config/schema.sql`, `TP6/config/seed.sql` | ✅ |
| 6 | Plan de tests + fiche de recette | `TP8/MATRICE_TESTS.md`, `TP8/preuves/` | ✅ |
| 7 | README d'installation reproductible | `TP9/README.md` | ✅ |
| 8 | Décisions (ADR) et dette technique | `TP9/README.md` (section Décisions) | ✅ |

## Contenu du projet

- **TP4** : squelette technique et contrat d'API.
- **TP5** : organisation Git et équipe.
- **TP6** : API des demandes (PHP 8, PDO, MariaDB).
- **TP7** : interface Web (HTML/CSS/JS) et intégration.
- **TP8** : matrice de tests (16 cas) et revue de code.
- **TP9** : déploiement, sauvegarde/restauration, ticket d'incident.

## Vérification
- Installation rejouée sur un second poste (base neuve `tp9_second`) : OK.
- Page de santé `GET /api/sante` : HTTP 200.
- Sauvegarde/restauration vérifiée (COUNT identiques).
- Matrice de tests : 16/16 passent.
