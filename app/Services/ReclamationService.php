<?php

namespace App\Services;

use App\Models\Reclamation;
use App\Models\HistoriqueStatut;
use App\Models\Client;
use App\Models\Notification;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class ReclamationService
{
    private const VALID_TRANSITIONS = [
        'en_attente' => ['en_cours'],
        'en_cours' => ['resolu', 'rejete', 'attente_client'],
        'attente_client' => ['en_cours'],
        'resolu' => ['archive'],
        'rejete' => ['archive'],
    ];

    private const STATUT_LABELS = [
        'en_attente' => 'En attente',
        'en_cours' => 'En cours',
        'attente_client' => 'En attente client',
        'resolu' => 'Résolu',
        'rejete' => 'Rejeté',
        'archive' => 'Archivé',
    ];

    public function creerReclamation(array $donnees, ?int $utilisateurId = null): Reclamation
    {
        return DB::transaction(function () use ($donnees, $utilisateurId) {
            $client = Client::firstOrCreate(
                ['email' => $donnees['email']],
                [
                    'nom' => $donnees['nom'],
                    'prenom' => $donnees['prenom'] ?? '',
                    'telephone' => $donnees['telephone'] ?? null,
                    'adresse' => $donnees['adresse'] ?? null,
                    'type' => $donnees['type_client'] ?? 'particulier',
                ]
            );

            $reclamation = Reclamation::create([
                'reference' => Reclamation::genererReference(),
                'client_id' => $client->id,
                'type_id' => $donnees['type_id'],
                'sous_type_id' => $donnees['sous_type_id'] ?? null,
                'sujet' => $donnees['sujet'],
                'description' => $donnees['description'],
                'priorite' => $donnees['priorite'] ?? 'moyenne',
                'reference_externe' => $donnees['reference_externe'] ?? null,
                'statut' => 'en_attente',
                'assigne_a' => null,
                'date_creation' => now(),
            ]);

            HistoriqueStatut::create([
                'reclamation_id' => $reclamation->id,
                'ancien_statut' => null,
                'nouveau_statut' => 'en_attente',
                'utilisateur_id' => $utilisateurId,
                'commentaire' => __('Réclamation créée'),
                'date_changement' => now(),
            ]);

            $this->logAudit($utilisateurId, 'creation', 'Reclamation', $reclamation->id, null, $reclamation->toArray());

            $this->notifierAdminsEtGestionnaires(
                __('Nouvelle réclamation'),
                __('La réclamation') . " {$reclamation->reference} " . __('a été créée') . " : {$reclamation->sujet}",
                'reclamation',
                $reclamation->id
            );

            return $reclamation;
        });
    }

    public function changerStatut(Reclamation $reclamation, string $nouveauStatut, int $utilisateurId, ?string $commentaire = null): bool
    {
        $ancienStatut = $reclamation->statut;

        if (!$this->transitionValide($ancienStatut, $nouveauStatut)) {
            return false;
        }

        return DB::transaction(function () use ($reclamation, $nouveauStatut, $utilisateurId, $ancienStatut, $commentaire) {
            $anciennesValeurs = $reclamation->toArray();

            $updateData = [
                'statut' => $nouveauStatut,
                'date_derniere_modification' => now(),
            ];

            if (in_array($nouveauStatut, ['resolu', 'rejete', 'archive'])) {
                $updateData['date_cloture'] = now();
            }

            $reclamation->update($updateData);

            HistoriqueStatut::create([
                'reclamation_id' => $reclamation->id,
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => $nouveauStatut,
                'utilisateur_id' => $utilisateurId,
                'commentaire' => $commentaire,
                'date_changement' => now(),
            ]);

            $this->logAudit($utilisateurId, 'changement_statut', 'Reclamation', $reclamation->id, $anciennesValeurs, $reclamation->toArray());

            $labelAncien = __(self::STATUT_LABELS[$ancienStatut] ?? $ancienStatut);
            $labelNouveau = __(self::STATUT_LABELS[$nouveauStatut] ?? $nouveauStatut);

            if ($reclamation->assigne_a && $reclamation->assigne_a !== $utilisateurId) {
                $this->creerNotification(
                    $reclamation->assigne_a,
                    __('Changement de statut'),
                    __('La réclamation') . " {$reclamation->reference} " . __('est passée de') . " « {$labelAncien} » " . __('à') . " « {$labelNouveau} ».",
                    'reclamation',
                    $reclamation->id
                );
            }

            if ($nouveauStatut === 'attente_client' && $reclamation->client) {
                $this->creerNotification(
                    $utilisateurId,
                    __('Information requise'),
                    __('La réclamation') . " {$reclamation->reference} " . __('nécessite des informations complémentaires.'),
                    'reclamation',
                    $reclamation->id
                );
            }

            return true;
        });
    }

    public function assigner(Reclamation $reclamation, int $utilisateurId, int $assigneA): void
    {
        $anciennesValeurs = $reclamation->toArray();

        $reclamation->update(['assigne_a' => $assigneA, 'date_derniere_modification' => now()]);

        HistoriqueStatut::create([
            'reclamation_id' => $reclamation->id,
            'ancien_statut' => $reclamation->statut,
            'nouveau_statut' => $reclamation->statut,
            'utilisateur_id' => $utilisateurId,
            'commentaire' => __('Réclamation assignée'),
            'date_changement' => now(),
        ]);

        $this->logAudit($utilisateurId, 'assignation', 'Reclamation', $reclamation->id, $anciennesValeurs, $reclamation->toArray());

        if ($assigneA !== $utilisateurId) {
            $this->creerNotification(
                $assigneA,
                __('Nouvelle assignation'),
                __('Vous avez été assigné à la réclamation') . " {$reclamation->reference} : {$reclamation->sujet}.",
                'reclamation',
                $reclamation->id
            );
        }
    }

    public function transitionValide(string $de, string $vers): bool
    {
        return in_array($vers, self::VALID_TRANSITIONS[$de] ?? []);
    }

    public function getTransitionsValides(string $statutActuel): array
    {
        return self::VALID_TRANSITIONS[$statutActuel] ?? [];
    }

    public function creerNotification(int $utilisateurId, string $sujet, string $contenu, ?string $type = null, ?string $modeleId = null): void
    {
        Notification::create([
            'utilisateur_id' => $utilisateurId,
            'type_notification' => $type ?? 'systeme',
            'sujet' => $sujet,
            'contenu' => $contenu,
            'statut_envoi' => 'envoye',
            'lu' => false,
        ]);
    }

    private function notifierAdminsEtGestionnaires(string $sujet, string $contenu, ?string $type = null, ?string $modeleId = null): void
    {
        $destinataires = User::whereIn('role', ['admin', 'gestionnaire'])
            ->where('is_active', true)
            ->pluck('id');

        foreach ($destinataires as $userId) {
            $this->creerNotification($userId, $sujet, $contenu, $type, $modeleId);
        }
    }

    private function logAudit(?int $userId, string $action, string $modele, $modeleId, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'utilisateur_id' => $userId,
            'action' => $action,
            'modele' => $modele,
            'modele_id' => $modeleId,
            'ancien_valeurs' => $old,
            'nouveau_valeurs' => $new,
            'adresse_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
