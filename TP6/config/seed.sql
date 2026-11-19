-- TP6 — jeu d'essai fictif
-- Mots de passe en clair pour la démonstration : etudiant123, responsable123, secretariat123.
-- Seuls les hachages bcrypt sont stockés.

SET NAMES utf8mb4;

INSERT INTO utilisateur (id, email, mot_de_passe_hash, role, nom, prenom) VALUES
(1, 'etudiant@exemple.test', '$2y$12$/KWEKjf0jMFGhuXIFAgHw.BaIOnnnzexHCT6wYvUbil5ZwPKaiwKO', 'ETUDIANT', 'Diallo', 'Awa'),
(2, 'responsable@exemple.test', '$2y$12$D8wtaDYZh9Lfu4Y2Sv7cyuK06czJhL3pj8BHQWz1awyTIslJni/o.', 'RESPONSABLE', 'Traoré', 'Moussa'),
(3, 'secretariat@exemple.test', '$2y$12$OCH8MpVprwfmW44XxPS7C.76MKezCJmKDfsSP6QSI4uml/X4Pxd7W', 'SECRETARIAT', 'Ndiaye', 'Fatou');

INSERT INTO entreprise (id, nom, ville) VALUES
(1, 'Atelier Numérique', 'Dakar'),
(2, 'Data Sahel', 'Thiès');

INSERT INTO demande_stage (id, etudiant_id, entreprise_id, intitule, date_debut, date_fin, statut, motif_refus, cree_le) VALUES
(1, 1, 1, 'Stage développement web', '2027-01-05', '2027-03-27', 'BROUILLON', NULL, '2026-11-10 09:00:00'),
(2, 1, 2, 'Stage analyse de données', '2027-02-01', '2027-04-30', 'SOUMISE', NULL, '2026-11-11 10:00:00'),
(3, 1, 1, 'Stage support informatique', '2027-01-10', '2027-03-10', 'VALIDEE', NULL, '2026-11-12 11:00:00'),
(4, 1, 2, 'Stage administration réseau', '2027-02-15', '2027-05-15', 'REFUSEE', 'Entreprise non conventionnée', '2026-11-13 14:00:00');

INSERT INTO historique_statut (demande_id, ancien_statut, nouveau_statut, acteur_id, cree_le) VALUES
(2, 'BROUILLON', 'SOUMISE', 1, '2026-11-11 10:05:00'),
(3, 'BROUILLON', 'SOUMISE', 1, '2026-11-12 11:05:00'),
(3, 'SOUMISE', 'VALIDEE', 2, '2026-11-12 15:00:00'),
(4, 'BROUILLON', 'SOUMISE', 1, '2026-11-13 14:05:00'),
(4, 'SOUMISE', 'REFUSEE', 2, '2026-11-13 16:00:00');
