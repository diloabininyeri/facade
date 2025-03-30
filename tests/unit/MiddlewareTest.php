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
class MiddlewareTest extends TestCase
{

    /**
     * @return void
     */
    #[Test]
    public function middleware():void
    {
        $container = new Container();
        $container->bind('http',HttpService::class);
        AbstractFacade::setContainer($container);

        Http::middleware(static function (string $method,array &$parameters,\Closure $next){

            if ('post' === $method) {
                $parameters[0] = 'https://diloabininyeri.com';
            }
            return $next($method, $parameters);
        });

        $this->assertEquals('https://diloabininyeri.com',Http::post('http://localhost'));
    }
}