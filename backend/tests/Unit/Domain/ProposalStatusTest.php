<?php

namespace Tests\Unit\Domain;

use App\Domain\Proposal\Enums\ProposalStatus;
use PHPUnit\Framework\TestCase;

class ProposalStatusTest extends TestCase
{
    public function test_draft_can_transition_to_sent(): void
    {
        $this->assertTrue(ProposalStatus::Draft->canTransitionTo(ProposalStatus::Sent));
    }

    public function test_approved_cannot_transition(): void
    {
        $this->assertFalse(ProposalStatus::Approved->canTransitionTo(ProposalStatus::Sent));
    }

    public function test_editable_statuses(): void
    {
        $this->assertTrue(ProposalStatus::Draft->isEditable());
        $this->assertTrue(ProposalStatus::Sent->isEditable());
        $this->assertFalse(ProposalStatus::Approved->isEditable());
    }
}
