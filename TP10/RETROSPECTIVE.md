# TP10 — Rétrospective

## Point positif
La séparation nette entre l'API (TP6) et les pages (TP7) a rendu les tests (TP8) et
le déploiement (TP9) simples : on a pu rejouer le scénario de bout en bout et
vérifier la restauration sans toucher au code.

## Difficulté
La gestion des sessions entre les pages et l'API a demandé de servir les deux par la
même origine ; l'ouverture des pages en `file://` a d'abord fait croire à un serveur
injoignable. La documentation de lancement a dû être complétée (`.env`, routeur).

## Changement pour la version 2
- Ajouter un jeton CSRF dédié et une limitation des tentatives de connexion.
- Exposer les noms d'entreprises dans l'API (éviter le codage en dur côté client).
- Ajouter la pagination et un filtre sur la liste des demandes.
- Mettre en place des notifications (email) lors du changement de statut.
