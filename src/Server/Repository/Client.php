<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Server\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class Client implements ClientRepositoryInterface
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var array */
    private $grantTypes;
    /** @var string */
    private $identifier;
    /** @var string */
    private $secret;

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function __construct(string $identifier, string $secret, array $grants = [])
    {
        $this->identifier = $identifier;
        $this->secret = $secret;
        $this->grantTypes = $grants;
    }

    /**
     * Create an empty client.
     *
     * @return ClientEntityInterface
     */
    public function createClientEntity() : ClientEntityInterface
    {
        return new \Pdsinterop\Authentication\Server\Entity\Client();
    }

    /**
     * Get a client.
     *
     * @param string $identifier The client's identifier
     *
     * @return ClientEntityInterface|null
     */
    public function getClientEntity($identifier) : ?ClientEntityInterface
    {
        $client = new \Pdsinterop\Authentication\Server\Entity\Client();
        $client->setIdentifier($identifier);

        return $client;
    }

    /**
     * Validate a client's secret.
     *
     * @param string $clientIdentifier The client's identifier
     * @param null|string $clientSecret The client's secret (if sent)
     * @param null|string $grantType The type of grant the client is using (if sent)
     *
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType) : bool
    {
        /*/
            This method is called to validate a client’s credentials.

            The client secret may or may not be provided depending on the request sent by the client.
            If the client is confidential (i.e. is capable of securely storing a secret) then the secret must be validated.

            You can use the grant type to determine if the client is permitted to use the grant type.

            If the client’s credentials are validated you should return true, otherwise return false.
        /*/

        return $clientIdentifier === $this->identifier
            && $clientSecret === $this->secret
            && in_array($grantType, $this->grantTypes, true)
        ;
    }
}
