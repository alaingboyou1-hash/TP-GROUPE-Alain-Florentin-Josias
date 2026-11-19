-- TP6 — schéma de la base
-- Quatre tables : utilisateur, entreprise, demande_stage, historique_statut.

CREATE TABLE utilisateur (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    role ENUM('ETUDIANT', 'RESPONSABLE', 'SECRETARIAT') NOT NULL,
    nom VARCHAR(80) NOT NULL,
    prenom VARCHAR(80) NOT NULL,
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE entreprise (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(120) NOT NULL,
    ville VARCHAR(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE demande_stage (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    etudiant_id INT UNSIGNED NOT NULL,
    entreprise_id INT UNSIGNED NOT NULL,
    intitule VARCHAR(160) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    statut ENUM('BROUILLON', 'SOUMISE', 'VALIDEE', 'REFUSEE') NOT NULL DEFAULT 'BROUILLON',
    motif_refus VARCHAR(255) NULL,
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    maj_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_demande_etudiant FOREIGN KEY (etudiant_id) REFERENCES utilisateur (id),
    CONSTRAINT fk_demande_entreprise FOREIGN KEY (entreprise_id) REFERENCES entreprise (id),
    CONSTRAINT chk_demande_dates CHECK (date_fin >= date_debut),
    CONSTRAINT chk_demande_motif CHECK (statut <> 'REFUSEE' OR (motif_refus IS NOT NULL AND motif_refus <> ''))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE historique_statut (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    demande_id INT UNSIGNED NOT NULL,
    ancien_statut ENUM('BROUILLON', 'SOUMISE', 'VALIDEE', 'REFUSEE') NOT NULL,
    nouveau_statut ENUM('BROUILLON', 'SOUMISE', 'VALIDEE', 'REFUSEE') NOT NULL,
    acteur_id INT UNSIGNED NOT NULL,
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historique_demande FOREIGN KEY (demande_id) REFERENCES demande_stage (id) ON DELETE CASCADE,
    CONSTRAINT fk_historique_acteur FOREIGN KEY (acteur_id) REFERENCES utilisateur (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
