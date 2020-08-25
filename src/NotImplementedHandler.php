<?php declare(strict_types=1);

namespace Pdsinterop\Authentication;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NotImplementedHandler extends AbstractHandler
{
    public function __invoke(Request $request, array $args) : Response
    {
        return $this->getResponse()->withStatus(501);
    }
}
