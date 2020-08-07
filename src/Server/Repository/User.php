<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Server\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Pdsinterop\Authentication\Server\Entity\User as UserEntity;

class User implements UserRepositoryInterface
{
    /**
     * Get a user entity.
     *
     * @param string $username
     * @param string $password
     * @param string $grantType The grant type used
     * @param ClientEntityInterface $clientEntity
     *
     * @return UserEntityInterface|null
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) : ?UserEntityInterface {
        /*/
            This method is called to validate a user’s credentials.

            You can use the grant type to determine if the user is permitted to use the grant type.

            You can use the client entity to determine to if the user is permitted to use the client.

            If the client’s credentials are validated you should return an instance of UserEntityInterface
        /*/
        $user = new UserEntity();
        $user->setIdentifier($username);

        return $user;
    }
}
