<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Em dev, N+1 e atributo fora do fillable viram erro em vez de bug silencioso.
        Model::shouldBeStrict(! $this->app->isProduction());

        Gate::define('staff', fn (User $user) => $user->isStaff());
        Gate::define('admin', fn (User $user) => $user->isAdmin());
    }
}
