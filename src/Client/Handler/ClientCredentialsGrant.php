<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Client\Handler;

use Pdsinterop\Authentication\Client\Provider;
use Pdsinterop\Authentication\Enum\OAuth2\GrantType;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ClientCredentialsGrant extends AbstractHandler
{
    //////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function getGrantType() : string
    {
        return GrantType::CLIENT_CREDENTIALS;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function __invoke(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $provider = $this->getProvider();
        $response = $this->getResponse();

        $method = strtoupper($request->getMethod());

        switch ($method) {
            case 'POST':
                $response = $this->createPostResponse(
                    $provider,
                    $request,
                    $response
                );
                break;

            case 'GET':
            default:
                $response = $this->createGetResponse($provider, $response);
                break;
        }

        return $response;
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function createGetResponse(Provider $provider, ResponseInterface $response) : ResponseInterface
    {
        $html = $this->buildClientForm('Client Credentials Grant', <<<'HTML'
<p>
    The client credentials grant is meant for machine-to-machine authentication.
</p>
<p>
    This grant is useful when a client that represent a user needs to authenticate
    itself, representing the user
</p>
<p>
    The client sends the following request to the authorization server:
</p>
HTML
);

        $response->getBody()->write($html);

        return $response;
    }

    private function createPostResponse(
        Provider $provider,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : ResponseInterface {
        $post = $request->getParsedBody();

        $message = <<<'HTML'
<p>The authorization server will respond with a JSON object containing the following properties:</p>

<li>token_type with the value Bearer</li>
<li>expires_in with an integer representing the TTL of the access token</li>
<li>access_token a JWT signed with the authorization server’s private key</li>
HTML;

        $response->getBody()->write($message);

        return $response;
    }
}
