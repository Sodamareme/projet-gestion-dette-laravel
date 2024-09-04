<?php
namespace App\Providers;

use App\Services\AuthentificationPassport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Services\AuthentificationServiceInterface;
use App\Services\AuthentificationService;
use App\Services\Client\ClientServiceImpl;
use App\Services\Client\ClientServiceInterface;
use App\Services\Client\PhotoServiceImpl;
use App\Services\Client\PhotoServiceInterface;
use App\Services\Client\ValidationInterface;
use App\Services\Client\ValidationService;
use App\Services\Client\FileManagementInterface;
use App\Services\Client\FileManagementService;
use App\Services\Client\ClientUserServiceInterface;
use App\Services\Client\ClientUserServiceImpl;
use App\Services\Client\ClientEtUserServiceInterface;
use App\Services\Client\ClientEtUserServiceImpl;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClientServiceInterface::class, ClientServiceImpl::class);
        $this->app->bind(PhotoServiceInterface::class, PhotoServiceImpl::class);
        $this->app->bind(ValidationInterface::class, ValidationService::class);
        $this->app->bind(FileManagementInterface::class, FileManagementService::class);
        $this->app->bind(ClientUserServiceInterface::class, ClientUserServiceImpl::class);
        $this->app->bind(ClientEtUserServiceInterface::class, ClientEtUserServiceImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Vous pouvez ajouter d'autres configurations ici si nécessaire.
    
    }
}
