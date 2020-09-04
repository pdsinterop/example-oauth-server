<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Server;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\UserEntityInterface as User;
use League\Route\RouteGroup;
use Pdsinterop\Authentication\AbstractRouter;
use Pdsinterop\Authentication\Enum\Authorization;
use Pdsinterop\Authentication\RedirectWithSlashHandler as RedirectHandler;
use Pdsinterop\Authentication\Server\Factory\AuthorizationServerFactory as ServerFactoryAlias;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Router extends AbstractRouter
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var array */
    private $grantTypes;
    /** @var ServerFactoryAlias */
    private $serverFactory;
    /** @var User */
    private $user;

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function __construct(
        Response $response,
        ServerFactoryAlias $serverFactory,
        User $user,
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

        // @TODO: The definitions $router->map() calls should stay here but the methods should (probably?) be moved to another class
        return function (RouteGroup $router) use ($response, $server, $user) {
            $router->map('GET', '/', new RedirectHandler($response));
            $router->map('GET', '', $this->handleHomePage($response));

            $router->map('POST', '/authorize', $this->routeAuthorizationCodeGrant($response, $server, $user));
            $router->map('POST', '/access_token', $this->accessTokenHandler($response, $server));
        };
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function accessTokenHandler(Response $response, AuthorizationServer $server) : callable
    {
        return static function (Request $request/*, array $args*/) use ($response, $server) {
            return $server->respondToAccessTokenRequest($request, $response);
        };
    }

    private function routeAuthorizationCodeGrant(
        Response $response,
        AuthorizationServer $server,
        User $user
    ) : callable {
        return static function (Request $request/*, array $args*/) use ($response, $server, $user) {
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

            return $response;
        };
    }

    private function handleHomePage(Response $response) : callable
    {
        return static function (/*ServerRequestInterface $request, array $args*/) use ($response) {
            $response->getBody()->write(<<<"HTML"
                    <h1>Server URLs</h1>

                    <p>In OAuth2, there are several ways to authenticate a client against a server.</p>

                    <p>The end result of authentication is always that an access token is obtained by the client.</p>

                    <p>The mechanism used to acquiring an access token is called a "grant"</p>

                    <p>Several grants are available, they can be visited at the following URLs:</p>

                    <ul>
                        <li><a href="./authorize">/authorize</a></li>
                        <li><a href="./access_token">/access_token</a></li>
                    </ul>
HTML
            );

            return $response;
        };
    }
}
