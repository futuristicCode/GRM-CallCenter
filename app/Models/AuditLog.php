<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'utilisateur_id',
        'action',
        'modele',
        'modele_id',
        'ancien_valeurs',
        'nouveau_valeurs',
        'adresse_ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'ancien_valeurs' => 'array',
            'nouveau_valeurs' => 'array',
        ];
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}
