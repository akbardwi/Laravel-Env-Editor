<?php

namespace Akbardwi\LaravelEnvEditor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * The LaravelEnvEditor facade.
 *
 * @package Akbardwi\LaravelEnvEditor\Facades
 *
 * @author Akbar Dwi Syahputra <admin@mail.akbardwi.my.id>
 */
class LaravelEnvEditor extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-dotenv-editor';
    }
}
