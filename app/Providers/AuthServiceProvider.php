<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Policies\ArticlePolicy;
use App\Models\Article;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
      Gate::define('Admin', [UserPolicy::class, 'isAdmin']);
        // Register Passport routes
        // Passport::routes();
        
        // // Optional: Configure Passport tokens
        // Passport::tokensExpireIn(now()->addDays(15));
        // Passport::refreshTokensExpireIn(now()->addDays(30));
        // Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        // Configure Passport tokens
    Passport::tokensExpireIn(now()->addMinutes(5)); // Short-lived access tokens
    Passport::refreshTokensExpireIn(now()->addDays(30)); // Refresh tokens expire in 30 days
    Passport::personalAccessTokensExpireIn(now()->addMonths(6)); // Personal access tokens expire in 6 months
    }
}
