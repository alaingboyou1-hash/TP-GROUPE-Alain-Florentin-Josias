<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UtilisateurRepository;
use App\Support\ApiException;

/**
 * Connexion : mot de passe vérifié, session régénérée.
 */
final class AuthService
{
    public function __construct(private readonly UtilisateurRepository $utilisateurs)
    {
    }

    /**
     * @return array{id:int,email:string,role:string,nom:string,prenom:string}
     */
    public function connecter(string $email, string $motDePasse): array
    {
        $utilisateur = $this->utilisateurs->findByEmail($email);

        // Même message pour un compte inconnu et un mot de passe faux.
        if ($utilisateur === null || !password_verify($motDePasse, (string) $utilisateur['mot_de_passe_hash'])) {
            throw new ApiException(401, 'identifiants_invalides', 'Identifiants invalides');
        }

        session_regenerate_id(true);

        $_SESSION['utilisateur'] = [
            'id' => (int) $utilisateur['id'],
            'email' => (string) $utilisateur['email'],
            'role' => (string) $utilisateur['role'],
            'nom' => (string) $utilisateur['nom'],
            'prenom' => (string) $utilisateur['prenom'],
        ];

        return $_SESSION['utilisateur'];
    }

    /**
     * @return array{id:int,email:string,role:string,nom:string,prenom:string}|null
     */
    public static function courant(): ?array
    {
        /** @var array{id:int,email:string,role:string,nom:string,prenom:string}|null $utilisateur */
        $utilisateur = $_SESSION['utilisateur'] ?? null;

        return $utilisateur;
    }
}
