<?php

namespace Pdsinterop\Authentication\Resource\Handler;

use Pdsinterop\Authentication\AbstractHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Resource extends AbstractHandler
{
    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /**
     * In order to visit this resource, a user needs to be authenticated.
     *
     * This can be done with any of the OAuth2 grant types supported by the
     * server (listed under the `/client` URI).
     *
     * If the user is authorized, the authorization method/token/etc. should be shown.
     *
     * Otherwise the user is redirected to a screen asking them for authorization.
     *
     * The resource is returned as JSON.
     *
     * @param ServerRequestInterface $request
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, array $args) : ResponseInterface
    {
        $response = $this->getResponse();

        $content = '';

        $isAuthorized = $this->isAuthorized();

        if ($isAuthorized) {
            $content = '{"message": "Information about the resource owner goes here"}';
        } else {
            $response = $response->withHeader('Location', rtrim($request->getRequestTarget(), '/') . '/authenticate')->withStatus(302);
        }

        $response->getBody()->write($content);

        return $response->withHeader('content-type', 'application/json; charset=UTF-8');
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function isAuthorized() : bool
    {
        return false;
    }
}
