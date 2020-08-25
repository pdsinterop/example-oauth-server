<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Client\Handler;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Pdsinterop\Authentication\Client\Enum\Field;
use Pdsinterop\Authentication\Enum\OAuth2\GrantType;
use Pdsinterop\Authentication\Enum\OAuth2\Parameter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class PasswordGrant extends AbstractHandler
{
    //////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function getGrantType() : string
    {
        return GrantType::PASSWORD;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function __invoke(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $response = $this->getResponse();

        $method = strtoupper($request->getMethod());

        switch ($method) {
            case 'POST':
                $response = $this->createPostResponse(
                    $this->getProvider(),
                    $request,
                    $response
                );
                break;

            case 'GET':
            default:
                $response = $this->createGetResponse($response);
                break;
        }

        return $response;
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function createPostResponse(
        AbstractProvider $provider,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : ResponseInterface {
        $post = $request->getParsedBody();

        try {
            // Try to get an access token using the resource owner password credentials grant.
            $accessToken = $provider->getAccessToken(GrantType::PASSWORD, [
                Parameter::USERNAME => $post[Field::USERNAME],
                Parameter::PASSWORD => $post[Field::PASSWORD],
            ]);
        } catch (IdentityProviderException $exception) {
            // Failed to get the access token
            exit('Error retrieving identity: ' . $exception->getMessage());
        } catch (Throwable $exception) {
            // Some other problem
            exit(get_class($exception) . ': ' . $exception->getMessage());
        }

        $message = <<<"HTML"
<p>Received Access Token</p>
<pre style="white-space: pre-wrap; word-wrap: break-word"><code>{$accessToken}</code></pre>
HTML;

        $response->getBody()->write($message);

        return $response;
    }

    private function createGetResponse(ResponseInterface $response) : ResponseInterface
    {
        $username = Field::USERNAME;
        $password = Field::PASSWORD;

        $response->getBody()->write(<<<"HTML"
            <h1>Log in</h1>

            <form action="" method="post">
                <label><em>User Name</em><input type="text" name="{$username}"/></label>
                <label><em>Password</em><input type="password" name="{$password}" /></label>
                <button>Submit</button>
            </form>
HTML
        );

        return $response;
    }
}
