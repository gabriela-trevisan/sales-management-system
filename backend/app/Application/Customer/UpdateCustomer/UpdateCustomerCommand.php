<?php

namespace App\Application\Customer\UpdateCustomer;

class UpdateCustomerCommand
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name = null,
        public readonly ?string $document = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly bool $hasPhone = false,
        public readonly ?string $status = null,
        public readonly ?int $segmentId = null,
        public readonly bool $hasSegmentId = false,
    ) {}
}
