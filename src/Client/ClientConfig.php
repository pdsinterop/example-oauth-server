<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Client;

use Defuse\Crypto\Exception\BadFormatException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Key;

class ClientConfig
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var string */
    private $clientIdentifier;
    /** @var string */
    private $clientSecret;
    /** @var string */
    private $encryptionKey;
    /** @var string */
    private $privateKey;

    //////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function getClientIdentifier() : string
    {
        return $this->clientIdentifier;
    }

    public function getClientSecret() : string
    {
        return $this->clientSecret;
    }

    public function getEncryptionKey() : string
    {
        return $this->encryptionKey;
    }

    public function getPrivateKey() : string
    {
        return $this->privateKey;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /**
     * ClientConfig constructor.
     *
     * @param string $clientIdentifier
     * @param string $clientSecret
     * @param string $encryptionKey
     * @param string $privateKey
     * @param false $isCrypto
     *
     * @throws BadFormatException
     * @throws EnvironmentIsBrokenException
     */
    final public function __construct(
        string $clientIdentifier,
        string $clientSecret,
        string $encryptionKey,
        string $privateKey,
        $isCrypto = false
    ) {
        $this->clientIdentifier = $clientIdentifier;
        $this->clientSecret = $clientSecret;
        $this->encryptionKey = $encryptionKey;
        $this->privateKey = $privateKey;

        // @FIXME: Add proper error handling
        if ($isCrypto === true) {
            try {
                $this->encryptionKey = Key::loadFromAsciiSafeString($this->encryptionKey);
            } catch (BadFormatException $exception) {
                throw $exception;
            } catch (EnvironmentIsBrokenException $exception) {
                throw $exception;
            }
        }
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
}
