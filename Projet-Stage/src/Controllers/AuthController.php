<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Support\ApiException;
use App\Support\Http;

final class AuthController
{
    public function __construct(private readonly AuthService $auth)
    {
    }

    public function connexion(): never
    {
        $corps = Http::corpsJson();

        $email = trim((string) ($corps['email'] ?? ''));
        $motDePasse = (string) ($corps['mot_de_passe'] ?? '');

        if ($email === '' || $motDePasse === '') {
            throw new ApiException(400, 'donnees_invalides', 'Email et mot de passe requis');
        }

        $utilisateur = $this->auth->connecter($email, $motDePasse);

        Http::json(['utilisateur' => $utilisateur]);
    }
}
