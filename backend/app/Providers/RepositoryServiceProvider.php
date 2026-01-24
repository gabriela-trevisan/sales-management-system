<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Customer
use App\Domain\Customer\Contracts\CustomerRepositoryInterface;
use App\Infrastructure\Repositories\EloquentCustomerRepository;

// Product
use App\Domain\Product\Contracts\ProductRepositoryInterface;
use App\Infrastructure\Repositories\EloquentProductRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public $bindings = [
        // Customer Domain
        CustomerRepositoryInterface::class => EloquentCustomerRepository::class,
        
        // Product Domain
        ProductRepositoryInterface::class => EloquentProductRepository::class,
        
        // Sales Domain (to be added)
        // OpportunityRepositoryInterface::class => EloquentOpportunityRepository::class,
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
