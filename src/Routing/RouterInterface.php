<?php

namespace Simplex\Routing;

use Simplex\Http\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;

interface RouterInterface
{

    /**
     * Load routes from given file and return corresponding route
     *
     * @param string $from
     * @param array $options
     * @return void
     */
    public function import(string $from, array $options = []);

    /**
     * Match only given HTTP methods
     *
     * @param string $methods
     * @param string $path
     * @param string|callable $controller
     * @param string|null $name
     * @return void
     */
    public function match(string $methods, string $path, $controller, ?string $name = null);

    /**
     * Mount a set of routes under a common prefix
     *
     * @param string $prefix
     * @param \Closure $factory
     * @return void
     */
    // public function group($prefix, \Closure $factory);

    /**
     * Dispatches a request
     *
     * @param Request $request
     * @return Route
     */
    public function dispatch(Request $request): Route;

    /**
     * Generate url for given route name
     *
     * @param string $name
     * @param array $parameters
     * @return void
     */
    public function generate(string $name, array $parameters = []): string;

    /**
     * Add route specific middlewares to router
     *
     * @param MiddlewareInterface $middleware
     * @return void
     */
    public function middleware(MiddlewareInterface $middleware);

    /**
     * Set middleware group
     *
     * @param string $strategy
     * @return void
     */
    public function setStrategy(string $strategy);
}