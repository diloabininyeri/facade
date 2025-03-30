<?php

namespace Zeus\Facade\Tests\stubs;

use Zeus\Facade\AbstractFacade;

/**
 * @method static mixed get(string $url)
 * @method static mixed post(string $url)
 */
class Http  extends AbstractFacade
{

    public static function getFacade(): string
    {
        return 'http';
    }
}