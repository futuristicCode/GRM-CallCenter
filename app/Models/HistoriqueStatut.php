<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueStatut extends Model
{
    use HasFactory;

    protected $table = 'historique_statuts';

    protected $fillable = [
        'reclamation_id',
        'ancien_statut',
        'nouveau_statut',
        'utilisateur_id',
        'commentaire',
        'date_changement',
    ];

    protected function casts(): array
    {
        return [
            'date_changement' => 'datetime',
        ];
    }

    public function reclamation(): BelongsTo
    {
        return $this->belongsTo(Reclamation::class);
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}
