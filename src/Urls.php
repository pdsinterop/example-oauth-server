<?php declare(strict_types=1);

namespace Pdsinterop\Authentication;

class Urls
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var string */
    private $accessToken;
    /** @var string */
    private $authorize;
    /** @var string */
    private $host;
    /** @var string */
    private $resourceOwnerDetails;

    //////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function getAccessToken() : string
    {
        return $this->accessToken;
    }

    final public function getAccessTokenWithHost() : string
    {
        return $this->host . $this->accessToken;
    }

    final public function getAuthorize() : string
    {
        return $this->authorize;
    }

    final public function getAuthorizeWithHost() : string
    {
        return $this->host . $this->authorize;
    }

    final public function getResourceOwnerDetails() : string
    {
        return $this->resourceOwnerDetails;
    }

    final public function getResourceOwnerDetailsWithHost() : string
    {
        return $this->host . $this->resourceOwnerDetails;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function __construct(string $host, string $accessToken, string $authorize, string $resourceOwnerDetails)
    {
        $this->host = rtrim($host, '/');

        $this->accessToken = $this->clean($accessToken);
        $this->authorize = $this->clean($authorize);
        $this->resourceOwnerDetails = $this->clean($resourceOwnerDetails);
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function clean(string $subject) : string
    {
        return '/' . ltrim($subject, '/');
    }
}
