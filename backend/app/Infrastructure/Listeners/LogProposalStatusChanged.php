<?php

namespace App\Infrastructure\Listeners;

use App\Domain\Proposal\Events\ProposalStatusChanged;
use Illuminate\Support\Facades\Log;

class LogProposalStatusChanged
{
    public function handle(ProposalStatusChanged $event): void
    {
        Log::info('Status da proposta alterado', [
            'proposal_id' => $event->proposalId,
            'from' => $event->from->value,
            'to' => $event->to->value,
            'occurred_on' => $event->occurredOn()->format(\DateTimeInterface::ATOM),
        ]);
    }
}
