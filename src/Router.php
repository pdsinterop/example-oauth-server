<?php declare(strict_types=1);

namespace Pdsinterop\Authentication;

use League\Route\RouteGroup;
use Pdsinterop\Authentication\Handler\HomepageHandler;

class Router extends AbstractRouter
{
    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function route() : callable
    {
        $response = $this->getResponse();

        return static function (RouteGroup $router) use ($response) {
            $router->map('GET', '/', new HomepageHandler($response));
            $router->map('GET', '/.well-known/oauth-authorization-server', new NotImplementedHandler($response));
            $router->map('GET', '/.well-known/openid-configuration', new NotImplementedHandler($response));
        };
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
}
