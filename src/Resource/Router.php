<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Resource;

use League\Flysystem\Filesystem;
use League\Route\RouteGroup;
use Pdsinterop\Authentication\AbstractRouter;
use Pdsinterop\Authentication\RedirectWithSlashHandler as RedirectHandler;
use Pdsinterop\Authentication\Resource\Handler\Authentication;
use Pdsinterop\Authentication\Resource\Handler\Resource as ResourceHandler;
use Psr\Http\Message\ResponseInterface as Response;

class Router extends AbstractRouter
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var Authentication */
    private $authentication;
    /** @var Filesystem */
    private $filesystem;
    /** @var string[] */
    private $publicResources;

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function __construct(
        Response $response,
        Filesystem $filesystem,
        Authentication $authentication,
        array $publicResources
    ) {
        $this->authentication = $authentication;
        $this->filesystem = $filesystem;
        $this->publicResources = $publicResources;

        parent::__construct($response);
    }

    final public function route() : callable
    {
        $authentication = $this->authentication;
        $filesystem = $this->filesystem;
        $publicResources = $this->publicResources;
        $response = $this->getResponse();

        return function (RouteGroup $router) use ($authentication, $filesystem, $publicResources, $response) {
            $router->map('GET', '/', new RedirectHandler($response));
            $router->map('GET', '', $this->handleRootRequest($response));

            $resourceHandler = new ResourceHandler($response, $filesystem, $authentication);
            $resourceHandler->setPublicResources($publicResources);
            $router->map('GET', '{path:.*}', $resourceHandler);
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
