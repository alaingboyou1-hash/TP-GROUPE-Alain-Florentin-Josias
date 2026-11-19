<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\DemandeRepository;
use App\Repositories\EntrepriseRepository;
use App\Repositories\HistoriqueRepository;
use App\Support\ApiException;
use DateTimeImmutable;
use PDO;
use Throwable;

/**
 * Règles métier des demandes : validation, transitions, transaction.
 */
final class DemandeService
{
    private const TRANSITIONS = [
        'BROUILLON' => ['SOUMISE'],
        'SOUMISE' => ['VALIDEE', 'REFUSEE'],
        'REFUSEE' => ['BROUILLON'],
        'VALIDEE' => [],
    ];

    private const STATUTS = ['BROUILLON', 'SOUMISE', 'VALIDEE', 'REFUSEE'];

    public function __construct(
        private readonly DemandeRepository $demandes,
        private readonly EntrepriseRepository $entreprises,
        private readonly HistoriqueRepository $historiques,
        private readonly PDO $pdo
    ) {
    }

    public static function peutPasser(string $de, string $vers): bool
    {
        return in_array($vers, self::TRANSITIONS[$de] ?? [], true);
    }

    /**
     * @param array{id:int,email:string,role:string,nom:string,prenom:string} $utilisateur
     * @return list<array<string, mixed>>
     */
    public function lister(array $utilisateur): array
    {
        if ($utilisateur['role'] === 'ETUDIANT') {
            return $this->demandes->findByStudent($utilisateur['id']);
        }

        return $this->demandes->findAll();
    }

    /**
     * @param array<string, mixed> $corps
     * @return array<string, mixed>
     */
    public function creer(array $corps, int $etudiantId): array
    {
        $entrepriseId = filter_var($corps['entreprise_id'] ?? null, FILTER_VALIDATE_INT);

        if ($entrepriseId === false || $entrepriseId < 1) {
            throw new ApiException(400, 'donnees_invalides', 'entreprise_id invalide');
        }

        if (!$this->entreprises->existe((int) $entrepriseId)) {
            throw new ApiException(400, 'donnees_invalides', 'Entreprise inconnue');
        }

        $intitule = trim((string) ($corps['intitule'] ?? ''));

        if (mb_strlen($intitule) < 3 || mb_strlen($intitule) > 160) {
            throw new ApiException(400, 'donnees_invalides', 'Intitulé invalide (3 à 160 caractères)');
        }

        $dateDebut = $this->dateValide($corps['date_debut'] ?? null);
        $dateFin = $this->dateValide($corps['date_fin'] ?? null);

        if ($dateDebut === null || $dateFin === null) {
            throw new ApiException(400, 'donnees_invalides', 'Dates invalides (format AAAA-MM-JJ)');
        }

        if ($dateFin < $dateDebut) {
            throw new ApiException(400, 'donnees_invalides', 'La date de fin précède la date de début');
        }

        $id = $this->demandes->create([
            'etudiant_id' => $etudiantId,
            'entreprise_id' => (int) $entrepriseId,
            'intitule' => $intitule,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
        ]);

        return $this->demandes->findById($id)
            ?? throw new ApiException(500, 'erreur_interne', 'Erreur interne');
    }

    /**
     * Valide le changement de statut dans une transaction : statut puis historique.
     *
     * @param array<string, mixed> $corps
     * @param array{id:int,email:string,role:string,nom:string,prenom:string} $acteur
     * @return array<string, mixed>
     */
    public function changerStatut(int $demandeId, array $corps, array $acteur): array
    {
        $statut = (string) ($corps['statut'] ?? '');

        if (!in_array($statut, self::STATUTS, true)) {
            throw new ApiException(400, 'statut_invalide', 'Statut inconnu');
        }

        $motif = null;

        if ($statut === 'REFUSEE') {
            $motif = trim((string) ($corps['motif'] ?? ''));

            if ($motif === '') {
                throw new ApiException(400, 'motif_requis', 'Le motif de refus est obligatoire');
            }

            if (mb_strlen($motif) > 255) {
                throw new ApiException(400, 'motif_invalide', 'Le motif de refus est trop long');
            }
        }

        $this->pdo->beginTransaction();

        try {
            $demande = $this->demandes->findById($demandeId);

            if ($demande === null) {
                throw new ApiException(404, 'demande_introuvable', 'Demande introuvable');
            }

            $ancienStatut = (string) $demande['statut'];

            if (!self::peutPasser($ancienStatut, $statut)) {
                throw new ApiException(
                    409,
                    'transition_interdite',
                    'Transition ' . $ancienStatut . ' → ' . $statut . ' interdite'
                );
            }

            $this->demandes->updateStatus($demandeId, $statut, $motif);
            $this->historiques->ajouter($demandeId, $ancienStatut, $statut, $acteur['id']);

            $demandeAJour = $this->demandes->findById($demandeId);

            $this->pdo->commit();

            return $demandeAJour
                ?? throw new ApiException(500, 'erreur_interne', 'Erreur interne');
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            if ($e instanceof ApiException) {
                throw $e;
            }

            error_log('[DEMANDE] ' . $e->getMessage());

            throw new ApiException(500, 'erreur_interne', 'Erreur interne');
        }
    }

    private function dateValide(mixed $valeur): ?string
    {
        if (!is_string($valeur) || $valeur === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $valeur);

        if ($date === false || $date->format('Y-m-d') !== $valeur) {
            return null;
        }

        return $valeur;
    }
}
