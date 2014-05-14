<?php

namespace Fenos\Rally\Facades;


use Illuminate\Support\Facades\Facade;

class Rally extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'rally'; }

} 