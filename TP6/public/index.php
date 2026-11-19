<?php

declare(strict_types=1);

use App\Config\Database;
use App\Controllers\AuthController;
use App\Controllers\DemandeController;
use App\Repositories\DemandeRepository;
use App\Repositories\EntrepriseRepository;
use App\Repositories\HistoriqueRepository;
use App\Repositories\UtilisateurRepository;
use App\Services\AuthService;
use App\Services\DemandeService;
use App\Support\ApiException;
use App\Support\Http;

require __DIR__ . '/../config/Database.php';
require __DIR__ . '/../src/Support/ApiException.php';
require __DIR__ . '/../src/Support/Http.php';
require __DIR__ . '/../src/Repositories/UtilisateurRepository.php';
require __DIR__ . '/../src/Repositories/EntrepriseRepository.php';
require __DIR__ . '/../src/Repositories/DemandeRepository.php';
require __DIR__ . '/../src/Repositories/HistoriqueRepository.php';
require __DIR__ . '/../src/Services/AuthService.php';
require __DIR__ . '/../src/Services/DemandeService.php';
require __DIR__ . '/../src/Controllers/AuthController.php';
require __DIR__ . '/../src/Controllers/DemandeController.php';

/** @var array{db: array{host:string,port:int,name:string,user:string,password:string}} $config */
$config = require __DIR__ . '/../config/config.php';

session_name('stages_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

try {
    $pdo = Database::connect($config['db']);

    $auth = new AuthService(new UtilisateurRepository($pdo));
    $demandes = new DemandeService(
        new DemandeRepository($pdo),
        new EntrepriseRepository($pdo),
        new HistoriqueRepository($pdo),
        $pdo
    );

    $authController = new AuthController($auth);
    $demandeController = new DemandeController($demandes);

    $methode = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $chemin = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    if ($methode === 'POST' && $chemin === '/api/connexion') {
        $authController->connexion();
    }

    if ($methode === 'GET' && $chemin === '/api/demandes') {
        $demandeController->index();
    }

    if ($methode === 'POST' && $chemin === '/api/demandes') {
        $demandeController->creer();
    }

    if ($methode === 'PATCH' && preg_match('#^/api/demandes/(\d+)/statut$#', $chemin, $correspondances) === 1) {
        $demandeController->changerStatut((int) $correspondances[1]);
    }

    throw new ApiException(404, 'route_inconnue', 'Route introuvable');
} catch (ApiException $e) {
    Http::json([
        'erreur' => [
            'code' => $e->codeErreur(),
            'message' => $e->getMessage(),
        ],
    ], $e->statut());
} catch (Throwable $e) {
    error_log('[API] ' . $e->getMessage());

    Http::json([
        'erreur' => [
            'code' => 'erreur_interne',
            'message' => 'Erreur interne',
        ],
    ], 500);
}
