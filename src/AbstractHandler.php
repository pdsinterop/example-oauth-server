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

    final public function createJsonResponse(array $contents) : Response
    {
        $response = $this->response;

        $json = json_encode($contents, JSON_PRETTY_PRINT);

        $response->getBody()->write($json);

        return $response->withHeader('content-type', 'application/json; charset=UTF-8');
    }
}
