<?php
namespace App\Facades;

use App\Repositories\ClientServiceFacade;
use Illuminate\Support\Facades\Facade;

class ClientRepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ClientRepositoryInterface::class;
    }
}