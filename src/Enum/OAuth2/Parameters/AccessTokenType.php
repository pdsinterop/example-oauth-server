<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Enum\OAuth2\Parameters;

/**
 * OAuth Access Token Types
 *
 * @see https://www.iana.org/assignments/oauth-parameters/oauth-parameters.xhtml#token-types
 */
class AccessTokenType
{
    // RFC6750 - The OAuth 2.0 Authorization Framework: Bearer Token Usage
    public const BEARER = 'bearer';

    // draft-ietf-oauth-v2-http-mac - OAuth 2.0 Message Authentication Code (MAC) Tokens
    public const MESSAGE_AUTHENTICATION_CODE = 'MAC';
}
