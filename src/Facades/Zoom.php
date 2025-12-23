<?php

namespace laraSDKs\Zoom\Facades;

use Illuminate\Support\Facades\Facade;
use laraSDKs\Zoom\Client;

/**
 * @see Client
 */
class Zoom extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}
