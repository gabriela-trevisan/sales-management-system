<?php

namespace App\Application\Customer\CreateCustomer;

use App\Domain\Customer\Contracts\CustomerRepositoryInterface;
use App\Domain\Customer\Models\Customer;
use App\Domain\Shared\ValueObjects\Document;
use Illuminate\Validation\ValidationException;

class CreateCustomerHandler
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {}

    /**
     * Handle the create customer command
     *
     * @throws ValidationException
     */
    public function handle(CreateCustomerCommand $command): Customer
    {
        $document = Document::fromString($command->document);

        if ($this->customerRepository->findByDocument($document->value())) {
            throw ValidationException::withMessages([
                'document' => ['Cliente com este documento já existe.'],
            ]);
        }

        $customer = $this->customerRepository->create($command->toArray());

        return $customer;
    }
}
