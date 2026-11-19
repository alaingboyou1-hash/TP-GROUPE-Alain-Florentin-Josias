<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

/**
 * Erreur métier portant le code HTTP à renvoyer au client.
 * Le message est destiné au client ; aucun détail technique.
 */
final class ApiException extends RuntimeException
{
    public function __construct(
        private readonly int $statut,
        private readonly string $codeErreur,
        string $message
    ) {
        parent::__construct($message);
    }

    public function statut(): int
    {
        return $this->statut;
    }

    public function codeErreur(): string
    {
        return $this->codeErreur;
    }
}
