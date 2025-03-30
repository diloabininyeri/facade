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
     * @noinspection PhpUnused
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
     * @noinspection PhpUnused
     * @param string $method
     * @return bool
     */
    public function hasFake(string $method): bool
    {
        return isset(static::$fakes[static::class][$method]);
    }
    /***
     * @noinspection PhpUnused
     * @return bool
     */
    public function hasMiddleware():bool
    {
        return isset(static::$middlewares[static::class]);
    }

    /***
     * @noinspection PhpUnused
     * @return void
     */
    public function removeMiddleware(): void
    {
        unset(static::$middlewares[static::class]);
    }

    /***
     * @param string|array|null $method
     * @return void
     */
    public static function clearFake(null|string|array $method=null): void
    {
        if ($method === null) {
            unset(static::$fakes[static::class]);
            return;
        }
        if (is_array($method)) {
            foreach ($method as $m) {
                static::clearFake($m);
            }
            return;
        }
        unset(static::$fakes[static::class][$method]);
    }
    /**
     * @return string
     */
    abstract public static function getFacade(): string;
}