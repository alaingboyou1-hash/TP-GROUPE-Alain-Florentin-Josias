<?php

declare(strict_types=1);

use App\Config\Database;
use App\Controllers\SanteController;

require __DIR__ . '/../config/Database.php';
require __DIR__ . '/../src/Controllers/SanteController.php';

/** @var array{db: array{host:string,port:int,name:string,user:string,password:string}} $config */
$config = require __DIR__ . '/../config/config.php';

// Point d'entrée unique : aucune autre page n'est accessible directement.
try {
    $pdo = Database::connect($config['db']);
} catch (Throwable $e) {
    error_log('[BOOT] ' . $e->getMessage());
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(['statut' => 'erreur', 'base' => 'indisponible']);
    exit;
}

$methode = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$chemin = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($methode === 'GET' && $chemin === '/api/sante') {
    (new SanteController($pdo))->index();
}

header('Content-Type: application/json; charset=utf-8');
http_response_code(404);
echo json_encode(['erreur' => 'Route introuvable']);
