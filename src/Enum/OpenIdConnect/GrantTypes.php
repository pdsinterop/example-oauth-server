<?php declare(strict_types=1);


namespace Pdsinterop\Authentication\Enum\OpenIdConnect;

/**
 * The OAuth 2.0 Grant Type values used by OpenID Connect are
 *
 * If omitted, the default is that the Client will use only the
 * authorization_code Grant Type.
 */
class GrantTypes
{
    /**
     * The Authorization Code Grant Type described in OAuth 2.0 Section 4.1.
     */
    public const AUTHORIZATION_CODE = 'authorization_code';

    /**
     * The Implicit Grant Type described in OAuth 2.0 Section 4.2.
     *
     * @deprecated In favour of Authorization Code flow with PKCE
     */
    public const IMPLICIT = 'implicit';

    /**
     * The Refresh Token Grant Type described in OAuth 2.0 Section 6.
     */
    public const REFRESH_TOKEN = 'refresh_token';

}
