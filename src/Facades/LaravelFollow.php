<?php

namespace TimGavin\LaravelFollow\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelFollow extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-follow';
    }
}
