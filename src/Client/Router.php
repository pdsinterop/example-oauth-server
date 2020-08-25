<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Client;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\Route\RouteGroup;
use Pdsinterop\Authentication\AbstractRouter;
use Pdsinterop\Authentication\Client\Handler\AuthorizationCodeGrant as CodeGrantHandler;
use Pdsinterop\Authentication\Client\Handler\ClientCredentialsGrant as CredentialsGrantHandler;
use Pdsinterop\Authentication\Client\Handler\PasswordGrant as PasswordGrantHandler;
use Pdsinterop\Authentication\Enum\OAuth2\GrantType;
use Pdsinterop\Authentication\Enum\OAuth2\Parameter;
use Pdsinterop\Authentication\RedirectWithSlashHandler as RedirectHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;
use Throwable;

class Router extends AbstractRouter
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var string[] */
    private $grantTypes;
    /** @var Provider */
    private $provider;

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function __construct(Response $response, Provider $provider, array $grantTypes)
    {
        $this->grantTypes = $grantTypes;
        $this->provider = $provider;

        parent::__construct($response);
    }

    final public function route() : callable
    {
        return function (RouteGroup $router) {
            $provider = $this->provider;
            $response = $this->getResponse();
            $grantTypes = $this->grantTypes;

            // To debug output, a debug handler is available that can be used instead of the regular handler:
            // $debugHandler = new \Pdsinterop\Authentication\Client\Handler\Debug($response, $provider);

            $router->map('GET', '/', new RedirectHandler($response));

            $router->map('GET', '', $this->handleHomePage($response, $grantTypes));

            $codeGrantHandler = new CodeGrantHandler($response, $provider);
            $router->map('GET', '/authorize', $codeGrantHandler);

            $credentialsGrantHandler = new CredentialsGrantHandler($response, $provider);
            $router->map('GET', '/client_credentials', $credentialsGrantHandler);

            $passwordGrantHandler = new PasswordGrantHandler($response, $provider);
            $router->map('GET', '/password', $passwordGrantHandler);
            $router->map('POST', '/password', $passwordGrantHandler);

            $router->map('GET', '/redirect-url', $this->handleRedirect($response, $provider));
        };
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @noinspection HtmlUnknownTarget */
    private function handleHomePage(Response $response, array $grantTypes) : callable
    {
        $grantTypesHtml = '';

        if (in_array(GrantType::AUTH_CODE, $grantTypes, true)) {
            $grantTypesHtml .= <<<HTML
                <li>
                    <a href="/client/authorize">Authorization code grant</a>
                    <em>(Application running on a web server, in the browser or from a mobile app)</em>
                </li>
HTML;
        }
        if (in_array(GrantType::CLIENT_CREDENTIALS, $grantTypes, true)) {
            $grantTypesHtml .= <<<HTML
                <li>
                    <a href="/client/client_credentials">Client credentials grant</a>
                     <em>(Application access)</em>
                </li>
HTML;
        }
        if (in_array(GrantType::PASSWORD, $grantTypes, true)) {
            $grantTypesHtml .= <<<HTML
                <li>
                    <del>
                        <a href="/client/password">Password grant</a>
                        <em>(Log in using a username and password)</em>
                    </del>
                    <strong>DEPRECATED!</strong>
                </li>
HTML;
        }

        return static function (/*ServerRequestInterface $request, array $args*/) use ($grantTypesHtml, $response) {
            $response->getBody()->write(<<<"HTML"
                    <h1>Client URLs</h1>

                    <p>In OAuth2, there are several ways to authenticate a client against a server.</p>

                    <p>The end result of authentication is always that an access token is obtained by the client.</p>

                    <p>The mechanism used to acquiring an access token is called a "grant"</p>

                    <p>Several grants are available, they can be visited at the following URLs:</p>

                    <ul>{$grantTypesHtml}</ul>
<!--
                    <p>Other URLs used by the client are:</p>
                    <ul>
                        <li>
                            <a href="/client/redirect-url">/redirect-url</a>
                        </li>
                    </ul>
-->
HTML
            );

            return $response;
        };
    }

    private function handleRedirect(Response $response, Provider $provider) : callable
    {
        return static function (Request $request/*, array $args*/) use ($response, $provider) {
            $html = '';

            $queryParams = $request->getQueryParams();

            if (array_key_exists(Parameter::CODE, $queryParams) === false) {
                // @FIXME: This should not be needed as this rout should only be called when $_GET['code'] is present!
                throw new RuntimeException('Missing required "code" parameter');
            }

            $code = $queryParams[Parameter::CODE];

            try {
                // Try to get an access token using the authorization code grant.
                /** @var AccessToken $accessToken */
                $accessToken = $provider->getAccessToken(GrantType::AUTH_CODE, [
                    // @CHECKME: Should the CODE_VERIFIER be stored somewhere other than in the session?
                    Parameter::CODE => $code,
                    Parameter::GRANT_TYPE => GrantType::AUTH_CODE,
                    Parameter::CODE_VERIFIER => $_SESSION['code_verifier'],
                ]);
            } catch (Throwable $exception) {

                $error = get_class($exception);
                // @FIXME: Add error handling!
                $html = <<<"HTML"
                            <h1>Uh-Oh</h1>
                            <p>An error occurred whilst fetching an AccessToken from the Authorization Server.</p>

                            <code><strong>{$error}</strong> {$exception->getMessage()}</code>
HTML;
                if (method_exists($exception, 'getRequest')) {
                    /** @var \GuzzleHttp\Psr7\Request $remoteRequest */
                    $remoteRequest = $exception->getRequest();
                    $html .= '<h2>Request</h2><pre style="white-space: normal"><code>' .
                        $remoteRequest->getMethod() . ' ' . $remoteRequest->getUri() . '<br>' .
                        $remoteRequest->getBody() .
                        '</code></pre>';
                }
                if (method_exists($exception, 'getResponse')) {
                    $remoteResponse = $exception->getResponse();
                    $html .= '<h2>Response</h2><pre style="white-space: normal"><code>' . $remoteResponse->getBody()->getContents() . '</code></pre>';
                }
            }

            if ($html === '') {

                // We have an access token, which we may use in authenticated
                // requests against the service provider's API.
                $html .= ''
                    . '<h2>Access Token</h2><pre><code>' . $accessToken->getToken() . '</code></pre>' . "\n"
                    . '<h2>Refresh Token</h2><pre><code>' . $accessToken->getRefreshToken() . '</code></pre>' . "\n"
                    . '<h2>Expires</h2><pre><code>' . date('Y-m-d H:i:s', $accessToken->getExpires())
                    . ' (Current time: ' . date('Y-m-d H:i:s') . ')</code></pre>' . "\n"
                    . '<h2>Expired</h2><code>' . ($accessToken->hasExpired() ? 'yes' : 'no') . '</code></pre>' . "\n";

                // Using the access token, we may look up details about the resource owner.
                $resourceOwner = $provider->getResourceOwner($accessToken)->toArray();

                $resourceOwner = $resourceOwner['resource']
                    ? base64_decode($resourceOwner['resource'])
                    : var_export($resourceOwner, true);

                $html .= '<h2>Resource Owner</h2><pre>' . $resourceOwner . '</pre>';

                try {
                    // The provider provides a way to get an authenticated API request for
                    // the service, using the access token; it returns an object conforming
                    // to Psr\Http\Message\RequestInterface.
                    $request = $provider->getAuthenticatedRequest(
                        'GET',
                        'https://server/resource',
                        $accessToken
                    );

                    $html .= '<h2>Request</h2>'
                        . '<li><strong>Body</strong> : <code>' . $request->getBody()->getContents() . '</code>'
                        . '<li><strong>Headers</strong> : <pre><code>' . var_export($request->getHeaders(),
                            true) . '</code></pre>'
                        . '<li><strong>Method</strong> : <code>' . $request->getMethod() . '</code>'
                        . '<li><strong>RequestTarget</strong> : <code>' . $request->getRequestTarget() . '</code>'
                        . '<li><strong>Uri</strong> : <code>' . $request->getUri() . '</code>';
                } catch (IdentityProviderException $exception) {
                    // Failed to get the access token or user details.
                    $html .= '<h2>Identity Provider Error:</h2>' . $exception->getMessage();
                    // exit($exception->getMessage());
                }
            }

            $response->getBody()->write($html);

            return $response;
        };
    }
}
