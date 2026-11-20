<?php

declare(strict_types=1);

/**
 * Configuration de l'application.
 * Les valeurs sensibles viennent de .env, jamais du code versionné.
 */

$racine = dirname(__DIR__);
$fichierEnv = $racine . '/.env';
$env = [];

if (is_file($fichierEnv)) {
    $lu = parse_ini_file($fichierEnv, false, INI_SCANNER_RAW);
    if (is_array($lu)) {
        $env = $lu;
    }
}

return [
    'db' => [
        'host' => $env['DB_HOST'] ?? '127.0.0.1',
        'port' => (int) ($env['DB_PORT'] ?? 3306),
        'name' => $env['DB_NAME'] ?? 'stages',
        'user' => $env['DB_USER'] ?? 'stages_app',
        'password' => $env['DB_PASSWORD'] ?? '',
    ],
];
