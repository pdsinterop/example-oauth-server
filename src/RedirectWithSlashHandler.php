<?php declare(strict_types=1);

namespace Pdsinterop\Authentication;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RedirectWithSlashHandler
{
    final public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function __invoke(ServerRequestInterface $request, array $args) : ResponseInterface
    {
        return $this->response->withHeader('Location', $request->getRequestTarget() . '/')->withStatus(302);
    }
}
