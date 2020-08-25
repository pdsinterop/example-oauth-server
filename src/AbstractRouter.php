<?php declare(strict_types=1);

namespace Pdsinterop\Authentication;

use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractRouter
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var ResponseInterface */
    private $response;

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

    abstract public function route() : callable;
}
