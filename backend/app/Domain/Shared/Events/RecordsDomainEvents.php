<?php

namespace App\Domain\Shared\Events;

trait RecordsDomainEvents
{
    /** @var array<int, DomainEvent> */
    private array $domainEvents = [];

    protected function recordDomainEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * @return array<int, DomainEvent>
     */
    public function releaseDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}
