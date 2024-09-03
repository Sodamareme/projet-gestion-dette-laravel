<?php
namespace App\Contracts;

interface AuthenticationServiceInterface
{
    public function authenticate(array $credentials);
    public function logout();
}
