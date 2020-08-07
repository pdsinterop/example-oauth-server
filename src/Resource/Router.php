<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Resource;

use League\Route\RouteGroup;
use Pdsinterop\Authentication\AbstractRouter;
use Pdsinterop\Authentication\Resource\Handler\Authentication;
use Pdsinterop\Authentication\Resource\Handler\Resource as ResourceHandler;

class Router extends AbstractRouter
{
    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function route() : callable
    {
        $response = $this->getResponse();

        return static function (RouteGroup $router) use ($response) {
            $router->map('GET', '', new ResourceHandler($response));
            $router->map('GET', '/authenticate', new Authentication($response));
        };
    }
}
