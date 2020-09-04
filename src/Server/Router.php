<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Server;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\UserEntityInterface as User;
use League\Route\RouteGroup;
use Pdsinterop\Authentication\AbstractRouter;
use Pdsinterop\Authentication\Enum\Authorization;
use Pdsinterop\Authentication\Enum\ServerPrefix;
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
            $router->map('GET', '', $this->handleHomePageRequest($response));

            $router->map('GET', '/login', $this->handleLoginRequest($response));
            $router->map('POST', '/login', $this->handleLoginRequest($response));

            $router->map('GET', '/logout', $this->handleLogoutRequest($response));
            $router->map('POST', '/logout', $this->handleLogoutRequest($response));

            $router->map('GET', '/approve', $this->handleApprovalRequest($response));
            $router->map('POST', '/approve', $this->handleApprovalRequest($response));

            $router->map('POST', '/authorize', $this->handleAuthorizationCodeRequest($response, $server, $user));
            $router->map('POST', '/access_token', $this->handleAccessTokenRequest($response, $server));
        };
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function handleAccessTokenRequest(Response $response, AuthorizationServer $server) : callable
    {
        return static function (Request $request/*, array $args*/) use ($response, $server) {
            return $server->respondToAccessTokenRequest($request, $response);
        };
    }

    private function handleApprovalRequest(Response $response) : callable
    {
        return static function (Request $request/*, array $args*/) use ($response) {
            $clientPrefix = ServerPrefix::CLIENT;
            $serverPrefix = ServerPrefix::AUTHORIZATION;
            $field = 'approval';

            $response->getBody()->write('<h1>Approve Authorization</h1>');
            $post = $request->getParsedBody();

            if (isset($post[$field])) {
                switch ($post[$field]) {
                    case 'approved':
                        $_SESSION['approved'] = Authorization::APPROVED;
                        $status = 'approved';
                        $statusColor = 'green';
                        break;

                    case 'denied':
                        $_SESSION['approved'] = Authorization::DENIED;
                        $status = 'denied';
                        $statusColor = 'crimson';
                        break;

                    default:
                        unset($_SESSION['approved']);
                        $status = 'reset';
                        $statusColor = 'dodgerblue';
                        break;
                }

                $response->getBody()->write('<p style="color: white; background: ' . $statusColor . ';">Authorization has been ' . $status . '</p>');
            }

            $checked = [
                'yes' => isset($_SESSION['approved']) && $_SESSION['approved'] === Authorization::APPROVED ? 'checked' : '',
                'no' => isset($_SESSION['approved']) && $_SESSION['approved'] === Authorization::DENIED ? 'checked' : '',
                'reset' => ! isset($_SESSION['approved']) ? 'checked' : '',
            ];

            $response->getBody()->write(<<<HTML
                <form action="" method="post">
                    <label>
                        <input type="radio" value="approved" name="{$field}" {$checked['yes']}/>
                        <em>Approve</em>
                    </label>
                    <label>
                        <input type="radio" value="denied" name="{$field}" {$checked['no']}/>
                        <em>Deny</em>
                    </label>
                    <label>
                        <input type="radio" value="" name="{$field}" {$checked['reset']}/>
                        <em>Reset</em>
                    </label>
                    <button>Submit</button>
                </form>
HTML
            );

            if (isset($post[$field])) {

                $response->getBody()->write(<<<"HTML"
                    <h2>Continue</h2>
                    <p>
                        Normally the authorization process would continue by
                        redirecting back to the client: <a href="{$clientPrefix}/authorize">{$clientPrefix}/authorize</a>
                    </p>
HTML
                );
            }

            $response->getBody()->write(<<<"HTML"
                <h2>Start over</h2>
                <p>
                    To start the process over, reset the given Authorization and logout before going back to the authorization URL above.
                </p>
HTML
            );

            return $response;
        };
    }

    private function handleAuthorizationCodeRequest(
        Response $response,
        AuthorizationServer $server,
        User $user
    ) : callable {
        return static function (Request $request/*, array $args*/) use ($response, $server, $user) {
            $authorization = $_SESSION['approved'] ?? null;
            $userId = $user->getIdentifier();
            $prefix = ServerPrefix::AUTHORIZATION;

            // Validate the HTTP request and return an AuthorizationRequest object.
            // The auth request object can be serialized and saved into a user's session.
            $authRequest = $server->validateAuthorizationRequest($request);

            if ($userId === null && $authorization === null) {
                // You will probably want to redirect the user at this point to a login endpoint.
                $response->getBody()->write('<p>Normally at this point you would be redirected to <a href="' . $prefix . '/login">login</a></p>');
            } elseif ($userId !== null && $authorization === null) {
                // Once the user has logged in set the user on the AuthorizationRequest
                $authRequest->setUser($user);
                // At this point you should redirect the user to an authorization page.
                // This form will ask the user to approve the client and the scopes requested.
                $response->getBody()->write('<p>Normally at this point you would be redirected to <a href="' . $prefix . '/approve">approve</a> the request</p>');
            } elseif (is_bool($authorization)) {
                $authRequest->setUser($user);
                // Once the user has approved or denied the client update the status
                $authRequest->setAuthorizationApproved($authorization);
                // Return the HTTP redirect response
                $response = $server->completeAuthorizationRequest($authRequest, $response);
            } else {
                throw new \LogicException('Developer F*&#@! up.');
            }

            return $response;
        };
    }

    private function handleHomePageRequest(Response $response) : callable
    {
        return static function (/*ServerRequestInterface $request, array $args*/) use ($response) {
            $prefix = ServerPrefix::AUTHORIZATION;

            $response->getBody()->write(<<<"HTML"
                    <h1>Server URLs</h1>

                    <p>In OAuth2, there are several ways to authenticate a client against a server.</p>

                    <p>The end result of authentication is always that an access token is obtained by the client.</p>

                    <p>The mechanism used to acquiring an access token is called a "grant"</p>

                    <p>Several grants are available, they can be visited at the following URLs:</p>

                    <ul>
                        <li><a href="{$prefix}/access_token">{$prefix}/access_token</a></li>
                        <li><a href="{$prefix}/approve">{$prefix}/approve</a></li>
                        <li><a href="{$prefix}/authorize">{$prefix}/authorize</a></li>
                        <li><a href="{$prefix}/login">{$prefix}/login</a></li>
                        <li><a href="{$prefix}/logout">{$prefix}/logout</a></li>
                    </ul>
HTML
            );

            return $response;
        };
    }

    private function handleLoginRequest(Response $response) : callable
    {
        return static function (Request $request/*, array $args*/) use ($response) {
            $field = 'username';
            $clientPrefix = ServerPrefix::CLIENT;
            $serverPrefix = ServerPrefix::AUTHORIZATION;

            $response->getBody()->write('<h1>Log in</h1>');
            $post = $request->getParsedBody();

            if ($post !== []) {
                $username = $post[$field] ?? '';
                if ($username !== '') {
                    $_SESSION['user_id'] = $username;
                } else {
                    $error = 'Please provide a user name!';
                }
            }

            if (isset($error)) {
                $response->getBody()->write('<p style="color: white; background: crimson;">' . $error . '</p>');
            }

            if (isset($_SESSION['user_id']) && is_string($_SESSION['user_id'])) {
                $response->getBody()->write(<<<"HTML"
                    <p style="color: white; background: green;">User is logged in</p>
                    <h2>Continue</h2>
                    <p>
                        Normally the authorization process would continue by
                        redirecting back to the client: <a href="{$clientPrefix}/authorize">{$clientPrefix}/authorize</a>
                    </p>
                    <h2>Start over</h2>
                    <p>
                        To start the process all over, first logout at
                        <a href="{$serverPrefix}/logout">{$serverPrefix}/logout</a>
                        before going back to the authorization URL above.
                    </p>
HTML
                );
            } else {
                $response->getBody()->write(<<<"HTML"
                <form action="" method="post">
                    <label><em>User Name</em><input type="text" name="{$field}"/></label>
                    <label><em>Password</em><input type="password" readonly value="S0m3 P@SsW0rD"/></label>
                    <button>Submit</button>
                </form>
                <p>As this is a dummy example, no password is needed. The field is only shown here for familiarity.</p>
HTML
                );
            }

            return $response;
        };
    }

    private function handleLogoutRequest(Response $response) : callable
    {
        return static function (Request $request/*, array $args*/) use ($response) {
            $clientPrefix = ServerPrefix::CLIENT;
            $serverPrefix = ServerPrefix::AUTHORIZATION;
            $field = 'LOG_ME_OUT';

            $response->getBody()->write('<h1>Log out</h1>');
            $post = $request->getParsedBody();

            if ($post !== []) {
                if (isset($post[$field])) {
                    unset($_SESSION['user_id']);
                }
            }

            if (isset($_SESSION['user_id']) && is_string($_SESSION['user_id'])) {
                $response->getBody()->write('<form action="" method="post"><button name="' . $field . '">Submit to log out</button></form>');
            } else {
                $response->getBody()->write(<<<"HTML"
                    <p style="color: white; background: green;">User is logged out</p>
                    <h2>Continue</h2>
                    <p>
                        Log back in: <a href="{$serverPrefix}/login">{$serverPrefix}/login</a>
                        or go to the client: <a href="{$clientPrefix}">{$clientPrefix}</a>
                    </p>
HTML
                );
            }

            return $response;
        };
    }
}
