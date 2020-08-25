<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Client\Enum;

class ClientOption
{
    public const ACCESS_TOKEN_METHOD = 'accessTokenMethod';
    public const CLIENT_ID = 'clientId'; // The client ID assigned to you by the provider
    public const CLIENT_SECRET = 'clientSecret'; // The client password assigned to you by the provider

    public const REDIRECT_URI = 'redirectUri';// Return URL of the Client Application

    /*/ Authorization server URLs /*/
    public const URL_AUTHORIZE = 'urlAuthorize';
    public const URL_ACCESS_TOKEN = 'urlAccessToken';
    public const URL_RESOURCE_OWNER_DETAILS = 'urlResourceOwnerDetails';

    public const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'accessTokenResourceOwnerId';
    public const RESPONSE_CODE = 'responseCode';
    public const RESPONSE_ERROR = 'responseError';
    public const RESPONSE_RESOURCE_OWNER_ID = 'responseResourceOwnerId';
    public const SCOPES = 'scopes';
    public const SCOPE_SEPARATOR = 'scopeSeparator';

    /*/ HttpClient Options /*/
    public const TIMEOUT = 'timeout';
    public const PROXY = 'proxy';
    public const VERIFY = 'verify';

}
