<?php

namespace FikriMastor\MyKad\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FikriMastor\MyKad\MyKad
 */
class MyKad extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \FikriMastor\MyKad\MyKad::class;
    }
}
