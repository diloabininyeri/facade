<?php

namespace Zeus\Facade\Tests\stubs;

use Zeus\Facade\AbstractFacade;


/**
 * @see LoginController
 * @method static bool authenticateUser(string $username, string $password)
 * @method static string|null getLastLoginTime()
 */
class Login extends AbstractFacade
{

    public static function getFacade(): string
    {
        return 'login';
    }
}