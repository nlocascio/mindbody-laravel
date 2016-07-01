<?php

namespace Nlocascio\Mindbody\Facades;

use Illuminate\Support\Facades\Facade;

class Mindbody extends Facade {

    /**
     * Return facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mindbody';
    }
}