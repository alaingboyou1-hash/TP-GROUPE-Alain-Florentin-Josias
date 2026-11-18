# Projet Stages — squelette technique

**TP4 · Projet Informatique 2 · L3 SIL · données fictives uniquement**

Application de gestion des stages et soutenances, en PHP 8 sans framework.

## Prérequis

- PHP 8.1 ou plus, avec l'extension `pdo_mysql`
- MySQL 8 ou MariaDB 10.4
- Git
- Un navigateur ou `curl`

## Arborescence

```text
Projet-Stage/
├── public/                 # point d'entrée unique
│   └── index.php
├── config/                 # configuration et connexion PDO
│   ├── config.php
│   └── Database.php
├── src/
│   ├── Controllers/        # réception des requêtes
│   │   └── SanteController.php
│   ├── Services/           # règles métier (TP6)
│   └── Repositories/       # SQL (TP6)
├── tests/                  # test de connexion
│   └── test_connexion.php
├── .env.example            # modèle de configuration, sans secret
├── .gitignore              # exclut .env
├── CONTRAT_ROUTES.md       # contrat des trois routes
├── ADR-001.md              # décision d'architecture (5 lignes)
└── README.md
```

## Installation

1. Copier le modèle de configuration :

   ```bash
   cp .env.example .env
   ```

2. Créer la base et l'utilisateur, puis adapter `.env` :

   ```sql
   CREATE DATABASE stages CHARACTER SET utf8mb4;
   CREATE USER 'stages_app'@'localhost' IDENTIFIED BY 'changez_moi';
   GRANT ALL ON stages.* TO 'stages_app'@'localhost';
   ```

3. Tester la connexion PDO :

   ```bash
   php tests/test_connexion.php
   ```

   Sortie attendue :

   ```text
   Connexion PDO : OK
   Base : stages_app@127.0.0.1:3306/stages
   Serveur : 10.4.32-MariaDB
   SELECT 1 => 1
   ```

## Lancer l'application

```bash
php -S localhost:8000 -t public public/index.php
```

Route de santé :

```bash
curl -i http://localhost:8000/api/sante
```

Réponse `200` si la base est joignable :

```json
{"statut":"ok","base":"ok"}
```

Réponse `500` si la base est indisponible :

```json
{"statut":"erreur","base":"indisponible"}
```

## Secrets

- `.env` contient les identifiants réels et **n'est jamais versionné**.
- `.env.example` est versionné avec des valeurs fictives.
- Vérification : `git check-ignore -v .env`.

## Documents du TP4

- Contrat des trois routes : [`CONTRAT_ROUTES.md`](CONTRAT_ROUTES.md)
- Décision d'architecture : [`ADR-001.md`](ADR-001.md)
