<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migra dados da tabela audit_logs antiga para audits (Laravel Auditing).
     * 
     * Mapeia eventos customizados para eventos padrão do Eloquent.
     */
    public function up(): void
    {
        // Mapear eventos antigos para novos
        $eventMap = [
            'customer_created' => 'created',
            'customer_updated' => 'updated',
            'customer_deleted' => 'deleted',
            'opportunity_created' => 'created',
            'opportunity_updated' => 'updated',
            'opportunity_deleted' => 'deleted',
        ];

        // Buscar registros da tabela antiga
        $oldAudits = DB::table('audit_logs')
            ->whereIn('event', array_keys($eventMap))
            ->get();

        foreach ($oldAudits as $oldAudit) {
            DB::table('audits')->insert([
                'user_type' => 'App\\Models\\User',
                'user_id' => $oldAudit->user_id,
                'event' => $eventMap[$oldAudit->event] ?? 'updated',
                'auditable_type' => $oldAudit->auditable_type,
                'auditable_id' => $oldAudit->auditable_id,
                'old_values' => $oldAudit->old_values,
                'new_values' => $oldAudit->new_values,
                'url' => $oldAudit->request_path ?? null,
                'ip_address' => $oldAudit->ip_address,
                'user_agent' => $oldAudit->user_agent,
                'tags' => null,
                'created_at' => $oldAudit->created_at,
                'updated_at' => $oldAudit->created_at,
            ]);
        }

        echo "✅ Migrados {$oldAudits->count()} registros de audit_logs → audits\n";
    }

    /**
     * Rollback: não remove dados da tabela audits por segurança.
     */
    public function down(): void
    {
        // Não remove dados de auditoria por segurança (compliance LGPD)
        echo "⚠️  Rollback não remove dados de auditoria por motivos de compliance.\n";
    }
};
