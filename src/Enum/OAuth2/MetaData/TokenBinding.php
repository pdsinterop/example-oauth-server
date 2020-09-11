<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Enum\OAuth2\MetaData;

/**
 * draft-ietf-oauth-token-binding - OAuth 2.0 Token Binding
 */
class TokenBinding
{
    public const AS_ACCESS_TOKEN_TOKEN_BINDING_SUPPORTED = 'as_access_token_token_binding_supported';
    public const AS_REFRESH_TOKEN_TOKEN_BINDING_SUPPORTED = 'as_refresh_token_token_binding_supported';
    public const CLIENT_ACCESS_TOKEN_TOKEN_BINDING_SUPPORTED = 'client_access_token_token_binding_supported';
    public const CLIENT_REFRESH_TOKEN_TOKEN_BINDING_SUPPORTED = 'client_refresh_token_token_binding_supported';
    public const CLIENT_SECRET_TOKEN_BOUND_JWT = 'client_secret_token_bound_jwt';
    public const PRIVATE_KEY_TOKEN_BOUND_JWT = 'private_key_token_bound_jwt';
}
