# TP9 — README d'installation et de maintenance

## Prérequis
- PHP 8.x, MariaDB/MySQL, Git, navigateur.
- Données fictives uniquement.

## Installation sur un second poste (reproductible)

1. Cloner le dépôt :
   ```bash
   git clone <adresse-du-depot> projet-stages
   cd projet-stages
   ```
2. Configurer l'environnement :
   ```bash
   cp .env.example .env
   # puis éditer .env (hôte, port, base, utilisateur, mot de passe)
   ```
3. Créer la base et l'utilisateur SQL dédié (jamais root) :
   ```sql
   CREATE DATABASE tp6_stages CHARACTER SET utf8mb4;
   CREATE USER 'tp6_app'@'127.0.0.1' IDENTIFIED BY 'tp6_mot_de_passe';
   GRANT SELECT, INSERT, UPDATE, DELETE ON tp6_stages.* TO 'tp6_app'@'127.0.0.1';
   FLUSH PRIVILEGES;
   ```
4. Importer les scripts SQL :
   ```bash
   mysql -h 127.0.0.1 -u tp6_app -p tp6_stages < config/schema.sql
   mysql -h 127.0.0.1 -u tp6_app -p tp6_stages < config/seed.sql
   ```
5. Copier les pages Web du TP7 dans `public/` et créer le routeur :
   ```bash
   cp -r TP7/pages/. public/
   cat > public/routeur.php <<'PHP'
   <?php
   $chemin = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
   $fichier = __DIR__ . $chemin;
   if ($chemin !== '/' && is_file($fichier) && $fichier !== __FILE__) { return false; }
   if ($chemin === '/') { header('Location: /connexion.html'); exit; }
   require __DIR__ . '/index.php';
   PHP
   ```
6. Démarrer le serveur :
   ```bash
   php -S localhost:8000 -t public public/routeur.php
   ```
7. Vérifier la page de santé :
   ```bash
   curl http://localhost:8000/api/sante   # {"statut":"ok","base":"joignable"}, HTTP 200
   ```
   Puis ouvrir <http://localhost:8000/>.

## Configuration et secrets
- `.env` est local et ignoré par Git ; seul `.env.example` est versionné.
- L'application se connecte avec un utilisateur SQL dédié aux droits limités
  (SELECT, INSERT, UPDATE, DELETE), jamais avec root (moindre privilège).

## Sauvegarde et restauration
- Export :
  ```bash
  mysqldump -u tp6_app -p tp6_stages > sauvegarde.sql
  ```
- Restauration (sur une base de test) :
  ```bash
  mysql -u tp6_app -p tp6_stages_test < sauvegarde.sql
  ```
- Vérification : comparer `SELECT COUNT(*) FROM demande_stage;` avant et après.
  Une sauvegarde n'existe que si elle a été restaurée avec succès.

Preuve : `preuves/03_sauvegarde_restoration.txt`.

## Page de santé
`GET /api/sante` répond `200` si la base est joignable, `500` sinon.
Preuve : `preuves/01_installation_second_poste.txt`.

## Comptes de test
| Rôle | Adresse | Mot de passe |
|---|---|---|
| Étudiant | `etudiant@exemple.test` | `etudiant123` |
| Responsable | `responsable@exemple.test` | `responsable123` |
| Secrétariat | `secretariat@exemple.test` | `secretariat123` |

## Décisions (ADR)
- PHP + PDO : simplicité de déploiement, requêtes préparées.
- Compte SQL dédié : moindre privilège.
- Transaction sur le changement de statut : aucun état partiel.

## Dette technique connue
- Pas de pagination sur la liste des demandes.
- Pas de jeton CSRF dédié (protection via `SameSite=Lax`).
- Pas de route de déconnexion.
- Pas de limitation des tentatives de connexion.

## Incident
Voir `TICKET_INCIDENT.md` (double-clic sur « Envoyer » crée deux demandes).
