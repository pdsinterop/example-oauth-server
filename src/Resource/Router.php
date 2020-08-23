<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Resource;

use League\Route\RouteGroup;
use Pdsinterop\Authentication\AbstractRouter;
use Pdsinterop\Authentication\RedirectWithSlashHandler as RedirectHandler;
use Pdsinterop\Authentication\Resource\Handler\Authentication;
use Psr\Http\Message\ResponseInterface as Response;

class Router extends AbstractRouter
{
    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function route() : callable
    {
        $response = $this->getResponse();

        return function (RouteGroup $router) use ($response) {
            $router->map('GET', '/', new RedirectHandler($response));
            $router->map('GET', '', $this->handleRootRequest($response));
            $router->map('GET', '/authenticate', new Authentication($response));
        };
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function handleRootRequest(Response $response) : callable
    {
        return static function (/*Request $request, array $args*/) use ($response) {
            /** @noinspection HtmlUnknownTarget */
            $response->getBody()->write(<<<'HTML'
<h1>Available resources</h1>

<p>The following resources are available:</p>

<ul>
    <li>a <a href="./private_resource.txt">private resource</a> that requires authentication</li>
    <li>a <a href="./public_resource.txt">public resource</a> that can always be accessed</li>
</ul>
HTML
            );

            return $response;
        };
    }
}
