<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PieceJointe extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'pieces_jointes';

    protected $fillable = [
        'reclamation_id',
        'nom_fichier',
        'chemin_stockage',
        'taille_octets',
        'type_mime',
    ];

    protected function casts(): array
    {
        return [
            'taille_octets' => 'integer',
        ];
    }

    public function reclamation(): BelongsTo
    {
        return $this->belongsTo(Reclamation::class);
    }
}
