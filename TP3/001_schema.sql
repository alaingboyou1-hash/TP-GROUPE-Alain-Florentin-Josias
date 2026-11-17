-- =====================================================================
-- TP3 — 001_schema.sql
-- Modèle : Étudiant, Entreprise, Encadreur, DemandeStage, Soutenance
-- SGBD   : MySQL 8 / MariaDB 10.4+
-- Usage  : à exécuter dans une base vide (aucune table au préalable)
-- Données : fictives uniquement
-- =====================================================================

SET NAMES utf8mb4;

-- 1. Étudiant : une ligne par étudiant identifié par son email.
CREATE TABLE etudiant (
    id      INT          NOT NULL AUTO_INCREMENT,
    nom     VARCHAR(60)  NOT NULL,
    prenom  VARCHAR(60)  NOT NULL,
    email   VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT uq_etudiant_email UNIQUE (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 2. Entreprise : une ligne par organisme d'accueil.
CREATE TABLE entreprise (
    id    INT         NOT NULL AUTO_INCREMENT,
    nom   VARCHAR(80) NOT NULL,
    ville VARCHAR(60) NOT NULL,
    PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 3. Encadreur : une ligne par encadreur pédagogique.
CREATE TABLE encadreur (
    id      INT          NOT NULL AUTO_INCREMENT,
    nom     VARCHAR(60)  NOT NULL,
    prenom  VARCHAR(60)  NOT NULL,
    email   VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT uq_encadreur_email UNIQUE (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 4. DemandeStage : association Étudiant — Entreprise, avec un encadreur
--    facultatif et un statut contrôlé.
CREATE TABLE demande_stage (
    id            INT          NOT NULL AUTO_INCREMENT,
    etudiant_id   INT          NOT NULL,
    entreprise_id INT          NOT NULL,
    encadreur_id  INT          NULL,
    statut        VARCHAR(10)  NOT NULL DEFAULT 'BROUILLON',
    motif_refus   VARCHAR(255) NULL,
    cree_le       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_demande_etudiant
        FOREIGN KEY (etudiant_id) REFERENCES etudiant (id),
    CONSTRAINT fk_demande_entreprise
        FOREIGN KEY (entreprise_id) REFERENCES entreprise (id),
    CONSTRAINT fk_demande_encadreur
        FOREIGN KEY (encadreur_id) REFERENCES encadreur (id),
    CONSTRAINT ck_demande_statut
        CHECK (statut IN ('BROUILLON', 'SOUMISE', 'VALIDEE', 'REFUSEE'))
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 5. Soutenance : au plus une par demande (UNIQUE sur demande_id),
--    et un seul créneau par salle (UNIQUE sur salle + date_heure).
CREATE TABLE soutenance (
    id         INT         NOT NULL AUTO_INCREMENT,
    demande_id INT         NOT NULL,
    date_heure DATETIME    NOT NULL,
    salle      VARCHAR(20) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT uq_soutenance_demande UNIQUE (demande_id),
    CONSTRAINT uq_soutenance_creneau UNIQUE (salle, date_heure),
    CONSTRAINT fk_soutenance_demande
        FOREIGN KEY (demande_id) REFERENCES demande_stage (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
