<?php

namespace Zeus\Facade\Tests\unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Zeus\Facade\AbstractFacade;
use Zeus\Facade\Container;
use Zeus\Facade\Tests\stubs\Http;
use Zeus\Facade\Tests\stubs\HttpService;

/**
 *
 */
class FakeTest extends TestCase
{

    /**
     * @return void
     */
    #[Test]
    public function fake():void
    {

        $container = new Container();
        $container->bind('http',HttpService::class);
        AbstractFacade::setContainer($container);

        $this->assertEquals('http://localhost',Http::get('http://localhost'));

        Http::fake('get',static function (){
            return 'Fake HTTP request';
        });

        $this->assertEquals('Fake HTTP request',Http::get('http://localhost'));

    }
}