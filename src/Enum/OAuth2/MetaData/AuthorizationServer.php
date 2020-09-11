<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Enum\OAuth2\MetaData;

/**
 * - RFC8414 - OAuth 2.0 Authorization Server Metadata
 */
class AuthorizationServer
{
    public const AUTHORIZATION_ENDPOINT = 'authorization_endpoint';
    public const CODE_CHALLENGE_METHODS_SUPPORTED = 'code_challenge_methods_supported';
    public const GRANT_TYPES_SUPPORTED = 'grant_types_supported';
    public const INTROSPECTION_ENDPOINT = 'introspection_endpoint';
    public const INTROSPECTION_ENDPOINT_AUTH_METHODS_SUPPORTED = 'introspection_endpoint_auth_methods_supported';
    public const INTROSPECTION_ENDPOINT_AUTH_SIGNING_ALG_VALUES_SUPPORTED = 'introspection_endpoint_auth_signing_alg_values_supported';
    public const ISSUER = 'issuer';
    public const JWKS_URI = 'jwks_uri';
    public const OP_POLICY_URI = 'op_policy_uri';
    public const OP_TOS_URI = 'op_tos_uri';
    public const REGISTRATION_ENDPOINT = 'registration_endpoint';
    public const RESPONSE_MODES_SUPPORTED = 'response_modes_supported';
    public const RESPONSE_TYPES_SUPPORTED = 'response_types_supported';
    public const REVOCATION_ENDPOINT = 'revocation_endpoint';
    public const REVOCATION_ENDPOINT_AUTH_METHODS_SUPPORTED = 'revocation_endpoint_auth_methods_supported';
    public const REVOCATION_ENDPOINT_AUTH_SIGNING_ALG_VALUES_SUPPORTED = 'revocation_endpoint_auth_signing_alg_values_supported';
    public const SCOPES_SUPPORTED = 'scopes_supported';
    public const SERVICE_DOCUMENTATION = 'service_documentation';
    public const TOKEN_ENDPOINT = 'token_endpoint';
    public const TOKEN_ENDPOINT_AUTH_METHODS_SUPPORTED = 'token_endpoint_auth_methods_supported';
    public const TOKEN_ENDPOINT_AUTH_SIGNING_ALG_VALUES_SUPPORTED = 'token_endpoint_auth_signing_alg_values_supported';
    public const UI_LOCALES_SUPPORTED = 'ui_locales_supported';
}
