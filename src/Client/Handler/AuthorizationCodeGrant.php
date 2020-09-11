<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Client\Handler;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Pdsinterop\Authentication\Enum\OAuth2\GrantType;
use Pdsinterop\Authentication\Enum\OAuth2\Parameter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationCodeGrant extends AbstractHandler
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    //////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function getGrantType() : string
    {
        return GrantType::AUTH_CODE;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function __invoke(ServerRequestInterface $request, array $args) : ResponseInterface
    {
        // @TODO: Check if this is safe without `clone`
        $provider = $this->getProvider();
        $response = $this->getResponse();

        if ( ! isset($_GET['code'])) {
            // If we don't have an authorization code then get one

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).

            // The `code_verifier` is the original random string
            // The `code_challenge` is the hashed version of the `code_verifier` string.

            $codeVerifier = strtr(rtrim(base64_encode(random_bytes(64)), '='), '+/', '-_');
            $challenge_bytes = hash('sha256', $codeVerifier, true);
            $codeChallenge = strtr(rtrim(base64_encode($challenge_bytes), '='),'+/', '-_');
            $_SESSION['code_verifier'] = $codeVerifier;

            $authorizationUrl = $provider->getAuthorizationUrl([
                // @FIXME: The $codeVerifier and/or $codeChallenge MUST be stored so they can be checked against later
                Parameter::CODE_CHALLENGE => $codeChallenge,
                Parameter::CODE_CHALLENGE_METHOD => 'S256',
            ]);

            // Get the state generated for you and store it to the session.
            $_SESSION['oauth2state'] = $provider->getState();

            // Normally one would redirect the user to the authorization URL:
            // header('Location: ' . $authorizationUrl);
            // For this exercise we show the user the redirect link

            $html = $this->buildClientForm('Authorization Code Grant', '', $authorizationUrl);

            $response->getBody()->write($html);
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            // Check given state against previously stored one to mitigate CSRF attack
            unset($_SESSION['oauth2state']);
            $response->getBody()->write('Invalid state');
        } else {
            $html = '';

            try {
                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken(GrantType::AUTH_CODE, [
                    'code' => $_GET['code'],
                    // @CHECKME: These parameters might change depending on the demands of the used grant type
                ]);

                // We have an access token, which we may use in authenticated
                // requests against the service provider's API.
                $html .= $accessToken->getToken() . "\n";
                $html .= $accessToken->getRefreshToken() . "\n";
                $html .= $accessToken->getExpires() . "\n";
                $html .= ($accessToken->hasExpired() ? 'expired' : 'not expired') . "\n";

                // Using the access token, we may look up details about the
                // resource owner.
                $resourceOwner = $provider->getResourceOwner($accessToken);

                $html .= var_export($resourceOwner->toArray(), true);

                // The provider provides a way to get an authenticated API request for
                // the service, using the access token; it returns an object conforming
                // to Psr\Http\Message\RequestInterface.
                $request = $provider->getAuthenticatedRequest(
                    'GET',
                    'https://server/resource',
                    $accessToken
                );

                $html .= var_export($request, true);
            } catch (IdentityProviderException $exception) {
                // Failed to get the access token or user details.
                exit($exception->getMessage());
            }
            $response->getBody()->write($html);
        }

        return $response->withStatus(200);
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
}
