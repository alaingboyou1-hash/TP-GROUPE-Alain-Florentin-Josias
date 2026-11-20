<?php
$chemin = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$fichier = __DIR__ . $chemin;

if ($chemin !== '/' && is_file($fichier) && $fichier !== __FILE__) {
    return false;                       // page, CSS ou JS : servi tel quel
}

if ($chemin === '/') {
    header('Location: /connexion.html');
    exit;
}

require __DIR__ . '/index.php';         // tout le reste part vers l'API
