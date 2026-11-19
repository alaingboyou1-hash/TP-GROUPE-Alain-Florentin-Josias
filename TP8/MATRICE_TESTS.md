# TP8 — Matrice de tests et audit de sécurité

Jeu d'essai : base `tp6_stages` rechargée (`config/seed.sql`), plus un étudiant B
fictif (`etudiant2@exemple.test`, mot de passe `etudiant123`) pour le test
d'autorisation. Serveur `php -S 127.0.0.1:8140`. Toutes les personnes et données sont
fictives.

Chaque ligne donne **l'attendu écrit avant l'exécution** ; la colonne « Observé » est
le résultat réel, la colonne « Preuve » pointe vers `preuves/`.

| # | Test | Exigence | Données | Attendu | Observé | Preuve | Verdict |
|---|---|---|---|---|---|---|---|
| 1 | Connexion valide (étudiant) | Authentification | `etudiant@exemple.test` / `etudiant123` | HTTP 200, utilisateur renvoyé, cookie de session posé | HTTP 200, `{"utilisateur":{"id":1,...}}`, cookie `stages_session` | `preuves/01_connexion_valide.txt` | ✅ |
| 2 | Connexion invalide | Authentification | `etudiant@exemple.test` / `mauvais` | HTTP 401, pas de session | HTTP 401 `identifiants_invalides` | `preuves/02_connexion_invalide.txt` | ✅ |
| 3 | Liste par étudiant | Autorisation lecture | Cookie étudiant A | HTTP 200, uniquement les demandes de A | HTTP 200, 4 demandes, toutes `etudiant_id=1` | `preuves/03_liste_etudiant.txt` | ✅ |
| 4 | Liste par responsable | Autorisation lecture | Cookie responsable | HTTP 200, toutes les demandes | HTTP 200, 4 demandes (étudiants 1) | `preuves/04_liste_responsable.txt` | ✅ |
| 5 | Création valide | Création de demande | `entreprise_id=1`, intitulé 3-160 car., dates valides | HTTP 201, statut `BROUILLON` | HTTP 201, id 5, statut `BROUILLON` | `preuves/05_creation_valide.txt` | ✅ |
| 6 | Création par non-étudiant | Autorisation création | Cookie responsable | HTTP 403 | HTTP 403 `acces_interdit` | `preuves/06_creation_non_etudiant.txt` | ✅ |
| 7 | Demande inexistante | Gestion des erreurs | `PATCH /api/demandes/999/statut` | HTTP 404 | HTTP 404 `demande_introuvable` | `preuves/07_demande_inexistante.txt` | ✅ |
| 8 | Refus sans motif | Règle métier | `PATCH .../2/statut {"statut":"REFUSEE"}` | HTTP 400, motif obligatoire | HTTP 400 `motif_requis` | `preuves/08_refus_sans_motif.txt` | ✅ |
| 9 | Validation répétée | Transitions | `PATCH .../3/statut {"statut":"SOUMISE"}` (déjà `VALIDEE`) | HTTP 409, transition interdite | HTTP 409 `transition_interdite` | `preuves/09_validation_repetee.txt` | ✅ |
| 10 | Date invalide (format) | Validation des données | `date_debut=2027-13-40` | HTTP 400 | HTTP 400 `Dates invalides` | `preuves/10_date_invalide.txt` | ✅ |
| 11 | Date inversée | Validation des données | début `2027-08-31`, fin `2027-06-01` | HTTP 400 | HTTP 400 `La date de fin précède la date de début` | `preuves/10_date_invalide.txt` | ✅ |
| 12 | Lecture croisée (B lit A) | Autorisation lecture | Cookie étudiant B | Aucune donnée de A exposée | HTTP 200, 0 demande pour B ; aucune demande de A | `preuves/11_liste_etu2.json` | ✅ |
| 13 | Mutation par étudiant | Autorisation écriture | Cookie étudiant B, `PATCH .../1/statut` | HTTP 403 | HTTP 403 `acces_interdit` | `preuves/11_patch_par_etudiant.txt` | ✅ |
| 14 | Injection SQL (`1 OR 1=1`) | Injection | Identifiant `1 OR 1=1` dans l'URL et dans l'email | Pas d'injection : 404 (route) / 401 (identifiants) | 404 `route_inconnue` puis 401 `identifiants_invalides` | `preuves/12_injection_id.txt` | ✅ |
| 15 | Mots de passe hachés | Stockage des secrets | Lecture directe de la table `utilisateur` | Hachage bcrypt (`$2y$`), longueur 60 | 4 comptes, préfixe `$2y$12$`, longueur 60 | `preuves/13_mots_de_passe.txt` | ✅ |
| 16 | Protection CSRF | Sécurité session | En-têtes de la réponse de connexion | Cookie `HttpOnly` et `SameSite=Lax` | `Set-Cookie: stages_session=...; path=/; HttpOnly; SameSite=Lax` | `preuves/14_csrf_headers.txt` | ✅ |

## Cas limites exécutés (point 2 du TP)

1. **Demande inexistante** → `preuves/07_demande_inexistante.txt` : HTTP 404.
2. **Refus sans motif** → `preuves/08_refus_sans_motif.txt` : HTTP 400.
3. **Validation répétée** → `preuves/09_validation_repetee.txt` : HTTP 409.
4. **Date invalide** → `preuves/10_date_invalide.txt` : HTTP 400 (format puis ordre).

## Autorisations et injection (point 3 du TP)

- Étudiant B (`etudiant2@exemple.test`) appelle `GET /api/demandes` : la liste est filtrée par
  `etudiant_id` côté serveur ; aucune demande de l'étudiant A n'est renvoyée.
  L'API n'expose pas de route unitaire `GET /api/demandes/{id}` ; la confidentialité est
  assurée par le filtrage de la liste (pas de fuite de données).
- Mutation par un non-responsable (`PATCH .../statut`) → HTTP 403.
- Chaîne `1 OR 1=1` : dans l'URL, la route ne correspond pas au motif `\d+` → HTTP 404 ;
  dans l'email, les identifiants sont rejetés → HTTP 401. Les requêtes sont préparées
  (`PDO::prepare`), donc pas d'injection SQL exploitable.

## Mots de passe et CSRF (point 4 du TP)

- Mots de passe : hachage bcrypt (coût 12), longueur 60, stockés dans `mot_de_passe_hash`.
  Aucun mot de passe en clair en base (`preuves/13_mots_de_passe.txt`).
- CSRF : cookie de session `HttpOnly`, `SameSite=Lax`, `path=/`, durée de session.
  Pas de jeton CSRF dédié : la protection repose sur `SameSite=Lax` et sur l'origine
  commune des pages et de l'API. Limite assumée : à renforcer (jeton double-submit) si
  l'API est un jour appelée depuis une autre origine.

## Conclusion

Les 16 cas de la matrice passent. Les quatre cas limites renvoient les bons codes HTTP,
les autorisations sont respectées, l'injection SQL est neutralisée par les requêtes
préparées et le filtrage, et les mots de passe sont hachés.
