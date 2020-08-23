<?php declare(strict_types=1);

namespace Pdsinterop\Authentication;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class AbstractHandler
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var Response */
    private $response;

    //////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function getResponse() : Response
    {
        return $this->response;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    abstract public function __invoke(Request $request, array $args) : Response;
}
