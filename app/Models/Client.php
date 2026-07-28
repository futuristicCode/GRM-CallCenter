<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Client extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'type',
    ];

    public function reclamations(): HasMany
    {
        return $this->hasMany(Reclamation::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
