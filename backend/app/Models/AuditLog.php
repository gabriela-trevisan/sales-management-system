<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Model de Audit Log para compliance LGPD Art. 46
 * 
 * Registra operações críticas do sistema para:
 * - Rastreabilidade de acessos e modificações
 * - Investigações de segurança
 * - Evidências para auditoria
 * - Compliance LGPD
 */
class AuditLog extends Model
{
    /**
     * Desabilita updated_at (apenas created_at)
     */
    const UPDATED_AT = null;

    /**
     * Campos preenchíveis
     */
    protected $fillable = [
        'event',
        'auditable_type',
        'auditable_id',
        'user_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'request_method',
        'request_path',
    ];

    /**
     * Casts de tipos
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relacionamento com usuário que executou a ação
     *
     * @return BelongsTo<User, AuditLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento polimórfico com o modelo auditado
     *
     * @return MorphTo<Model, AuditLog>
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Helper: Criar log de auditoria
     *
     * @param string $event
     * @param Model|null $model
     * @param array<string, mixed>|null $oldValues
     * @param array<string, mixed>|null $newValues
     * @return void
     */
    public static function log(
        string $event,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        $request = request();

        self::create([
            'event' => $event,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id' => $model?->id,
            'user_id' => auth()->check() ? auth()->id() : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_method' => $request->method(),
            'request_path' => $request->path(),
        ]);
    }
}

