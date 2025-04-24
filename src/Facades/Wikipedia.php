<?php

namespace Denason\Wikipedia\Facades;

use Denason\Wikipedia\WikipediaInterface;
use Illuminate\Support\Facades\Facade;

class Wikipedia extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WikipediaInterface::class;
    }
}
