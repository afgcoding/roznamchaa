<?php

namespace App\Providers;

use App\Enums\StoreFeature;
use App\Filament\Auth\RegistrationResponse;
use App\Models\User;
use App\Support\StoreFeatures;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse as RegistrationResponseContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RegistrationResponseContract::class, RegistrationResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Allow mass assignment on all models (no $fillable needed)
        Model::unguard();

        Gate::define('store-feature', function (?User $user, StoreFeature|string $feature): bool {
            $feature = $feature instanceof StoreFeature
                ? $feature
                : StoreFeature::tryFrom($feature);

            return $feature instanceof StoreFeature && StoreFeatures::enabled($feature);
        });
    }
}
