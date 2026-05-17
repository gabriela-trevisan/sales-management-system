<?php

namespace App\Domain\Proposal\Events;

use App\Domain\Proposal\Enums\ProposalStatus;
use App\Domain\Shared\Events\DomainEvent;

final readonly class ProposalStatusChanged implements DomainEvent
{
    public function __construct(
        public int $proposalId,
        public ProposalStatus $from,
        public ProposalStatus $to,
        private \DateTimeImmutable $occurredOn = new \DateTimeImmutable(),
    ) {}

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
