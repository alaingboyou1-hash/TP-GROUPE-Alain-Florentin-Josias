# Scénario « Valider une demande »

**Consigne 3 du TP3 :** décrire les étapes et les codes d'échec.

## Préconditions

- L'acteur est authentifié et possède le rôle `RESPONSABLE`.
- La demande existe et son statut est `SOUMISE`.
- Le corps de la requête contient `{"statut": "VALIDEE"}`.

## Étapes

| # | Acteur / composant | Action | Échec possible | Code |
|---|---|---|---|---|
| 1 | Responsable | `PATCH /api/demandes/{id}/statut` avec `{"statut":"VALIDEE"}` | — | — |
| 2 | Contrôleur | Vérifier la session | Session absente ou expirée | **401** |
| 3 | Contrôleur | Vérifier le jeton CSRF | Jeton absent ou invalide | **403** |
| 4 | Contrôleur | Lire et valider le corps JSON | JSON invalide ou statut inconnu | **400** |
| 5 | Service | Charger la demande par son identifiant | Demande introuvable | **404** |
| 6 | Service | Vérifier le rôle de l'acteur | Rôle insuffisant | **403** |
| 7 | Service | Vérifier la transition `SOUMISE → VALIDEE` | Statut incompatible | **409** |
| 8 | Service + dépôt | Mettre à jour le statut et tracer l'événement dans une transaction | Erreur d'écriture (rollback) | **500** |
| 9 | Contrôleur | Renvoyer la demande à jour | — | **200** |

## Codes d'échec

| Situation | Code HTTP | Réponse |
|---|---|---|
| Demande introuvable | **404** | `{"erreur":"Demande introuvable"}` |
| Rôle insuffisant | **403** | `{"erreur":"Accès interdit"}` |
| Statut incompatible (déjà validée, refusée ou brouillon) | **409** | `{"erreur":"Transition interdite"}` |
| Corps JSON invalide ou statut inconnu | **400** | `{"erreur":"Requête invalide"}` |
| Session absente ou expirée | **401** | `{"erreur":"Authentification requise"}` |
| Jeton CSRF absent ou invalide | **403** | `{"erreur":"Jeton CSRF invalide"}` |
| Erreur technique pendant l'écriture | **500** | `{"erreur":"Erreur interne"}` |

## Réponses attendues par le cours

| Cas | Code |
|---|---|
| Demande introuvable | **404** |
| Rôle insuffisant | **403** |
| Statut incompatible | **409** |

## Règle de conception

Le contrôleur ne décide pas : il vérifie la session, le jeton et la forme de la requête, puis
délègue au service. Le service recharge la demande en base et contrôle lui-même le rôle et la
transition ; il ne fait pas confiance au statut envoyé par le client.
