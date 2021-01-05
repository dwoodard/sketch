<?php

namespace Dwoodard\Sketch\Facades;

use Illuminate\Support\Facades\Facade;

class Sketch extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sketch';
    }
}
