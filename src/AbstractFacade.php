<?php

namespace Zeus\Facade;

use Closure;
use ReflectionException;

/**
 *
 */
abstract class AbstractFacade
{
    /***
     * @var Container|null
     */
    private static ?Container $container = null;

    private static array $middlewares = [];

    private static array $fakes = [];

    /**
     * @param Container $container
     * @return void
     */
    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }

    /**
     * @return Container|null
     */
    public static function getContainer(): ?Container
    {
        return self::$container;
    }

    /***
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws ReflectionException
     */
    private static function executeMethod(string $method, array $arguments): mixed
    {
        $facade = static::getFacade();
        if (static::$container->has($facade)) {
            return self::$container->make($facade)?->$method(...$arguments);
        }
        throw new FacadeException(sprintf('Facade %s does not exist.', $facade));
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return null
     * @throws ReflectionException
     */
    public function __call(string $method, array $arguments)
    {
        return static::__callStatic($method, $arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return null
     * @throws ReflectionException
     */
    public static function __callStatic(string $method, array $arguments)
    {
        if (null === static::$container) {
            throw new FacadeException('Container not set.');
        }
        $fake = static::$fakes[static::class][$method]?? null;
        if ($fake) {
            return $fake($arguments);
        }

        $middleware = static::$middlewares[static::class] ?? null;
        if ($middleware) {
            return $middleware(
                $method,
                $arguments,
                fn(string $method, array $parameters) => static::executeMethod($method, $parameters)
            );
        }
        return static::executeMethod($method, $arguments);
    }

    /**
     * @param Closure $closure
     * @return void
     */
    public static function middleware(Closure $closure): void
    {
        static::$middlewares[static::class] = $closure;
    }

    /***
     * @param string $method
     * @param Closure $callback
     * @return void
     */
    public static function fake(string $method,Closure $callback): void
    {
        static::$fakes[static::class][$method] = $callback;
    }
    /**
     * @return string
     */
    abstract public static function getFacade(): string;
}