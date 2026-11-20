# Projet-Stage — Gestion des demandes de stage

#LES MEMBRES DU GROUPE 
1- GBOYOU Alain
2- CAKPO Josias
3- DOUGLOUI Florentin

Projet autonome , prêt à lancer.

## Lancement

1. Configurer l'environnement :
   ```bash
   cp .env.example .env
   # adapter DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD si besoin
   ```
2. Créer la base et l'utilisateur SQL, puis importer :
   ```bash
   mysql -h 127.0.0.1 -u tp6_app -p tp6_stages < config/schema.sql
   mysql -h 127.0.0.1 -u tp6_app -p tp6_stages < config/seed.sql
   ```
3. Démarrer le serveur :
   ```bash
   php -S localhost:8000 -t public public/routeur.php
   ```
4. Ouvrir <http://localhost:8000/>

## Page de santé
`GET /api/sante` → `{"statut":"ok","base":"joignable"}` (HTTP 200).

## Comptes de test
| Rôle | Adresse | Mot de passe |
|---|---|---|
| Étudiant | `etudiant@exemple.test` | `etudiant123` |
| Responsable | `responsable@exemple.test` | `responsable123` |
| Secrétariat | `secretariat@exemple.test` | `secretariat123` |

⚠️ Toujours ouvrir via `http://localhost:8000/`, jamais en `file://`.
