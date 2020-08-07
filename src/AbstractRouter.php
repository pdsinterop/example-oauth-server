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

    public static function createExceptionResponse(
        Exception $exception,
        ResponseInterface $response
    ) : ResponseInterface {
        $response = $response->withStatus(500);
        $response->getBody()->write($exception);

        if ($exception instanceof OAuthServerException) {
            // All instances of OAuthServerException can be formatted into a HTTP response
            $response = $exception->generateHttpResponse($response);
        }

        return $response;
    }

    abstract public function route() : callable;
}
