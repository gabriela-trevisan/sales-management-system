<?php

namespace App\Application\Customer\UpdateCustomer;

use App\Domain\Customer\Contracts\CustomerRepositoryInterface;
use App\Domain\Customer\Enums\CustomerStatus;
use App\Domain\Customer\Models\Customer;
use App\Domain\Shared\ValueObjects\Document;
use App\Domain\Shared\ValueObjects\Email;
use App\Domain\Shared\ValueObjects\Phone;
use Illuminate\Validation\ValidationException;

class UpdateCustomerHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
    ) {}

    /**
     * @throws ValidationException
     */
    public function handle(UpdateCustomerCommand $command): Customer
    {
        $customer = $this->customerRepository->findById($command->id);

        if (! $customer) {
            throw ValidationException::withMessages([
                'id' => ['Cliente não encontrado.'],
            ]);
        }

        if ($command->document !== null) {
            $document = Document::fromString($command->document);
            $existing = $this->customerRepository->findByDocument($document->value());

            if ($existing && $existing->id !== $customer->id) {
                throw ValidationException::withMessages([
                    'document' => ['Cliente com este documento já existe.'],
                ]);
            }
        }

        if ($command->email !== null) {
            $email = Email::fromString($command->email);
            $existing = Customer::where('email', $email->value())
                ->where('id', '!=', $customer->id)
                ->first();

            if ($existing) {
                throw ValidationException::withMessages([
                    'email' => ['Cliente com este e-mail já existe.'],
                ]);
            }
        }

        $customer->updateProfile(
            name: $command->name,
            document: $command->document !== null ? Document::fromString($command->document) : null,
            email: $command->email !== null ? Email::fromString($command->email) : null,
        );

        if ($command->hasPhone) {
            $customer->updatePhone(
                $command->phone !== null && $command->phone !== ''
                    ? Phone::fromString($command->phone)
                    : null
            );
        }

        if ($command->hasSegmentId) {
            $customer->assignSegment($command->segmentId);
        }

        if ($command->status !== null) {
            $customer->applyStatus(CustomerStatus::from($command->status));
        }

        return $this->customerRepository->save($customer);
    }
}
