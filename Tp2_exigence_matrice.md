TP2 - Exigences et matrice de traçabilité
Groupe : Alain GBOYOU , Florentin , Josias

1. Exigences fonctionnelles (EF)

    EF-01 (Must, Tous) : Le système doit permettre à un utilisateur de se connecter avec un compte.

    EF-02 (Must, Étudiant) : Le système doit permettre à l'étudiant de soumettre une demande de stage.

    EF-03 (Must, Responsable) : Le système doit permettre au responsable de valider ou refuser une demande (avec motif obligatoire en cas de refus).

    EF-04 (Must, Secrétariat) : Le système doit permettre d'affecter un encadreur à une demande validée.

    EF-05 (Must, Secrétariat) : Le système doit permettre de programmer la soutenance (date, heure, salle) pour une demande validée.

    EF-06 (Should, Étudiant) : Le système doit permettre à l'étudiant de consulter l'état de ses demandes.

    EF-07 (Should, Secrétariat) : Le système doit permettre de rechercher un dossier par nom d'étudiant ou par statut.

    EF-08 (Could, Responsable) : Le système doit permettre de consulter l'historique des décisions prises.

2. Exigences non fonctionnelles (ENF)

    ENF-01 (Autorisation) : Toute lecture ou modification d'un dossier est soumise à un contrôle d'autorisation. Si l'utilisateur n'a pas le bon rôle, le serveur renvoie une erreur 403.

    ENF-02 (Validation serveur) : Tous les champs de formulaire sont validés côté serveur (type, longueur, format). Une valeur invalide renvoie une erreur 422 sans être enregistrée en base.

    ENF-03 (Sauvegarde) : La base de données doit pouvoir être sauvegardée et restaurée. Cette procédure doit être testée et documentée par une preuve (capture d'écran ou log).

3. User stories et critères d'acceptation

User Story 1 (Cas nominal - EF-03) :
En tant que responsable, je veux valider une demande de stage afin de lancer la programmation de la soutenance.

    Given une demande au statut SOUMISE et un responsable connecté,

    When le responsable clique sur "Valider",

    Then le statut de la demande devient VALIDEE et une ligne est ajoutée à l'historique.

User Story 2 (Cas d'erreur - EF-03) :
En tant que responsable, je ne dois pas pouvoir refuser une demande qui est déjà validée.

    Given une demande au statut VALIDEE,

    When le responsable tente de la refuser,

    Then le serveur renvoie une erreur 409 (conflit) et le statut de la demande reste inchangé (VALIDEE).

4. Matrice de traçabilité
(Chaque exigence est reliée à un composant, un test et un statut)

    EF-01 | Composant : AuthController::login | Test : T-01 (session créée) | Statut : À faire

    EF-02 | Composant : DemandeController::soumettre | Test : T-02 (statut = SOUMISE) | Statut : À faire

    EF-03 | Composant : DemandeService::valider | Test : T-03 (SOUMISE -> VALIDEE) | Statut : À faire

    EF-04 | Composant : DemandeService::affecter | Test : T-04 (encadreur_id non nul) | Statut : À faire

    EF-05 | Composant : SoutenanceService::programmer | Test : T-05 (créneau unique) | Statut : À faire

    EF-06 | Composant : DemandeController::mesDemandes | Test : T-06 (liste filtrée) | Statut : À faire

    EF-07 | Composant : DemandeRepo::rechercher | Test : T-07 (filtre nom/statut) | Statut : À faire

    EF-08 | Composant : HistoriqueService::lister | Test : T-08 (historique chrono) | Statut : À faire

5. Périmètre minimal (25 heures)
Pour tenir dans les 25 heures, nous ne gardons que les exigences Must (EF-01 à EF-05).
 Les exigences Should (EF-06 et EF-07) seront faites s'il reste du temps. L'exigence Could (EF-08) est reportée à la version
 2. Tout ce qui est exclu du MVP (comme le paiement en ligne) n'est pas développé.
