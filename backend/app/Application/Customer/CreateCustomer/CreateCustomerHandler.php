<?php

namespace App\Application\Customer\CreateCustomer;

use App\Domain\Customer\Contracts\CustomerRepositoryInterface;
use App\Domain\Customer\Models\Customer;
use Illuminate\Support\Facades\Validator;
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
        if ($this->customerRepository->findByDocument($command->document)) {
            throw ValidationException::withMessages([
                'document' => ['Cliente com este documento já existe.']
            ]);
        }

        $customer = $this->customerRepository->create($command->toArray());

        return $customer;
    }
}
