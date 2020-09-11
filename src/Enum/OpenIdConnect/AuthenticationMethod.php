<?php declare(strict_types=1);


namespace Pdsinterop\Authentication\Enum\OpenIdConnect;

/**
 * As described in Section 9 of OpenID Connect.
 * Other authentication methods MAY be defined by extensions.
 * If omitted, the default is client_secret_basic
 */
class AuthenticationMethod
{
    public const CLIENT_SECRET_BASIC = 'client_secret_basic';
    public const DEFAULT = self::CLIENT_SECRET_BASIC;
    public const CLIENT_SECRET_JWT = 'client_secret_jwt';
    public const CLIENT_SECRET_POST = 'client_secret_post';
    public const NONE = 'none';
    public const PRIVATE_KEY_JWT = 'private_key_jwt';
}
