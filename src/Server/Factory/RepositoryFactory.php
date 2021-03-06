<?php /** @noinspection PhpIncompatibleReturnTypeInspection */

declare(strict_types=1);

namespace Pdsinterop\Authentication\Server\Factory;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\RepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Pdsinterop\Authentication\Server\Enum\Repository;

class RepositoryFactory
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var array */
    private $repositories;

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function __construct(array $repositories = [])
    {
        $this->repositories = $repositories;
    }

    final public function createAccessTokenRepository() : AccessTokenRepositoryInterface
    {
        return $this->createOnce(Repository::ACCESS_TOKEN);
    }

    final public function createAuthCodeRepository() : AuthCodeRepositoryInterface
    {
        static $clientEntity;

        if ($this->repositories[Repository::AUTH_CODE] === null) {
            $clientEntity = $this->createClientRepository()->createClientEntity();
        }

        return $this->createOnce(Repository::AUTH_CODE, [$clientEntity]);
    }

    final public function createClientRepository() : ClientRepositoryInterface
    {
        return $this->createOnce(Repository::CLIENT);
    }

    final public function createRefreshTokenRepository() : RefreshTokenRepositoryInterface
    {
        return $this->createOnce(Repository::REFRESH_TOKEN);
    }

    final public function createScopeRepository() : ScopeRepositoryInterface
    {
        return $this->createOnce(Repository::SCOPE);
    }

    final public function createUserRepository() : UserRepositoryInterface
    {
        return $this->createOnce(Repository::USER);
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function createOnce(string $className, array $properties = []): RepositoryInterface
    {
        if ($this->repositories[$className] === null) {
            $this->repositories[$className] = $this->create($className, $properties);
        }

        return $this->repositories[$className];
    }

    private function create(string $className, array $properties = []) : RepositoryInterface
    {
        return new $className(...$properties);
    }
}
