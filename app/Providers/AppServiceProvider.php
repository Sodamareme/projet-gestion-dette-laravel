<?php
namespace App\Providers;

use App\Services\AuthentificationPassport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Services\AuthentificationServiceInterface;
use App\Services\AuthentificationService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthentificationServiceInterface::class, AuthentificationPassport::class);
    
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Vous pouvez ajouter d'autres configurations ici si nécessaire.
    
    }
}
