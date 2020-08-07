<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Server;

use Exception;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\Route\RouteGroup;
use Pdsinterop\Authentication\AbstractRouter;
use Pdsinterop\Authentication\Enum\Authorization;
use Pdsinterop\Authentication\Server\Factory\AuthorizationServerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router extends AbstractRouter
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var array */
    private $grantTypes;
    /** @var AuthorizationServerFactory */
    private $serverFactory;
    /** @var UserEntityInterface */
    private $user;

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function __construct(
        ResponseInterface $response,
        AuthorizationServerFactory $serverFactory,
        UserEntityInterface $user,
        array $grantTypes
    ) {
        parent::__construct($response);

        $this->serverFactory = $serverFactory;
        $this->user = $user;
        $this->grantTypes = $grantTypes;
    }

    final public function route() : callable
    {
        $response = $this->getResponse();
        $serverFactory = $this->serverFactory;
        $user = $this->user;

        $grantTypes = $this->grantTypes;

        $server = $serverFactory->createAuthorizationServer($grantTypes);

        return function (RouteGroup $router) use ($response, $server, $user) {
            $router->map('GET', '/authorize', $this->routeAuthorizationCodeGrant($response, $server, $user));
            $router->map('POST', '/authorize', $this->routeAuthorizationCodeGrant($response, $server, $user));

            $router->map('GET', '/access_token', $this->accessTokenHandler($response, $server));
            $router->map('POST', '/access_token', $this->accessTokenHandler($response, $server));
        };
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function accessTokenHandler(ResponseInterface $response, AuthorizationServer $server) : callable
    {
        return static function (ServerRequestInterface $request/*, array $args*/) use ($response, $server) {
            try {
                return $server->respondToAccessTokenRequest($request, $response);
            } catch (Exception $exception) {
                return static::createExceptionResponse($exception, $response);
            }
        };
    }

    private function routeAuthorizationCodeGrant(
        ResponseInterface $response,
        AuthorizationServer $server,
        UserEntityInterface $user
    ) : callable {
        return static function (ServerRequestInterface $request/*, array $args*/) use ($response, $server, $user) {
            try {
                // Validate the HTTP request and return an AuthorizationRequest object.
                $authRequest = $server->validateAuthorizationRequest($request);

                // The auth request object can be serialized and saved into a user's session.
                // You will probably want to redirect the user at this point to a login endpoint.

                // Once the user has logged in set the user on the AuthorizationRequest
                $authRequest->setUser($user);

                // At this point you should redirect the user to an authorization page.
                // This form will ask the user to approve the client and the scopes requested.

                // Once the user has approved or denied the client update the status
                $authRequest->setAuthorizationApproved(Authorization::APPROVED);

                // Return the HTTP redirect response
                $response = $server->completeAuthorizationRequest($authRequest, $response);

            } catch (Exception $exception) {
                $response = static::createExceptionResponse($exception, $response);
            }

            return $response;
        };
    }
}
