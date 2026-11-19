<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Réponses JSON et lecture du corps de requête.
 */
final class Http
{
    /**
     * @param array<string, mixed> $donnees
     */
    public static function json(array $donnees, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');

        echo json_encode($donnees, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }

    /**
     * @return array<string, mixed>
     */
    public static function corpsJson(): array
    {
        $brut = file_get_contents('php://input');

        if ($brut === false || trim($brut) === '') {
            throw new ApiException(400, 'json_invalide', 'Corps JSON requis');
        }

        $donnees = json_decode($brut, true);

        if (!is_array($donnees)) {
            throw new ApiException(400, 'json_invalide', 'Corps JSON invalide');
        }

        return $donnees;
    }
}
