<?php

namespace App\Facades;

use App\Services\Client\ClientServiceInterface;
use Illuminate\Support\Facades\Facade;

class ClientServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ClientServiceInterface::class;
    }
}
