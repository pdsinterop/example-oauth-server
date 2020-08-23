<?php declare(strict_types=1);

namespace Pdsinterop\Authentication;

use League\Route\RouteGroup;
use Pdsinterop\Authentication\Enum\ServerPrefix;
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
            $prefix = [
                'client' => ServerPrefix::CLIENT,
                'resource' => ServerPrefix::RESOURCE,
                'server' => ServerPrefix::AUTHORIZATION,
            ];

            $response->getBody()->write(<<<"HTML"

<h1>Hello world!</h1>

<p>
    Some dummy resources have been added, to complete the example.
</p>

<p>The following URLs can be visited:</p>

<ul>
    <li><a href="/">/</a> The page you find yourself on now.</li>
    <li><a href="{$prefix['resource']}">{$prefix['resource']}</a> Example <strong>resources</strong> to trigger the authentication process using the server and client URLs.</li>
</ul>

HTML
            );

            return $response->withStatus(200);
        };
    }
}
