<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Server\Factory;

use DateInterval;
use Exception;
use InvalidArgumentException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use Pdsinterop\Authentication\Enum\OAuth2\GrantType;
use Pdsinterop\Authentication\Server\Expiration;

class GrantTypeFactory
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var Expiration */
    private $expiration;
    /** @var RepositoryFactory */
    private $repositoryFactory;

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function __construct(Expiration $expiration, RepositoryFactory $repositoryFactory)
    {
        $this->expiration = $expiration;
        $this->repositoryFactory = $repositoryFactory;
    }

    /**
     * @param string $grantType
     *
     * @return GrantTypeInterface
     *
     * @throws Exception
     */
    final public function createGrantType(string $grantType) : GrantTypeInterface
    {
        $expiration = $this->expiration;
        $factory = $this->repositoryFactory;

        switch ($grantType) {
            case GrantType::AUTH_CODE:
                $grant = $this->createAuthCodeGrant($factory, $expiration->forAuthCode());
                $grant->setRefreshTokenTTL($expiration->forRefreshToken());
                break;


            case GrantType::CLIENT_CREDENTIALS:
                $grant = $this->createClientCredentialsGrant();
                break;

            case GrantType::IMPLICIT:
                $grant = $this->createImplicitGrant($expiration->forAccessToken());
                break;

            case GrantType::PASSWORD:
                $grant = $this->createPasswordGrant($factory);
                break;

            case GrantType::REFRESH_TOKEN:
                $grant = $this->createRefreshTokenGrant($factory);
                break;

            default:
                throw new InvalidArgumentException('Given grant type "' . $grantType . '"is not supported');
        }

        return $grant;
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function createClientCredentialsGrant() : ClientCredentialsGrant
    {
        return new ClientCredentialsGrant();
    }

    /**
     * @param RepositoryFactory $factory
     * @param DateInterval $expiration
     *
     * @return AuthCodeGrant
     *
     * @throws Exception
     */
    private function createAuthCodeGrant(RepositoryFactory $factory, DateInterval $expiration) : AuthCodeGrant
    {
        return new AuthCodeGrant(
            $factory->createAuthCodeRepository(),
            $factory->createRefreshTokenRepository(),
            $expiration
        );
    }

    private function createImplicitGrant(DateInterval $expiration) : ImplicitGrant
    {
        return new ImplicitGrant($expiration);
    }

    private function createPasswordGrant(RepositoryFactory $factory) : PasswordGrant
    {
        return new PasswordGrant(
            $factory->createUserRepository(),
            $factory->createRefreshTokenRepository()
        );
    }

    private function createRefreshTokenGrant(RepositoryFactory $factory) : RefreshTokenGrant
    {
        return new RefreshTokenGrant(
            $factory->createRefreshTokenRepository()
        );
    }
}
