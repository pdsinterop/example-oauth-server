<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Enum\OAuth2\MetaData;

/**
 * - RFC7591 - OAuth 2.0 Dynamic Client Registration Protocol
 */
class DynamicClientRegistration
{
    public const CLIENT_NAME = 'client_name';
    public const CLIENT_URI = 'client_uri';
    public const CONTACTS = 'contacts';
    public const GRANT_TYPES = 'grant_types';
    public const JWKS = 'jwks';
    public const JWKS_URI = 'jwks_uri';
    public const LOGO_URI = 'logo_uri';
    public const POLICY_URI = 'policy_uri';
    public const REDIRECT_URIS = 'redirect_uris';
    public const RESPONSE_TYPES = 'response_types';
    public const SCOPE = 'scope';
    public const SOFTWARE_ID = 'software_id';
    public const SOFTWARE_VERSION = 'software_version';
    public const TOKEN_ENDPOINT_AUTH_METHOD = 'token_endpoint_auth_method';
    public const TOS_URI = 'tos_uri';
}
