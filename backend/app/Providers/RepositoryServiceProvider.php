<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Customer
use App\Domain\Customer\Contracts\CustomerRepositoryInterface;
use App\Infrastructure\Repositories\EloquentCustomerRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        // Customer Domain
        CustomerRepositoryInterface::class => EloquentCustomerRepository::class,
        
        // Sales Domain (to be added)
        // OpportunityRepositoryInterface::class => EloquentOpportunityRepository::class,
        
        // Product Domain (to be added)
        // ProductRepositoryInterface::class => EloquentProductRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
