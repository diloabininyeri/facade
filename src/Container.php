<?php

namespace Zeus\Facade;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class Container
{
    /**
     * @var array
     */
    private array $bindings = [];
    /**
     * @var array
     */
    private array $context = [];

    /**
     * @param string $name
     * @param callable|string $concrete
     * @return void
     */
    public function bind(string $name, callable|string $concrete): void
    {
        if (!is_callable($concrete)) {
            $concrete = static fn(Container $container) => $container->resolve($concrete);
        }
        $this->bindings[$name] = $concrete;
    }

    /**
     * @param array $bindings
     * @return void
     */
    public function bindMultiple(array $bindings):void
    {
        foreach ($bindings as $name => $concrete) {
            if (!is_string($name)) {
                throw new InvalidArgumentException('Binding name must be a string, got ' . gettype($name));
            }
            $this->bind($name, $concrete);
        }
    }

    /**
     * @param string $name
     * @return mixed
     * @throws ReflectionException
     */
    public function make(string $name): mixed
    {
        if (!$this->has($name)) {
            return $this->resolve($name);
        }
        return $this->bindings[$name]($this);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->bindings[$name]);
    }

    /**
     * @param string $scope
     * @param string $implementation
     * @param string $concrete
     * @return void
     */
    public function addContext(string $scope, string $implementation, string $concrete): void
    {
        $this->context[$scope][$implementation] = static fn(Container $container) => $container->resolve($concrete);
    }

    /***
     * @param string $class
     * @return object
     * @throws ReflectionException
     */
    public function resolve(string $class): object
    {
        $reflectionClass = new ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return new $class();
        }

        return $reflectionClass->newInstanceArgs(
            array_map(
                fn(ReflectionParameter $parameter) => $this->resolveParameter($parameter, $class),
                $constructor->getParameters()
            )
        );
    }

    /**
     * @param string $class
     * @param string $interface
     * @return bool
     */
    private function hasContext(string $class, string $interface): bool
    {
        return isset($this->context[$class][$interface]);
    }

    /**
     * @param string $class
     * @param string $interface
     * @return callable
     */
    private function getContext(string $class, string $interface): callable
    {
        return $this->context[$class][$interface];
    }

    /**
     * @param ReflectionParameter $parameter
     * @param string $class
     * @return mixed
     * @throws ReflectionException
     */
    private function resolveParameter(ReflectionParameter $parameter, string $class): mixed
    {
        $interface = $parameter->getType()?->getName();

        if ($this->hasContext($class, $interface)) {
            return $this->getContext($class, $interface)($this);
        }

        return $this->make($interface);
    }
}
