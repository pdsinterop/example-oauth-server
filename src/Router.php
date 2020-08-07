<?php declare(strict_types=1);

namespace Pdsinterop\Authentication;

use League\Route\RouteGroup;
use Psr\Http\Message\ResponseInterface;

class Router extends AbstractRouter
{
    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function route() : callable
    {
        return function (RouteGroup $router) {
            $response = $this->getResponse();

            $router->map('GET', '/', $this->homepageHandler($response));
        };
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function homepageHandler(ResponseInterface $response) : callable
    {
        return static function (/*ServerRequestInterface $request, array $args*/) use ($response) {
            $response->getBody()->write(<<<'HTML'

<h1>Hello world!</h1>

<p>The following URLs can be visited:</p>

<ul>
    <li><a href="/">/</a> The page you find yourself on now.</li>
</ul>

HTML
            );

            return $response->withStatus(200);
        };
    }
}
