<?php declare(strict_types=1);

namespace Pdsinterop\Authentication;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RedirectWithSlashHandler extends AbstractHandler
{
    public function __invoke(ServerRequestInterface $request, array $args) : ResponseInterface
    {
        return $this->getResponse()->withHeader('Location', $request->getRequestTarget() . '/')->withStatus(302);
    }
}
