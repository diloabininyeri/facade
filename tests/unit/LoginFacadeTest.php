<?php


use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Zeus\Facade\AbstractFacade;
use Zeus\Facade\Container;
use Zeus\Facade\Tests\stubs\LoginController;
use Zeus\Facade\Tests\stubs\LoginService;
use Zeus\Facade\Tests\stubs\Login;
use Zeus\Facade\Tests\stubs\LoginServiceInterface;

class LoginFacadeTest extends TestCase
{

    #[Test]
    public function login():void
    {

        $container = new Container();
        AbstractFacade::setContainer($container);
        $container->bind(LoginServiceInterface::class, LoginService::class);
        $container->bind('login', LoginController::class);

        $login = Login::authenticateUser('test', 'test');

        $this->assertFalse($login);

        $lastLoginTime = Login::getLastLoginTime();
        $this->assertEquals($lastLoginTime,date('Y-m-d H:i'));

    }
}