# Contrat des trois routes

**TP4 — Squelette technique et contrat d'API**

Le contrat est écrit avant le code. Toutes les réponses sont en JSON UTF-8.
Les routes d'écriture (`POST`, `PATCH`) exigeront une session valide.

## 1. `GET /api/demandes`

| Élément | Valeur |
|---|---|
| Méthode | `GET` |
| Chemin | `/api/demandes` |
| Rôles autorisés | Responsable, secrétariat |
| Corps | aucun |
| Succès | `200` — liste des demandes |
| Erreurs | `401` non authentifié, `403` rôle insuffisant |

Réponse `200` :

```json
{ "demandes": [] }
```

## 2. `POST /api/demandes`

| Élément | Valeur |
|---|---|
| Méthode | `POST` |
| Chemin | `/api/demandes` |
| Rôle autorisé | Étudiant |
| Corps | JSON |
| Succès | `201` — demande créée |
| Erreurs | `400` corps invalide, `401` non authentifié |

Corps attendu :

```json
{
  "entreprise_id": 1,
  "intitule": "Stage de développement web",
  "date_debut": "2027-01-05",
  "date_fin": "2027-03-27"
}
```

Réponse `201` :

```json
{
  "demande": {
    "id": 1,
    "statut": "BROUILLON",
    "entreprise_id": 1,
    "intitule": "Stage de développement web",
    "date_debut": "2027-01-05",
    "date_fin": "2027-03-27"
  }
}
```

## 3. `PATCH /api/demandes/{id}/statut`

| Élément | Valeur |
|---|---|
| Méthode | `PATCH` |
| Chemin | `/api/demandes/{id}/statut` |
| Rôle autorisé | Responsable |
| Corps | JSON |
| Succès | `200` — statut mis à jour |
| Erreurs | `400` statut invalide, `403` rôle insuffisant, `404` demande introuvable, `409` transition interdite |

Corps attendu :

```json
{ "statut": "VALIDEE" }
```

Réponse `200` :

```json
{ "demande": { "id": 1, "statut": "VALIDEE" } }
```

## Codes communs

| Code | Cause |
|---|---|
| `400` | JSON invalide, champ manquant ou valeur inconnue |
| `401` | session absente ou expirée |
| `403` | rôle insuffisant |
| `404` | ressource introuvable |
| `409` | transition de statut interdite |

Enveloppe d'erreur :

```json
{ "erreur": { "code": "conflit_etat", "message": "Transition BROUILLON → VALIDEE interdite" } }
```
