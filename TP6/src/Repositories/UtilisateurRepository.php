<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class UtilisateurRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $requete = $this->pdo->prepare(
            'SELECT id, email, mot_de_passe_hash, role, nom, prenom
             FROM utilisateur
             WHERE email = :email'
        );
        $requete->execute([':email' => $email]);

        return $requete->fetch() ?: null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $requete = $this->pdo->prepare(
            'SELECT id, email, role, nom, prenom
             FROM utilisateur
             WHERE id = :id'
        );
        $requete->execute([':id' => $id]);

        return $requete->fetch() ?: null;
    }
}
