<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Access\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') == 'production') {
            URL::forceScheme('https');
        }

        Gate::define('have-online-instances', function (User $user) {
            $firstOnlineInstance = $user->instances->where('online', 1)->first();
            if (!$firstOnlineInstance) {
                return Response::deny("Necessário pelo menos uma conexão online do whatsapp.");
            }
            return true;
        });
    }
}
