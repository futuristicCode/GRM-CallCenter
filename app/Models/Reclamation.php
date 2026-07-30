<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Reclamation extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'reclamations';

    protected $fillable = [
        'reference',
        'client_id',
        'type_id',
        'sous_type_id',
        'sujet',
        'description',
        'priorite',
        'reference_externe',
        'statut',
        'assigne_a',
        'motif_rejet',
        'date_creation',
        'date_derniere_modification',
        'date_cloture',
    ];

    protected function casts(): array
    {
        return [
            'date_creation' => 'datetime',
            'date_derniere_modification' => 'datetime',
            'date_cloture' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeReclamation::class, 'type_id');
    }

    public function sousType(): BelongsTo
    {
        return $this->belongsTo(SousType::class, 'sous_type_id');
    }

    public function assigne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigne_a');
    }

    public function historiqueStatuts(): HasMany
    {
        return $this->hasMany(HistoriqueStatut::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function piecesJointes(): HasMany
    {
        return $this->hasMany(PieceJointe::class);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeResolu($query)
    {
        return $query->where('statut', 'resolu');
    }

    public function scopeRejete($query)
    {
        return $query->where('statut', 'rejete');
    }

    public function scopeArchive($query)
    {
        return $query->where('statut', 'archive');
    }

    public function scopeAttenteClient($query)
    {
        return $query->where('statut', 'attente_client');
    }

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => __('En attente'),
            'en_cours' => __('En cours'),
            'resolu' => __('Résolu'),
            'rejete' => __('Rejeté'),
            'attente_client' => __('Attente client'),
            'archive' => __('Archivé'),
            default => $this->statut,
        };
    }

    public static function genererReference(): string
    {
        $year = date('Y');
        $prefix = "R-{$year}-";

        $lastReclamation = static::where('reference', 'like', $prefix . '%')
            ->orderBy('reference', 'desc')
            ->first();

        if ($lastReclamation) {
            $lastNumber = (int) substr($lastReclamation->reference, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function changerStatut(string $nouveauStatut, int $utilisateurId, ?string $commentaire = null): void
    {
        $ancienStatut = $this->statut;

        $this->update(['statut' => $nouveauStatut]);

        $this->historiqueStatuts()->create([
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'utilisateur_id' => $utilisateurId,
            'commentaire' => $commentaire,
            'date_changement' => now(),
        ]);
    }
}
