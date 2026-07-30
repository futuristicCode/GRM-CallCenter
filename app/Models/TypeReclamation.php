<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeReclamation extends Model
{
    use HasFactory;

    protected $table = 'types_reclamation';

    protected $fillable = [
        'code',
        'libelle',
        'delai_traitement_sla',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
            'delai_traitement_sla' => 'integer',
        ];
    }

    public function sousTypes(): HasMany
    {
        return $this->hasMany(SousType::class, 'type_id');
    }

    public function reclamations(): HasMany
    {
        return $this->hasMany(Reclamation::class, 'type_id');
    }
}
