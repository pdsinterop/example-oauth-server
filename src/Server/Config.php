<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Server;

use Pdsinterop\Authentication\Server\Config\Client;

class Config
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var Expiration */
    private $expiration;
    /** @var array */
    private $grantTypes;
    /**@var Keys */
    private $keys;

    //////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function getExpiration() : Expiration
    {
        return $this->expiration;
    }

    public function getGrantTypes() : array
    {
        return $this->grantTypes;
    }

    public function getKeys() : Keys
    {
        return $this->keys;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function __construct(Client $client, Expiration $expiration, array $grantTypes, Keys $keys)
    {
        $this->expiration = $expiration;
        $this->grantTypes = $grantTypes;
        $this->keys = $keys;
    }
}

