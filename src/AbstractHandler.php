<?php declare(strict_types=1);

namespace Pdsinterop\Authentication;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractHandler
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var ResponseInterface */
    protected $response;

    //////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function getResponse() : ResponseInterface
    {
        return $this->response;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    abstract public function __invoke(ServerRequestInterface $request, array $args) : ResponseInterface;
}
