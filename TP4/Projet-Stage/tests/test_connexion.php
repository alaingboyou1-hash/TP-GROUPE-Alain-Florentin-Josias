<?php

declare(strict_types=1);

use App\Config\Database;

require __DIR__ . '/../config/Database.php';

/** @var array{db: array{host:string,port:int,name:string,user:string,password:string}} $config */
$config = require __DIR__ . '/../config/config.php';

try {
    $pdo = Database::connect($config['db']);
    $valeur = $pdo->query('SELECT 1')->fetchColumn();

    echo "Connexion PDO : OK\n";
    echo 'Base : ' . $config['db']['user'] . '@' . $config['db']['host']
        . ':' . $config['db']['port'] . '/' . $config['db']['name'] . "\n";
    echo 'Serveur : ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
    echo 'SELECT 1 => ' . $valeur . "\n";

    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Connexion PDO : ECHEC\n" . $e->getMessage() . "\n");
    exit(1);
}
