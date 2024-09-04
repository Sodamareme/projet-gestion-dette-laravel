<?php
namespace App\Providers;

interface AuthenticationServiceInterface
{
    public function authenticate(array $credentials);
    public function logout();
}
