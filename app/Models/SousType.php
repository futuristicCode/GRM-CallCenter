<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SousType extends Model
{
    use HasFactory;

    protected $table = 'sous_types';

    protected $fillable = [
        'type_id',
        'libelle',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeReclamation::class, 'type_id');
    }

    public function reclamations(): HasMany
    {
        return $this->hasMany(Reclamation::class, 'sous_type_id');
    }
}
