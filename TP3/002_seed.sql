-- =====================================================================
-- TP3 — 002_seed.sql
-- Jeu d'essai fictif : 3 étudiants, 2 entreprises, 4 demandes
-- Compléments nécessaires au modèle : 2 encadreurs, 1 soutenance
-- À exécuter après 001_schema.sql, dans la même base.
-- =====================================================================

SET NAMES utf8mb4;

-- 3 étudiants
INSERT INTO etudiant (id, nom, prenom, email) VALUES
    (1, 'ADJOVI', 'Koffi', 'koffi.adjovi@univ.test'),
    (2, 'BELLO',  'Aicha', 'aicha.bello@univ.test'),
    (3, 'DOSSOU', 'Marc',  'marc.dossou@univ.test');

-- 2 entreprises
INSERT INTO entreprise (id, nom, ville) VALUES
    (1, 'Atelier Numerique', 'Porto-Novo'),
    (2, 'Reseau Plus',       'Cotonou');

-- 2 encadreurs (aucun n'est obligatoire sur une demande)
INSERT INTO encadreur (id, nom, prenom, email) VALUES
    (1, 'HOUNKPE', 'Alice',  'alice.hounkpe@univ.test'),
    (2, 'SOSSOU',  'Bruno',  'bruno.sossou@univ.test');

-- 4 demandes : une par statut du cycle de vie
INSERT INTO demande_stage
    (id, etudiant_id, entreprise_id, encadreur_id, statut, motif_refus)
VALUES
    (1, 1, 1, NULL, 'SOUMISE',   NULL),
    (2, 2, 1, 1,    'VALIDEE',   NULL),
    (3, 3, 2, NULL, 'BROUILLON', NULL),
    (4, 1, 2, NULL, 'REFUSEE',   'Convention incomplete');

-- 1 soutenance : illustre la multiplicité DemandeStage 1 — 0..1 Soutenance
INSERT INTO soutenance (id, demande_id, date_heure, salle) VALUES
    (1, 2, '2026-12-10 09:00:00', 'B12');
