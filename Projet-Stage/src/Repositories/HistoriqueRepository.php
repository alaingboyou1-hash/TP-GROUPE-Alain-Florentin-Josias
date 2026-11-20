<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

/**
 * Journal des changements de statut.
 */
final class HistoriqueRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function ajouter(int $demandeId, string $ancienStatut, string $nouveauStatut, int $acteurId): void
    {
        $requete = $this->pdo->prepare(
            'INSERT INTO historique_statut
                (demande_id, ancien_statut, nouveau_statut, acteur_id)
             VALUES
                (:demande_id, :ancien_statut, :nouveau_statut, :acteur_id)'
        );
        $requete->execute([
            ':demande_id' => $demandeId,
            ':ancien_statut' => $ancienStatut,
            ':nouveau_statut' => $nouveauStatut,
            ':acteur_id' => $acteurId,
        ]);
    }
}
