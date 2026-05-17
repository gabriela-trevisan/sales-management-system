<?php

namespace App\Domain\Proposal\Enums;

enum ProposalStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Expired = 'expired';

    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Sent], true);
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft => in_array($next, [self::Sent, self::Approved, self::Rejected, self::Expired], true),
            self::Sent => in_array($next, [self::Approved, self::Rejected, self::Expired], true),
            self::Approved, self::Rejected, self::Expired => false,
        };
    }
}
