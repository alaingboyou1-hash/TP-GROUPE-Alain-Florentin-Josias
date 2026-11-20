# TP10 — Questions individuelles (préparation à l'oral)

Chaque membre doit pouvoir expliquer sa contribution. Exemples de questions et
réponses attendues (à adapter selon l'auteur réel de chaque partie).

## Règle métier
- « Montrez où le motif de refus est rendu obligatoire et le test qui le vérifie. »
  Réponse : `DemandeService::changerStatut()` lève `ApiException(400, 'motif_requis', ...)`
  si `statut === 'REFUSEE'` et `motif === ''` ; test T-08 (TP8) échoue avant, réussit après.

## Requête
- « Expliquez la requête qui liste les demandes d'un étudiant. »
  Réponse : `DemandeRepository::findByStudent()` filtre `WHERE etudiant_id = :etudiant_id`,
  ordonné par `cree_le DESC, id DESC` ; paramètre lié, pas de concaténation.

## Test
- « Quel test vérifie qu'un étudiant ne voit pas les demandes d'un autre ? »
  Réponse : T-12 (TP8) — cookie étudiant B, `GET /api/demandes` renvoie 0 demande de A.

## Commit
- « Montrez le commit où vous avez ajouté la validation des dates. »
  Réponse : commit modifiant `DemandeService::creer()` et `dateValide()` ; test T-10
  (TP8) échoue avant et réussit après.

## Justifications techniques
- PDO : simplicité de déploiement + requêtes préparées (pas d'injection).
- Transaction de validation : évite un état partiel (statut mis à jour sans historique).
- Contrôle d'accès côté serveur à chaque requête : le client n'est jamais fait confiance.
