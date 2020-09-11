<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Enum\OAuth2\MetaData;

/**
 * RFC8705 - OAuth 2.0 Mutual-TLS Client Authentication and Certificate-Bound Access Tokens Metadata
 */
class CertificateBoundAccessTokens
{
    public const MTLS_ENDPOINT_ALIASES = 'mtls_endpoint_aliases';
    public const SELF_SIGNED_TLS_CLIENT_AUTH = 'self_signed_tls_client_auth';
    public const TLS_CLIENT_AUTH = 'tls_client_auth';
    public const TLS_CLIENT_AUTH_SAN_DNS = 'tls_client_auth_san_dns';
    public const TLS_CLIENT_AUTH_SAN_EMAIL = 'tls_client_auth_san_email';
    public const TLS_CLIENT_AUTH_SAN_IP = 'tls_client_auth_san_ip';
    public const TLS_CLIENT_AUTH_SAN_URI = 'tls_client_auth_san_uri';
    public const TLS_CLIENT_AUTH_SUBJECT_DN = 'tls_client_auth_subject_dn';
    public const TLS_CLIENT_CERTIFICATE_BOUND_ACCESS_TOKENS = 'tls_client_certificate_bound_access_tokens';
}
