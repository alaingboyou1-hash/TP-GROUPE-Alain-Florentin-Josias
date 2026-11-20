<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\DemandeService;
use App\Support\ApiException;
use App\Support\Http;

final class DemandeController
{
    public function __construct(private readonly DemandeService $demandes)
    {
    }

    public function index(): never
    {
        $utilisateur = $this->exigerAuthentification();

        if (!in_array($utilisateur['role'], ['ETUDIANT', 'RESPONSABLE', 'SECRETARIAT'], true)) {
            throw new ApiException(403, 'acces_interdit', 'Accès interdit');
        }

        Http::json(['demandes' => $this->demandes->lister($utilisateur)]);
    }

    public function creer(): never
    {
        $utilisateur = $this->exigerAuthentification();

        if ($utilisateur['role'] !== 'ETUDIANT') {
            throw new ApiException(403, 'acces_interdit', 'Accès interdit');
        }

        $corps = Http::corpsJson();
        $demande = $this->demandes->creer($corps, $utilisateur['id']);

        Http::json(['demande' => $demande], 201);
    }

    public function changerStatut(int $demandeId): never
    {
        $utilisateur = $this->exigerAuthentification();

        if ($utilisateur['role'] !== 'RESPONSABLE') {
            throw new ApiException(403, 'acces_interdit', 'Accès interdit');
        }

        $corps = Http::corpsJson();
        $demande = $this->demandes->changerStatut($demandeId, $corps, $utilisateur);

        Http::json(['demande' => $demande]);
    }

    /**
     * @return array{id:int,email:string,role:string,nom:string,prenom:string}
     */
    private function exigerAuthentification(): array
    {
        $utilisateur = AuthService::courant();

        if ($utilisateur === null) {
            throw new ApiException(401, 'non_authentifie', 'Authentification requise');
        }

        return $utilisateur;
    }
}
