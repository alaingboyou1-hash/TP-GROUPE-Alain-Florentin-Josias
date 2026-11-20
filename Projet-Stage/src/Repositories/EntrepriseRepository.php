<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class EntrepriseRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function existe(int $id): bool
    {
        $requete = $this->pdo->prepare('SELECT 1 FROM entreprise WHERE id = :id');
        $requete->execute([':id' => $id]);

        return $requete->fetchColumn() !== false;
    }
}
