<?php

namespace App\Infrastructure\Listeners;

use App\Domain\Proposal\Enums\ProposalStatus;
use App\Domain\Proposal\Events\ProposalStatusChanged;
use Illuminate\Support\Facades\Log;

/**
 * Ponto de extensão para notificações (e-mail, push, etc.).
 * Por ora registra intenção de notificação; integrar canal real quando disponível.
 */
class NotifyProposalStatusChanged
{
    public function handle(ProposalStatusChanged $event): void
    {
        if ($event->to !== ProposalStatus::Sent) {
            return;
        }

        Log::notice('Notificação: proposta enviada ao cliente', [
            'proposal_id' => $event->proposalId,
        ]);
    }
}
