<?php

declare(strict_types=1);

namespace App\Controllers;

use PDO;
use Throwable;

/**
 * Route de santé : répond 200 si la base est joignable, 500 sinon.
 */
final class SanteController
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function index(): never
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');

        try {
            $this->pdo->query('SELECT 1');
        } catch (Throwable $e) {
            error_log('[SANTE] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['statut' => 'erreur', 'base' => 'indisponible']);
            exit;
        }

        http_response_code(200);
        echo json_encode(['statut' => 'ok', 'base' => 'ok']);
        exit;
    }
}
