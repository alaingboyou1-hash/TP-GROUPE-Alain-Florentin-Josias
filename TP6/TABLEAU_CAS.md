# Tableau des six cas testés

**TP6 — API des demandes**

Environnement des tests : PHP 8.4, MariaDB 10.4, base réinitialisée avant l'exécution.

| # | Cas | Requête | Attendu | Obtenu | Réponse | Preuve |
|---|---|---|---|---|---|---|
| 1 | Succès | `PATCH /api/demandes/2/statut` `{"statut":"VALIDEE"}` en Responsable | `200` | `200` | `{"demande":{...,"statut":"VALIDEE"}}` | `preuves/cas_01_succes.txt` |
| 2 | Données invalides | `PATCH /api/demandes/1/statut` `{"statut":"REFUSEE"}` sans motif | `400` | `400` | `{"erreur":{"code":"motif_requis",...}}` | `preuves/cas_02_400.txt` |
| 3 | Non authentifié | `PATCH /api/demandes/1/statut` sans session | `401` | `401` | `{"erreur":{"code":"non_authentifie",...}}` | `preuves/cas_03_401.txt` |
| 4 | Accès interdit | `PATCH /api/demandes/1/statut` en Étudiant | `403` | `403` | `{"erreur":{"code":"acces_interdit",...}}` | `preuves/cas_04_403.txt` |
| 5 | Introuvable | `PATCH /api/demandes/999/statut` en Responsable | `404` | `404` | `{"erreur":{"code":"demande_introuvable",...}}` | `preuves/cas_05_404.txt` |
| 6 | Conflit d'état | `PATCH /api/demandes/3/statut` sur une demande déjà validée | `409` | `409` | `{"erreur":{"code":"transition_interdite",...}}` | `preuves/cas_06_409.txt` |

## Capture d'écran de l'exécution

Exécution réelle des six cas dans un terminal : `preuves/capture_execution.png`.

![Exécution des six cas](preuves/capture_execution.png)

## Sorties réelles

```text
cas_01_succes.txt        HTTP 200
cas_02_400.txt           HTTP 400
cas_03_401.txt           HTTP 401
cas_04_403.txt           HTTP 403
cas_05_404.txt           HTTP 404
cas_06_409.txt           HTTP 409
```

### Cas 1 — succès

```http
HTTP/1.1 200 OK
Content-Type: application/json; charset=utf-8
Cache-Control: no-store

{"demande":{"id":2,"etudiant_id":1,"entreprise_id":2,"intitule":"Stage analyse de données","date_debut":"2027-02-01","date_fin":"2027-04-30","statut":"VALIDEE","motif_refus":null,"cree_le":"2026-11-11 10:00:00","maj_le":"2026-11-19 15:11:27"}}
```

### Cas 2 — 400

```http
HTTP/1.1 400 Bad Request

{"erreur":{"code":"motif_requis","message":"Le motif de refus est obligatoire"}}
```

### Cas 3 — 401

```http
HTTP/1.1 401 Unauthorized

{"erreur":{"code":"non_authentifie","message":"Authentification requise"}}
```

### Cas 4 — 403

```http
HTTP/1.1 403 Forbidden

{"erreur":{"code":"acces_interdit","message":"Accès interdit"}}
```

### Cas 5 — 404

```http
HTTP/1.1 404 Not Found

{"erreur":{"code":"demande_introuvable","message":"Demande introuvable"}}
```

### Cas 6 — 409

```http
HTTP/1.1 409 Conflict

{"erreur":{"code":"transition_interdite","message":"Transition VALIDEE → VALIDEE interdite"}}
```

