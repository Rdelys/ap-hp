<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalAudit extends Model
{
    protected $table = 'journaux_audit'; // ⚠️ à ajouter — Eloquent aurait sinon cherché "journal_audits"

    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'action', 'objet_type', 'objet_id',
        'adresse_ip', 'user_agent', 'contexte',
    ];

    protected $casts = ['contexte' => 'array'];

    public function update(array $attributes = [], array $options = []): bool
    {
        throw new \RuntimeException('journaux_audit est append-only : modification interdite.');
    }

    public function delete(): bool|null
    {
        throw new \RuntimeException('journaux_audit est append-only : suppression interdite.');
    }

    public static function tracer(string $action, ?Model $objet = null, array $contexte = []): void
    {
        static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'objet_type' => $objet ? get_class($objet) : null,
            'objet_id' => $objet?->id,
            'adresse_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'contexte' => $contexte,
        ]);
    }
}