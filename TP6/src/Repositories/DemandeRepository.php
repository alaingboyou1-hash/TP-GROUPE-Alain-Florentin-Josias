<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

/**
 * Tout le SQL de la demande de stage.
 */
final class DemandeRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $requete = $this->pdo->prepare(
            'SELECT id, etudiant_id, entreprise_id, intitule, date_debut, date_fin,
                    statut, motif_refus, cree_le, maj_le
             FROM demande_stage
             WHERE id = :id'
        );
        $requete->execute([':id' => $id]);

        return $requete->fetch() ?: null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findByStudent(int $etudiantId): array
    {
        $requete = $this->pdo->prepare(
            'SELECT id, etudiant_id, entreprise_id, intitule, date_debut, date_fin,
                    statut, motif_refus, cree_le
             FROM demande_stage
             WHERE etudiant_id = :etudiant_id
             ORDER BY cree_le DESC, id DESC'
        );
        $requete->execute([':etudiant_id' => $etudiantId]);

        return $requete->fetchAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findAll(): array
    {
        $requete = $this->pdo->query(
            'SELECT id, etudiant_id, entreprise_id, intitule, date_debut, date_fin,
                    statut, motif_refus, cree_le
             FROM demande_stage
             ORDER BY cree_le DESC, id DESC'
        );

        return $requete->fetchAll();
    }

    /**
     * @param array{etudiant_id:int,entreprise_id:int,intitule:string,date_debut:string,date_fin:string} $donnees
     */
    public function create(array $donnees): int
    {
        $requete = $this->pdo->prepare(
            'INSERT INTO demande_stage
                (etudiant_id, entreprise_id, intitule, date_debut, date_fin, statut)
             VALUES
                (:etudiant_id, :entreprise_id, :intitule, :date_debut, :date_fin, :statut)'
        );
        $requete->execute([
            ':etudiant_id' => $donnees['etudiant_id'],
            ':entreprise_id' => $donnees['entreprise_id'],
            ':intitule' => $donnees['intitule'],
            ':date_debut' => $donnees['date_debut'],
            ':date_fin' => $donnees['date_fin'],
            ':statut' => 'BROUILLON',
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateStatus(int $id, string $statut, ?string $motifRefus): void
    {
        $requete = $this->pdo->prepare(
            'UPDATE demande_stage
             SET statut = :statut, motif_refus = :motif_refus
             WHERE id = :id'
        );
        $requete->execute([
            ':statut' => $statut,
            ':motif_refus' => $motifRefus,
            ':id' => $id,
        ]);
    }
}
