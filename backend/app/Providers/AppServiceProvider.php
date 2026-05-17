<?php

namespace App\Providers;

use App\Domain\Customer\Models\Customer;
use App\Domain\Customer\Policies\CustomerPolicy;
use App\Domain\Proposal\Events\ProposalStatusChanged;
use App\Domain\Proposal\Models\Proposal;
use App\Domain\Proposal\Policies\ProposalPolicy;
use App\Infrastructure\Listeners\LogProposalStatusChanged;
use App\Infrastructure\Listeners\NotifyProposalStatusChanged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     *
     * As policies são registradas explicitamente porque os Models estão em
     * namespaces não-padrão (Domain layer), impedindo a descoberta automática
     * do Laravel pelo padrão de nomenclatura convencional.
     */
    public function boot(): void
    {
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Proposal::class, ProposalPolicy::class);

        Event::listen(ProposalStatusChanged::class, LogProposalStatusChanged::class);
        Event::listen(ProposalStatusChanged::class, NotifyProposalStatusChanged::class);
    }
}
