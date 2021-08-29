<?php

namespace Touhidurabir\ModelHashid\Facades;

use Illuminate\Support\Facades\Facade;

class ModelHashid extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {

        return 'model-hashid';
    }
}