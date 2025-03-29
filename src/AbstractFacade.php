<?php

namespace Zeus\Facade;

use ReflectionException;

/**
 *
 */
abstract class AbstractFacade
{
    /***
     * @var Container|null
     */
    private static ?Container $container=null;

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
        $facade = static::getFacade();
        if (static::$container->has($facade)) {
            return self::$container->make($facade)?->$method(...$arguments);
        }
        throw new FacadeException(sprintf('Facade %s does not exist.', $facade));
    }

    /**
     * @return string
     */
    abstract public static function getFacade(): string;
}