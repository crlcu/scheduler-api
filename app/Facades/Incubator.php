<?php

namespace App\Facades;
 
use Illuminate\Support\Facades\Facade;

class Incubator extends Facade 
{
    protected static function getFacadeAccessor()
    {
        return 'incubator';
    }
}
