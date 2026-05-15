<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Customer\Contracts\CustomerRepositoryInterface;
use App\Infrastructure\Repositories\EloquentCustomerRepository;
use App\Domain\Product\Contracts\ProductRepositoryInterface;
use App\Infrastructure\Repositories\EloquentProductRepository;
use App\Domain\Proposal\Contracts\ProposalRepositoryInterface;
use App\Infrastructure\Repositories\EloquentProposalRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public $bindings = [
        CustomerRepositoryInterface::class => EloquentCustomerRepository::class,
        ProductRepositoryInterface::class => EloquentProductRepository::class,
        ProposalRepositoryInterface::class => EloquentProposalRepository::class,
        // TODO(#?): adicionar OpportunityRepositoryInterface => EloquentOpportunityRepository quando o módulo Opportunities for implementado
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
