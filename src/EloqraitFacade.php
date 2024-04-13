<?php

namespace Kwidoo\Eloqrait;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kwidoo\Eloqrait\Skeleton\SkeletonClass
 */
class EloqraitFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'eloqrait';
    }
}
