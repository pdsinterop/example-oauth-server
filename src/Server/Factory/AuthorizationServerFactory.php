<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Server\Factory;

use League\OAuth2\Server\AuthorizationServer;
use Pdsinterop\Authentication\Server\Expiration;
use Pdsinterop\Authentication\Server\Keys;

class AuthorizationServerFactory
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var Expiration */
    private $expiration;
    /** @var GrantTypeFactory */
    private $grantTypeFactory;
    /** @var Keys */
    private $keys;
    /** @var RepositoryFactory */
    private $repositoryFactory;

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function __construct(RepositoryFactory $repositoryFactory, GrantTypeFactory $grantTypeFactory, Keys $keys, Expiration $expiration)
    {
        $this->expiration = $expiration;
        $this->grantTypeFactory = $grantTypeFactory;
        $this->keys = $keys;
        $this->repositoryFactory = $repositoryFactory;
    }

    final public function createAuthorizationServer($grantTypes) : AuthorizationServer
    {
        $expiration = $this->expiration;
        $grantTypeFactory = $this->grantTypeFactory;
        $keys = $this->keys;

        $server = $this->createServer($keys);

        array_walk($grantTypes, static function ($grantType) use ($expiration, $grantTypeFactory, $server) {
            $grant = $grantTypeFactory->createGrantType($grantType);

            $server->enableGrantType(
                $grant,
                $expiration->forAccessToken()
            );
        });

        return $server;
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function createServer(Keys $keys) : AuthorizationServer
    {
        return new AuthorizationServer(
            $this->repositoryFactory->createClientRepository(),
            $this->repositoryFactory->createAccessTokenRepository(),
            $this->repositoryFactory->createScopeRepository(),
            $keys->getPrivateKey(),
            $keys->getEncryptionKey()
        );
    }
}
